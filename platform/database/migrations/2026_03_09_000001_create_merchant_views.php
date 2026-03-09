<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // These views use MySQL-specific functions (JSON_ARRAYAGG, CONCAT_WS, IF, etc.)
        // Skip on non-MySQL drivers (e.g. SQLite used in tests)
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // ── 1) vw_journey_merchant_stats ──
        // KPI summary per journey
        DB::statement("
            CREATE OR REPLACE VIEW vw_journey_merchant_stats AS
            SELECT
                j.journey_id,
                j.journey_code,
                COUNT(DISTINCT pm.merchant_id) AS merchant_distinct_count,
                COUNT(pm.merchant_id) AS merchant_rows,
                COUNT(DISTINCT js.place_id) AS place_with_merchant_count,
                ROUND(AVG(sub_rating.avg_rating), 2) AS merchant_avg_rating,
                SUM(CASE WHEN pm.is_primary = 1 THEN 1 ELSE 0 END) AS merchant_primary_rows
            FROM journey j
            JOIN journey_step js ON js.journey_id = j.journey_id
            JOIN place_merchant pm ON pm.place_id = js.place_id
            JOIN merchant m ON m.merchant_id = pm.merchant_id AND m.is_active = 1
            LEFT JOIN (
                SELECT merchant_id, AVG(rating) AS avg_rating
                FROM merchant_review
                WHERE status = 'PUBLISHED' AND is_public = 1
                GROUP BY merchant_id
            ) sub_rating ON sub_rating.merchant_id = pm.merchant_id
            WHERE j.status = 'ACTIVE'
            GROUP BY j.journey_id, j.journey_code
        ");

        // ── 2) vw_merchant_search_public ──
        // Flat merchant rows for public search
        DB::statement("
            CREATE OR REPLACE VIEW vw_merchant_search_public AS
            SELECT
                j.journey_code,
                js.step_no,
                p.place_id,
                p.place_code,
                p.place_name_th,
                p.place_name_en,
                m.merchant_id,
                m.merchant_code,
                m.merchant_name_th,
                m.merchant_name_en,
                m.default_tier_code AS tier_code,
                m.price_level,
                m.open_hours,
                m.service_tags,
                pm.is_primary,
                pm.sort_order,
                COALESCE(sub_r.avg_rating, 0) AS avg_rating,
                COALESCE(sub_r.review_count, 0) AS review_count
            FROM place_merchant pm
            JOIN merchant m ON m.merchant_id = pm.merchant_id AND m.is_active = 1
            JOIN place p ON p.place_id = pm.place_id AND p.is_active = 1
            LEFT JOIN journey_step js ON js.place_id = p.place_id
            LEFT JOIN journey j ON j.journey_id = js.journey_id AND j.status = 'ACTIVE'
            LEFT JOIN (
                SELECT merchant_id,
                       ROUND(AVG(rating), 2) AS avg_rating,
                       COUNT(*) AS review_count
                FROM merchant_review
                WHERE status = 'PUBLISHED' AND is_public = 1
                GROUP BY merchant_id
            ) sub_r ON sub_r.merchant_id = m.merchant_id
        ");

        // ── 3) vw_merchant_search_blob_public ──
        // Searchable text blob for full-text-like search
        DB::statement("
            CREATE OR REPLACE VIEW vw_merchant_search_blob_public AS
            SELECT
                m.merchant_id,
                j.journey_id,
                CONCAT_WS(' ',
                    m.merchant_name_th, m.merchant_name_en,
                    m.merchant_desc_th, m.merchant_desc_en,
                    m.service_tags, m.onsite_note,
                    p.place_name_th, p.place_name_en,
                    j.journey_code
                ) AS search_text
            FROM place_merchant pm
            JOIN merchant m ON m.merchant_id = pm.merchant_id AND m.is_active = 1
            JOIN place p ON p.place_id = pm.place_id
            LEFT JOIN journey_step js ON js.place_id = p.place_id
            LEFT JOIN journey j ON j.journey_id = js.journey_id AND j.status = 'ACTIVE'
        ");

        // ── 4) vw_merchant_search_user ──
        // User-context merchant search (includes user state)
        DB::statement("
            CREATE OR REPLACE VIEW vw_merchant_search_user AS
            SELECT
                pub.*,
                u.id AS user_id,
                COALESCE(mc_agg.visit_count, 0) AS visit_count,
                IF(COALESCE(mc_agg.visit_count, 0) > 0, 1, 0) AS visited,
                mc_agg.last_checkin_at,
                IF(mf.id IS NOT NULL, 1, 0) AS is_favorite,
                IF(mw.id IS NOT NULL, 1, 0) AS is_wishlist,
                COALESCE(mr_agg.review_count_by_user, 0) AS review_count_by_user,
                mr_agg.last_review_at,
                mr_agg.last_rating_by_user
            FROM vw_merchant_search_public pub
            CROSS JOIN users u
            LEFT JOIN (
                SELECT user_id, merchant_id,
                       COUNT(*) AS visit_count,
                       MAX(created_at) AS last_checkin_at
                FROM merchant_checkin
                GROUP BY user_id, merchant_id
            ) mc_agg ON mc_agg.user_id = u.id AND mc_agg.merchant_id = pub.merchant_id
            LEFT JOIN merchant_favorite mf
                ON mf.user_id = u.id AND mf.merchant_id = pub.merchant_id
            LEFT JOIN merchant_wishlist mw
                ON mw.user_id = u.id AND mw.merchant_id = pub.merchant_id
            LEFT JOIN (
                SELECT user_id, merchant_id,
                       COUNT(*) AS review_count_by_user,
                       MAX(created_at) AS last_review_at,
                       MAX(rating) AS last_rating_by_user
                FROM merchant_review
                GROUP BY user_id, merchant_id
            ) mr_agg ON mr_agg.user_id = u.id AND mr_agg.merchant_id = pub.merchant_id
        ");

        // ── 5) vw_merchant_search_blob_user ──
        DB::statement("
            CREATE OR REPLACE VIEW vw_merchant_search_blob_user AS
            SELECT
                u.id AS user_id,
                b.merchant_id,
                b.journey_id,
                b.search_text
            FROM vw_merchant_search_blob_public b
            CROSS JOIN users u
        ");

        // ── 6) vw_journey_place_merchant_json ──
        // JSON aggregation of merchants per journey (public)
        DB::statement("
            CREATE OR REPLACE VIEW vw_journey_place_merchant_json AS
            SELECT
                j.journey_id,
                j.journey_code,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'step_no', js.step_no,
                        'place_code', p.place_code,
                        'merchant_code', m.merchant_code,
                        'merchant_name_th', m.merchant_name_th,
                        'merchant_name_en', m.merchant_name_en,
                        'tier_code', m.default_tier_code,
                        'is_primary', pm.is_primary,
                        'sort_order', pm.sort_order,
                        'open_hours', m.open_hours,
                        'service_tags', m.service_tags,
                        'avg_rating', COALESCE(sub_r.avg_rating, 0),
                        'review_count', COALESCE(sub_r.review_count, 0)
                    )
                ) AS merchants_json
            FROM journey j
            JOIN journey_step js ON js.journey_id = j.journey_id
            JOIN place p ON p.place_id = js.place_id
            JOIN place_merchant pm ON pm.place_id = p.place_id
            JOIN merchant m ON m.merchant_id = pm.merchant_id AND m.is_active = 1
            LEFT JOIN (
                SELECT merchant_id,
                       ROUND(AVG(rating), 2) AS avg_rating,
                       COUNT(*) AS review_count
                FROM merchant_review
                WHERE status = 'PUBLISHED' AND is_public = 1
                GROUP BY merchant_id
            ) sub_r ON sub_r.merchant_id = m.merchant_id
            WHERE j.status = 'ACTIVE'
            GROUP BY j.journey_id, j.journey_code
        ");

        // ── 7) vw_journey_merchant_json_user ──
        // JSON merchants + user state per journey
        DB::statement("
            CREATE OR REPLACE VIEW vw_journey_merchant_json_user AS
            SELECT
                j.journey_id,
                j.journey_code,
                u.id AS user_id,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'step_no', js.step_no,
                        'place_code', p.place_code,
                        'merchant_code', m.merchant_code,
                        'merchant_name_th', m.merchant_name_th,
                        'merchant_name_en', m.merchant_name_en,
                        'tier_code', m.default_tier_code,
                        'is_primary', pm.is_primary,
                        'sort_order', pm.sort_order,
                        'user_state', JSON_OBJECT(
                            'visit_count', COALESCE(mc_agg.visit_count, 0),
                            'last_checkin_at', mc_agg.last_checkin_at,
                            'visited', IF(COALESCE(mc_agg.visit_count, 0) > 0, 1, 0),
                            'is_favorite', IF(mf.id IS NOT NULL, 1, 0),
                            'is_wishlist', IF(mw.id IS NOT NULL, 1, 0),
                            'review_count_by_user', COALESCE(mr_agg.review_count_by_user, 0),
                            'last_review_at', mr_agg.last_review_at,
                            'last_rating', mr_agg.last_rating_by_user
                        )
                    )
                ) AS merchants_json
            FROM journey j
            JOIN journey_step js ON js.journey_id = j.journey_id
            JOIN place p ON p.place_id = js.place_id
            JOIN place_merchant pm ON pm.place_id = p.place_id
            JOIN merchant m ON m.merchant_id = pm.merchant_id AND m.is_active = 1
            CROSS JOIN users u
            LEFT JOIN (
                SELECT user_id, merchant_id,
                       COUNT(*) AS visit_count,
                       MAX(created_at) AS last_checkin_at
                FROM merchant_checkin
                GROUP BY user_id, merchant_id
            ) mc_agg ON mc_agg.user_id = u.id AND mc_agg.merchant_id = m.merchant_id
            LEFT JOIN merchant_favorite mf
                ON mf.user_id = u.id AND mf.merchant_id = m.merchant_id
            LEFT JOIN merchant_wishlist mw
                ON mw.user_id = u.id AND mw.merchant_id = m.merchant_id
            LEFT JOIN (
                SELECT user_id, merchant_id,
                       COUNT(*) AS review_count_by_user,
                       MAX(created_at) AS last_review_at,
                       MAX(rating) AS last_rating_by_user
                FROM merchant_review
                GROUP BY user_id, merchant_id
            ) mr_agg ON mr_agg.user_id = u.id AND mr_agg.merchant_id = m.merchant_id
            WHERE j.status = 'ACTIVE'
            GROUP BY j.journey_id, j.journey_code, u.id
        ");

        // ── 8) vw_api_journey_onecall_with_merchants_stats_final ──
        // Full one-call view: journey core + i18n + merchants JSON + stats
        DB::statement("
            CREATE OR REPLACE VIEW vw_api_journey_onecall_with_merchants_stats_final AS
            SELECT
                j.journey_id,
                j.journey_code,
                j.journey_name_th,
                j.journey_name_en,
                j.group_size,
                j.gmv_per_person,
                j.gmv_per_group,
                j.tp_total_normal,
                j.tp_total_goal,
                j.tp_total_special,
                j.total_minutes_sum,
                j.luxury_tone_th,
                j.luxury_tone_en,
                j.target_visitors,
                pmj.merchants_json,
                COALESCE(stats.merchant_rows, 0) AS merchant_rows,
                COALESCE(stats.merchant_distinct_count, 0) AS merchant_distinct_count,
                COALESCE(stats.place_with_merchant_count, 0) AS place_with_merchant_count,
                COALESCE(stats.merchant_avg_rating, 0) AS merchant_avg_rating,
                COALESCE(stats.merchant_primary_rows, 0) AS merchant_primary_rows
            FROM journey j
            LEFT JOIN vw_journey_place_merchant_json pmj
                ON pmj.journey_id = j.journey_id
            LEFT JOIN vw_journey_merchant_stats stats
                ON stats.journey_id = j.journey_id
            WHERE j.status = 'ACTIVE'
        ");

        // ── 9) vw_api_journey_onecall_with_merchants_user ──
        // User one-call: journey core + user merchants JSON
        DB::statement("
            CREATE OR REPLACE VIEW vw_api_journey_onecall_with_merchants_user AS
            SELECT
                j.journey_id,
                j.journey_code,
                j.journey_name_th,
                j.journey_name_en,
                j.group_size,
                j.gmv_per_person,
                j.gmv_per_group,
                j.tp_total_normal,
                j.tp_total_goal,
                j.tp_total_special,
                j.total_minutes_sum,
                j.luxury_tone_th,
                j.luxury_tone_en,
                umj.user_id,
                umj.merchants_json AS merchants_json_user
            FROM journey j
            JOIN vw_journey_merchant_json_user umj
                ON umj.journey_id = j.journey_id
            WHERE j.status = 'ACTIVE'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('DROP VIEW IF EXISTS vw_api_journey_onecall_with_merchants_user');
        DB::statement('DROP VIEW IF EXISTS vw_api_journey_onecall_with_merchants_stats_final');
        DB::statement('DROP VIEW IF EXISTS vw_journey_merchant_json_user');
        DB::statement('DROP VIEW IF EXISTS vw_journey_place_merchant_json');
        DB::statement('DROP VIEW IF EXISTS vw_merchant_search_blob_user');
        DB::statement('DROP VIEW IF EXISTS vw_merchant_search_user');
        DB::statement('DROP VIEW IF EXISTS vw_merchant_search_blob_public');
        DB::statement('DROP VIEW IF EXISTS vw_merchant_search_public');
        DB::statement('DROP VIEW IF EXISTS vw_journey_merchant_stats');
    }
};
