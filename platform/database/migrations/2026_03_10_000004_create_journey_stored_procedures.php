<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Creates stored procedures from the production database dump (u504097778_journey.sql).
 * These procedures provide optimized API endpoints for journey search, listing, and recommendations.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_api_journeys_list`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_api_journeys_list` (IN `p_query` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_tag_code` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_persona_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_country_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_min_gmv` INT, IN `p_max_gmv` INT, IN `p_max_minutes` INT, IN `p_limit` INT, IN `p_offset` INT)
BEGIN
DECLARE v_limit INT DEFAULT 50;
  DECLARE v_offset INT DEFAULT 0;

  IF p_limit IS NOT NULL AND p_limit > 0 THEN SET v_limit = p_limit; END IF;
  IF p_offset IS NOT NULL AND p_offset >= 0 THEN SET v_offset = p_offset; END IF;

  SET SESSION group_concat_max_len = 1000000;

  SELECT l.*
  FROM vw_api_journey_list l
  LEFT JOIN vw_journey_search_blob sb ON sb.journey_id = l.journey_id
  WHERE 1=1
    -- LIKE collation
    AND (
      p_query IS NULL
      OR (sb.search_text COLLATE utf8mb4_uca1400_ai_ci
          LIKE (CONCAT('%', p_query, '%') COLLATE utf8mb4_uca1400_ai_ci))
    )
    AND (p_min_gmv IS NULL OR l.gmv_per_person >= p_min_gmv)
    AND (p_max_gmv IS NULL OR l.gmv_per_person <= p_max_gmv)
    AND (p_max_minutes IS NULL OR l.total_minutes_sum <= p_max_minutes)

    -- '=' collation: TAG
    AND (p_tag_code IS NULL OR EXISTS (
      SELECT 1
      FROM journey_tag jt
      JOIN md_tag t ON t.tag_id = jt.tag_id
      WHERE jt.journey_id = l.journey_id
        AND (t.tag_code COLLATE utf8mb4_uca1400_ai_ci) = (p_tag_code COLLATE utf8mb4_uca1400_ai_ci)
    ))

    -- '=' collation: PERSONA
    AND (p_persona_code IS NULL OR EXISTS (
      SELECT 1
      FROM journey_segment js
      WHERE js.journey_id = l.journey_id
        AND (js.persona_code COLLATE utf8mb4_uca1400_ai_ci) = (p_persona_code COLLATE utf8mb4_uca1400_ai_ci)
    ))

    -- '=' collation: COUNTRY
    AND (p_country_code IS NULL OR EXISTS (
      SELECT 1
      FROM journey_market_fit mf
      WHERE mf.journey_id = l.journey_id
        AND (mf.country_code COLLATE utf8mb4_uca1400_ai_ci) = (p_country_code COLLATE utf8mb4_uca1400_ai_ci)
    ))

  ORDER BY l.gmv_per_person DESC, l.journey_code
  LIMIT v_offset, v_limit;
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_api_journey_detail`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_api_journey_detail` (IN `p_code` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci)
BEGIN
SELECT * FROM vw_journey_json_multi
  WHERE (journey_code COLLATE utf8mb4_uca1400_ai_ci) = (p_code COLLATE utf8mb4_uca1400_ai_ci);
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_api_journey_get`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_api_journey_get` (IN `p_journey_code` VARCHAR(20))
BEGIN
SELECT *
  FROM vw_api_journey_onecall_multi
  WHERE (journey_code COLLATE utf8mb4_unicode_ci)
        = (CONVERT(p_journey_code USING utf8mb4) COLLATE utf8mb4_unicode_ci)
  LIMIT 1;
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_api_search`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_api_search` (IN `p_query` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_limit` INT)
BEGIN
DECLARE v_limit INT DEFAULT 50;
  IF p_limit IS NOT NULL AND p_limit > 0 THEN SET v_limit = p_limit; END IF;

  SET SESSION group_concat_max_len = 1000000;

  SELECT
    l.journey_code,
    l.journey_name_th,
    l.gmv_per_person,
    l.total_minutes_sum,
    LEFT(sb.search_text, 300) AS matched_preview
  FROM vw_api_journey_list l
  JOIN vw_journey_search_blob sb ON sb.journey_id = l.journey_id
  WHERE (sb.search_text COLLATE utf8mb4_uca1400_ai_ci
         LIKE (CONCAT('%', p_query, '%') COLLATE utf8mb4_uca1400_ai_ci))
  ORDER BY l.gmv_per_person DESC
  LIMIT v_limit;
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_recommend_journeys`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_recommend_journeys` (IN `p_seed_code` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_country_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_limit` INT)
BEGIN
DECLARE v_seed_id INT;
  DECLARE v_limit INT DEFAULT 10;

  IF p_limit IS NOT NULL AND p_limit > 0 THEN SET v_limit = p_limit; END IF;

  SELECT journey_id INTO v_seed_id
  FROM journey
  WHERE (journey_code COLLATE utf8mb4_uca1400_ai_ci) = (p_seed_code COLLATE utf8mb4_uca1400_ai_ci)
  LIMIT 1;

  -- Next5
  SELECT
    'NEXT5' AS source,
    j2.journey_code,
    ji2.journey_name AS journey_name_th,
    s2.gmv_per_person,
    m2.total_minutes_sum,
    jn.next_rank AS rank_or_score
  FROM journey_next jn
  JOIN journey j2 ON j2.journey_id=jn.next_journey_id
  JOIN journey_i18n ji2 ON ji2.journey_id=j2.journey_id AND ji2.lang='th'
  JOIN vw_journey_summary_th s2 ON s2.journey_id=j2.journey_id
  JOIN vw_journey_minutes m2 ON m2.journey_id=j2.journey_id
  WHERE jn.journey_id=v_seed_id
  ORDER BY jn.next_rank;

  -- Similar (ตัด seed + ตัด next5)
  SELECT
    'SIMILAR' AS source,
    j.journey_code,
    ji.journey_name AS journey_name_th,
    s.gmv_per_person,
    m.total_minutes_sum,
    (COALESCE(mf.fit_level,0)) AS rank_or_score
  FROM journey j
  JOIN journey_i18n ji ON ji.journey_id=j.journey_id AND ji.lang='th'
  JOIN vw_journey_summary_th s ON s.journey_id=j.journey_id
  JOIN vw_journey_minutes m ON m.journey_id=j.journey_id
  LEFT JOIN (
    SELECT journey_id, fit_level
    FROM journey_market_fit
    WHERE (country_code COLLATE utf8mb4_uca1400_ai_ci) = (p_country_code COLLATE utf8mb4_uca1400_ai_ci)
  ) mf ON mf.journey_id=j.journey_id
  WHERE j.is_active=1
    AND j.journey_id <> v_seed_id
    AND j.journey_id NOT IN (SELECT next_journey_id FROM journey_next WHERE journey_id=v_seed_id)
  ORDER BY rank_or_score DESC, s.gmv_per_person DESC
  LIMIT v_limit;
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_refresh_journey_market_zones`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_refresh_journey_market_zones` ()
BEGIN
-- rebuild for all active journeys
  DELETE jmz
  FROM journey_market_zone jmz
  JOIN journey j ON j.journey_id=jmz.journey_id
  WHERE j.is_active=1;

  -- Insert rule-based zones (aggregate to max fit_level per journey+zone)
  INSERT INTO journey_market_zone(journey_id, zone_code, fit_level, note)
  SELECT journey_id, zone_code, MAX(fit_level) AS fit_level, MAX(note) AS note
  FROM (
    -- Default base: ASEAN
    SELECT j.journey_id, 'ZONE_ASEAN' zone_code, 3 fit_level, 'Base fit for Pattaya short-haul travelers' note
    FROM journey j WHERE j.is_active=1

    UNION ALL
    -- Budget / markets / street food
    SELECT j.journey_id,'ZONE_ASEAN',5,'Budget/market/food friendly'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_BUDGET' COLLATE utf8mb4_unicode_ci,
          'TAG_STREET_FOOD' COLLATE utf8mb4_unicode_ci,
          'TAG_MARKET' COLLATE utf8mb4_unicode_ci,
          'TAG_NIGHT_MARKET' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_INDIA',4,'Value + group/family preference'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_BUDGET' COLLATE utf8mb4_unicode_ci,
          'TAG_STREET_FOOD' COLLATE utf8mb4_unicode_ci,
          'TAG_FAMILY' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_CHINA',4,'Food + shopping + landmarks'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_MARKET' COLLATE utf8mb4_unicode_ci,
          'TAG_SHOPPING' COLLATE utf8mb4_unicode_ci,
          'TAG_PHOTO_SPOT' COLLATE utf8mb4_unicode_ci
        )
    )

    UNION ALL
    -- Nightlife / Shows
    SELECT j.journey_id,'ZONE_CHINA',5,'Shows + nightlife are strong drivers'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_SHOW' COLLATE utf8mb4_unicode_ci,
          'TAG_NIGHTLIFE' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_EU_E',5,'Eastern Europe (incl. RU) aligns with nightlife/beach'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_SHOW' COLLATE utf8mb4_unicode_ci,
          'TAG_NIGHTLIFE' COLLATE utf8mb4_unicode_ci,
          'TAG_BEACH' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_SOUTH_AMERICA',3,'Fun & nightlife friendly'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_NIGHTLIFE' COLLATE utf8mb4_unicode_ci,
          'TAG_SHOW' COLLATE utf8mb4_unicode_ci
        )
    )

    UNION ALL
    -- Wellness / Spa / Green
    SELECT j.journey_id,'ZONE_EU_W',5,'Wellness & sustainability preference'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WELLNESS' COLLATE utf8mb4_unicode_ci,
          'TAG_SPA' COLLATE utf8mb4_unicode_ci,
          'TAG_GREEN' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_JAPAN',5,'Quality + wellness + cafes'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WELLNESS' COLLATE utf8mb4_unicode_ci,
          'TAG_SPA' COLLATE utf8mb4_unicode_ci,
          'TAG_CAFE' COLLATE utf8mb4_unicode_ci,
          'TAG_PHOTO_SPOT' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_NA',4,'Wellness & full-day activities'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WELLNESS' COLLATE utf8mb4_unicode_ci,
          'TAG_SPA' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_OCEANIA',4,'Beach + wellness + activities'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WELLNESS' COLLATE utf8mb4_unicode_ci,
          'TAG_BEACH' COLLATE utf8mb4_unicode_ci,
          'TAG_ISLAND' COLLATE utf8mb4_unicode_ci
        )
    )

    UNION ALL
    -- Business / Workation / MICE
    SELECT j.journey_id,'ZONE_NA',5,'Business + MICE + networking'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WORKATION' COLLATE utf8mb4_unicode_ci,
          'TAG_COWORKING' COLLATE utf8mb4_unicode_ci,
          'TAG_NETWORKING' COLLATE utf8mb4_unicode_ci,
          'TAG_MICE' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_EU_W',5,'Business + standards'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WORKATION' COLLATE utf8mb4_unicode_ci,
          'TAG_COWORKING' COLLATE utf8mb4_unicode_ci,
          'TAG_MICE' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_JAPAN',4,'Business short breaks / workation'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WORKATION' COLLATE utf8mb4_unicode_ci,
          'TAG_COWORKING' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_CHINA',4,'Business + entertainment mix'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_WORKATION' COLLATE utf8mb4_unicode_ci,
          'TAG_SHOW' COLLATE utf8mb4_unicode_ci,
          'TAG_SHOPPING' COLLATE utf8mb4_unicode_ci
        )
    )

    UNION ALL
    -- Premium / VIP / Hotel / Yacht / Shopping
    SELECT j.journey_id,'ZONE_ARAB',4,'Premium/VIP + safety-first service'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_VIP' COLLATE utf8mb4_unicode_ci,
          'TAG_YACHT' COLLATE utf8mb4_unicode_ci,
          'TAG_HOTEL' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_OCEANIA',3,'Premium beach lifestyle'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_YACHT' COLLATE utf8mb4_unicode_ci,
          'TAG_BEACH' COLLATE utf8mb4_unicode_ci
        )
    )
    UNION ALL
    SELECT j.journey_id,'ZONE_EU_E',4,'Beach + nightlife + yacht'
    FROM journey j
    WHERE j.is_active=1 AND EXISTS (
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=j.journey_id
        AND (t.tag_code COLLATE utf8mb4_unicode_ci) IN (
          'TAG_YACHT' COLLATE utf8mb4_unicode_ci,
          'TAG_BEACH' COLLATE utf8mb4_unicode_ci,
          'TAG_NIGHTLIFE' COLLATE utf8mb4_unicode_ci
        )
    )
  ) z
  GROUP BY journey_id, zone_code;
END
SQL);

        DB::unprepared(<<<'SQL'
DROP PROCEDURE IF EXISTS `sp_search_journeys`;
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE `sp_search_journeys` (IN `p_query` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_tag_code` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_persona_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_country_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci, IN `p_min_gmv` INT, IN `p_max_gmv` INT, IN `p_max_minutes` INT, IN `p_limit` INT, IN `p_offset` INT)
BEGIN
DECLARE v_limit INT DEFAULT 50;
  DECLARE v_offset INT DEFAULT 0;

  IF p_limit IS NOT NULL AND p_limit > 0 THEN SET v_limit = p_limit; END IF;
  IF p_offset IS NOT NULL AND p_offset >= 0 THEN SET v_offset = p_offset; END IF;

  SELECT
    f.journey_code,
    f.journey_name_th,
    f.gmv_per_person,
    f.total_minutes_sum,

    (CASE WHEN p_query IS NOT NULL AND
      (sb.search_text COLLATE utf8mb4_uca1400_ai_ci
       LIKE (CONCAT('%', p_query, '%') COLLATE utf8mb4_uca1400_ai_ci))
      THEN 10 ELSE 0 END)

    + (CASE WHEN p_tag_code IS NOT NULL AND EXISTS(
        SELECT 1
        FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
        WHERE jt.journey_id=f.journey_id
          AND (t.tag_code COLLATE utf8mb4_uca1400_ai_ci) = (p_tag_code COLLATE utf8mb4_uca1400_ai_ci)
      ) THEN 5 ELSE 0 END)

    + (CASE WHEN p_persona_code IS NOT NULL AND EXISTS(
        SELECT 1
        FROM journey_segment js
        WHERE js.journey_id=f.journey_id
          AND (js.persona_code COLLATE utf8mb4_uca1400_ai_ci) = (p_persona_code COLLATE utf8mb4_uca1400_ai_ci)
      ) THEN 4 ELSE 0 END)

    + (CASE WHEN p_country_code IS NOT NULL AND EXISTS(
        SELECT 1
        FROM journey_market_fit mf
        WHERE mf.journey_id=f.journey_id
          AND (mf.country_code COLLATE utf8mb4_uca1400_ai_ci) = (p_country_code COLLATE utf8mb4_uca1400_ai_ci)
      ) THEN 3 ELSE 0 END) AS match_score

  FROM vw_rec_journey_features f
  LEFT JOIN vw_journey_search_blob sb ON sb.journey_id=f.journey_id
  WHERE 1=1
    AND (
      p_query IS NULL
      OR (sb.search_text COLLATE utf8mb4_uca1400_ai_ci
          LIKE (CONCAT('%', p_query, '%') COLLATE utf8mb4_uca1400_ai_ci))
    )
    AND (p_min_gmv IS NULL OR f.gmv_per_person >= p_min_gmv)
    AND (p_max_gmv IS NULL OR f.gmv_per_person <= p_max_gmv)
    AND (p_max_minutes IS NULL OR f.total_minutes_sum <= p_max_minutes)

    AND (p_tag_code IS NULL OR EXISTS(
      SELECT 1 FROM journey_tag jt JOIN md_tag t ON t.tag_id=jt.tag_id
      WHERE jt.journey_id=f.journey_id
        AND (t.tag_code COLLATE utf8mb4_uca1400_ai_ci) = (p_tag_code COLLATE utf8mb4_uca1400_ai_ci)
    ))
    AND (p_persona_code IS NULL OR EXISTS(
      SELECT 1 FROM journey_segment js
      WHERE js.journey_id=f.journey_id
        AND (js.persona_code COLLATE utf8mb4_uca1400_ai_ci) = (p_persona_code COLLATE utf8mb4_uca1400_ai_ci)
    ))
    AND (p_country_code IS NULL OR EXISTS(
      SELECT 1 FROM journey_market_fit mf
      WHERE mf.journey_id=f.journey_id
        AND (mf.country_code COLLATE utf8mb4_uca1400_ai_ci) = (p_country_code COLLATE utf8mb4_uca1400_ai_ci)
    ))

  ORDER BY match_score DESC, f.gmv_per_person DESC, f.journey_code
  LIMIT v_offset, v_limit;
END
SQL);

    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_api_journeys_list`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_api_journey_detail`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_api_journey_get`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_api_search`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_recommend_journeys`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_refresh_journey_market_zones`');
        DB::unprepared('DROP PROCEDURE IF EXISTS `sp_search_journeys`');
    }
};
