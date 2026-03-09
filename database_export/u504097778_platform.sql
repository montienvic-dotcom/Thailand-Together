-- ============================================================
-- Thailand Together Platform — Complete Database Export
-- Database: u504097778_platform
-- Generated: 2026-03-09
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- PART 1: LARAVEL BASE TABLES
-- ============================================================

-- ── migrations (Laravel tracking) ──
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── users ──
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NULL DEFAULT NULL,
    `avatar` VARCHAR(255) NULL DEFAULT NULL,
    `locale` VARCHAR(10) NOT NULL DEFAULT 'th',
    `sso_provider` VARCHAR(255) NULL DEFAULT NULL,
    `sso_provider_id` VARCHAR(255) NULL DEFAULT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_cluster` VARCHAR(255) NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_status_index` (`status`),
    KEY `users_sso_index` (`sso_provider`, `sso_provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── password_reset_tokens ──
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── sessions ──
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` TEXT NULL DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── cache ──
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── jobs ──
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL DEFAULT NULL,
    `cancelled_at` INT NULL DEFAULT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── personal_access_tokens (Sanctum) ──
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT NULL DEFAULT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PART 2: PLATFORM TABLES
-- ============================================================

-- ── countries ──
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `code_alpha2` VARCHAR(2) NOT NULL,
    `currency_code` VARCHAR(3) NOT NULL DEFAULT 'THB',
    `timezone` VARCHAR(255) NOT NULL DEFAULT 'Asia/Bangkok',
    `default_locale` VARCHAR(10) NOT NULL DEFAULT 'th',
    `supported_locales` JSON NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `countries_code_unique` (`code`),
    UNIQUE KEY `countries_code_alpha2_unique` (`code_alpha2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── clusters ──
DROP TABLE IF EXISTS `clusters`;
CREATE TABLE `clusters` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `country_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `timezone` VARCHAR(255) NULL DEFAULT NULL,
    `default_locale` VARCHAR(10) NULL DEFAULT NULL,
    `settings` JSON NULL DEFAULT NULL,
    `database_connection` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `launch_date` DATE NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clusters_slug_unique` (`slug`),
    UNIQUE KEY `clusters_code_unique` (`code`),
    KEY `clusters_country_id_is_active_index` (`country_id`, `is_active`),
    CONSTRAINT `clusters_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── applications ──
DROP TABLE IF EXISTS `applications`;
CREATE TABLE `applications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `code` VARCHAR(20) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `icon` VARCHAR(255) NULL DEFAULT NULL,
    `color` VARCHAR(7) NULL DEFAULT NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT 'web',
    `base_url` VARCHAR(255) NULL DEFAULT NULL,
    `source` VARCHAR(255) NOT NULL DEFAULT 'internal',
    `source_version` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `show_in_menu` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `settings` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `applications_slug_unique` (`slug`),
    UNIQUE KEY `applications_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── cluster_application (pivot) ──
DROP TABLE IF EXISTS `cluster_application`;
CREATE TABLE `cluster_application` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `config_overrides` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cluster_application_unique` (`cluster_id`, `application_id`),
    CONSTRAINT `ca_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ca_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── modules ──
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `code` VARCHAR(30) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `icon` VARCHAR(255) NULL DEFAULT NULL,
    `route_prefix` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_premium` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `settings` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `modules_application_id_slug_unique` (`application_id`, `slug`),
    UNIQUE KEY `modules_application_id_code_unique` (`application_id`, `code`),
    CONSTRAINT `modules_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── groups ──
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `scope` VARCHAR(255) NOT NULL,
    `country_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `groups_slug_unique` (`slug`),
    KEY `groups_scope_country_cluster_index` (`scope`, `country_id`, `cluster_id`),
    CONSTRAINT `groups_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
    CONSTRAINT `groups_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── roles ──
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `level` VARCHAR(255) NOT NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── permissions ──
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `category` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── role_permission ──
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission` (
    `role_id` BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `rp_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rp_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── group_user ──
DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
    `group_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`group_id`, `user_id`),
    CONSTRAINT `gu_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `gu_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── role_user ──
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `country_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `role_user_unique` (`user_id`, `role_id`, `country_id`, `cluster_id`),
    KEY `role_user_user_country_cluster_index` (`user_id`, `country_id`, `cluster_id`),
    CONSTRAINT `ru_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ru_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ru_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
    CONSTRAINT `ru_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── user_app_access ──
DROP TABLE IF EXISTS `user_app_access`;
CREATE TABLE `user_app_access` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `has_access` TINYINT(1) NOT NULL DEFAULT 1,
    `settings` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_app_cluster_unique` (`user_id`, `cluster_id`, `application_id`),
    CONSTRAINT `uaa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uaa_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uaa_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── user_module_access ──
DROP TABLE IF EXISTS `user_module_access`;
CREATE TABLE `user_module_access` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `has_access` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_module_cluster_unique` (`user_id`, `cluster_id`, `module_id`),
    CONSTRAINT `uma_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uma_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uma_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── group_app_access ──
DROP TABLE IF EXISTS `group_app_access`;
CREATE TABLE `group_app_access` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `has_access` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `group_app_cluster_unique` (`group_id`, `cluster_id`, `application_id`),
    CONSTRAINT `gaa_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `gaa_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `gaa_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── group_module_access ──
DROP TABLE IF EXISTS `group_module_access`;
CREATE TABLE `group_module_access` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `has_access` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `group_module_cluster_unique` (`group_id`, `cluster_id`, `module_id`),
    CONSTRAINT `gma_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `gma_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `gma_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── api_providers ──
DROP TABLE IF EXISTS `api_providers`;
CREATE TABLE `api_providers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `base_url` VARCHAR(255) NULL DEFAULT NULL,
    `docs_url` VARCHAR(255) NULL DEFAULT NULL,
    `adapter_class` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_shared` TINYINT(1) NOT NULL DEFAULT 1,
    `supported_countries` JSON NULL DEFAULT NULL,
    `default_config` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `api_providers_slug_unique` (`slug`),
    KEY `api_providers_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── api_credentials ──
DROP TABLE IF EXISTS `api_credentials`;
CREATE TABLE `api_credentials` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `api_provider_id` BIGINT UNSIGNED NOT NULL,
    `country_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `environment` VARCHAR(255) NOT NULL DEFAULT 'sandbox',
    `credentials` JSON NULL DEFAULT NULL,
    `config` JSON NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `api_credentials_provider_env_index` (`api_provider_id`, `environment`),
    KEY `api_credentials_country_cluster_index` (`country_id`, `cluster_id`),
    CONSTRAINT `ac_api_provider_id_foreign` FOREIGN KEY (`api_provider_id`) REFERENCES `api_providers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ac_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
    CONSTRAINT `ac_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── api_logs ──
DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE `api_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `api_provider_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `method` VARCHAR(10) NOT NULL,
    `endpoint` VARCHAR(255) NOT NULL,
    `status_code` INT NULL DEFAULT NULL,
    `response_time_ms` INT NULL DEFAULT NULL,
    `request_summary` JSON NULL DEFAULT NULL,
    `error_message` TEXT NULL DEFAULT NULL,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `api_logs_provider_created_index` (`api_provider_id`, `created_at`),
    KEY `api_logs_cluster_created_index` (`cluster_id`, `created_at`),
    CONSTRAINT `al_api_provider_id_foreign` FOREIGN KEY (`api_provider_id`) REFERENCES `api_providers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `al_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL,
    CONSTRAINT `al_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── reward_wallets ──
DROP TABLE IF EXISTS `reward_wallets`;
CREATE TABLE `reward_wallets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `cluster_id` BIGINT UNSIGNED NOT NULL,
    `balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(10) NOT NULL DEFAULT 'POINT',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `reward_wallets_unique` (`user_id`, `cluster_id`, `currency`),
    CONSTRAINT `rw_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rw_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── reward_transactions ──
DROP TABLE IF EXISTS `reward_transactions`;
CREATE TABLE `reward_transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `wallet_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `balance_after` DECIMAL(15,2) NOT NULL,
    `reference_type` VARCHAR(255) NULL DEFAULT NULL,
    `reference_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `source_cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `target_cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `metadata` JSON NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `rt_user_created_index` (`user_id`, `created_at`),
    KEY `rt_wallet_type_index` (`wallet_id`, `type`),
    CONSTRAINT `rt_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rt_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `reward_wallets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rt_source_cluster_foreign` FOREIGN KEY (`source_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL,
    CONSTRAINT `rt_target_cluster_foreign` FOREIGN KEY (`target_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── reward_exchange_rates ──
DROP TABLE IF EXISTS `reward_exchange_rates`;
CREATE TABLE `reward_exchange_rates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_cluster_id` BIGINT UNSIGNED NOT NULL,
    `to_cluster_id` BIGINT UNSIGNED NOT NULL,
    `rate` DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `reward_exchange_rates_unique` (`from_cluster_id`, `to_cluster_id`),
    CONSTRAINT `rer_from_cluster_foreign` FOREIGN KEY (`from_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `rer_to_cluster_foreign` FOREIGN KEY (`to_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── campaigns ──
DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `scope` VARCHAR(255) NOT NULL,
    `country_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `target_clusters` JSON NULL DEFAULT NULL,
    `type` VARCHAR(255) NOT NULL,
    `rules` JSON NULL DEFAULT NULL,
    `rewards` JSON NULL DEFAULT NULL,
    `starts_at` TIMESTAMP NULL DEFAULT NULL,
    `ends_at` TIMESTAMP NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `campaigns_slug_unique` (`slug`),
    KEY `campaigns_scope_active_index` (`scope`, `is_active`),
    KEY `campaigns_dates_index` (`starts_at`, `ends_at`),
    CONSTRAINT `campaigns_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
    CONSTRAINT `campaigns_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── cross_cluster_recommendations ──
DROP TABLE IF EXISTS `cross_cluster_recommendations`;
CREATE TABLE `cross_cluster_recommendations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_cluster_id` BIGINT UNSIGNED NOT NULL,
    `to_cluster_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `content` JSON NULL DEFAULT NULL,
    `priority` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `ccr_from_cluster_active_index` (`from_cluster_id`, `is_active`),
    CONSTRAINT `ccr_from_cluster_foreign` FOREIGN KEY (`from_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ccr_to_cluster_foreign` FOREIGN KEY (`to_cluster_id`) REFERENCES `clusters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── menu_items ──
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NULL DEFAULT NULL,
    `url` VARCHAR(255) NULL DEFAULT NULL,
    `route_name` VARCHAR(255) NULL DEFAULT NULL,
    `application_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `scope` VARCHAR(255) NOT NULL DEFAULT 'global',
    `country_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `target` VARCHAR(255) NOT NULL DEFAULT '_self',
    `visibility` VARCHAR(255) NOT NULL DEFAULT 'all',
    `required_permissions` JSON NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `menu_items_scope_active_sort_index` (`scope`, `is_active`, `sort_order`),
    KEY `menu_items_parent_id_index` (`parent_id`),
    CONSTRAINT `mi_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL,
    CONSTRAINT `mi_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL,
    CONSTRAINT `mi_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
    CONSTRAINT `mi_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── audit_logs ──
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `action` VARCHAR(255) NOT NULL,
    `resource_type` VARCHAR(255) NULL DEFAULT NULL,
    `resource_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `old_values` JSON NULL DEFAULT NULL,
    `new_values` JSON NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `audit_logs_user_created_index` (`user_id`, `created_at`),
    KEY `audit_logs_action_created_index` (`action`, `created_at`),
    KEY `audit_logs_resource_index` (`resource_type`, `resource_id`),
    CONSTRAINT `audit_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `audit_cluster_id_foreign` FOREIGN KEY (`cluster_id`) REFERENCES `clusters` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PART 3: MERCHANT / JOURNEY TABLES
-- ============================================================

-- ── journey ──
DROP TABLE IF EXISTS `journey`;
CREATE TABLE `journey` (
    `journey_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_code` VARCHAR(10) NOT NULL,
    `journey_group` VARCHAR(5) NOT NULL COMMENT 'A,B,C,...H',
    `journey_name_th` VARCHAR(255) NOT NULL,
    `journey_name_en` VARCHAR(255) NOT NULL,
    `group_size` TINYINT UNSIGNED NOT NULL DEFAULT 4,
    `gmv_per_person` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `gmv_per_group` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tp_total_normal` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `tp_total_goal` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `tp_total_special` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `total_minutes_sum` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `luxury_tone_th` VARCHAR(255) NULL DEFAULT NULL,
    `luxury_tone_en` VARCHAR(255) NULL DEFAULT NULL,
    `target_visitors` VARCHAR(255) NULL DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`journey_id`),
    UNIQUE KEY `journey_journey_code_unique` (`journey_code`),
    KEY `journey_journey_group_index` (`journey_group`),
    KEY `journey_cluster_id_index` (`cluster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_i18n ──
DROP TABLE IF EXISTS `journey_i18n`;
CREATE TABLE `journey_i18n` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `lang` VARCHAR(5) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_i18n_unique` (`journey_id`, `lang`),
    CONSTRAINT `ji18n_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_tag ──
DROP TABLE IF EXISTS `journey_tag`;
CREATE TABLE `journey_tag` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `tag_code` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_tag_unique` (`journey_id`, `tag_code`),
    CONSTRAINT `jt_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_persona ──
DROP TABLE IF EXISTS `journey_persona`;
CREATE TABLE `journey_persona` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `persona_code` VARCHAR(30) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_persona_unique` (`journey_id`, `persona_code`),
    CONSTRAINT `jp_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_market ──
DROP TABLE IF EXISTS `journey_market`;
CREATE TABLE `journey_market` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `country_code` VARCHAR(5) NOT NULL,
    `fit_level` TINYINT UNSIGNED NOT NULL DEFAULT 3,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_market_unique` (`journey_id`, `country_code`),
    CONSTRAINT `jm_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_zone ──
DROP TABLE IF EXISTS `journey_zone`;
CREATE TABLE `journey_zone` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `zone_code` VARCHAR(30) NOT NULL,
    `fit_level` TINYINT UNSIGNED NOT NULL DEFAULT 3,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_zone_unique` (`journey_id`, `zone_code`),
    CONSTRAINT `jz_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_next5 ──
DROP TABLE IF EXISTS `journey_next5`;
CREATE TABLE `journey_next5` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `next_rank` TINYINT UNSIGNED NOT NULL,
    `next_journey_code` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_next5_unique` (`journey_id`, `next_rank`),
    CONSTRAINT `jn5_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── place ──
DROP TABLE IF EXISTS `place`;
CREATE TABLE `place` (
    `place_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `place_code` VARCHAR(80) NOT NULL,
    `place_name_th` VARCHAR(255) NOT NULL,
    `place_name_en` VARCHAR(255) NOT NULL,
    `place_desc_th` TEXT NULL DEFAULT NULL,
    `place_desc_en` TEXT NULL DEFAULT NULL,
    `lat` DECIMAL(10,7) NULL DEFAULT NULL,
    `lng` DECIMAL(10,7) NULL DEFAULT NULL,
    `place_type` VARCHAR(30) NULL DEFAULT NULL COMMENT 'restaurant,hotel,spa,attraction,...',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`place_id`),
    UNIQUE KEY `place_place_code_unique` (`place_code`),
    KEY `place_cluster_id_index` (`cluster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── journey_step ──
DROP TABLE IF EXISTS `journey_step`;
CREATE TABLE `journey_step` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `journey_id` BIGINT UNSIGNED NOT NULL,
    `place_id` BIGINT UNSIGNED NOT NULL,
    `step_no` TINYINT UNSIGNED NOT NULL,
    `duration_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    `tp_normal` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `tp_goal` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `tp_special` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `spend_estimate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `step_note` TEXT NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `journey_step_unique` (`journey_id`, `step_no`),
    CONSTRAINT `js_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE CASCADE,
    CONSTRAINT `js_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `place` (`place_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant ──
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
    `merchant_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `merchant_code` VARCHAR(80) NOT NULL,
    `merchant_name_th` VARCHAR(255) NOT NULL,
    `merchant_name_en` VARCHAR(255) NOT NULL,
    `merchant_desc_th` TEXT NULL DEFAULT NULL,
    `merchant_desc_en` TEXT NULL DEFAULT NULL,
    `default_tier_code` VARCHAR(5) NOT NULL DEFAULT 'S' COMMENT 'XL,E,M,S',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `phone` VARCHAR(30) NULL DEFAULT NULL,
    `website` VARCHAR(255) NULL DEFAULT NULL,
    `price_level` TINYINT UNSIGNED NOT NULL DEFAULT 2 COMMENT '1-5',
    `lat` DECIMAL(10,7) NULL DEFAULT NULL,
    `lng` DECIMAL(10,7) NULL DEFAULT NULL,
    `open_hours` VARCHAR(50) NULL DEFAULT NULL,
    `service_tags` VARCHAR(255) NULL DEFAULT NULL COMMENT 'comma-separated',
    `onsite_note` VARCHAR(255) NULL DEFAULT NULL,
    `source_ref` VARCHAR(255) NULL DEFAULT NULL COMMENT 'import source reference',
    `cluster_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`merchant_id`),
    UNIQUE KEY `merchant_merchant_code_unique` (`merchant_code`),
    KEY `merchant_default_tier_code_index` (`default_tier_code`),
    KEY `merchant_cluster_id_index` (`cluster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_i18n ──
DROP TABLE IF EXISTS `merchant_i18n`;
CREATE TABLE `merchant_i18n` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `lang` VARCHAR(5) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `merchant_i18n_unique` (`merchant_id`, `lang`),
    CONSTRAINT `mi18n_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── place_merchant ──
DROP TABLE IF EXISTS `place_merchant`;
CREATE TABLE `place_merchant` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `place_id` BIGINT UNSIGNED NOT NULL,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `place_merchant_unique` (`place_id`, `merchant_id`),
    CONSTRAINT `pm_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `place` (`place_id`) ON DELETE CASCADE,
    CONSTRAINT `pm_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_checkin ──
DROP TABLE IF EXISTS `merchant_checkin`;
CREATE TABLE `merchant_checkin` (
    `checkin_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `place_id` BIGINT UNSIGNED NOT NULL,
    `journey_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `checkin_method` VARCHAR(10) NOT NULL DEFAULT 'QR',
    `note` VARCHAR(255) NULL DEFAULT NULL,
    `tp_awarded` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`checkin_id`),
    KEY `merchant_checkin_user_merchant_index` (`user_id`, `merchant_id`),
    KEY `merchant_checkin_journey_index` (`journey_id`),
    CONSTRAINT `mc_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE,
    CONSTRAINT `mc_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `place` (`place_id`) ON DELETE CASCADE,
    CONSTRAINT `mc_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_favorite ──
DROP TABLE IF EXISTS `merchant_favorite`;
CREATE TABLE `merchant_favorite` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `merchant_favorite_unique` (`user_id`, `merchant_id`),
    CONSTRAINT `mf_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_wishlist ──
DROP TABLE IF EXISTS `merchant_wishlist`;
CREATE TABLE `merchant_wishlist` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `merchant_wishlist_unique` (`user_id`, `merchant_id`),
    CONSTRAINT `mw_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_review ──
DROP TABLE IF EXISTS `merchant_review`;
CREATE TABLE `merchant_review` (
    `review_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `merchant_id` BIGINT UNSIGNED NOT NULL,
    `place_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `journey_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `rating` TINYINT UNSIGNED NOT NULL COMMENT '1-5',
    `title` VARCHAR(255) NULL DEFAULT NULL,
    `review_text` TEXT NULL DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'PUBLISHED',
    `is_public` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`review_id`),
    KEY `merchant_review_merchant_status_index` (`merchant_id`, `status`, `is_public`),
    KEY `merchant_review_user_index` (`user_id`),
    CONSTRAINT `mr_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchant` (`merchant_id`) ON DELETE CASCADE,
    CONSTRAINT `mr_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `place` (`place_id`) ON DELETE SET NULL,
    CONSTRAINT `mr_journey_id_foreign` FOREIGN KEY (`journey_id`) REFERENCES `journey` (`journey_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── merchant_import_batch ──
DROP TABLE IF EXISTS `merchant_import_batch`;
CREATE TABLE `merchant_import_batch` (
    `batch_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `batch_code` VARCHAR(50) NOT NULL,
    `batch_label` VARCHAR(255) NULL DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    `total_rows` INT UNSIGNED NOT NULL DEFAULT 0,
    `imported_rows` INT UNSIGNED NOT NULL DEFAULT 0,
    `error_rows` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`batch_id`),
    UNIQUE KEY `merchant_import_batch_code_unique` (`batch_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── stg_merchant_import ──
DROP TABLE IF EXISTS `stg_merchant_import`;
CREATE TABLE `stg_merchant_import` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `batch_code` VARCHAR(50) NOT NULL,
    `merchant_code` VARCHAR(80) NOT NULL,
    `merchant_name_th` VARCHAR(255) NOT NULL,
    `merchant_name_en` VARCHAR(255) NULL DEFAULT NULL,
    `merchant_desc_th` TEXT NULL DEFAULT NULL,
    `merchant_desc_en` TEXT NULL DEFAULT NULL,
    `default_tier_code` VARCHAR(5) NOT NULL DEFAULT 'S',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `phone` VARCHAR(30) NULL DEFAULT NULL,
    `website` VARCHAR(255) NULL DEFAULT NULL,
    `price_level` TINYINT UNSIGNED NOT NULL DEFAULT 2,
    `lat` DECIMAL(10,7) NULL DEFAULT NULL,
    `lng` DECIMAL(10,7) NULL DEFAULT NULL,
    `place_code` VARCHAR(80) NULL DEFAULT NULL,
    `is_primary_hint` TINYINT(1) NOT NULL DEFAULT 0,
    `onsite_note` VARCHAR(255) NULL DEFAULT NULL,
    `open_hours` VARCHAR(50) NULL DEFAULT NULL,
    `service_tags` VARCHAR(255) NULL DEFAULT NULL,
    `source_ref` VARCHAR(255) NULL DEFAULT NULL,
    `validation_status` VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    `validation_errors` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `stg_merchant_import_batch_index` (`batch_code`),
    KEY `stg_merchant_import_code_index` (`merchant_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PART 4: SQL VIEWS
-- ============================================================

-- ── 1) vw_journey_merchant_stats ──
CREATE OR REPLACE VIEW `vw_journey_merchant_stats` AS
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
GROUP BY j.journey_id, j.journey_code;

-- ── 2) vw_merchant_search_public ──
CREATE OR REPLACE VIEW `vw_merchant_search_public` AS
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
) sub_r ON sub_r.merchant_id = m.merchant_id;

-- ── 3) vw_merchant_search_blob_public ──
CREATE OR REPLACE VIEW `vw_merchant_search_blob_public` AS
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
LEFT JOIN journey j ON j.journey_id = js.journey_id AND j.status = 'ACTIVE';

-- ── 4) vw_merchant_search_user ──
CREATE OR REPLACE VIEW `vw_merchant_search_user` AS
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
) mr_agg ON mr_agg.user_id = u.id AND mr_agg.merchant_id = pub.merchant_id;

-- ── 5) vw_merchant_search_blob_user ──
CREATE OR REPLACE VIEW `vw_merchant_search_blob_user` AS
SELECT
    u.id AS user_id,
    b.merchant_id,
    b.journey_id,
    b.search_text
FROM vw_merchant_search_blob_public b
CROSS JOIN users u;

-- ── 6) vw_journey_place_merchant_json ──
CREATE OR REPLACE VIEW `vw_journey_place_merchant_json` AS
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
GROUP BY j.journey_id, j.journey_code;

-- ── 7) vw_journey_merchant_json_user ──
CREATE OR REPLACE VIEW `vw_journey_merchant_json_user` AS
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
GROUP BY j.journey_id, j.journey_code, u.id;

-- ── 8) vw_api_journey_onecall_with_merchants_stats_final ──
CREATE OR REPLACE VIEW `vw_api_journey_onecall_with_merchants_stats_final` AS
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
WHERE j.status = 'ACTIVE';

-- ── 9) vw_api_journey_onecall_with_merchants_user ──
CREATE OR REPLACE VIEW `vw_api_journey_onecall_with_merchants_user` AS
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
WHERE j.status = 'ACTIVE';


-- ============================================================
-- PART 5: SEED DATA
-- ============================================================

-- ── 5.1 migrations tracking ──
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2026_02_28_000001_create_countries_table', 2),
('2026_02_28_000002_create_clusters_table', 2),
('2026_02_28_000003_create_applications_table', 2),
('2026_02_28_000004_create_modules_table', 2),
('2026_02_28_000005_create_sso_permission_tables', 2),
('2026_02_28_000006_create_api_integrations_table', 2),
('2026_02_28_000007_create_cross_cluster_tables', 2),
('2026_03_01_000001_seed_missing_app_descriptions_and_modules', 3),
('2026_03_02_000001_add_five_new_applications', 3),
('2026_03_08_000001_create_merchant_tables', 4),
('2026_03_09_000001_create_merchant_views', 4);

-- ── 5.2 Countries ──
INSERT INTO `countries` (`id`, `name`, `code`, `code_alpha2`, `currency_code`, `timezone`, `default_locale`, `supported_locales`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Thailand', 'THA', 'TH', 'THB', 'Asia/Bangkok', 'th', '["th","en","zh","ja","ko","ru"]', 1, 1, NOW(), NOW()),
(2, 'Vietnam', 'VNM', 'VN', 'VND', 'Asia/Ho_Chi_Minh', 'vi', '["vi","en","zh","ja","ko"]', 0, 2, NOW(), NOW());

-- ── 5.3 Clusters ──
INSERT INTO `clusters` (`id`, `country_id`, `name`, `slug`, `code`, `description`, `timezone`, `default_locale`, `is_active`, `launch_date`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pattaya', 'pattaya', 'PTY', 'Phase 1 - Pattaya tourism cluster', 'Asia/Bangkok', 'th', 1, '2026-03-01', 1, NOW(), NOW()),
(2, 1, 'Chiang Mai', 'chiangmai', 'CNX', 'Future - Chiang Mai tourism cluster', NULL, NULL, 0, NULL, 2, NOW(), NOW()),
(3, 2, 'Danang', 'danang', 'DAD', 'Future - Danang tourism cluster', NULL, NULL, 0, NULL, 1, NOW(), NOW());

-- ── 5.4 Roles ──
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `level`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Global Admin', 'global-admin', 'Full access to all countries and clusters', 'global', 1, NOW(), NOW()),
(2, 'Country Admin', 'country-admin', 'Full access within a specific country', 'country', 1, NOW(), NOW()),
(3, 'Cluster Admin', 'cluster-admin', 'Full access within a specific cluster', 'cluster', 1, NOW(), NOW()),
(4, 'App Admin', 'app-admin', 'Admin of a specific application', 'app', 1, NOW(), NOW()),
(5, 'Operator', 'operator', 'Cluster operator with limited admin access', 'cluster', 0, NOW(), NOW()),
(6, 'Merchant', 'merchant', 'Business owner/merchant', 'cluster', 0, NOW(), NOW()),
(7, 'Tourist', 'tourist', 'Tourist/end user', 'cluster', 0, NOW(), NOW());

-- ── 5.5 Groups ──
INSERT INTO `groups` (`id`, `name`, `slug`, `description`, `scope`, `country_id`, `cluster_id`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Operators', 'operators', 'System operators and staff', 'global', NULL, NULL, 1, 0, NOW(), NOW()),
(2, 'Merchants', 'merchants', 'Pattaya merchants and business owners', 'cluster', NULL, 1, 1, 0, NOW(), NOW()),
(3, 'Tourists', 'tourists', 'All registered tourists', 'global', NULL, NULL, 1, 0, NOW(), NOW()),
(4, 'VIP Members', 'vip-members', 'VIP loyalty program members', 'global', NULL, NULL, 1, 0, NOW(), NOW());

-- ── 5.6 Permissions ──
INSERT INTO `permissions` (`id`, `name`, `slug`, `category`, `created_at`, `updated_at`) VALUES
(1, 'View Dashboard', 'dashboard.view', 'dashboard', NOW(), NOW()),
(2, 'Manage Users', 'users.manage', 'users', NOW(), NOW()),
(3, 'View Users', 'users.view', 'users', NOW(), NOW()),
(4, 'Manage Roles', 'roles.manage', 'roles', NOW(), NOW()),
(5, 'Manage Permissions', 'permissions.manage', 'permissions', NOW(), NOW()),
(6, 'Manage Applications', 'applications.manage', 'applications', NOW(), NOW()),
(7, 'Manage Clusters', 'clusters.manage', 'clusters', NOW(), NOW()),
(8, 'Manage Countries', 'countries.manage', 'countries', NOW(), NOW()),
(9, 'Manage Campaigns', 'campaigns.manage', 'campaigns', NOW(), NOW()),
(10, 'View Analytics', 'analytics.view', 'analytics', NOW(), NOW()),
(11, 'Manage API Integrations', 'api.manage', 'api', NOW(), NOW()),
(12, 'Manage Menu', 'menu.manage', 'menu', NOW(), NOW()),
(13, 'Manage Rewards', 'rewards.manage', 'rewards', NOW(), NOW()),
(14, 'Apply Patches', 'patches.apply', 'patches', NOW(), NOW());

-- ── 5.7 Applications (15 apps) ──
INSERT INTO `applications` (`id`, `name`, `slug`, `code`, `description`, `icon`, `color`, `type`, `source`, `is_active`, `show_in_menu`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,  'App Together', 'app-together', 'APP_TOGETHER', 'All-in-one mobile app for tourists — explore, book, navigate, and earn rewards in one place', 'compass', '#FF6B35', 'mobile', 'internal', 1, 1, 1, NOW(), NOW()),
(2,  'Hotel Management', 'hotel-management', 'HOTEL_MGMT', 'Complete hotel operations — room management, reservations, guest services, and revenue tracking', 'building', '#004E89', 'web', 'external', 1, 1, 2, NOW(), NOW()),
(3,  'Tour Booking', 'tour-booking', 'TOUR_BOOKING', 'Browse and book tours, activities, and experiences — day trips, island hopping, shows, and more', 'map', '#1A936F', 'hybrid', 'external', 1, 1, 3, NOW(), NOW()),
(4,  'Marketplace', 'marketplace', 'MARKETPLACE', 'Local shops, souvenirs, food delivery, and services — support local merchants while you travel', 'shopping-bag', '#C14953', 'hybrid', 'external', 1, 1, 4, NOW(), NOW()),
(5,  'Rewards Center', 'rewards-center', 'REWARDS', 'Earn and redeem points across all services — exclusive deals, tier benefits, and cross-cluster rewards', 'gift', '#F4A261', 'web', 'internal', 1, 1, 5, NOW(), NOW()),
(6,  'HelpDesk', 'helpdesk', 'HELPDESK', '24/7 multilingual support — AI chatbot, live agents, ticket tracking, and emergency assistance', 'headphones', '#6C757D', 'web', 'external', 1, 1, 6, NOW(), NOW()),
(7,  'City Location - Digital Twin', 'city-digital-twin', 'CITY_DIGITAL_TWIN', 'Interactive 3D city map with AR navigation, real-time data, and virtual tours of Pattaya', 'globe', '#2EC4B6', 'hybrid', 'internal', 1, 1, 7, NOW(), NOW()),
(8,  'Social Network', 'social-network', 'SOCIAL_NETWORK', 'Connect with fellow travelers — share experiences, join groups, find events, and make friends', 'users', '#E71D36', 'hybrid', 'internal', 1, 1, 8, NOW(), NOW()),
(9,  'Referral & Partner Hub', 'partner-hub', 'PARTNER_HUB', 'Earn commissions by referring friends and merchants — influencer tools, tracking, and payouts', 'share-2', '#8338EC', 'web', 'internal', 1, 1, 9, NOW(), NOW()),
(10, 'UGC & AI Content Hub', 'ugc-ai-hub', 'UGC_AI_HUB', 'AI-powered content creation — auto-translate, generate itineraries, and curate user reviews', 'edit-3', '#3A86FF', 'hybrid', 'internal', 1, 1, 10, NOW(), NOW()),
(11, 'City Dashboard', 'city-dashboard', 'CITY_DASHBOARD', 'Real-time city intelligence — tourism statistics, traffic flow, revenue analytics, and public safety monitoring for city officials', 'presentation-chart-bar', '#0EA5E9', 'web', 'internal', 1, 1, 11, NOW(), NOW()),
(12, 'Government ERP (NEW GFMIS)', 'government-erp', 'GOV_ERP', 'Government financial management system — budget planning, procurement, financial reporting, asset tracking, and audit compliance (NEW GFMIS standard)', 'building-library', '#0F766E', 'web', 'external', 1, 1, 12, NOW(), NOW()),
(13, 'Event & MICE', 'event-mice', 'EVENT_MICE', 'Complete event management platform — Meetings, Incentives, Conferences, Exhibitions — venue booking, registration, exhibitor management, and analytics', 'calendar-days', '#DB2777', 'hybrid', 'internal', 1, 1, 13, NOW(), NOW()),
(14, 'Project Management', 'project-management', 'PROJECT_MGMT', 'End-to-end project management — planning, task tracking, Gantt charts, resource allocation, budget control, risk management, and executive reporting', 'clipboard-document-check', '#7C3AED', 'web', 'internal', 1, 1, 14, NOW(), NOW()),
(15, 'Data Exchange', 'data-exchange', 'DATA_EXCHANGE', 'Central data exchange hub — data catalog, quality management, security governance, high-value datasets, sandbox analytics, data requests, and usage monitoring', 'circle-stack', '#059669', 'web', 'internal', 1, 1, 15, NOW(), NOW());

-- ── 5.8 cluster_application (all 15 apps in Pattaya) ──
INSERT INTO `cluster_application` (`cluster_id`, `application_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW()), (1, 2, 1, NOW(), NOW()), (1, 3, 1, NOW(), NOW()),
(1, 4, 1, NOW(), NOW()), (1, 5, 1, NOW(), NOW()), (1, 6, 1, NOW(), NOW()),
(1, 7, 1, NOW(), NOW()), (1, 8, 1, NOW(), NOW()), (1, 9, 1, NOW(), NOW()),
(1, 10, 1, NOW(), NOW()), (1, 11, 1, NOW(), NOW()), (1, 12, 1, NOW(), NOW()),
(1, 13, 1, NOW(), NOW()), (1, 14, 1, NOW(), NOW()), (1, 15, 1, NOW(), NOW());


-- ── 5.9 Modules (all 15 apps) ──
-- App 1: APP_TOGETHER
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Explore', 'explore', 'EXPLORE', NULL, 1, 0, 1, NOW(), NOW()),
(1, 'Booking', 'booking', 'BOOKING', NULL, 1, 0, 2, NOW(), NOW()),
(1, 'Map & Navigation', 'map', 'MAP', NULL, 1, 0, 3, NOW(), NOW()),
(1, 'Chat & Support', 'chat', 'CHAT', NULL, 1, 0, 4, NOW(), NOW()),
(1, 'My Rewards', 'rewards', 'MY_REWARDS', NULL, 1, 0, 5, NOW(), NOW()),
(1, 'My Profile', 'profile', 'PROFILE', NULL, 1, 0, 6, NOW(), NOW()),
(1, 'Deals & Promotions', 'deals', 'DEALS', NULL, 1, 0, 7, NOW(), NOW()),
(1, 'Reviews & Ratings', 'reviews', 'REVIEWS', NULL, 1, 0, 8, NOW(), NOW());

-- App 2: HOTEL_MGMT
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'Room Management', 'room-mgmt', 'ROOM_MGMT', 'Room types, inventory, pricing, and availability calendar', 1, 0, 1, NOW(), NOW()),
(2, 'Reservations', 'reservations', 'RESERVATIONS', 'Booking management, check-in/out, and guest records', 1, 0, 2, NOW(), NOW()),
(2, 'Guest Services', 'guest-services', 'GUEST_SERVICES', 'Room service, housekeeping requests, and concierge', 1, 0, 3, NOW(), NOW()),
(2, 'Channel Manager', 'channel-mgr', 'CHANNEL_MGR', 'Sync availability across OTAs (Agoda, Booking.com, etc.)', 1, 0, 4, NOW(), NOW()),
(2, 'Revenue & Reports', 'revenue-reports', 'REVENUE_REPORTS', 'Occupancy rates, revenue analytics, and financial reports', 1, 0, 5, NOW(), NOW()),
(2, 'Review Management', 'review-mgmt', 'REVIEW_MGMT', 'Monitor and respond to guest reviews across platforms', 1, 0, 6, NOW(), NOW());

-- App 3: TOUR_BOOKING
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(3, 'Tour Catalog', 'tour-catalog', 'TOUR_CATALOG', 'Browse tours, activities, day trips, and experiences', 1, 0, 1, NOW(), NOW()),
(3, 'Booking Engine', 'booking-engine', 'BOOKING_ENGINE', 'Real-time availability, instant booking, and payment', 1, 0, 2, NOW(), NOW()),
(3, 'Tour Operator Panel', 'operator-panel', 'OPERATOR_PANEL', 'Tour operators manage listings, schedules, and guides', 1, 0, 3, NOW(), NOW()),
(3, 'Itinerary Builder', 'itinerary', 'ITINERARY', 'Create custom multi-day itineraries with AI suggestions', 1, 0, 4, NOW(), NOW()),
(3, 'Transport & Transfers', 'transport', 'TRANSPORT', 'Airport transfers, car rentals, and local transport booking', 1, 0, 5, NOW(), NOW()),
(3, 'Reviews & Ratings', 'tour-reviews', 'TOUR_REVIEWS', 'Verified reviews and ratings from past participants', 1, 0, 6, NOW(), NOW());

-- App 4: MARKETPLACE
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(4, 'Shop Directory', 'shop-directory', 'SHOP_DIRECTORY', 'Browse local shops, restaurants, and service providers', 1, 0, 1, NOW(), NOW()),
(4, 'Product Listings', 'product-listings', 'PRODUCT_LISTINGS', 'Search and filter products, souvenirs, and local goods', 1, 0, 2, NOW(), NOW()),
(4, 'Order & Delivery', 'order-delivery', 'ORDER_DELIVERY', 'Place orders with hotel delivery or pickup options', 1, 0, 3, NOW(), NOW()),
(4, 'Merchant Dashboard', 'merchant-dash', 'MERCHANT_DASH', 'Merchants manage products, orders, and promotions', 1, 0, 4, NOW(), NOW()),
(4, 'Deals & Coupons', 'deals-coupons', 'DEALS_COUPONS', 'Tourist-exclusive deals, flash sales, and discount codes', 1, 0, 5, NOW(), NOW()),
(4, 'Food Delivery', 'food-delivery', 'FOOD_DELIVERY', 'Order from local restaurants with real-time tracking', 1, 0, 6, NOW(), NOW());

-- App 5: REWARDS
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(5, 'Points Dashboard', 'points-dashboard', 'POINTS_DASH', 'View balance, earning history, and point expiry dates', 1, 0, 1, NOW(), NOW()),
(5, 'Earn Points', 'earn-points', 'EARN_POINTS', 'Earn from bookings, check-ins, reviews, and referrals', 1, 0, 2, NOW(), NOW()),
(5, 'Redeem Rewards', 'redeem-rewards', 'REDEEM_REWARDS', 'Redeem for discounts, vouchers, upgrades, and experiences', 1, 0, 3, NOW(), NOW()),
(5, 'Tier & Benefits', 'tier-benefits', 'TIER_BENEFITS', 'Bronze to Platinum tiers with exclusive perks at each level', 1, 0, 4, NOW(), NOW()),
(5, 'Special Campaigns', 'campaigns', 'CAMPAIGNS', 'Limited-time bonus point events and seasonal promotions', 1, 0, 5, NOW(), NOW()),
(5, 'Transfer & Exchange', 'point-transfer', 'POINT_TRANSFER', 'Transfer points to friends or exchange across clusters', 1, 0, 6, NOW(), NOW());

-- App 6: HELPDESK
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(6, 'AI Chatbot', 'ai-chatbot', 'AI_CHATBOT', 'Instant AI-powered answers in 6 languages, 24/7', 1, 0, 1, NOW(), NOW()),
(6, 'Live Support', 'live-support', 'LIVE_SUPPORT', 'Connect with multilingual human agents for complex issues', 1, 0, 2, NOW(), NOW()),
(6, 'Ticket System', 'ticket-system', 'TICKET_SYSTEM', 'Submit and track support tickets with SLA guarantees', 1, 0, 3, NOW(), NOW()),
(6, 'Emergency Assist', 'emergency', 'EMERGENCY', 'One-tap emergency contacts: police, hospital, embassy', 1, 0, 4, NOW(), NOW()),
(6, 'FAQ & Guides', 'faq-guides', 'FAQ_GUIDES', 'Travel guides, visa info, local tips, and how-to articles', 1, 0, 5, NOW(), NOW()),
(6, 'Feedback & Surveys', 'feedback', 'FEEDBACK', 'Rate your experience and help us improve our services', 1, 0, 6, NOW(), NOW());

-- App 7: CITY_DIGITAL_TWIN
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(7, '3D City Map', 'city-map-3d', 'CITY_MAP_3D', 'Digital Twin 3D city map', 1, 0, 1, NOW(), NOW()),
(7, 'Points of Interest', 'poi', 'POI', 'Landmarks, shops, restaurants, attractions', 1, 0, 2, NOW(), NOW()),
(7, 'AR Navigation', 'ar-nav', 'AR_NAV', 'Augmented reality walking navigation', 1, 0, 3, NOW(), NOW()),
(7, 'Virtual Tour 360', 'virtual-tour', 'VIRTUAL_TOUR', 'Virtual 360-degree city exploration', 1, 0, 4, NOW(), NOW()),
(7, 'Real-time City Data', 'city-realtime', 'CITY_REALTIME', 'Live city data: weather, traffic, crowd density', 1, 0, 5, NOW(), NOW()),
(7, 'Smart Directory', 'smart-directory', 'SMART_DIRECTORY', 'Location and hours-based shop/service search', 1, 0, 6, NOW(), NOW()),
(7, 'Route Planner', 'route-planner', 'ROUTE_PLANNER', 'Optimized sightseeing, dining, and shopping routes', 1, 0, 7, NOW(), NOW());

-- App 8: SOCIAL_NETWORK
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(8, 'Feed & Timeline', 'feed', 'FEED', 'Share travel experiences', 1, 0, 1, NOW(), NOW()),
(8, 'Travel Stories', 'stories', 'STORIES', 'Travel stories with photos and videos', 1, 0, 2, NOW(), NOW()),
(8, 'Friends & Followers', 'friends', 'FRIENDS', 'Connect with other travelers', 1, 0, 3, NOW(), NOW()),
(8, 'Messaging', 'messaging', 'MESSAGING', 'Private and group chat', 1, 0, 4, NOW(), NOW()),
(8, 'Groups & Communities', 'groups', 'GROUPS', 'Interest and destination-based groups', 1, 0, 5, NOW(), NOW()),
(8, 'Events', 'events', 'EVENTS', 'Local events and traveler meetups', 1, 0, 6, NOW(), NOW()),
(8, 'Check-in & Places', 'checkin', 'CHECKIN', 'Location check-ins, recommendations, point collection', 1, 0, 7, NOW(), NOW());

-- App 9: PARTNER_HUB
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(9, 'Referrer Program', 'referrer', 'REFERRER', 'Referral codes, tracking links, multi-tier rewards', 1, 0, 1, NOW(), NOW()),
(9, 'Influencer Management', 'influencer', 'INFLUENCER', 'KOL/influencer profiles, campaigns, performance tracking', 1, 0, 2, NOW(), NOW()),
(9, 'Content Builder', 'content-builder', 'CONTENT_BUILDER', 'Drag-and-drop content editor with templates', 1, 0, 3, NOW(), NOW()),
(9, 'Merchant Inviter', 'merchant-inviter', 'MERCHANT_INVITER', 'Merchant invitation tracking and onboarding', 1, 0, 4, NOW(), NOW()),
(9, 'Commission & Payout', 'commission', 'COMMISSION', 'Commission rules, payout schedules, wallet management', 1, 0, 5, NOW(), NOW()),
(9, 'Tier & Incentives', 'tier-incentives', 'TIER_INCENTIVES', 'Partner tiers (Bronze-Platinum), auto-upgrade, leaderboard', 1, 0, 6, NOW(), NOW()),
(9, 'Performance Analytics', 'partner-analytics', 'PARTNER_ANALYTICS', 'Conversion funnels, ROI analysis, channel heatmaps', 1, 0, 7, NOW(), NOW());

-- App 10: UGC_AI_HUB
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(10, 'UGC Feed', 'ugc-feed', 'UGC_FEED', 'User-generated reviews, photos, videos, and tips', 1, 0, 1, NOW(), NOW()),
(10, 'AI Content Generator', 'ai-content-gen', 'AI_CONTENT_GEN', 'AI-powered descriptions, itineraries, and social posts', 1, 0, 2, NOW(), NOW()),
(10, 'AI Resource Database', 'ai-resource-db', 'AI_RESOURCE_DB', 'AI-enriched venue database with auto-categorization', 1, 0, 3, NOW(), NOW()),
(10, 'Content Moderation', 'content-mod', 'CONTENT_MOD', 'AI spam detection, sentiment analysis, review queue', 1, 0, 4, NOW(), NOW()),
(10, 'Media Library', 'media-lib', 'MEDIA_LIB', 'Asset management with AI auto-tagging and CDN', 1, 0, 5, NOW(), NOW()),
(10, 'Multi-language Engine', 'multi-lang', 'MULTI_LANG', 'AI auto-translation (TH/EN/ZH/JA/KO/RU) with TTS', 1, 0, 6, NOW(), NOW()),
(10, 'Content Curation', 'content-curation', 'CONTENT_CURATION', 'AI quality scoring, editorial picks, auto-publishing', 1, 0, 7, NOW(), NOW());

-- App 11: CITY_DASHBOARD
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(11, 'Tourism Statistics', 'tourism-stats', 'TOURISM_STATS', 'Real-time visitor counts, nationality breakdown, spending analytics, seasonal trends', 1, 0, 1, NOW(), NOW()),
(11, 'Traffic & Mobility', 'traffic-mobility', 'TRAFFIC_MOBILITY', 'Live traffic flow, parking occupancy, public transport ridership, congestion heatmaps', 1, 0, 2, NOW(), NOW()),
(11, 'Revenue & Tax Monitor', 'revenue-tax', 'REVENUE_TAX', 'Hotel tax collection, tourism fee tracking, revenue forecasting per zone/category', 1, 0, 3, NOW(), NOW()),
(11, 'Public Safety Monitor', 'public-safety', 'PUBLIC_SAFETY', 'Incident tracking, crime heatmaps, emergency response times, CCTV integration', 1, 0, 4, NOW(), NOW()),
(11, 'Environmental Monitor', 'env-monitor', 'ENV_MONITOR', 'Air quality index, beach water quality, noise levels, waste management metrics', 1, 0, 5, NOW(), NOW()),
(11, 'Business Intelligence', 'city-bi', 'CITY_BI', 'Cross-data analytics, KPI scorecards, trend predictions, policy impact analysis', 1, 0, 6, NOW(), NOW()),
(11, 'Citizen Feedback', 'citizen-feedback', 'CITIZEN_FEEDBACK', 'Public complaints, satisfaction surveys, improvement suggestions, response tracking', 1, 0, 7, NOW(), NOW());

-- App 12: GOV_ERP
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(12, 'Budget Management', 'budget-mgmt', 'BUDGET_MGMT', 'Annual budget planning, allocation, tracking, and variance analysis per department', 1, 0, 1, NOW(), NOW()),
(12, 'Procurement & e-GP', 'procurement', 'PROCUREMENT', 'e-Government Procurement integration, bidding, vendor selection, purchase orders', 1, 0, 2, NOW(), NOW()),
(12, 'Financial Reporting', 'fin-reporting', 'FIN_REPORTING', 'GFMIS-compliant financial statements, cash flow, trial balance, audit-ready reports', 1, 0, 3, NOW(), NOW()),
(12, 'Asset Management', 'asset-mgmt', 'ASSET_MGMT', 'Government asset registry, depreciation tracking, maintenance schedules, QR tagging', 1, 0, 4, NOW(), NOW()),
(12, 'HR & Payroll', 'hr-payroll', 'HR_PAYROLL', 'Government employee records, payroll processing, leave management, performance reviews', 1, 0, 5, NOW(), NOW()),
(12, 'Document Management', 'doc-mgmt', 'DOC_MGMT', 'e-Document workflow, digital signatures, document tracking, retention policies', 1, 0, 6, NOW(), NOW()),
(12, 'Audit & Compliance', 'audit-compliance', 'AUDIT_COMPLIANCE', 'Internal audit tools, compliance checklists, risk assessment, audit preparation', 1, 0, 7, NOW(), NOW());

-- App 13: EVENT_MICE
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(13, 'Event Management', 'event-mgmt', 'EVENT_MGMT', 'Create and manage events with timeline and task tracking', 1, 0, 1, NOW(), NOW()),
(13, 'Venue Booking', 'venue-booking', 'VENUE_BOOKING', 'Search, compare, and book venues with floor plan viewer', 1, 0, 2, NOW(), NOW()),
(13, 'MICE Planner', 'mice-planner', 'MICE_PLANNER', 'Meetings, Incentives, Conferences, Exhibitions planning tools', 1, 0, 3, NOW(), NOW()),
(13, 'Ticket & Registration', 'ticket-registration', 'TICKET_REG', 'Online registration, ticket sales, QR code e-tickets', 1, 0, 4, NOW(), NOW()),
(13, 'Exhibitor Portal', 'exhibitor-portal', 'EXHIBITOR_PORTAL', 'Exhibitor self-service — booth selection, lead collection', 1, 0, 5, NOW(), NOW()),
(13, 'Attendee Engagement', 'attendee-engagement', 'ATTENDEE_ENGAGE', 'Mobile event app, agenda builder, networking matchmaking', 1, 0, 6, NOW(), NOW()),
(13, 'Event Analytics', 'event-analytics', 'EVENT_ANALYTICS', 'Attendance tracking, engagement metrics, revenue reports', 1, 0, 7, NOW(), NOW());

-- App 14: PROJECT_MGMT
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(14, 'Project Dashboard', 'project-dashboard', 'PROJECT_DASH', 'Project overview — status, timeline, milestones, KPI scorecards', 1, 0, 1, NOW(), NOW()),
(14, 'Task & Gantt', 'task-gantt', 'TASK_GANTT', 'Task management with Gantt chart, Kanban board, dependencies', 1, 0, 2, NOW(), NOW()),
(14, 'Resource Management', 'resource-mgmt', 'RESOURCE_MGMT', 'Allocate team members, equipment — capacity planning', 1, 0, 3, NOW(), NOW()),
(14, 'Budget & Cost Tracking', 'budget-cost', 'BUDGET_COST', 'Project budgets, expense tracking, cost forecasting', 1, 0, 4, NOW(), NOW()),
(14, 'Document & Files', 'project-docs', 'PROJECT_DOCS', 'Project documents, version control, file sharing', 1, 0, 5, NOW(), NOW()),
(14, 'Risk Management', 'risk-mgmt', 'RISK_MGMT', 'Risk identification, impact assessment, mitigation plans', 1, 0, 6, NOW(), NOW()),
(14, 'Reporting & Analytics', 'project-reports', 'PROJECT_REPORTS', 'Status reports, burn-down charts, performance metrics', 1, 0, 7, NOW(), NOW());

-- App 15: DATA_EXCHANGE
INSERT INTO `modules` (`application_id`, `name`, `slug`, `code`, `description`, `is_active`, `is_premium`, `sort_order`, `created_at`, `updated_at`) VALUES
(15, 'Data Index', 'data-index', 'DATA_INDEX', 'Central data directory — search across agencies, metadata registry', 1, 0, 1, NOW(), NOW()),
(15, 'Data Catalog', 'data-catalog', 'DATA_CATALOG', 'Dataset listings with descriptions, schema, tags, ownership', 1, 0, 2, NOW(), NOW()),
(15, 'Data Quality', 'data-quality', 'DATA_QUALITY', 'Automated quality checks — completeness, accuracy, consistency', 1, 0, 3, NOW(), NOW()),
(15, 'Data Security', 'data-security', 'DATA_SECURITY', 'Access control, encryption policies, data masking, PII detection', 1, 0, 4, NOW(), NOW()),
(15, 'High Value Dataset', 'high-value-dataset', 'HIGH_VALUE_DS', 'Curated high-value open datasets — downloadable formats, API access', 1, 0, 5, NOW(), NOW()),
(15, 'Data Sandbox', 'data-sandbox', 'DATA_SANDBOX', 'Safe analysis environment — Jupyter notebooks, BI tools', 1, 0, 6, NOW(), NOW()),
(15, 'Data Request', 'data-request', 'DATA_REQUEST', 'Request data from agencies — approval workflow, SLA tracking', 1, 0, 7, NOW(), NOW()),
(15, 'Tracking & Monitoring', 'data-tracking', 'DATA_TRACKING', 'Usage analytics, download stats, API call monitoring', 1, 0, 8, NOW(), NOW());


-- ── 5.10 API Providers ──
INSERT INTO `api_providers` (`name`, `slug`, `category`, `description`, `is_active`, `is_shared`, `created_at`, `updated_at`) VALUES
('Payment Gateway', 'payment-gateway', 'payment', 'Main payment processing service', 1, 1, NOW(), NOW()),
('SMS Provider', 'sms-provider', 'sms', 'SMS messaging service', 1, 1, NOW(), NOW()),
('AI Agent Service', 'ai-agent', 'ai_agent', 'AI chatbot, translation, TTS, call center', 1, 1, NOW(), NOW()),
('Cloud Point API', 'cloud-point', 'cloud_point', 'External loyalty point system', 1, 1, NOW(), NOW()),
('Data Exchange API', 'data-exchange', 'data_exchange', 'Data import/export/sync', 1, 1, NOW(), NOW()),
('HelpDesk API', 'helpdesk-api', 'helpdesk', 'Customer support ticketing', 1, 1, NOW(), NOW()),
('Translation API', 'translate-api', 'translate', 'Multi-language translation', 1, 1, NOW(), NOW()),
('Text-to-Speech API', 'tts-api', 'tts', 'Voice synthesis service', 1, 1, NOW(), NOW()),
('Currency Exchange API', 'currency-exchange', 'currency', 'Real-time currency exchange rates and conversion for multi-country tourism transactions', 1, 1, NOW(), NOW());

-- ── 5.11 Users (Admin + Demo) ──
-- Password: 'password' hashed with bcrypt
INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@thailandtogether.com', '$2y$12$LmPBbDvPxRfC4Tq.A.gYCeP/M1nD0yqXzNbFq9E3u.V7wL.Dh1JSa', 'active', NOW(), NOW(), NOW()),
(2, 'Pattaya Operator', 'operator@thailandtogether.com', '$2y$12$LmPBbDvPxRfC4Tq.A.gYCeP/M1nD0yqXzNbFq9E3u.V7wL.Dh1JSa', 'active', NOW(), NOW(), NOW()),
(3, 'Demo Tourist', 'tourist@thailandtogether.com', '$2y$12$LmPBbDvPxRfC4Tq.A.gYCeP/M1nD0yqXzNbFq9E3u.V7wL.Dh1JSa', 'active', NOW(), NOW(), NOW());

-- ── 5.12 Role assignments ──
INSERT INTO `role_user` (`user_id`, `role_id`, `country_id`, `cluster_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NOW(), NOW()),
(2, 3, NULL, 1, NOW(), NOW());

-- ── 5.13 Group membership ──
INSERT INTO `group_user` (`group_id`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 3, NOW(), NOW());

-- ── 5.14 Group app access (tourists → all 15 apps in Pattaya) ──
INSERT INTO `group_app_access` (`group_id`, `cluster_id`, `application_id`, `has_access`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 1, NOW(), NOW()), (3, 1, 2, 1, NOW(), NOW()), (3, 1, 3, 1, NOW(), NOW()),
(3, 1, 4, 1, NOW(), NOW()), (3, 1, 5, 1, NOW(), NOW()), (3, 1, 6, 1, NOW(), NOW()),
(3, 1, 7, 1, NOW(), NOW()), (3, 1, 8, 1, NOW(), NOW()), (3, 1, 9, 1, NOW(), NOW()),
(3, 1, 10, 1, NOW(), NOW()), (3, 1, 11, 1, NOW(), NOW()), (3, 1, 12, 1, NOW(), NOW()),
(3, 1, 13, 1, NOW(), NOW()), (3, 1, 14, 1, NOW(), NOW()), (3, 1, 15, 1, NOW(), NOW());

-- ── 5.15 Menu Items ──
INSERT INTO `menu_items` (`label`, `icon`, `url`, `scope`, `visibility`, `sort_order`, `application_id`, `target`, `is_active`, `created_at`, `updated_at`) VALUES
('Home', 'home', '/', 'global', 'all', 1, NULL, '_self', 1, NOW(), NOW()),
('Explore', 'compass', '/explore', 'global', 'all', 2, 1, '_self', 1, NOW(), NOW()),
('Bookings', 'calendar', '/bookings', 'global', 'authenticated', 3, NULL, '_self', 1, NOW(), NOW()),
('Rewards', 'gift', '/rewards', 'global', 'authenticated', 4, NULL, '_self', 1, NOW(), NOW()),
('Admin', 'settings', '/admin', 'global', 'admin', 99, NULL, '_self', 1, NOW(), NOW());


-- ── 5.16 Places (Pattaya) ──
INSERT INTO `place` (`place_id`, `place_code`, `place_name_th`, `place_name_en`, `lat`, `lng`, `place_type`, `is_active`, `cluster_id`, `created_at`, `updated_at`) VALUES
(1, 'PLACE_TERMINAL21_PIER21', 'Terminal 21 Pattaya – Pier 21 Food Court / Landmark Walk', 'Terminal 21 Pattaya – Pier 21 Food Court / Landmark Walk', 12.9340000, 100.8828000, 'mall', 1, 1, NOW(), NOW()),
(2, 'PLACE_THEPPRASIT_NIGHT_MARKET', 'ตลาดเทพประสิทธิ์ไนท์มาร์เก็ต', 'Thepprasit Night Market', 12.9010000, 100.8850000, 'market', 1, 1, NOW(), NOW()),
(3, 'PLACE_CENTRAL_FESTIVAL', 'เซ็นทรัลเฟสติวัล พัทยาบีช', 'Central Festival Pattaya Beach', 12.9450000, 100.8850000, 'mall', 1, 1, NOW(), NOW()),
(4, 'PLACE_LAN_PHO_NAKLUA', 'ตลาดลานโพธิ์นาเกลือ', 'Lan Pho Naklua Seafood Market', 12.9650000, 100.8900000, 'market', 1, 1, NOW(), NOW()),
(5, 'PLACE_PUPEN_SEAFOOD', 'ภูเพ็ญซีฟู้ด จอมเทียน', 'Pupen Seafood (Jomtien)', 12.8860000, 100.8720000, 'restaurant', 1, 1, NOW(), NOW()),
(6, 'PLACE_SKY_GALLERY', 'The Sky Gallery Pattaya', 'The Sky Gallery Pattaya', 12.9200000, 100.8600000, 'restaurant', 1, 1, NOW(), NOW()),
(7, 'PLACE_CHOCOLATE_FACTORY', 'The Chocolate Factory Pattaya', 'The Chocolate Factory Pattaya', 12.8920000, 100.8710000, 'restaurant', 1, 1, NOW(), NOW()),
(8, 'PLACE_GLASS_HOUSE', 'The Glass House Pattaya', 'The Glass House Pattaya (Beachfront)', 12.8850000, 100.8680000, 'restaurant', 1, 1, NOW(), NOW()),
(9, 'PLACE_HORIZON_ROOFTOP', 'Horizon Rooftop Restaurant & Bar (Hilton Pattaya)', 'Horizon Rooftop Restaurant & Bar (Hilton Pattaya)', 12.9445000, 100.8842000, 'restaurant', 1, 1, NOW(), NOW()),
(10, 'PLACE_TREE_TOWN', 'Tree Town Pattaya', 'Tree Town Pattaya (Eat drinks + Live Music)', 12.9300000, 100.8780000, 'restaurant', 1, 1, NOW(), NOW()),
(11, 'PLACE_PATTAYA_NIGHT_BAZAAR', 'พัทยาไนท์บาซาร์', 'Pattaya Night Bazaar (Eat/Shop)', 12.9280000, 100.8810000, 'market', 1, 1, NOW(), NOW()),
(12, 'PLACE_JOMTIEN_NIGHT_MARKET', 'จอมเทียนไนท์มาร์เก็ต', 'Jomtien Night Market', 12.8800000, 100.8700000, 'market', 1, 1, NOW(), NOW()),
(13, 'PLACE_FLOATING_MARKET', 'ตลาดน้ำ 4 ภาค พัทยา', 'Pattaya Floating Market', 12.9010000, 100.8540000, 'attraction', 1, 1, NOW(), NOW()),
(14, 'PLACE_ROYAL_GARDEN', 'รอยัล การ์เด้น พลาซ่า', 'Royal Garden Plaza Pattaya', 12.9380000, 100.8840000, 'mall', 1, 1, NOW(), NOW()),
(15, 'PLACE_CENTRAL_MARINA', 'เซ็นทรัล มารีน่า พัทยา', 'Central Marina Pattaya', 12.9500000, 100.8830000, 'mall', 1, 1, NOW(), NOW()),
(16, 'PLACE_LOCAL_BREAKFAST', 'ร้านอาหารเช้าท้องถิ่น', 'Local Breakfast Stall', 12.9350000, 100.8790000, 'restaurant', 1, 1, NOW(), NOW()),
(17, 'PLACE_LOCAL_LUNCH_STREET', 'ถนนข้าวกลางวัน', 'Local Lunch Street (street food)', 12.9320000, 100.8800000, 'restaurant', 1, 1, NOW(), NOW()),
(18, 'PLACE_CAFE_STOP', 'ร้านกาแฟ & ขนม', 'Cafe stop (coffee + dessert)', 12.9360000, 100.8830000, 'cafe', 1, 1, NOW(), NOW()),
(19, 'PLACE_PARTNER_DINNER', 'ร้านอาหารพาร์ทเนอร์', 'Dinner (partner restaurant / seafood)', 12.9310000, 100.8810000, 'restaurant', 1, 1, NOW(), NOW()),
(20, 'PLACE_PARTNER_MEAL', 'ร้านอาหารโซนเซ็นทรัล', 'Meal (Central Pattaya zone)', 12.9400000, 100.8840000, 'restaurant', 1, 1, NOW(), NOW()),
(21, 'PLACE_TIFFANY_SHOW', 'ทิฟฟานี่โชว์ พัทยา', 'Tiffany''s Show Pattaya', 12.9530000, 100.8900000, 'show', 1, 1, NOW(), NOW()),
(22, 'PLACE_ALCAZAR_SHOW', 'อัลคาซาร์ คาบาเร่ต์โชว์', 'Alcazar Cabaret Show', 12.9410000, 100.8870000, 'show', 1, 1, NOW(), NOW()),
(23, 'PLACE_WALKING_STREET', 'วอล์คกิ้งสตรีท พัทยา', 'Walking Street Pattaya', 12.9260000, 100.8710000, 'entertainment', 1, 1, NOW(), NOW()),
(24, 'PLACE_HARBOR_PATTAYA', 'ฮาร์เบอร์ พัทยา', 'Harbor Pattaya – Family Arcade / Indoor Fun', 12.9350000, 100.8860000, 'entertainment', 1, 1, NOW(), NOW()),
(25, 'PLACE_BOWLING_ZONE', 'โบว์ลิ่งโซน พัทยา', 'Bowling Zone (Pattaya)', 12.9370000, 100.8850000, 'entertainment', 1, 1, NOW(), NOW()),
(26, 'PLACE_LETS_RELAX_SPA', 'เล็ทส์ รีแล็กซ์ สปา', 'Let''s Relax Spa (Foot Massage 60 min.)', 12.9390000, 100.8830000, 'spa', 1, 1, NOW(), NOW()),
(27, 'PLACE_HEALTH_LAND', 'เฮลท์แลนด์ สปา & มาสซาจ', 'Health Land Spa & Massage (Thai Massage 120 min.)', 12.9250000, 100.8770000, 'spa', 1, 1, NOW(), NOW()),
(28, 'PLACE_LUXURY_SPA', 'ลักซ์ชัวรี่สปา', 'Luxury Spa Package (premium 120 min.)', 12.9420000, 100.8840000, 'spa', 1, 1, NOW(), NOW()),
(29, 'PLACE_DETOX_JUICE', 'ดีท็อกซ์จูซบาร์', 'Detox Juice Bar', 12.9330000, 100.8810000, 'cafe', 1, 1, NOW(), NOW()),
(30, 'PLACE_SUNRISE_YOGA', 'โยคะริมหาด', 'Sunrise Beach Yoga', 12.9280000, 100.8650000, 'wellness', 1, 1, NOW(), NOW()),
(31, 'PLACE_HEALTHY_CAFE', 'ร้านอาหารเพื่อสุขภาพ', 'Healthy Cafe Meal (clean food)', 12.9340000, 100.8820000, 'cafe', 1, 1, NOW(), NOW()),
(32, 'PLACE_WELLNESS_CLINIC', 'คลินิกสุขภาพ', 'Wellness Check-Up Clinic', 12.9400000, 100.8860000, 'wellness', 1, 1, NOW(), NOW()),
(33, 'PLACE_MEDITATION_GARDEN', 'สวนนั่งสมาธิ', 'Meditation Garden', 12.9100000, 100.8600000, 'wellness', 1, 1, NOW(), NOW()),
(34, 'PLACE_FITNESS_BOOTCAMP', 'ฟิตเนสบูทแคมป์', 'Fitness Bootcamp', 12.9270000, 100.8660000, 'wellness', 1, 1, NOW(), NOW()),
(35, 'PLACE_ICE_BATH', 'ไอซ์บาทรีคัฟเวอรี่', 'Ice Bath Recovery', 12.9280000, 100.8670000, 'wellness', 1, 1, NOW(), NOW()),
(36, 'PLACE_HOTEL_SPA', 'สปาในโรงแรม', 'Hotel Spa Package (90-120 mins)', 12.9430000, 100.8840000, 'spa', 1, 1, NOW(), NOW()),
(37, 'PLACE_MIDRANGE_HOTEL', 'โรงแรมระดับกลาง Day-Use', 'Mid-Range Hotel Day-Use (Pattaya)', 12.9380000, 100.8830000, 'hotel', 1, 1, NOW(), NOW()),
(38, 'PLACE_BUDGET_HOTEL', 'โรงแรมราคาประหยัด Day-Use', 'Budget Hotel Day-Use (Pattaya)', 12.9350000, 100.8810000, 'hotel', 1, 1, NOW(), NOW()),
(39, 'PLACE_LUXURY_HOTEL', 'โรงแรมหรู Staycation', 'Luxury Hotel Day-Use / Staycation (Pattaya)', 12.9440000, 100.8840000, 'hotel', 1, 1, NOW(), NOW()),
(40, 'PLACE_FAMILY_RESORT', 'แฟมิลี่รีสอร์ท', 'Family Resort Day Pass (Pool + Kids Club)', 12.9200000, 100.8750000, 'hotel', 1, 1, NOW(), NOW()),
(41, 'PLACE_COWORKING', 'พาร์ทเนอร์ Co-Working Space', 'Partner Co-Working Space (Pattaya)', 12.9390000, 100.8830000, 'office', 1, 1, NOW(), NOW()),
(42, 'PLACE_VAN_GUIDE', 'รถตู้+ไกด์เต็มวัน', 'Van + full-day guide', 12.9340000, 100.8800000, 'transport', 1, 1, NOW(), NOW()),
(43, 'PLACE_SUNSET_YACHT', 'เรือยอชท์ชมพระอาทิตย์ตก', 'Sunset Yacht Charter (per person)', 12.9260000, 100.8640000, 'tour', 1, 1, NOW(), NOW()),
(44, 'PLACE_SAFE_RIDE', 'Safe Ride กลับโรงแรม', 'Safe Ride (Bolt/Grab) back to hotel', 12.9340000, 100.8800000, 'transport', 1, 1, NOW(), NOW()),
(45, 'PLACE_BALI_HAI_PIER', 'ท่าเรือบาลีฮาย', 'Bali Hai Pier – to Koh Larn', 12.9230000, 100.8630000, 'transport', 1, 1, NOW(), NOW()),
(46, 'PLACE_KOH_LARN', 'เกาะล้าน จุดดำน้ำ', 'Koh Larn Snorkeling Spot', 12.9170000, 100.7850000, 'beach', 1, 1, NOW(), NOW()),
(47, 'PLACE_SCOOTER_RENTAL', 'ร้านเช่ามอเตอร์ไซค์', 'Licensed Scooter Rental Partner', 12.9350000, 100.8800000, 'transport', 1, 1, NOW(), NOW()),
(48, 'PLACE_BIKE_ROUTE', 'เส้นทางจักรยาน Low-Carbon', 'Low-Carbon City Bike Route', 12.9300000, 100.8750000, 'tour', 1, 1, NOW(), NOW()),
(49, 'PLACE_WATER_SPORTS', 'โซนกีฬาทางน้ำ', 'Pattaya Water Sports Zone', 12.9290000, 100.8650000, 'tour', 1, 1, NOW(), NOW()),
(50, 'PLACE_FULL_DAY_YACHT', 'เรือยอชท์เต็มวัน', 'Full-Day Yacht Charter', 12.9260000, 100.8640000, 'tour', 1, 1, NOW(), NOW()),
(51, 'PLACE_CITY_PASS', 'พัทยา City Pass', 'Pattaya City Pass / Tourist Pass', 12.9340000, 100.8800000, 'service', 1, 1, NOW(), NOW()),
(52, 'PLACE_NONG_NOOCH', 'สวนนงนุช', 'Nong Nooch Tropical Garden', 12.7650000, 100.9350000, 'attraction', 1, 1, NOW(), NOW()),
(53, 'PLACE_SANCTUARY_TRUTH', 'ปราสาทสัจธรรม', 'The Sanctuary Of Truth', 12.9710000, 100.8890000, 'attraction', 1, 1, NOW(), NOW()),
(54, 'PLACE_ART_IN_PARADISE', 'Art In Paradise Pattaya', 'Art In Paradise Pattaya', 12.9420000, 100.8870000, 'attraction', 1, 1, NOW(), NOW()),
(55, 'PLACE_FROST_ICE', 'Frost Magical Ice Of Siam', 'Frost Magical Ice Of Siam', 12.9050000, 100.8550000, 'attraction', 1, 1, NOW(), NOW()),
(56, 'PLACE_VIEWPOINT', 'จุดชมวิว เขาพระตำหนัก', 'Pattaya Viewpoint (Khao Phra Tamnak)', 12.9170000, 100.8590000, 'attraction', 1, 1, NOW(), NOW()),
(57, 'PLACE_BEACH_ROAD', 'ถนนพัทยาบีช', 'Pattaya Beach Road Walk', 12.9380000, 100.8720000, 'beach', 1, 1, NOW(), NOW()),
(58, 'PLACE_3MERMAIDS', '3 Mermaids Cafe', '3 Mermaids Cafe (Pratumnak)', 12.9150000, 100.8600000, 'cafe', 1, 1, NOW(), NOW()),
(59, 'PLACE_SHELL_TANGKE', 'เชลล์ ตังเก ซีฟู้ด', 'Shell Tangke Seafood (Na Kluea)', 12.9600000, 100.8880000, 'restaurant', 1, 1, NOW(), NOW()),
(60, 'PLACE_NAKLUA_SEAFOOD', 'ร้านซีฟู้ดนาเกลือ', 'Naklua Seafood', 12.9620000, 100.8890000, 'restaurant', 1, 1, NOW(), NOW()),
(61, 'PLACE_PEACH', 'PEACH ศูนย์ประชุมพัทยา', 'PEACH (Pattaya Exhibition And Convention Hall)', 12.9100000, 100.8580000, 'venue', 1, 1, NOW(), NOW()),
(62, 'PLACE_ROYAL_CLIFF', 'โรยัล คลิฟ โฮเทลส์', 'Royal Cliff Hotels Group', 12.9100000, 100.8570000, 'hotel', 1, 1, NOW(), NOW());


-- ── 5.17 Journeys (80 rows) ──
INSERT INTO `journey` (`journey_id`, `journey_code`, `journey_group`, `journey_name_th`, `journey_name_en`, `group_size`, `gmv_per_person`, `gmv_per_group`, `tp_total_normal`, `tp_total_goal`, `tp_total_special`, `total_minutes_sum`, `luxury_tone_en`, `status`, `cluster_id`, `created_at`, `updated_at`) VALUES
(1, "A1", "A", "เส้นทางอาหาร – ท้องถิ่นสู่ห้าง", "Food Route – Local to Mall", 5, 1640.00, 8200.00, 96, 145, 262, 525, "lively enjoyable, strong revenue distribution", "ACTIVE", 1, NOW(), NOW()),
(2, "A2", "A", "เส้นทางซีฟู้ด – นาเกลือถึงจอมเทียน", "Seafood Route – Naklua to Jomtien", 4, 2380.00, 9520.00, 125, 183, 320, 445, "local fully satisfying family-friendly", "ACTIVE", 1, NOW(), NOW()),
(3, "A3", "A", "เส้นทางโรแมนติก – กลางวันสู่กลางคืน", "Day-To-Night Romantic Dining", 3, 1930.00, 5790.00, 107, 160, 284, 405, "Romantic, scenic, photogenic", "ACTIVE", 1, NOW(), NOW()),
(4, "A4", "A", "เส้นทางอาหารครอบครัว – ตลาดสู่ห้าง", "Family Food Trail – Market to Mall", 5, 2150.00, 10750.00, 106, 152, 252, 595, "fun, comfortable, and great value for the whole family", "ACTIVE", 1, NOW(), NOW()),
(5, "A5", "A", "เส้นทางอาหารริมทาง – ประหยัด", "Budget Street Food Trail", 5, 930.00, 4650.00, 67, 109, 204, 470, "value-oriented, flexible, and easy to enjoy", "ACTIVE", 1, NOW(), NOW()),
(6, "A6", "A", "เส้นทางอาหารพรีเมียม", "Premium Dining Journey", 3, 3250.00, 9750.00, 150, 209, 340, 375, "premium, high-image, and easy to close", "ACTIVE", 1, NOW(), NOW()),
(7, "A7", "A", "เส้นทางอาหารพหุวัฒนธรรม", "Multicultural Food Route", 4, 1700.00, 6800.00, 88, 127, 216, 400, "Multicultural-friendly and accessible", "ACTIVE", 1, NOW(), NOW()),
(8, "A8", "A", "เส้นทางคาเฟ่ & ขนม ถ่ายรูป", "Cafe & Dessert Photo Route", 4, 1350.00, 5400.00, 84, 130, 238, 380, "light, photogenic, and easy to share", "ACTIVE", 1, NOW(), NOW()),
(9, "A9", "A", "เส้นทางอาหารตลาดกลางคืน", "Night Market Food Trail", 4, 1300.00, 5200.00, 72, 107, 184, 440, "lively enjoyable, strong revenue distribution", "ACTIVE", 1, NOW(), NOW()),
(10, "A10", "A", "เส้นทางอาหาร Terminal 21", "Terminal 21 Food & Mall Route", 3, 1000.00, 3000.00, 80, 131, 260, 420, "lively enjoyable, strong revenue distribution", "ACTIVE", 1, NOW(), NOW()),
(11, "B1", "B", "ทำงาน & พัก – โรงแรม + Co-Working", "Work & Stay – Hotel + Co-Working", 5, 3530.00, 17650.00, 161, 222, 362, 660, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(12, "B2", "B", "ครอบครัว รีสอร์ท เดย์ เอาท์", "Family Resort Day Out", 4, 2950.00, 11800.00, 148, 214, 366, 635, "comfortable, easy to use, and family-friendly", "ACTIVE", 1, NOW(), NOW()),
(13, "B3", "B", "Luxury Staycation & สปา", "Luxury Staycation & Spa", 4, 8150.00, 32600.00, 346, 464, 732, 655, "Premium, high-image, high-spending", "ACTIVE", 1, NOW(), NOW()),
(14, "B4", "B", "โรงแรมประหยัด + เที่ยวเมือง", "Budget Hotel + City Explore", 4, 1800.00, 7200.00, 102, 153, 274, 630, "value-driven, flexible, and easy to continue after the stay", "ACTIVE", 1, NOW(), NOW()),
(15, "B5", "B", "โรงแรมกลาง + สปา & เวลเนส", "Mid-Range + Spa & Wellness", 3, 2930.00, 8790.00, 147, 212, 364, 490, "relaxed, balanced, and quality-time oriented", "ACTIVE", 1, NOW(), NOW()),
(16, "B6", "B", "พัก & ช้อป – โรงแรม + ห้าง", "Stay & Shop – Hotel + Malls", 4, 2850.00, 11400.00, 134, 187, 308, 555, "comfortable stay with easy add-on extension", "ACTIVE", 1, NOW(), NOW()),
(17, "B7", "B", "โรงแรมหรู ประสบการณ์เต็มรูปแบบ", "Luxury Hotel Full Experience", 4, 8150.00, 32600.00, 356, 484, 782, 710, "Premium, high-image, high-spending", "ACTIVE", 1, NOW(), NOW()),
(18, "B8", "B", "โรงแรมธุรกิจ + MICE", "Business Hotel + MICE", 6, 4200.00, 25200.00, 188, 257, 416, 770, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(19, "B9", "B", "โรงแรมครอบครัว + กิจกรรม", "Family Hotel + Fun Activities", 5, 3150.00, 15750.00, 146, 203, 332, 730, "comfortable, easy to use, and family-friendly", "ACTIVE", 1, NOW(), NOW()),
(20, "B10", "B", "โรงแรม + City Pass เต็มวัน", "Hotel + City Pass Full Day", 6, 4650.00, 27900.00, 206, 281, 452, 725, "Premium, high-image, high-spending", "ACTIVE", 1, NOW(), NOW()),
(21, "C1", "C", "อาหารเย็น & โชว์", "Dinner & Show Night", 4, 2300.00, 9200.00, 112, 159, 264, 405, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(22, "C2", "C", "คาบาเร่ต์พรีเมียม & รูฟท็อป", "Premium Cabaret & Rooftop", 3, 3500.00, 10500.00, 160, 222, 360, 325, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(23, "C3", "C", "วอล์คกิ้งสตรีท ถ่ายรูป", "Walking Street Photo Walk", 2, 550.00, 1100.00, 62, 108, 224, 315, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(24, "C4", "C", "ห้าง + ไลฟ์มิวสิคไนท์", "Mall + Live Music Night", 4, 1530.00, 6120.00, 81, 119, 202, 445, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(25, "C5", "C", "อาร์เคด + โบว์ลิ่ง + โชว์", "Arcade + Bowling + Show", 4, 2700.00, 10800.00, 128, 180, 296, 480, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(26, "C6", "C", "สปา + อาหารเย็น + โชว์", "Spa + Dinner + Show", 3, 2650.00, 7950.00, 126, 177, 292, 340, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(27, "C7", "C", "โบว์ลิ่ง + อาร์เคดไนท์", "Bowling + Arcade Night", 3, 1250.00, 3750.00, 70, 105, 180, 430, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(28, "C8", "C", "ทัวร์ตลาดกลางคืนสามแห่ง", "Night Market Triple Tour", 4, 1400.00, 5600.00, 66, 93, 162, 500, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(29, "C9", "C", "เรือยอชท์พระอาทิตย์ตก + อาหารพรีเมียม", "Sunset Yacht + Premium Dining", 3, 6550.00, 19650.00, 282, 381, 604, 450, "premium, visually striking, and high-spending", "ACTIVE", 1, NOW(), NOW()),
(30, "C10", "C", "ทัวร์เต็มวัน + โชว์กลางคืน", "Full Day Tour + Night Show", 4, 3550.00, 14200.00, 162, 224, 364, 930, "lively high-energy, but controlled safe", "ACTIVE", 1, NOW(), NOW()),
(31, "D1", "D", "เวลเนสตอนเช้า", "Morning Wellness Reset", 3, 1550.00, 4650.00, 92, 141, 254, 330, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(32, "D2", "D", "สปาพรีเมียม & ดีท็อกซ์", "Premium Spa & Detox Day", 3, 4800.00, 14400.00, 212, 290, 464, 345, "premium, relaxing, and health-connected", "ACTIVE", 1, NOW(), NOW()),
(33, "D3", "D", "โยคะ & นวด", "Active Yoga & Massage", 3, 1250.00, 3750.00, 80, 125, 230, 370, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(34, "D4", "D", "ตรวจสุขภาพ + สปา", "Health Check + Spa Package", 3, 4950.00, 14850.00, 218, 297, 476, 345, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(35, "D5", "D", "ครอบครัว + เวลเนส", "Family Fun + Wellness", 3, 1050.00, 3150.00, 72, 114, 214, 380, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(36, "D6", "D", "คาเฟ่ & สปาบ่าย", "Cafe & Spa Afternoon", 3, 1250.00, 3750.00, 80, 125, 230, 220, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(37, "D7", "D", "ดีท็อกซ์ & อาหารรูฟท็อป", "Premium Detox & Rooftop Dining", 3, 4800.00, 14400.00, 212, 290, 464, 345, "premium, relaxing, and health-connected", "ACTIVE", 1, NOW(), NOW()),
(38, "D8", "D", "นั่งสมาธิ & อาหารสะอาด", "Meditation & Clean Food", 3, 750.00, 2250.00, 60, 99, 190, 310, "relaxing, restorative, and balanced", "ACTIVE", 1, NOW(), NOW()),
(39, "D9", "D", "ฟิตเนส & ฟื้นฟูร่างกาย", "Fitness Bootcamp & Recovery", 4, 2150.00, 8600.00, 106, 152, 252, 330, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(40, "D10", "D", "สปา + อาหาร + โชว์", "Spa + Dinner + Show Night", 3, 2650.00, 7950.00, 126, 177, 292, 330, "Relaxing, restorative, balanced", "ACTIVE", 1, NOW(), NOW()),
(41, "E1", "E", "ตลาดกลางคืน + ซีฟู้ดวอล์ค", "Night Market + Seafood Walk", 2, 1000.00, 2000.00, 80, 132, 260, 395, "Agile, practical, easy to book", "ACTIVE", 1, NOW(), NOW()),
(42, "E2", "E", "เกาะล้าน ไอส์แลนด์ฮอป", "Koh Larn Island Hop", 3, 1600.00, 4800.00, 94, 143, 258, 580, "City-opening, fresh, high-movement", "ACTIVE", 1, NOW(), NOW()),
(43, "E3", "E", "กีฬาทางน้ำ & อาหาร", "Water Sports & Dining", 2, 2450.00, 4900.00, 128, 187, 326, 325, "City-opening, fresh, high-movement", "ACTIVE", 1, NOW(), NOW()),
(44, "E4", "E", "จักรยานเมือง Low-Carbon", "Low-Carbon City Bike", 1, 600.00, 600.00, 64, 111, 228, 310, "simple, sustainable, and story-driven", "ACTIVE", 1, NOW(), NOW()),
(45, "E5", "E", "เกาะล้านครอบครัว + อาร์เคด", "Koh Larn Family + Arcade", 3, 1250.00, 3750.00, 80, 125, 230, 515, "agile, practical, and easy to book", "ACTIVE", 1, NOW(), NOW()),
(46, "E6", "E", "รถตู้เต็มวัน + สวนนงนุช", "Full-Day Van + Nong Nooch", 3, 2000.00, 6000.00, 110, 164, 290, 790, "Agile, practical, easy to book", "ACTIVE", 1, NOW(), NOW()),
(47, "E7", "E", "ทัวร์ซีฟู้ดชายฝั่ง", "Coastal Seafood Tour", 3, 2500.00, 7500.00, 120, 169, 280, 650, "Agile, practical, easy to book", "ACTIVE", 1, NOW(), NOW()),
(48, "E8", "E", "มอเตอร์ไซค์สำรวจเมือง", "Scooter City Explorer", 4, 1550.00, 6200.00, 92, 140, 254, 380, "Agile, practical, easy to book", "ACTIVE", 1, NOW(), NOW()),
(49, "E9", "E", "เรือยอชท์เต็มวัน พรีเมียม", "Premium Yacht Full Day", 3, 10650.00, 31950.00, 446, 594, 932, 665, "City-opening, fresh, high-movement", "ACTIVE", 1, NOW(), NOW()),
(50, "E10", "E", "เกาะล้าน จักรยาน & เรือ", "Koh Larn Bike & Ferry", 2, 250.00, 500.00, 50, 93, 200, 470, "Agile, practical, easy to book", "ACTIVE", 1, NOW(), NOW()),
(51, "F1", "F", "งานประชุม + ประสบการณ์เมือง", "Conference + City Experience", 4, 3150.00, 12600.00, 146, 204, 332, 570, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(52, "F2", "F", "นิทรรศการ + วันช้อปปิ้ง", "Exhibition + Shopping Day", 4, 1900.00, 7600.00, 86, 119, 202, 420, "High-energy, crowd-building, UGC-driven", "ACTIVE", 1, NOW(), NOW()),
(53, "F3", "F", "กีฬา + ทัวร์เมือง", "Sports Event + City Tour", 4, 1650.00, 6600.00, 86, 125, 212, 525, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(54, "F4", "F", "เทศกาล + ตลาดกลางคืน", "Festival Night Market Walk", 3, 1000.00, 3000.00, 70, 112, 210, 545, "High-energy, crowd-building, UGC-driven", "ACTIVE", 1, NOW(), NOW()),
(55, "F5", "F", "กีฬา + ฟื้นฟูเวลเนส", "Sports + Wellness Recovery", 3, 1500.00, 4500.00, 90, 139, 250, 300, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(56, "F6", "F", "เวิร์คช้อป + อาหารเย็น + โชว์", "Workshop + Dinner + Show", 3, 2650.00, 7950.00, 126, 177, 292, 430, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(57, "F7", "F", "MICE เต็มวัน + กาล่า", "MICE Full Day + Gala", 4, 3850.00, 15400.00, 174, 240, 388, 555, "High-energy, crowd-building, UGC-driven", "ACTIVE", 1, NOW(), NOW()),
(58, "F8", "F", "อาหารวิวสวย & อีเว้นท์", "Scenic Dining & Event", 2, 1800.00, 3600.00, 112, 174, 324, 360, "High-energy, crowd-building, UGC-driven", "ACTIVE", 1, NOW(), NOW()),
(59, "F9", "F", "คอนเสิร์ต + อาหารเย็น", "Concert + Dinner Night", 3, 2250.00, 6750.00, 130, 197, 340, 405, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(60, "F10", "F", "ประชุม + กาล่าดินเนอร์ พรีเมียม", "Conference + Gala Dinner Premium", 3, 4800.00, 14400.00, 212, 290, 464, 600, "Professional, agile, conversion-focused", "ACTIVE", 1, NOW(), NOW()),
(61, "G1", "G", "โชว์ & เดินริมหาด", "Show & Beach Walk", 2, 2000.00, 4000.00, 88, 142, 276, 450, "content content-rich, dynamic, highly shareable", "ACTIVE", 1, NOW(), NOW()),
(62, "G2", "G", "ช้อปห้าง + คาเฟ่", "Mall Hopping + Cafe", 3, 1800.00, 5400.00, 72, 115, 214, 465, "Easy to walk, repeat-purchase friendly, revenue-distributing", "ACTIVE", 1, NOW(), NOW()),
(63, "G3", "G", "ศิลปะ & วัฒนธรรม", "Art & Culture Explorer", 3, 2800.00, 8400.00, 94, 143, 258, 495, "Story-rich, visually strong, culturally valuable", "ACTIVE", 1, NOW(), NOW()),
(64, "G4", "G", "ตลาดกลางคืน & เดินสตรีท", "Night Market & Street Walk", 3, 1850.00, 5550.00, 68, 110, 206, 475, "Local, satisfying, and easy to extend", "ACTIVE", 1, NOW(), NOW()),
(65, "G5", "G", "ห้าง + โชว์ + ตลาด", "Mall + Show + Market Triple", 3, 2350.00, 7050.00, 104, 156, 278, 465, "content content-rich, dynamic, highly shareable", "ACTIVE", 1, NOW(), NOW()),
(66, "G6", "G", "พิพิธภัณฑ์น้ำแข็ง + คาเฟ่ + โชว์", "Ice Museum + Cafe + Show", 3, 2650.00, 7950.00, 116, 171, 302, 475, "Fun, comfortable, easy to photograph", "ACTIVE", 1, NOW(), NOW()),
(67, "G7", "G", "ช้อปเซ็นทรัล + พระอาทิตย์ตก", "Central Shopping + Sunset", 3, 1300.00, 3900.00, 72, 115, 214, 450, "Light, balanced, and easy to share", "ACTIVE", 1, NOW(), NOW()),
(68, "G8", "G", "มารีน่า + Terminal 21 + ท้องถิ่น", "Marina + Terminal 21 + Local", 3, 950.00, 2850.00, 54, 92, 178, 390, "content content-rich, dynamic, highly shareable", "ACTIVE", 1, NOW(), NOW()),
(69, "G9", "G", "ปราสาทสัจธรรม + ตลาด + คาเฟ่", "Sanctuary + Market + Cafe", 3, 2200.00, 6600.00, 88, 136, 246, 490, "content content-rich, dynamic, highly shareable", "ACTIVE", 1, NOW(), NOW()),
(70, "G10", "G", "มรดก + ตลาด + คาเฟ่", "Heritage + Market + Cafe", 3, 1750.00, 5250.00, 100, 151, 270, 520, "Story-rich, visually strong, culturally valuable", "ACTIVE", 1, NOW(), NOW()),
(71, "H1", "H", "ตลาดน้ำ + ห้างเต็มวัน", "Floating Market + Mall Full Day", 3, 3050.00, 9150.00, 72, 115, 214, 655, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(72, "H2", "H", "สวนนงนุช + จุดชมวิว + คาเฟ่", "Nong Nooch + Viewpoint + Cafe", 3, 2150.00, 6450.00, 72, 114, 214, 715, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(73, "H3", "H", "Art In Paradise + ห้าง + ตลาด", "Art In Paradise + Mall + Market", 3, 2450.00, 7350.00, 72, 115, 214, 590, "Story-rich, visually strong, culturally valuable", "ACTIVE", 1, NOW(), NOW()),
(74, "H4", "H", "ศิลปะ + คาเฟ่ + ตลาดกลางคืน", "Art + Cafe + Night Market", 3, 1400.00, 4200.00, 74, 117, 218, 440, "Easy to walk, repeat-purchase friendly, revenue-distributing", "ACTIVE", 1, NOW(), NOW()),
(75, "H5", "H", "สวนนงนุช + คาเฟ่ + Terminal 21", "Nong Nooch + Cafe + Terminal 21", 3, 2550.00, 7650.00, 84, 130, 238, 705, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(76, "H6", "H", "ปราสาทสัจธรรม + เซ็นทรัล + Terminal", "Sanctuary + Central + Terminal", 3, 2700.00, 8100.00, 90, 139, 250, 540, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(77, "H7", "H", "Frost Ice + คาเฟ่ + Terminal 21", "Frost Ice + Cafe + Terminal 21", 3, 2650.00, 7950.00, 84, 130, 238, 675, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(78, "H8", "H", "เดินหาด + คาเฟ่ + ตลาด", "Beach Walk + Cafe + Market", 3, 2400.00, 7200.00, 54, 91, 178, 575, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW()),
(79, "H9", "H", "ตลาดน้ำ + Terminal + จุดชมวิว", "Floating Market + Terminal + Viewpoint", 3, 1950.00, 5850.00, 62, 102, 194, 560, "content-rich easy to use suitable for short-stay", "ACTIVE", 1, NOW(), NOW()),
(80, "H10", "H", "ตลาดน้ำ + Terminal + เซ็นทรัล", "Floating Market + Terminal + Central", 3, 2800.00, 8400.00, 74, 118, 218, 690, "fun comfortable family-friendly", "ACTIVE", 1, NOW(), NOW());

-- ── 5.18 Journey Steps (~240 rows) ──
INSERT INTO `journey_step` (`journey_id`, `place_id`, `step_no`, `duration_minutes`, `tp_normal`, `tp_goal`, `tp_special`) VALUES
(1, 16, 1, 175, 32, 48, 87),
(1, 2, 2, 175, 32, 48, 87),
(1, 3, 3, 175, 32, 48, 87),
(2, 4, 1, 148, 41, 61, 106),
(2, 18, 2, 148, 41, 61, 106),
(2, 5, 3, 148, 41, 61, 106),
(3, 6, 1, 135, 35, 53, 94),
(3, 18, 2, 135, 35, 53, 94),
(3, 19, 3, 135, 35, 53, 94),
(4, 13, 1, 198, 35, 50, 84),
(4, 20, 2, 198, 35, 50, 84),
(4, 3, 3, 198, 35, 50, 84),
(5, 16, 1, 156, 22, 36, 68),
(5, 17, 2, 156, 22, 36, 68),
(5, 18, 3, 156, 22, 36, 68),
(6, 8, 1, 125, 50, 69, 113),
(6, 9, 2, 125, 50, 69, 113),
(6, 7, 3, 125, 50, 69, 113),
(7, 20, 1, 133, 29, 42, 72),
(7, 19, 2, 133, 29, 42, 72),
(7, 7, 3, 133, 29, 42, 72),
(8, 18, 1, 126, 28, 43, 79),
(8, 7, 2, 126, 28, 43, 79),
(8, 14, 3, 126, 28, 43, 79),
(9, 2, 1, 146, 24, 35, 61),
(9, 11, 2, 146, 24, 35, 61),
(9, 1, 3, 146, 24, 35, 61),
(10, 1, 1, 140, 26, 43, 86),
(10, 18, 2, 140, 26, 43, 86),
(10, 19, 3, 140, 26, 43, 86),
(11, 37, 1, 220, 53, 74, 120),
(11, 18, 2, 220, 53, 74, 120),
(11, 41, 3, 220, 53, 74, 120),
(12, 40, 1, 211, 49, 71, 122),
(12, 20, 2, 211, 49, 71, 122),
(12, 7, 3, 211, 49, 71, 122),
(13, 39, 1, 218, 115, 154, 244),
(13, 36, 2, 218, 115, 154, 244),
(13, 9, 3, 218, 115, 154, 244),
(14, 38, 1, 210, 34, 51, 91),
(14, 1, 2, 210, 34, 51, 91),
(14, 2, 3, 210, 34, 51, 91),
(15, 37, 1, 163, 49, 70, 121),
(15, 36, 2, 163, 49, 70, 121),
(15, 31, 3, 163, 49, 70, 121),
(16, 37, 1, 185, 44, 62, 102),
(16, 3, 2, 185, 44, 62, 102),
(16, 14, 3, 185, 44, 62, 102),
(17, 39, 1, 236, 118, 161, 260),
(17, 36, 2, 236, 118, 161, 260),
(17, 9, 3, 236, 118, 161, 260),
(18, 37, 1, 256, 62, 85, 138),
(18, 18, 2, 256, 62, 85, 138),
(18, 41, 3, 256, 62, 85, 138),
(19, 38, 1, 243, 48, 67, 110),
(19, 24, 2, 243, 48, 67, 110),
(19, 1, 3, 243, 48, 67, 110),
(20, 37, 1, 241, 68, 93, 150),
(20, 51, 2, 241, 68, 93, 150),
(20, 3, 3, 241, 68, 93, 150),
(21, 20, 1, 135, 37, 53, 88),
(21, 21, 2, 135, 37, 53, 88),
(21, 2, 3, 135, 37, 53, 88),
(22, 18, 1, 108, 53, 74, 120),
(22, 22, 2, 108, 53, 74, 120),
(22, 9, 3, 108, 53, 74, 120),
(23, 23, 1, 157, 31, 54, 112),
(23, 7, 2, 157, 31, 54, 112),
(24, 1, 1, 148, 27, 39, 67),
(24, 10, 2, 148, 27, 39, 67),
(24, 11, 3, 148, 27, 39, 67),
(25, 24, 1, 160, 42, 60, 98),
(25, 25, 2, 160, 42, 60, 98),
(25, 21, 3, 160, 42, 60, 98),
(26, 26, 1, 113, 42, 59, 97),
(26, 20, 2, 113, 42, 59, 97),
(26, 21, 3, 113, 42, 59, 97),
(27, 25, 1, 143, 23, 35, 60),
(27, 24, 2, 143, 23, 35, 60),
(27, 12, 3, 143, 23, 35, 60),
(28, 11, 1, 166, 22, 31, 54),
(28, 2, 2, 166, 22, 31, 54),
(28, 12, 3, 166, 22, 31, 54),
(29, 43, 1, 150, 94, 127, 201),
(29, 6, 2, 150, 94, 127, 201),
(29, 7, 3, 150, 94, 127, 201),
(30, 42, 1, 310, 54, 74, 121),
(30, 52, 2, 310, 54, 74, 121),
(30, 22, 3, 310, 54, 74, 121),
(31, 30, 1, 110, 30, 47, 84),
(31, 31, 2, 110, 30, 47, 84),
(31, 26, 3, 110, 30, 47, 84),
(32, 28, 1, 115, 70, 96, 154),
(32, 29, 2, 115, 70, 96, 154),
(32, 9, 3, 115, 70, 96, 154),
(33, 29, 1, 123, 26, 41, 76),
(33, 30, 2, 123, 26, 41, 76),
(33, 27, 3, 123, 26, 41, 76),
(34, 32, 1, 115, 72, 99, 158),
(34, 31, 2, 115, 72, 99, 158),
(34, 26, 3, 115, 72, 99, 158),
(35, 24, 1, 126, 24, 38, 71),
(35, 31, 2, 126, 24, 38, 71),
(35, 2, 3, 126, 24, 38, 71),
(36, 18, 1, 73, 26, 41, 76),
(36, 26, 2, 73, 26, 41, 76),
(36, 29, 3, 73, 26, 41, 76),
(37, 28, 1, 115, 70, 96, 154),
(37, 29, 2, 115, 70, 96, 154),
(37, 9, 3, 115, 70, 96, 154),
(38, 33, 1, 103, 20, 33, 63),
(38, 31, 2, 103, 20, 33, 63),
(38, 7, 3, 103, 20, 33, 63),
(39, 34, 1, 110, 35, 50, 84),
(39, 35, 2, 110, 35, 50, 84),
(39, 31, 3, 110, 35, 50, 84),
(40, 26, 1, 110, 42, 59, 97),
(40, 20, 2, 110, 42, 59, 97),
(40, 21, 3, 110, 42, 59, 97),
(41, 2, 1, 197, 40, 66, 130),
(41, 19, 2, 197, 40, 66, 130),
(42, 45, 1, 193, 31, 47, 86),
(42, 46, 2, 193, 31, 47, 86),
(42, 60, 3, 193, 31, 47, 86),
(43, 49, 1, 162, 64, 93, 163),
(43, 20, 2, 162, 64, 93, 163),
(44, 48, 1, 310, 64, 111, 228),
(45, 45, 1, 171, 26, 41, 76),
(45, 24, 2, 171, 26, 41, 76),
(45, 19, 3, 171, 26, 41, 76),
(46, 42, 1, 263, 36, 54, 96),
(46, 52, 2, 263, 36, 54, 96),
(46, 20, 3, 263, 36, 54, 96),
(47, 42, 1, 216, 40, 56, 93),
(47, 5, 2, 216, 40, 56, 93),
(47, 4, 3, 216, 40, 56, 93),
(48, 47, 1, 126, 30, 46, 84),
(48, 18, 2, 126, 30, 46, 84),
(48, 1, 3, 126, 30, 46, 84),
(49, 50, 1, 221, 148, 198, 310),
(49, 46, 2, 221, 148, 198, 310),
(49, 60, 3, 221, 148, 198, 310),
(50, 45, 1, 235, 25, 46, 100),
(50, 48, 2, 235, 25, 46, 100),
(51, 61, 1, 190, 48, 68, 110),
(51, 62, 2, 190, 48, 68, 110),
(51, 20, 3, 190, 48, 68, 110),
(52, 3, 1, 140, 28, 39, 67),
(52, 20, 2, 140, 28, 39, 67),
(52, 1, 3, 140, 28, 39, 67),
(53, 20, 1, 175, 28, 41, 70),
(53, 1, 2, 175, 28, 41, 70),
(53, 2, 3, 175, 28, 41, 70),
(54, 1, 1, 181, 23, 37, 70),
(54, 2, 2, 181, 23, 37, 70),
(54, 7, 3, 181, 23, 37, 70),
(55, 31, 1, 100, 30, 46, 83),
(55, 26, 2, 100, 30, 46, 83),
(55, 20, 3, 100, 30, 46, 83),
(56, 20, 1, 143, 42, 59, 97),
(56, 21, 2, 143, 42, 59, 97),
(56, 18, 3, 143, 42, 59, 97),
(57, 61, 1, 185, 58, 80, 129),
(57, 62, 2, 185, 58, 80, 129),
(57, 20, 3, 185, 58, 80, 129),
(58, 6, 1, 180, 56, 87, 162),
(58, 19, 2, 180, 56, 87, 162),
(59, 20, 1, 135, 43, 65, 113),
(59, 19, 2, 135, 43, 65, 113),
(59, 18, 3, 135, 43, 65, 113),
(60, 61, 1, 200, 70, 96, 154),
(60, 62, 2, 200, 70, 96, 154),
(60, 19, 3, 200, 70, 96, 154),
(61, 21, 1, 225, 44, 71, 138),
(61, 57, 2, 225, 44, 71, 138),
(62, 1, 1, 155, 24, 38, 71),
(62, 3, 2, 155, 24, 38, 71),
(62, 58, 3, 155, 24, 38, 71),
(63, 56, 1, 165, 31, 47, 86),
(63, 54, 2, 165, 31, 47, 86),
(63, 22, 3, 165, 31, 47, 86),
(64, 2, 1, 158, 22, 36, 68),
(64, 15, 2, 158, 22, 36, 68),
(64, 23, 3, 158, 22, 36, 68),
(65, 1, 1, 155, 34, 52, 92),
(65, 21, 2, 155, 34, 52, 92),
(65, 2, 3, 155, 34, 52, 92),
(66, 55, 1, 158, 38, 57, 100),
(66, 58, 2, 158, 38, 57, 100),
(66, 22, 3, 158, 38, 57, 100),
(67, 3, 1, 150, 24, 38, 71),
(67, 2, 2, 150, 24, 38, 71),
(67, 23, 3, 150, 24, 38, 71),
(68, 15, 1, 130, 18, 30, 59),
(68, 1, 2, 130, 18, 30, 59),
(68, 56, 3, 130, 18, 30, 59),
(69, 53, 1, 163, 29, 45, 82),
(69, 13, 2, 163, 29, 45, 82),
(69, 58, 3, 163, 29, 45, 82),
(70, 53, 1, 173, 33, 50, 90),
(70, 13, 2, 173, 33, 50, 90),
(70, 58, 3, 173, 33, 50, 90),
(71, 13, 1, 218, 24, 38, 71),
(71, 1, 2, 218, 24, 38, 71),
(71, 12, 3, 218, 24, 38, 71),
(72, 52, 1, 238, 24, 38, 71),
(72, 56, 2, 238, 24, 38, 71),
(72, 58, 3, 238, 24, 38, 71),
(73, 54, 1, 196, 24, 38, 71),
(73, 1, 2, 196, 24, 38, 71),
(73, 2, 3, 196, 24, 38, 71),
(74, 54, 1, 146, 24, 39, 72),
(74, 58, 2, 146, 24, 39, 72),
(74, 12, 3, 146, 24, 39, 72),
(75, 52, 1, 235, 28, 43, 79),
(75, 58, 2, 235, 28, 43, 79),
(75, 1, 3, 235, 28, 43, 79),
(76, 53, 1, 180, 30, 46, 83),
(76, 3, 2, 180, 30, 46, 83),
(76, 1, 3, 180, 30, 46, 83),
(77, 55, 1, 225, 28, 43, 79),
(77, 58, 2, 225, 28, 43, 79),
(77, 1, 3, 225, 28, 43, 79),
(78, 57, 1, 191, 18, 30, 59),
(78, 58, 2, 191, 18, 30, 59),
(78, 12, 3, 191, 18, 30, 59),
(79, 13, 1, 186, 20, 34, 64),
(79, 1, 2, 186, 20, 34, 64),
(79, 56, 3, 186, 20, 34, 64),
(80, 13, 1, 230, 24, 39, 72),
(80, 1, 2, 230, 24, 39, 72),
(80, 3, 3, 230, 24, 39, 72);

-- ── 5.19 Merchants (463 rows) ──
INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(1, "MERCH_TERMINAL21_PIER21_01", "ร้านค้าในห้าง TERMINAL21_PIER21 #1", "Mall Shop TERMINAL21_PIER21 #1", "XL", 1, "0812234567", 2, 12.9297, 100.8791, "mall", 1, NOW(), NOW()),
(2, "MERCH_TERMINAL21_PIER21_02", "ร้านค้าในห้าง TERMINAL21_PIER21 #2", "Mall Shop TERMINAL21_PIER21 #2", "E", 1, "0823469134", 2, 12.9304, 100.8804, "mall", 1, NOW(), NOW()),
(3, "MERCH_TERMINAL21_PIER21_03", "ร้านค้าในห้าง TERMINAL21_PIER21 #3", "Mall Shop TERMINAL21_PIER21 #3", "M", 1, "0834703701", 2, 12.9311, 100.8817, "mall", 1, NOW(), NOW()),
(4, "MERCH_TERMINAL21_PIER21_04", "ร้านค้าในห้าง TERMINAL21_PIER21 #4", "Mall Shop TERMINAL21_PIER21 #4", "S", 1, "0845938268", 2, 12.9318, 100.883, "mall", 1, NOW(), NOW()),
(5, "MERCH_TERMINAL21_PIER21_05", "ร้านค้าในห้าง TERMINAL21_PIER21 #5", "Mall Shop TERMINAL21_PIER21 #5", "S", 1, "0857172835", 2, 12.9325, 100.8843, "mall", 1, NOW(), NOW()),
(6, "MERCH_TERMINAL21_PIER21_06", "ร้านค้าในห้าง TERMINAL21_PIER21 #6", "Mall Shop TERMINAL21_PIER21 #6", "S", 1, "0868407402", 2, 12.9332, 100.8856, "mall", 1, NOW(), NOW()),
(7, "MERCH_THEPPRASIT_NIGHT_MARKET_01", "ร้านค้า THEPPRASIT_NIGHT_MARKET #1", "Shop THEPPRASIT_NIGHT_MARKET #1", "XL", 1, "0879641969", 2, 12.9009, 100.8891, "market", 1, NOW(), NOW()),
(8, "MERCH_THEPPRASIT_NIGHT_MARKET_02", "ร้านค้า THEPPRASIT_NIGHT_MARKET #2", "Shop THEPPRASIT_NIGHT_MARKET #2", "E", 1, "0881876536", 2, 12.9016, 100.8803, "market", 1, NOW(), NOW()),
(9, "MERCH_THEPPRASIT_NIGHT_MARKET_03", "ร้านค้า THEPPRASIT_NIGHT_MARKET #3", "Shop THEPPRASIT_NIGHT_MARKET #3", "M", 1, "0893111103", 2, 12.9023, 100.8816, "market", 1, NOW(), NOW()),
(10, "MERCH_THEPPRASIT_NIGHT_MARKET_04", "ร้านค้า THEPPRASIT_NIGHT_MARKET #4", "Shop THEPPRASIT_NIGHT_MARKET #4", "S", 1, "0904345670", 2, 12.903, 100.8829, "market", 1, NOW(), NOW()),
(11, "MERCH_THEPPRASIT_NIGHT_MARKET_05", "ร้านค้า THEPPRASIT_NIGHT_MARKET #5", "Shop THEPPRASIT_NIGHT_MARKET #5", "S", 1, "0915580237", 2, 12.9037, 100.8842, "market", 1, NOW(), NOW()),
(12, "MERCH_THEPPRASIT_NIGHT_MARKET_06", "ร้านค้า THEPPRASIT_NIGHT_MARKET #6", "Shop THEPPRASIT_NIGHT_MARKET #6", "S", 1, "0926814804", 2, 12.9044, 100.8855, "market", 1, NOW(), NOW()),
(13, "MERCH_THEPPRASIT_NIGHT_MARKET_07", "ร้านค้า THEPPRASIT_NIGHT_MARKET #7", "Shop THEPPRASIT_NIGHT_MARKET #7", "S", 1, "0938049371", 2, 12.9051, 100.8868, "market", 1, NOW(), NOW()),
(14, "MERCH_CENTRAL_FESTIVAL_01", "ร้านค้าในห้าง CENTRAL_FESTIVAL #1", "Mall Shop CENTRAL_FESTIVAL #1", "XL", 1, "0949283938", 2, 12.9498, 100.8881, "mall", 1, NOW(), NOW()),
(15, "MERCH_CENTRAL_FESTIVAL_02", "ร้านค้าในห้าง CENTRAL_FESTIVAL #2", "Mall Shop CENTRAL_FESTIVAL #2", "E", 1, "0951518505", 2, 12.9404, 100.8894, "mall", 1, NOW(), NOW()),
(16, "MERCH_CENTRAL_FESTIVAL_03", "ร้านค้าในห้าง CENTRAL_FESTIVAL #3", "Mall Shop CENTRAL_FESTIVAL #3", "M", 1, "0962753072", 2, 12.9411, 100.8806, "mall", 1, NOW(), NOW()),
(17, "MERCH_CENTRAL_FESTIVAL_04", "ร้านค้าในห้าง CENTRAL_FESTIVAL #4", "Mall Shop CENTRAL_FESTIVAL #4", "S", 1, "0973987639", 2, 12.9418, 100.8819, "mall", 1, NOW(), NOW()),
(18, "MERCH_CENTRAL_FESTIVAL_05", "ร้านค้าในห้าง CENTRAL_FESTIVAL #5", "Mall Shop CENTRAL_FESTIVAL #5", "S", 1, "0985222206", 2, 12.9425, 100.8832, "mall", 1, NOW(), NOW()),
(19, "MERCH_CENTRAL_FESTIVAL_06", "ร้านค้าในห้าง CENTRAL_FESTIVAL #6", "Mall Shop CENTRAL_FESTIVAL #6", "S", 1, "0996456773", 2, 12.9432, 100.8845, "mall", 1, NOW(), NOW()),
(20, "MERCH_CENTRAL_FESTIVAL_07", "ร้านค้าในห้าง CENTRAL_FESTIVAL #7", "Mall Shop CENTRAL_FESTIVAL #7", "S", 1, "0807691340", 2, 12.9439, 100.8858, "mall", 1, NOW(), NOW()),
(21, "MERCH_CENTRAL_FESTIVAL_08", "ร้านค้าในห้าง CENTRAL_FESTIVAL #8", "Mall Shop CENTRAL_FESTIVAL #8", "S", 1, "0818925907", 2, 12.9446, 100.8871, "mall", 1, NOW(), NOW()),
(22, "MERCH_LAN_PHO_NAKLUA_01", "ร้านค้า LAN_PHO_NAKLUA #1", "Shop LAN_PHO_NAKLUA #1", "XL", 1, "0821160474", 2, 12.9653, 100.8934, "market", 1, NOW(), NOW()),
(23, "MERCH_LAN_PHO_NAKLUA_02", "ร้านค้า LAN_PHO_NAKLUA #2", "Shop LAN_PHO_NAKLUA #2", "E", 1, "0832395041", 2, 12.966, 100.8947, "market", 1, NOW(), NOW()),
(24, "MERCH_LAN_PHO_NAKLUA_03", "ร้านค้า LAN_PHO_NAKLUA #3", "Shop LAN_PHO_NAKLUA #3", "M", 1, "0843629608", 2, 12.9667, 100.8859, "market", 1, NOW(), NOW()),
(25, "MERCH_LAN_PHO_NAKLUA_04", "ร้านค้า LAN_PHO_NAKLUA #4", "Shop LAN_PHO_NAKLUA #4", "S", 1, "0854864175", 2, 12.9674, 100.8872, "market", 1, NOW(), NOW()),
(26, "MERCH_LAN_PHO_NAKLUA_05", "ร้านค้า LAN_PHO_NAKLUA #5", "Shop LAN_PHO_NAKLUA #5", "S", 1, "0866098742", 2, 12.9681, 100.8885, "market", 1, NOW(), NOW()),
(27, "MERCH_LAN_PHO_NAKLUA_06", "ร้านค้า LAN_PHO_NAKLUA #6", "Shop LAN_PHO_NAKLUA #6", "S", 1, "0877333309", 2, 12.9688, 100.8898, "market", 1, NOW(), NOW()),
(28, "MERCH_LAN_PHO_NAKLUA_07", "ร้านค้า LAN_PHO_NAKLUA #7", "Shop LAN_PHO_NAKLUA #7", "S", 1, "0888567876", 2, 12.9695, 100.8911, "market", 1, NOW(), NOW()),
(29, "MERCH_LAN_PHO_NAKLUA_08", "ร้านค้า LAN_PHO_NAKLUA #8", "Shop LAN_PHO_NAKLUA #8", "S", 1, "0899802443", 2, 12.9601, 100.8924, "market", 1, NOW(), NOW()),
(30, "MERCH_LAN_PHO_NAKLUA_09", "ร้านค้า LAN_PHO_NAKLUA #9", "Shop LAN_PHO_NAKLUA #9", "S", 1, "0902037010", 2, 12.9608, 100.8937, "market", 1, NOW(), NOW()),
(31, "MERCH_PUPEN_SEAFOOD_01", "ร้านอาหาร PUPEN_SEAFOOD #1", "Restaurant PUPEN_SEAFOOD #1", "XL", 1, "0913271577", 2, 12.8825, 100.877, "restaurant", 1, NOW(), NOW()),
(32, "MERCH_PUPEN_SEAFOOD_02", "ร้านอาหาร PUPEN_SEAFOOD #2", "Restaurant PUPEN_SEAFOOD #2", "E", 1, "0924506144", 2, 12.8832, 100.8682, "restaurant", 1, NOW(), NOW()),
(33, "MERCH_PUPEN_SEAFOOD_03", "ร้านอาหาร PUPEN_SEAFOOD #3", "Restaurant PUPEN_SEAFOOD #3", "M", 1, "0935740711", 2, 12.8839, 100.8695, "restaurant", 1, NOW(), NOW()),
(34, "MERCH_PUPEN_SEAFOOD_04", "ร้านอาหาร PUPEN_SEAFOOD #4", "Restaurant PUPEN_SEAFOOD #4", "S", 1, "0946975278", 2, 12.8846, 100.8708, "restaurant", 1, NOW(), NOW()),
(35, "MERCH_PUPEN_SEAFOOD_05", "ร้านอาหาร PUPEN_SEAFOOD #5", "Restaurant PUPEN_SEAFOOD #5", "S", 1, "0958209845", 2, 12.8853, 100.8721, "restaurant", 1, NOW(), NOW()),
(36, "MERCH_PUPEN_SEAFOOD_06", "ร้านอาหาร PUPEN_SEAFOOD #6", "Restaurant PUPEN_SEAFOOD #6", "S", 1, "0969444412", 2, 12.886, 100.8734, "restaurant", 1, NOW(), NOW()),
(37, "MERCH_PUPEN_SEAFOOD_07", "ร้านอาหาร PUPEN_SEAFOOD #7", "Restaurant PUPEN_SEAFOOD #7", "S", 1, "0971678979", 2, 12.8867, 100.8747, "restaurant", 1, NOW(), NOW()),
(38, "MERCH_PUPEN_SEAFOOD_08", "ร้านอาหาร PUPEN_SEAFOOD #8", "Restaurant PUPEN_SEAFOOD #8", "S", 1, "0982913546", 2, 12.8874, 100.876, "restaurant", 1, NOW(), NOW()),
(39, "MERCH_PUPEN_SEAFOOD_09", "ร้านอาหาร PUPEN_SEAFOOD #9", "Restaurant PUPEN_SEAFOOD #9", "S", 1, "0994148113", 2, 12.8881, 100.8672, "restaurant", 1, NOW(), NOW()),
(40, "MERCH_PUPEN_SEAFOOD_10", "ร้านอาหาร PUPEN_SEAFOOD #10", "Restaurant PUPEN_SEAFOOD #10", "S", 1, "0805382680", 2, 12.8888, 100.8685, "restaurant", 1, NOW(), NOW()),
(41, "MERCH_SKY_GALLERY_01", "ร้านอาหาร SKY_GALLERY #1", "Restaurant SKY_GALLERY #1", "XL", 1, "0816617247", 2, 12.9235, 100.8578, "restaurant", 1, NOW(), NOW()),
(42, "MERCH_SKY_GALLERY_02", "ร้านอาหาร SKY_GALLERY #2", "Restaurant SKY_GALLERY #2", "E", 1, "0827851814", 2, 12.9242, 100.8591, "restaurant", 1, NOW(), NOW()),
(43, "MERCH_SKY_GALLERY_03", "ร้านอาหาร SKY_GALLERY #3", "Restaurant SKY_GALLERY #3", "M", 1, "0839086381", 2, 12.9249, 100.8604, "restaurant", 1, NOW(), NOW()),
(44, "MERCH_SKY_GALLERY_04", "ร้านอาหาร SKY_GALLERY #4", "Restaurant SKY_GALLERY #4", "S", 1, "0841320948", 2, 12.9155, 100.8617, "restaurant", 1, NOW(), NOW()),
(45, "MERCH_SKY_GALLERY_05", "ร้านอาหาร SKY_GALLERY #5", "Restaurant SKY_GALLERY #5", "S", 1, "0852555515", 2, 12.9162, 100.863, "restaurant", 1, NOW(), NOW()),
(46, "MERCH_CHOCOLATE_FACTORY_01", "ร้านอาหาร CHOCOLATE_FACTORY #1", "Restaurant CHOCOLATE_FACTORY #1", "XL", 1, "0863790082", 2, 12.8889, 100.8753, "restaurant", 1, NOW(), NOW()),
(47, "MERCH_CHOCOLATE_FACTORY_02", "ร้านอาหาร CHOCOLATE_FACTORY #2", "Restaurant CHOCOLATE_FACTORY #2", "E", 1, "0875024649", 2, 12.8896, 100.8665, "restaurant", 1, NOW(), NOW()),
(48, "MERCH_CHOCOLATE_FACTORY_03", "ร้านอาหาร CHOCOLATE_FACTORY #3", "Restaurant CHOCOLATE_FACTORY #3", "M", 1, "0886259216", 2, 12.8903, 100.8678, "restaurant", 1, NOW(), NOW()),
(49, "MERCH_CHOCOLATE_FACTORY_04", "ร้านอาหาร CHOCOLATE_FACTORY #4", "Restaurant CHOCOLATE_FACTORY #4", "S", 1, "0897493783", 2, 12.891, 100.8691, "restaurant", 1, NOW(), NOW()),
(50, "MERCH_CHOCOLATE_FACTORY_05", "ร้านอาหาร CHOCOLATE_FACTORY #5", "Restaurant CHOCOLATE_FACTORY #5", "S", 1, "0908728350", 2, 12.8917, 100.8704, "restaurant", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(51, "MERCH_CHOCOLATE_FACTORY_06", "ร้านอาหาร CHOCOLATE_FACTORY #6", "Restaurant CHOCOLATE_FACTORY #6", "S", 1, "0919962917", 2, 12.8924, 100.8717, "restaurant", 1, NOW(), NOW()),
(52, "MERCH_GLASS_HOUSE_01", "ร้านอาหาร GLASS_HOUSE #1", "Restaurant GLASS_HOUSE #1", "XL", 1, "0922197484", 2, 12.8861, 100.87, "restaurant", 1, NOW(), NOW()),
(53, "MERCH_GLASS_HOUSE_02", "ร้านอาหาร GLASS_HOUSE #2", "Restaurant GLASS_HOUSE #2", "E", 1, "0933432051", 2, 12.8868, 100.8713, "restaurant", 1, NOW(), NOW()),
(54, "MERCH_GLASS_HOUSE_03", "ร้านอาหาร GLASS_HOUSE #3", "Restaurant GLASS_HOUSE #3", "M", 1, "0944666618", 2, 12.8875, 100.8726, "restaurant", 1, NOW(), NOW()),
(55, "MERCH_GLASS_HOUSE_04", "ร้านอาหาร GLASS_HOUSE #4", "Restaurant GLASS_HOUSE #4", "S", 1, "0955901185", 2, 12.8882, 100.8638, "restaurant", 1, NOW(), NOW()),
(56, "MERCH_GLASS_HOUSE_05", "ร้านอาหาร GLASS_HOUSE #5", "Restaurant GLASS_HOUSE #5", "S", 1, "0967135752", 2, 12.8889, 100.8651, "restaurant", 1, NOW(), NOW()),
(57, "MERCH_GLASS_HOUSE_06", "ร้านอาหาร GLASS_HOUSE #6", "Restaurant GLASS_HOUSE #6", "S", 1, "0978370319", 2, 12.8896, 100.8664, "restaurant", 1, NOW(), NOW()),
(58, "MERCH_GLASS_HOUSE_07", "ร้านอาหาร GLASS_HOUSE #7", "Restaurant GLASS_HOUSE #7", "S", 1, "0989604886", 2, 12.8802, 100.8677, "restaurant", 1, NOW(), NOW()),
(59, "MERCH_HORIZON_ROOFTOP_01", "ร้านอาหาร HORIZON_ROOFTOP #1", "Restaurant HORIZON_ROOFTOP #1", "XL", 1, "0991839453", 2, 12.9404, 100.8852, "restaurant", 1, NOW(), NOW()),
(60, "MERCH_HORIZON_ROOFTOP_02", "ร้านอาหาร HORIZON_ROOFTOP #2", "Restaurant HORIZON_ROOFTOP #2", "E", 1, "0803074020", 2, 12.9411, 100.8865, "restaurant", 1, NOW(), NOW()),
(61, "MERCH_HORIZON_ROOFTOP_03", "ร้านอาหาร HORIZON_ROOFTOP #3", "Restaurant HORIZON_ROOFTOP #3", "M", 1, "0814308587", 2, 12.9418, 100.8878, "restaurant", 1, NOW(), NOW()),
(62, "MERCH_HORIZON_ROOFTOP_04", "ร้านอาหาร HORIZON_ROOFTOP #4", "Restaurant HORIZON_ROOFTOP #4", "S", 1, "0825543154", 2, 12.9425, 100.8891, "restaurant", 1, NOW(), NOW()),
(63, "MERCH_HORIZON_ROOFTOP_05", "ร้านอาหาร HORIZON_ROOFTOP #5", "Restaurant HORIZON_ROOFTOP #5", "S", 1, "0836777721", 2, 12.9432, 100.8803, "restaurant", 1, NOW(), NOW()),
(64, "MERCH_HORIZON_ROOFTOP_06", "ร้านอาหาร HORIZON_ROOFTOP #6", "Restaurant HORIZON_ROOFTOP #6", "S", 1, "0848012288", 2, 12.9439, 100.8816, "restaurant", 1, NOW(), NOW()),
(65, "MERCH_HORIZON_ROOFTOP_07", "ร้านอาหาร HORIZON_ROOFTOP #7", "Restaurant HORIZON_ROOFTOP #7", "S", 1, "0859246855", 2, 12.9446, 100.8829, "restaurant", 1, NOW(), NOW()),
(66, "MERCH_HORIZON_ROOFTOP_08", "ร้านอาหาร HORIZON_ROOFTOP #8", "Restaurant HORIZON_ROOFTOP #8", "S", 1, "0861481422", 2, 12.9453, 100.8842, "restaurant", 1, NOW(), NOW()),
(67, "MERCH_TREE_TOWN_01", "ร้านอาหาร TREE_TOWN #1", "Restaurant TREE_TOWN #1", "XL", 1, "0872715989", 2, 12.9315, 100.8793, "restaurant", 1, NOW(), NOW()),
(68, "MERCH_TREE_TOWN_02", "ร้านอาหาร TREE_TOWN #2", "Restaurant TREE_TOWN #2", "E", 1, "0883950556", 2, 12.9322, 100.8806, "restaurant", 1, NOW(), NOW()),
(69, "MERCH_TREE_TOWN_03", "ร้านอาหาร TREE_TOWN #3", "Restaurant TREE_TOWN #3", "M", 1, "0895185123", 2, 12.9329, 100.8819, "restaurant", 1, NOW(), NOW()),
(70, "MERCH_TREE_TOWN_04", "ร้านอาหาร TREE_TOWN #4", "Restaurant TREE_TOWN #4", "S", 1, "0906419690", 2, 12.9336, 100.8731, "restaurant", 1, NOW(), NOW()),
(71, "MERCH_TREE_TOWN_05", "ร้านอาหาร TREE_TOWN #5", "Restaurant TREE_TOWN #5", "S", 1, "0917654257", 2, 12.9343, 100.8744, "restaurant", 1, NOW(), NOW()),
(72, "MERCH_TREE_TOWN_06", "ร้านอาหาร TREE_TOWN #6", "Restaurant TREE_TOWN #6", "S", 1, "0928888824", 2, 12.935, 100.8757, "restaurant", 1, NOW(), NOW()),
(73, "MERCH_TREE_TOWN_07", "ร้านอาหาร TREE_TOWN #7", "Restaurant TREE_TOWN #7", "S", 1, "0931123391", 2, 12.9256, 100.877, "restaurant", 1, NOW(), NOW()),
(74, "MERCH_TREE_TOWN_08", "ร้านอาหาร TREE_TOWN #8", "Restaurant TREE_TOWN #8", "S", 1, "0942357958", 2, 12.9263, 100.8783, "restaurant", 1, NOW(), NOW()),
(75, "MERCH_TREE_TOWN_09", "ร้านอาหาร TREE_TOWN #9", "Restaurant TREE_TOWN #9", "S", 1, "0953592525", 2, 12.927, 100.8796, "restaurant", 1, NOW(), NOW()),
(76, "MERCH_PATTAYA_NIGHT_BAZAAR_01", "ร้านค้า PATTAYA_NIGHT_BAZAAR #1", "Shop PATTAYA_NIGHT_BAZAAR #1", "XL", 1, "0964827092", 2, 12.9257, 100.8839, "market", 1, NOW(), NOW()),
(77, "MERCH_PATTAYA_NIGHT_BAZAAR_02", "ร้านค้า PATTAYA_NIGHT_BAZAAR #2", "Shop PATTAYA_NIGHT_BAZAAR #2", "E", 1, "0976061659", 2, 12.9264, 100.8852, "market", 1, NOW(), NOW()),
(78, "MERCH_PATTAYA_NIGHT_BAZAAR_03", "ร้านค้า PATTAYA_NIGHT_BAZAAR #3", "Shop PATTAYA_NIGHT_BAZAAR #3", "M", 1, "0987296226", 2, 12.9271, 100.8764, "market", 1, NOW(), NOW()),
(79, "MERCH_PATTAYA_NIGHT_BAZAAR_04", "ร้านค้า PATTAYA_NIGHT_BAZAAR #4", "Shop PATTAYA_NIGHT_BAZAAR #4", "S", 1, "0998530793", 2, 12.9278, 100.8777, "market", 1, NOW(), NOW()),
(80, "MERCH_PATTAYA_NIGHT_BAZAAR_05", "ร้านค้า PATTAYA_NIGHT_BAZAAR #5", "Shop PATTAYA_NIGHT_BAZAAR #5", "S", 1, "0809765360", 2, 12.9285, 100.879, "market", 1, NOW(), NOW()),
(81, "MERCH_PATTAYA_NIGHT_BAZAAR_06", "ร้านค้า PATTAYA_NIGHT_BAZAAR #6", "Shop PATTAYA_NIGHT_BAZAAR #6", "S", 1, "0811999927", 2, 12.9292, 100.8803, "market", 1, NOW(), NOW()),
(82, "MERCH_PATTAYA_NIGHT_BAZAAR_07", "ร้านค้า PATTAYA_NIGHT_BAZAAR #7", "Shop PATTAYA_NIGHT_BAZAAR #7", "S", 1, "0823234494", 2, 12.9299, 100.8816, "market", 1, NOW(), NOW()),
(83, "MERCH_PATTAYA_NIGHT_BAZAAR_08", "ร้านค้า PATTAYA_NIGHT_BAZAAR #8", "Shop PATTAYA_NIGHT_BAZAAR #8", "S", 1, "0834469061", 2, 12.9306, 100.8829, "market", 1, NOW(), NOW()),
(84, "MERCH_PATTAYA_NIGHT_BAZAAR_09", "ร้านค้า PATTAYA_NIGHT_BAZAAR #9", "Shop PATTAYA_NIGHT_BAZAAR #9", "S", 1, "0845703628", 2, 12.9313, 100.8842, "market", 1, NOW(), NOW()),
(85, "MERCH_PATTAYA_NIGHT_BAZAAR_10", "ร้านค้า PATTAYA_NIGHT_BAZAAR #10", "Shop PATTAYA_NIGHT_BAZAAR #10", "S", 1, "0856938195", 2, 12.932, 100.8855, "market", 1, NOW(), NOW()),
(86, "MERCH_JOMTIEN_NIGHT_MARKET_01", "ร้านค้า JOMTIEN_NIGHT_MARKET #1", "Shop JOMTIEN_NIGHT_MARKET #1", "XL", 1, "0868172762", 2, 12.8847, 100.8657, "market", 1, NOW(), NOW()),
(87, "MERCH_JOMTIEN_NIGHT_MARKET_02", "ร้านค้า JOMTIEN_NIGHT_MARKET #2", "Shop JOMTIEN_NIGHT_MARKET #2", "E", 1, "0879407329", 2, 12.8753, 100.867, "market", 1, NOW(), NOW()),
(88, "MERCH_JOMTIEN_NIGHT_MARKET_03", "ร้านค้า JOMTIEN_NIGHT_MARKET #3", "Shop JOMTIEN_NIGHT_MARKET #3", "M", 1, "0881641896", 2, 12.876, 100.8683, "market", 1, NOW(), NOW()),
(89, "MERCH_JOMTIEN_NIGHT_MARKET_04", "ร้านค้า JOMTIEN_NIGHT_MARKET #4", "Shop JOMTIEN_NIGHT_MARKET #4", "S", 1, "0892876463", 2, 12.8767, 100.8696, "market", 1, NOW(), NOW()),
(90, "MERCH_JOMTIEN_NIGHT_MARKET_05", "ร้านค้า JOMTIEN_NIGHT_MARKET #5", "Shop JOMTIEN_NIGHT_MARKET #5", "S", 1, "0904111030", 2, 12.8774, 100.8709, "market", 1, NOW(), NOW()),
(91, "MERCH_FLOATING_MARKET_01", "ท่องเที่ยว FLOATING_MARKET #1", "Attraction FLOATING_MARKET #1", "XL", 1, "0915345597", 2, 12.8991, 100.8562, "attraction", 1, NOW(), NOW()),
(92, "MERCH_FLOATING_MARKET_02", "ท่องเที่ยว FLOATING_MARKET #2", "Attraction FLOATING_MARKET #2", "E", 1, "0926580164", 2, 12.8998, 100.8575, "attraction", 1, NOW(), NOW()),
(93, "MERCH_FLOATING_MARKET_03", "ท่องเที่ยว FLOATING_MARKET #3", "Attraction FLOATING_MARKET #3", "M", 1, "0937814731", 2, 12.9005, 100.8588, "attraction", 1, NOW(), NOW()),
(94, "MERCH_FLOATING_MARKET_04", "ท่องเที่ยว FLOATING_MARKET #4", "Attraction FLOATING_MARKET #4", "S", 1, "0949049298", 2, 12.9012, 100.85, "attraction", 1, NOW(), NOW()),
(95, "MERCH_FLOATING_MARKET_05", "ท่องเที่ยว FLOATING_MARKET #5", "Attraction FLOATING_MARKET #5", "S", 1, "0951283865", 2, 12.9019, 100.8513, "attraction", 1, NOW(), NOW()),
(96, "MERCH_FLOATING_MARKET_06", "ท่องเที่ยว FLOATING_MARKET #6", "Attraction FLOATING_MARKET #6", "S", 1, "0962518432", 2, 12.9026, 100.8526, "attraction", 1, NOW(), NOW()),
(97, "MERCH_ROYAL_GARDEN_01", "ร้านค้าในห้าง ROYAL_GARDEN #1", "Mall Shop ROYAL_GARDEN #1", "XL", 1, "0973752999", 2, 12.9403, 100.8839, "mall", 1, NOW(), NOW()),
(98, "MERCH_ROYAL_GARDEN_02", "ร้านค้าในห้าง ROYAL_GARDEN #2", "Mall Shop ROYAL_GARDEN #2", "E", 1, "0984987566", 2, 12.941, 100.8852, "mall", 1, NOW(), NOW()),
(99, "MERCH_ROYAL_GARDEN_03", "ร้านค้าในห้าง ROYAL_GARDEN #3", "Mall Shop ROYAL_GARDEN #3", "M", 1, "0996222133", 2, 12.9417, 100.8865, "mall", 1, NOW(), NOW()),
(100, "MERCH_ROYAL_GARDEN_04", "ร้านค้าในห้าง ROYAL_GARDEN #4", "Mall Shop ROYAL_GARDEN #4", "S", 1, "0807456700", 2, 12.9424, 100.8878, "mall", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(101, "MERCH_ROYAL_GARDEN_05", "ร้านค้าในห้าง ROYAL_GARDEN #5", "Mall Shop ROYAL_GARDEN #5", "S", 1, "0818691267", 2, 12.933, 100.879, "mall", 1, NOW(), NOW()),
(102, "MERCH_ROYAL_GARDEN_06", "ร้านค้าในห้าง ROYAL_GARDEN #6", "Mall Shop ROYAL_GARDEN #6", "S", 1, "0829925834", 2, 12.9337, 100.8803, "mall", 1, NOW(), NOW()),
(103, "MERCH_ROYAL_GARDEN_07", "ร้านค้าในห้าง ROYAL_GARDEN #7", "Mall Shop ROYAL_GARDEN #7", "S", 1, "0832160401", 2, 12.9344, 100.8816, "mall", 1, NOW(), NOW()),
(104, "MERCH_CENTRAL_MARINA_01", "ร้านค้าในห้าง CENTRAL_MARINA #1", "Mall Shop CENTRAL_MARINA #1", "XL", 1, "0843394968", 2, 12.9471, 100.8819, "mall", 1, NOW(), NOW()),
(105, "MERCH_CENTRAL_MARINA_02", "ร้านค้าในห้าง CENTRAL_MARINA #2", "Mall Shop CENTRAL_MARINA #2", "E", 1, "0854629535", 2, 12.9478, 100.8832, "mall", 1, NOW(), NOW()),
(106, "MERCH_CENTRAL_MARINA_03", "ร้านค้าในห้าง CENTRAL_MARINA #3", "Mall Shop CENTRAL_MARINA #3", "M", 1, "0865864102", 2, 12.9485, 100.8845, "mall", 1, NOW(), NOW()),
(107, "MERCH_CENTRAL_MARINA_04", "ร้านค้าในห้าง CENTRAL_MARINA #4", "Mall Shop CENTRAL_MARINA #4", "S", 1, "0877098669", 2, 12.9492, 100.8858, "mall", 1, NOW(), NOW()),
(108, "MERCH_CENTRAL_MARINA_05", "ร้านค้าในห้าง CENTRAL_MARINA #5", "Mall Shop CENTRAL_MARINA #5", "S", 1, "0888333236", 2, 12.9499, 100.8871, "mall", 1, NOW(), NOW()),
(109, "MERCH_CENTRAL_MARINA_06", "ร้านค้าในห้าง CENTRAL_MARINA #6", "Mall Shop CENTRAL_MARINA #6", "S", 1, "0899567803", 2, 12.9506, 100.8783, "mall", 1, NOW(), NOW()),
(110, "MERCH_CENTRAL_MARINA_07", "ร้านค้าในห้าง CENTRAL_MARINA #7", "Mall Shop CENTRAL_MARINA #7", "S", 1, "0901802370", 2, 12.9513, 100.8796, "mall", 1, NOW(), NOW()),
(111, "MERCH_CENTRAL_MARINA_08", "ร้านค้าในห้าง CENTRAL_MARINA #8", "Mall Shop CENTRAL_MARINA #8", "S", 1, "0913036937", 2, 12.952, 100.8809, "mall", 1, NOW(), NOW()),
(112, "MERCH_LOCAL_BREAKFAST_01", "ร้านอาหาร LOCAL_BREAKFAST #1", "Restaurant LOCAL_BREAKFAST #1", "XL", 1, "0924271504", 2, 12.9377, 100.8782, "restaurant", 1, NOW(), NOW()),
(113, "MERCH_LOCAL_BREAKFAST_02", "ร้านอาหาร LOCAL_BREAKFAST #2", "Restaurant LOCAL_BREAKFAST #2", "E", 1, "0935506071", 2, 12.9384, 100.8795, "restaurant", 1, NOW(), NOW()),
(114, "MERCH_LOCAL_BREAKFAST_03", "ร้านอาหาร LOCAL_BREAKFAST #3", "Restaurant LOCAL_BREAKFAST #3", "M", 1, "0946740638", 2, 12.9391, 100.8808, "restaurant", 1, NOW(), NOW()),
(115, "MERCH_LOCAL_BREAKFAST_04", "ร้านอาหาร LOCAL_BREAKFAST #4", "Restaurant LOCAL_BREAKFAST #4", "S", 1, "0957975205", 2, 12.9398, 100.8821, "restaurant", 1, NOW(), NOW()),
(116, "MERCH_LOCAL_BREAKFAST_05", "ร้านอาหาร LOCAL_BREAKFAST #5", "Restaurant LOCAL_BREAKFAST #5", "S", 1, "0969209772", 2, 12.9304, 100.8834, "restaurant", 1, NOW(), NOW()),
(117, "MERCH_LOCAL_BREAKFAST_06", "ร้านอาหาร LOCAL_BREAKFAST #6", "Restaurant LOCAL_BREAKFAST #6", "S", 1, "0971444339", 2, 12.9311, 100.8746, "restaurant", 1, NOW(), NOW()),
(118, "MERCH_LOCAL_BREAKFAST_07", "ร้านอาหาร LOCAL_BREAKFAST #7", "Restaurant LOCAL_BREAKFAST #7", "S", 1, "0982678906", 2, 12.9318, 100.8759, "restaurant", 1, NOW(), NOW()),
(119, "MERCH_LOCAL_BREAKFAST_08", "ร้านอาหาร LOCAL_BREAKFAST #8", "Restaurant LOCAL_BREAKFAST #8", "S", 1, "0993913473", 2, 12.9325, 100.8772, "restaurant", 1, NOW(), NOW()),
(120, "MERCH_LOCAL_BREAKFAST_09", "ร้านอาหาร LOCAL_BREAKFAST #9", "Restaurant LOCAL_BREAKFAST #9", "S", 1, "0805148040", 2, 12.9332, 100.8785, "restaurant", 1, NOW(), NOW()),
(121, "MERCH_LOCAL_LUNCH_STREET_01", "ร้านอาหาร LOCAL_LUNCH_STREET #1", "Restaurant LOCAL_LUNCH_STREET #1", "XL", 1, "0816382607", 2, 12.9309, 100.8808, "restaurant", 1, NOW(), NOW()),
(122, "MERCH_LOCAL_LUNCH_STREET_02", "ร้านอาหาร LOCAL_LUNCH_STREET #2", "Restaurant LOCAL_LUNCH_STREET #2", "E", 1, "0827617174", 2, 12.9316, 100.8821, "restaurant", 1, NOW(), NOW()),
(123, "MERCH_LOCAL_LUNCH_STREET_03", "ร้านอาหาร LOCAL_LUNCH_STREET #3", "Restaurant LOCAL_LUNCH_STREET #3", "M", 1, "0838851741", 2, 12.9323, 100.8834, "restaurant", 1, NOW(), NOW()),
(124, "MERCH_LOCAL_LUNCH_STREET_04", "ร้านอาหาร LOCAL_LUNCH_STREET #4", "Restaurant LOCAL_LUNCH_STREET #4", "S", 1, "0841086308", 2, 12.933, 100.8847, "restaurant", 1, NOW(), NOW()),
(125, "MERCH_LOCAL_LUNCH_STREET_05", "ร้านอาหาร LOCAL_LUNCH_STREET #5", "Restaurant LOCAL_LUNCH_STREET #5", "S", 1, "0852320875", 2, 12.9337, 100.8759, "restaurant", 1, NOW(), NOW()),
(126, "MERCH_LOCAL_LUNCH_STREET_06", "ร้านอาหาร LOCAL_LUNCH_STREET #6", "Restaurant LOCAL_LUNCH_STREET #6", "S", 1, "0863555442", 2, 12.9344, 100.8772, "restaurant", 1, NOW(), NOW()),
(127, "MERCH_LOCAL_LUNCH_STREET_07", "ร้านอาหาร LOCAL_LUNCH_STREET #7", "Restaurant LOCAL_LUNCH_STREET #7", "S", 1, "0874790009", 2, 12.9351, 100.8785, "restaurant", 1, NOW(), NOW()),
(128, "MERCH_LOCAL_LUNCH_STREET_08", "ร้านอาหาร LOCAL_LUNCH_STREET #8", "Restaurant LOCAL_LUNCH_STREET #8", "S", 1, "0886024576", 2, 12.9358, 100.8798, "restaurant", 1, NOW(), NOW()),
(129, "MERCH_LOCAL_LUNCH_STREET_09", "ร้านอาหาร LOCAL_LUNCH_STREET #9", "Restaurant LOCAL_LUNCH_STREET #9", "S", 1, "0897259143", 2, 12.9365, 100.8811, "restaurant", 1, NOW(), NOW()),
(130, "MERCH_LOCAL_LUNCH_STREET_10", "ร้านอาหาร LOCAL_LUNCH_STREET #10", "Restaurant LOCAL_LUNCH_STREET #10", "S", 1, "0908493710", 2, 12.9271, 100.8824, "restaurant", 1, NOW(), NOW()),
(131, "MERCH_CAFE_STOP_01", "คาเฟ่ CAFE_STOP #1", "Cafe CAFE_STOP #1", "XL", 1, "0919728277", 2, 12.9318, 100.8867, "cafe", 1, NOW(), NOW()),
(132, "MERCH_CAFE_STOP_02", "คาเฟ่ CAFE_STOP #2", "Cafe CAFE_STOP #2", "E", 1, "0921962844", 2, 12.9325, 100.888, "cafe", 1, NOW(), NOW()),
(133, "MERCH_CAFE_STOP_03", "คาเฟ่ CAFE_STOP #3", "Cafe CAFE_STOP #3", "M", 1, "0933197411", 2, 12.9332, 100.8792, "cafe", 1, NOW(), NOW()),
(134, "MERCH_CAFE_STOP_04", "คาเฟ่ CAFE_STOP #4", "Cafe CAFE_STOP #4", "S", 1, "0944431978", 2, 12.9339, 100.8805, "cafe", 1, NOW(), NOW()),
(135, "MERCH_CAFE_STOP_05", "คาเฟ่ CAFE_STOP #5", "Cafe CAFE_STOP #5", "S", 1, "0955666545", 2, 12.9346, 100.8818, "cafe", 1, NOW(), NOW()),
(136, "MERCH_PARTNER_DINNER_01", "ร้านอาหาร PARTNER_DINNER #1", "Restaurant PARTNER_DINNER #1", "XL", 1, "0966901112", 2, 12.9303, 100.8811, "restaurant", 1, NOW(), NOW()),
(137, "MERCH_PARTNER_DINNER_02", "ร้านอาหาร PARTNER_DINNER #2", "Restaurant PARTNER_DINNER #2", "E", 1, "0978135679", 2, 12.931, 100.8824, "restaurant", 1, NOW(), NOW()),
(138, "MERCH_PARTNER_DINNER_03", "ร้านอาหาร PARTNER_DINNER #3", "Restaurant PARTNER_DINNER #3", "M", 1, "0989370246", 2, 12.9317, 100.8837, "restaurant", 1, NOW(), NOW()),
(139, "MERCH_PARTNER_DINNER_04", "ร้านอาหาร PARTNER_DINNER #4", "Restaurant PARTNER_DINNER #4", "S", 1, "0991604813", 2, 12.9324, 100.885, "restaurant", 1, NOW(), NOW()),
(140, "MERCH_PARTNER_DINNER_05", "ร้านอาหาร PARTNER_DINNER #5", "Restaurant PARTNER_DINNER #5", "S", 1, "0802839380", 2, 12.9331, 100.8762, "restaurant", 1, NOW(), NOW()),
(141, "MERCH_PARTNER_DINNER_06", "ร้านอาหาร PARTNER_DINNER #6", "Restaurant PARTNER_DINNER #6", "S", 1, "0814073947", 2, 12.9338, 100.8775, "restaurant", 1, NOW(), NOW()),
(142, "MERCH_PARTNER_MEAL_01", "ร้านอาหาร PARTNER_MEAL #1", "Restaurant PARTNER_MEAL #1", "XL", 1, "0825308514", 2, 12.9435, 100.8818, "restaurant", 1, NOW(), NOW()),
(143, "MERCH_PARTNER_MEAL_02", "ร้านอาหาร PARTNER_MEAL #2", "Restaurant PARTNER_MEAL #2", "E", 1, "0836543081", 2, 12.9442, 100.8831, "restaurant", 1, NOW(), NOW()),
(144, "MERCH_PARTNER_MEAL_03", "ร้านอาหาร PARTNER_MEAL #3", "Restaurant PARTNER_MEAL #3", "M", 1, "0847777648", 2, 12.9449, 100.8844, "restaurant", 1, NOW(), NOW()),
(145, "MERCH_PARTNER_MEAL_04", "ร้านอาหาร PARTNER_MEAL #4", "Restaurant PARTNER_MEAL #4", "S", 1, "0859012215", 2, 12.9355, 100.8857, "restaurant", 1, NOW(), NOW()),
(146, "MERCH_PARTNER_MEAL_05", "ร้านอาหาร PARTNER_MEAL #5", "Restaurant PARTNER_MEAL #5", "S", 1, "0861246782", 2, 12.9362, 100.887, "restaurant", 1, NOW(), NOW()),
(147, "MERCH_PARTNER_MEAL_06", "ร้านอาหาร PARTNER_MEAL #6", "Restaurant PARTNER_MEAL #6", "S", 1, "0872481349", 2, 12.9369, 100.8883, "restaurant", 1, NOW(), NOW()),
(148, "MERCH_PARTNER_MEAL_07", "ร้านอาหาร PARTNER_MEAL #7", "Restaurant PARTNER_MEAL #7", "S", 1, "0883715916", 2, 12.9376, 100.8795, "restaurant", 1, NOW(), NOW()),
(149, "MERCH_TIFFANY_SHOW_01", "โชว์ TIFFANY_SHOW #1", "Show TIFFANY_SHOW #1", "XL", 1, "0894950483", 2, 12.9513, 100.8868, "show", 1, NOW(), NOW()),
(150, "MERCH_TIFFANY_SHOW_02", "โชว์ TIFFANY_SHOW #2", "Show TIFFANY_SHOW #2", "E", 1, "0906185050", 2, 12.952, 100.8881, "show", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(151, "MERCH_TIFFANY_SHOW_03", "โชว์ TIFFANY_SHOW #3", "Show TIFFANY_SHOW #3", "M", 1, "0917419617", 2, 12.9527, 100.8894, "show", 1, NOW(), NOW()),
(152, "MERCH_TIFFANY_SHOW_04", "โชว์ TIFFANY_SHOW #4", "Show TIFFANY_SHOW #4", "S", 1, "0928654184", 2, 12.9534, 100.8907, "show", 1, NOW(), NOW()),
(153, "MERCH_TIFFANY_SHOW_05", "โชว์ TIFFANY_SHOW #5", "Show TIFFANY_SHOW #5", "S", 1, "0939888751", 2, 12.9541, 100.892, "show", 1, NOW(), NOW()),
(154, "MERCH_TIFFANY_SHOW_06", "โชว์ TIFFANY_SHOW #6", "Show TIFFANY_SHOW #6", "S", 1, "0942123318", 2, 12.9548, 100.8933, "show", 1, NOW(), NOW()),
(155, "MERCH_TIFFANY_SHOW_07", "โชว์ TIFFANY_SHOW #7", "Show TIFFANY_SHOW #7", "S", 1, "0953357885", 2, 12.9555, 100.8946, "show", 1, NOW(), NOW()),
(156, "MERCH_TIFFANY_SHOW_08", "โชว์ TIFFANY_SHOW #8", "Show TIFFANY_SHOW #8", "S", 1, "0964592452", 2, 12.9562, 100.8858, "show", 1, NOW(), NOW()),
(157, "MERCH_ALCAZAR_SHOW_01", "โชว์ ALCAZAR_SHOW #1", "Show ALCAZAR_SHOW #1", "XL", 1, "0975827019", 2, 12.9449, 100.8841, "show", 1, NOW(), NOW()),
(158, "MERCH_ALCAZAR_SHOW_02", "โชว์ ALCAZAR_SHOW #2", "Show ALCAZAR_SHOW #2", "E", 1, "0987061586", 2, 12.9456, 100.8854, "show", 1, NOW(), NOW()),
(159, "MERCH_ALCAZAR_SHOW_03", "โชว์ ALCAZAR_SHOW #3", "Show ALCAZAR_SHOW #3", "M", 1, "0998296153", 2, 12.9362, 100.8867, "show", 1, NOW(), NOW()),
(160, "MERCH_ALCAZAR_SHOW_04", "โชว์ ALCAZAR_SHOW #4", "Show ALCAZAR_SHOW #4", "S", 1, "0809530720", 2, 12.9369, 100.888, "show", 1, NOW(), NOW()),
(161, "MERCH_ALCAZAR_SHOW_05", "โชว์ ALCAZAR_SHOW #5", "Show ALCAZAR_SHOW #5", "S", 1, "0811765287", 2, 12.9376, 100.8893, "show", 1, NOW(), NOW()),
(162, "MERCH_ALCAZAR_SHOW_06", "โชว์ ALCAZAR_SHOW #6", "Show ALCAZAR_SHOW #6", "S", 1, "0822999854", 2, 12.9383, 100.8906, "show", 1, NOW(), NOW()),
(163, "MERCH_ALCAZAR_SHOW_07", "โชว์ ALCAZAR_SHOW #7", "Show ALCAZAR_SHOW #7", "S", 1, "0834234421", 2, 12.939, 100.8919, "show", 1, NOW(), NOW()),
(164, "MERCH_ALCAZAR_SHOW_08", "โชว์ ALCAZAR_SHOW #8", "Show ALCAZAR_SHOW #8", "S", 1, "0845468988", 2, 12.9397, 100.8831, "show", 1, NOW(), NOW()),
(165, "MERCH_ALCAZAR_SHOW_09", "โชว์ ALCAZAR_SHOW #9", "Show ALCAZAR_SHOW #9", "S", 1, "0856703555", 2, 12.9404, 100.8844, "show", 1, NOW(), NOW()),
(166, "MERCH_WALKING_STREET_01", "สถานบันเทิง WALKING_STREET #1", "Entertainment WALKING_STREET #1", "XL", 1, "0867938122", 2, 12.9261, 100.8697, "entertainment", 1, NOW(), NOW()),
(167, "MERCH_WALKING_STREET_02", "สถานบันเทิง WALKING_STREET #2", "Entertainment WALKING_STREET #2", "E", 1, "0879172689", 2, 12.9268, 100.871, "entertainment", 1, NOW(), NOW()),
(168, "MERCH_WALKING_STREET_03", "สถานบันเทิง WALKING_STREET #3", "Entertainment WALKING_STREET #3", "M", 1, "0881407256", 2, 12.9275, 100.8723, "entertainment", 1, NOW(), NOW()),
(169, "MERCH_WALKING_STREET_04", "สถานบันเทิง WALKING_STREET #4", "Entertainment WALKING_STREET #4", "S", 1, "0892641823", 2, 12.9282, 100.8736, "entertainment", 1, NOW(), NOW()),
(170, "MERCH_WALKING_STREET_05", "สถานบันเทิง WALKING_STREET #5", "Entertainment WALKING_STREET #5", "S", 1, "0903876390", 2, 12.9289, 100.8749, "entertainment", 1, NOW(), NOW()),
(171, "MERCH_WALKING_STREET_06", "สถานบันเทิง WALKING_STREET #6", "Entertainment WALKING_STREET #6", "S", 1, "0915110957", 2, 12.9296, 100.8661, "entertainment", 1, NOW(), NOW()),
(172, "MERCH_WALKING_STREET_07", "สถานบันเทิง WALKING_STREET #7", "Entertainment WALKING_STREET #7", "S", 1, "0926345524", 2, 12.9303, 100.8674, "entertainment", 1, NOW(), NOW()),
(173, "MERCH_WALKING_STREET_08", "สถานบันเทิง WALKING_STREET #8", "Entertainment WALKING_STREET #8", "S", 1, "0937580091", 2, 12.931, 100.8687, "entertainment", 1, NOW(), NOW()),
(174, "MERCH_WALKING_STREET_09", "สถานบันเทิง WALKING_STREET #9", "Entertainment WALKING_STREET #9", "S", 1, "0948814658", 2, 12.9216, 100.87, "entertainment", 1, NOW(), NOW()),
(175, "MERCH_WALKING_STREET_10", "สถานบันเทิง WALKING_STREET #10", "Entertainment WALKING_STREET #10", "S", 1, "0951049225", 2, 12.9223, 100.8713, "entertainment", 1, NOW(), NOW()),
(176, "MERCH_HARBOR_PATTAYA_01", "สถานบันเทิง HARBOR_PATTAYA #1", "Entertainment HARBOR_PATTAYA #1", "XL", 1, "0962283792", 2, 12.932, 100.8876, "entertainment", 1, NOW(), NOW()),
(177, "MERCH_HARBOR_PATTAYA_02", "สถานบันเทิง HARBOR_PATTAYA #2", "Entertainment HARBOR_PATTAYA #2", "E", 1, "0973518359", 2, 12.9327, 100.8889, "entertainment", 1, NOW(), NOW()),
(178, "MERCH_HARBOR_PATTAYA_03", "สถานบันเทิง HARBOR_PATTAYA #3", "Entertainment HARBOR_PATTAYA #3", "M", 1, "0984752926", 2, 12.9334, 100.8902, "entertainment", 1, NOW(), NOW()),
(179, "MERCH_HARBOR_PATTAYA_04", "สถานบันเทิง HARBOR_PATTAYA #4", "Entertainment HARBOR_PATTAYA #4", "S", 1, "0995987493", 2, 12.9341, 100.8814, "entertainment", 1, NOW(), NOW()),
(180, "MERCH_HARBOR_PATTAYA_05", "สถานบันเทิง HARBOR_PATTAYA #5", "Entertainment HARBOR_PATTAYA #5", "S", 1, "0807222060", 2, 12.9348, 100.8827, "entertainment", 1, NOW(), NOW()),
(181, "MERCH_BOWLING_ZONE_01", "สถานบันเทิง BOWLING_ZONE #1", "Entertainment BOWLING_ZONE #1", "XL", 1, "0818456627", 2, 12.9375, 100.883, "entertainment", 1, NOW(), NOW()),
(182, "MERCH_BOWLING_ZONE_02", "สถานบันเทิง BOWLING_ZONE #2", "Entertainment BOWLING_ZONE #2", "E", 1, "0829691194", 2, 12.9382, 100.8843, "entertainment", 1, NOW(), NOW()),
(183, "MERCH_BOWLING_ZONE_03", "สถานบันเทิง BOWLING_ZONE #3", "Entertainment BOWLING_ZONE #3", "M", 1, "0831925761", 2, 12.9389, 100.8856, "entertainment", 1, NOW(), NOW()),
(184, "MERCH_BOWLING_ZONE_04", "สถานบันเทิง BOWLING_ZONE #4", "Entertainment BOWLING_ZONE #4", "S", 1, "0843160328", 2, 12.9396, 100.8869, "entertainment", 1, NOW(), NOW()),
(185, "MERCH_BOWLING_ZONE_05", "สถานบันเทิง BOWLING_ZONE #5", "Entertainment BOWLING_ZONE #5", "S", 1, "0854394895", 2, 12.9403, 100.8882, "entertainment", 1, NOW(), NOW()),
(186, "MERCH_BOWLING_ZONE_06", "สถานบันเทิง BOWLING_ZONE #6", "Entertainment BOWLING_ZONE #6", "S", 1, "0865629462", 2, 12.941, 100.8895, "entertainment", 1, NOW(), NOW()),
(187, "MERCH_LETS_RELAX_SPA_01", "สปา LETS_RELAX_SPA #1", "Spa LETS_RELAX_SPA #1", "XL", 1, "0876864029", 2, 12.9437, 100.8787, "spa", 1, NOW(), NOW()),
(188, "MERCH_LETS_RELAX_SPA_02", "สปา LETS_RELAX_SPA #2", "Spa LETS_RELAX_SPA #2", "E", 1, "0888098596", 2, 12.9343, 100.88, "spa", 1, NOW(), NOW()),
(189, "MERCH_LETS_RELAX_SPA_03", "สปา LETS_RELAX_SPA #3", "Spa LETS_RELAX_SPA #3", "M", 1, "0899333163", 2, 12.935, 100.8813, "spa", 1, NOW(), NOW()),
(190, "MERCH_LETS_RELAX_SPA_04", "สปา LETS_RELAX_SPA #4", "Spa LETS_RELAX_SPA #4", "S", 1, "0901567730", 2, 12.9357, 100.8826, "spa", 1, NOW(), NOW()),
(191, "MERCH_LETS_RELAX_SPA_05", "สปา LETS_RELAX_SPA #5", "Spa LETS_RELAX_SPA #5", "S", 1, "0912802297", 2, 12.9364, 100.8839, "spa", 1, NOW(), NOW()),
(192, "MERCH_LETS_RELAX_SPA_06", "สปา LETS_RELAX_SPA #6", "Spa LETS_RELAX_SPA #6", "S", 1, "0924036864", 2, 12.9371, 100.8852, "spa", 1, NOW(), NOW()),
(193, "MERCH_LETS_RELAX_SPA_07", "สปา LETS_RELAX_SPA #7", "Spa LETS_RELAX_SPA #7", "S", 1, "0935271431", 2, 12.9378, 100.8865, "spa", 1, NOW(), NOW()),
(194, "MERCH_HEALTH_LAND_01", "สปา HEALTH_LAND #1", "Spa HEALTH_LAND #1", "XL", 1, "0946505998", 2, 12.9245, 100.8818, "spa", 1, NOW(), NOW()),
(195, "MERCH_HEALTH_LAND_02", "สปา HEALTH_LAND #2", "Spa HEALTH_LAND #2", "E", 1, "0957740565", 2, 12.9252, 100.873, "spa", 1, NOW(), NOW()),
(196, "MERCH_HEALTH_LAND_03", "สปา HEALTH_LAND #3", "Spa HEALTH_LAND #3", "M", 1, "0968975132", 2, 12.9259, 100.8743, "spa", 1, NOW(), NOW()),
(197, "MERCH_HEALTH_LAND_04", "สปา HEALTH_LAND #4", "Spa HEALTH_LAND #4", "S", 1, "0971209699", 2, 12.9266, 100.8756, "spa", 1, NOW(), NOW()),
(198, "MERCH_HEALTH_LAND_05", "สปา HEALTH_LAND #5", "Spa HEALTH_LAND #5", "S", 1, "0982444266", 2, 12.9273, 100.8769, "spa", 1, NOW(), NOW()),
(199, "MERCH_HEALTH_LAND_06", "สปา HEALTH_LAND #6", "Spa HEALTH_LAND #6", "S", 1, "0993678833", 2, 12.928, 100.8782, "spa", 1, NOW(), NOW()),
(200, "MERCH_HEALTH_LAND_07", "สปา HEALTH_LAND #7", "Spa HEALTH_LAND #7", "S", 1, "0804913400", 2, 12.9287, 100.8795, "spa", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(201, "MERCH_HEALTH_LAND_08", "สปา HEALTH_LAND #8", "Spa HEALTH_LAND #8", "S", 1, "0816147967", 2, 12.9294, 100.8808, "spa", 1, NOW(), NOW()),
(202, "MERCH_LUXURY_SPA_01", "สปา LUXURY_SPA #1", "Spa LUXURY_SPA #1", "XL", 1, "0827382534", 2, 12.937, 100.879, "spa", 1, NOW(), NOW()),
(203, "MERCH_LUXURY_SPA_02", "สปา LUXURY_SPA #2", "Spa LUXURY_SPA #2", "E", 1, "0838617101", 2, 12.9377, 100.8803, "spa", 1, NOW(), NOW()),
(204, "MERCH_LUXURY_SPA_03", "สปา LUXURY_SPA #3", "Spa LUXURY_SPA #3", "M", 1, "0849851668", 2, 12.9384, 100.8816, "spa", 1, NOW(), NOW()),
(205, "MERCH_LUXURY_SPA_04", "สปา LUXURY_SPA #4", "Spa LUXURY_SPA #4", "S", 1, "0852086235", 2, 12.9391, 100.8829, "spa", 1, NOW(), NOW()),
(206, "MERCH_LUXURY_SPA_05", "สปา LUXURY_SPA #5", "Spa LUXURY_SPA #5", "S", 1, "0863320802", 2, 12.9398, 100.8842, "spa", 1, NOW(), NOW()),
(207, "MERCH_LUXURY_SPA_06", "สปา LUXURY_SPA #6", "Spa LUXURY_SPA #6", "S", 1, "0874555369", 2, 12.9405, 100.8855, "spa", 1, NOW(), NOW()),
(208, "MERCH_LUXURY_SPA_07", "สปา LUXURY_SPA #7", "Spa LUXURY_SPA #7", "S", 1, "0885789936", 2, 12.9412, 100.8868, "spa", 1, NOW(), NOW()),
(209, "MERCH_LUXURY_SPA_08", "สปา LUXURY_SPA #8", "Spa LUXURY_SPA #8", "S", 1, "0897024503", 2, 12.9419, 100.8881, "spa", 1, NOW(), NOW()),
(210, "MERCH_LUXURY_SPA_09", "สปา LUXURY_SPA #9", "Spa LUXURY_SPA #9", "S", 1, "0908259070", 2, 12.9426, 100.8793, "spa", 1, NOW(), NOW()),
(211, "MERCH_DETOX_JUICE_01", "คาเฟ่ DETOX_JUICE #1", "Cafe DETOX_JUICE #1", "XL", 1, "0919493637", 2, 12.9343, 100.8776, "cafe", 1, NOW(), NOW()),
(212, "MERCH_DETOX_JUICE_02", "คาเฟ่ DETOX_JUICE #2", "Cafe DETOX_JUICE #2", "E", 1, "0921728204", 2, 12.935, 100.8789, "cafe", 1, NOW(), NOW()),
(213, "MERCH_DETOX_JUICE_03", "คาเฟ่ DETOX_JUICE #3", "Cafe DETOX_JUICE #3", "M", 1, "0932962771", 2, 12.9357, 100.8802, "cafe", 1, NOW(), NOW()),
(214, "MERCH_DETOX_JUICE_04", "คาเฟ่ DETOX_JUICE #4", "Cafe DETOX_JUICE #4", "S", 1, "0944197338", 2, 12.9364, 100.8815, "cafe", 1, NOW(), NOW()),
(215, "MERCH_DETOX_JUICE_05", "คาเฟ่ DETOX_JUICE #5", "Cafe DETOX_JUICE #5", "S", 1, "0955431905", 2, 12.9371, 100.8828, "cafe", 1, NOW(), NOW()),
(216, "MERCH_DETOX_JUICE_06", "คาเฟ่ DETOX_JUICE #6", "Cafe DETOX_JUICE #6", "S", 1, "0966666472", 2, 12.9378, 100.8841, "cafe", 1, NOW(), NOW()),
(217, "MERCH_DETOX_JUICE_07", "คาเฟ่ DETOX_JUICE #7", "Cafe DETOX_JUICE #7", "S", 1, "0977901039", 2, 12.9284, 100.8854, "cafe", 1, NOW(), NOW()),
(218, "MERCH_DETOX_JUICE_08", "คาเฟ่ DETOX_JUICE #8", "Cafe DETOX_JUICE #8", "S", 1, "0989135606", 2, 12.9291, 100.8766, "cafe", 1, NOW(), NOW()),
(219, "MERCH_DETOX_JUICE_09", "คาเฟ่ DETOX_JUICE #9", "Cafe DETOX_JUICE #9", "S", 1, "0991370173", 2, 12.9298, 100.8779, "cafe", 1, NOW(), NOW()),
(220, "MERCH_DETOX_JUICE_10", "คาเฟ่ DETOX_JUICE #10", "Cafe DETOX_JUICE #10", "S", 1, "0802604740", 2, 12.9305, 100.8792, "cafe", 1, NOW(), NOW()),
(221, "MERCH_SUNRISE_YOGA_01", "สุขภาพ SUNRISE_YOGA #1", "Wellness SUNRISE_YOGA #1", "XL", 1, "0813839307", 2, 12.9262, 100.8645, "wellness", 1, NOW(), NOW()),
(222, "MERCH_SUNRISE_YOGA_02", "สุขภาพ SUNRISE_YOGA #2", "Wellness SUNRISE_YOGA #2", "E", 1, "0825073874", 2, 12.9269, 100.8658, "wellness", 1, NOW(), NOW()),
(223, "MERCH_SUNRISE_YOGA_03", "สุขภาพ SUNRISE_YOGA #3", "Wellness SUNRISE_YOGA #3", "M", 1, "0836308441", 2, 12.9276, 100.8671, "wellness", 1, NOW(), NOW()),
(224, "MERCH_SUNRISE_YOGA_04", "สุขภาพ SUNRISE_YOGA #4", "Wellness SUNRISE_YOGA #4", "S", 1, "0847543008", 2, 12.9283, 100.8684, "wellness", 1, NOW(), NOW()),
(225, "MERCH_SUNRISE_YOGA_05", "สุขภาพ SUNRISE_YOGA #5", "Wellness SUNRISE_YOGA #5", "S", 1, "0858777575", 2, 12.929, 100.8697, "wellness", 1, NOW(), NOW()),
(226, "MERCH_HEALTHY_CAFE_01", "คาเฟ่ HEALTHY_CAFE #1", "Cafe HEALTHY_CAFE #1", "XL", 1, "0861012142", 2, 12.9357, 100.8779, "cafe", 1, NOW(), NOW()),
(227, "MERCH_HEALTHY_CAFE_02", "คาเฟ่ HEALTHY_CAFE #2", "Cafe HEALTHY_CAFE #2", "E", 1, "0872246709", 2, 12.9364, 100.8792, "cafe", 1, NOW(), NOW()),
(228, "MERCH_HEALTHY_CAFE_03", "คาเฟ่ HEALTHY_CAFE #3", "Cafe HEALTHY_CAFE #3", "M", 1, "0883481276", 2, 12.9371, 100.8805, "cafe", 1, NOW(), NOW()),
(229, "MERCH_HEALTHY_CAFE_04", "คาเฟ่ HEALTHY_CAFE #4", "Cafe HEALTHY_CAFE #4", "S", 1, "0894715843", 2, 12.9378, 100.8818, "cafe", 1, NOW(), NOW()),
(230, "MERCH_HEALTHY_CAFE_05", "คาเฟ่ HEALTHY_CAFE #5", "Cafe HEALTHY_CAFE #5", "S", 1, "0905950410", 2, 12.9385, 100.8831, "cafe", 1, NOW(), NOW()),
(231, "MERCH_HEALTHY_CAFE_06", "คาเฟ่ HEALTHY_CAFE #6", "Cafe HEALTHY_CAFE #6", "S", 1, "0917184977", 2, 12.9291, 100.8844, "cafe", 1, NOW(), NOW()),
(232, "MERCH_WELLNESS_CLINIC_01", "สุขภาพ WELLNESS_CLINIC #1", "Wellness WELLNESS_CLINIC #1", "XL", 1, "0928419544", 2, 12.9358, 100.8897, "wellness", 1, NOW(), NOW()),
(233, "MERCH_WELLNESS_CLINIC_02", "สุขภาพ WELLNESS_CLINIC #2", "Wellness WELLNESS_CLINIC #2", "E", 1, "0939654111", 2, 12.9365, 100.891, "wellness", 1, NOW(), NOW()),
(234, "MERCH_WELLNESS_CLINIC_03", "สุขภาพ WELLNESS_CLINIC #3", "Wellness WELLNESS_CLINIC #3", "M", 1, "0941888678", 2, 12.9372, 100.8822, "wellness", 1, NOW(), NOW()),
(235, "MERCH_WELLNESS_CLINIC_04", "สุขภาพ WELLNESS_CLINIC #4", "Wellness WELLNESS_CLINIC #4", "S", 1, "0953123245", 2, 12.9379, 100.8835, "wellness", 1, NOW(), NOW()),
(236, "MERCH_WELLNESS_CLINIC_05", "สุขภาพ WELLNESS_CLINIC #5", "Wellness WELLNESS_CLINIC #5", "S", 1, "0964357812", 2, 12.9386, 100.8848, "wellness", 1, NOW(), NOW()),
(237, "MERCH_WELLNESS_CLINIC_06", "สุขภาพ WELLNESS_CLINIC #6", "Wellness WELLNESS_CLINIC #6", "S", 1, "0975592379", 2, 12.9393, 100.8861, "wellness", 1, NOW(), NOW()),
(238, "MERCH_WELLNESS_CLINIC_07", "สุขภาพ WELLNESS_CLINIC #7", "Wellness WELLNESS_CLINIC #7", "S", 1, "0986826946", 2, 12.94, 100.8874, "wellness", 1, NOW(), NOW()),
(239, "MERCH_MEDITATION_GARDEN_01", "สุขภาพ MEDITATION_GARDEN #1", "Wellness MEDITATION_GARDEN #1", "XL", 1, "0998061513", 2, 12.9107, 100.8627, "wellness", 1, NOW(), NOW()),
(240, "MERCH_MEDITATION_GARDEN_02", "สุขภาพ MEDITATION_GARDEN #2", "Wellness MEDITATION_GARDEN #2", "E", 1, "0809296080", 2, 12.9114, 100.864, "wellness", 1, NOW(), NOW()),
(241, "MERCH_MEDITATION_GARDEN_03", "สุขภาพ MEDITATION_GARDEN #3", "Wellness MEDITATION_GARDEN #3", "M", 1, "0811530647", 2, 12.9121, 100.8552, "wellness", 1, NOW(), NOW()),
(242, "MERCH_MEDITATION_GARDEN_04", "สุขภาพ MEDITATION_GARDEN #4", "Wellness MEDITATION_GARDEN #4", "S", 1, "0822765214", 2, 12.9128, 100.8565, "wellness", 1, NOW(), NOW()),
(243, "MERCH_MEDITATION_GARDEN_05", "สุขภาพ MEDITATION_GARDEN #5", "Wellness MEDITATION_GARDEN #5", "S", 1, "0833999781", 2, 12.9135, 100.8578, "wellness", 1, NOW(), NOW()),
(244, "MERCH_MEDITATION_GARDEN_06", "สุขภาพ MEDITATION_GARDEN #6", "Wellness MEDITATION_GARDEN #6", "S", 1, "0845234348", 2, 12.9142, 100.8591, "wellness", 1, NOW(), NOW()),
(245, "MERCH_MEDITATION_GARDEN_07", "สุขภาพ MEDITATION_GARDEN #7", "Wellness MEDITATION_GARDEN #7", "S", 1, "0856468915", 2, 12.9149, 100.8604, "wellness", 1, NOW(), NOW()),
(246, "MERCH_MEDITATION_GARDEN_08", "สุขภาพ MEDITATION_GARDEN #8", "Wellness MEDITATION_GARDEN #8", "S", 1, "0867703482", 2, 12.9055, 100.8617, "wellness", 1, NOW(), NOW()),
(247, "MERCH_FITNESS_BOOTCAMP_01", "สุขภาพ FITNESS_BOOTCAMP #1", "Wellness FITNESS_BOOTCAMP #1", "XL", 1, "0878938049", 2, 12.9232, 100.869, "wellness", 1, NOW(), NOW()),
(248, "MERCH_FITNESS_BOOTCAMP_02", "สุขภาพ FITNESS_BOOTCAMP #2", "Wellness FITNESS_BOOTCAMP #2", "E", 1, "0881172616", 2, 12.9239, 100.8703, "wellness", 1, NOW(), NOW()),
(249, "MERCH_FITNESS_BOOTCAMP_03", "สุขภาพ FITNESS_BOOTCAMP #3", "Wellness FITNESS_BOOTCAMP #3", "M", 1, "0892407183", 2, 12.9246, 100.8615, "wellness", 1, NOW(), NOW()),
(250, "MERCH_FITNESS_BOOTCAMP_04", "สุขภาพ FITNESS_BOOTCAMP #4", "Wellness FITNESS_BOOTCAMP #4", "S", 1, "0903641750", 2, 12.9253, 100.8628, "wellness", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(251, "MERCH_FITNESS_BOOTCAMP_05", "สุขภาพ FITNESS_BOOTCAMP #5", "Wellness FITNESS_BOOTCAMP #5", "S", 1, "0914876317", 2, 12.926, 100.8641, "wellness", 1, NOW(), NOW()),
(252, "MERCH_FITNESS_BOOTCAMP_06", "สุขภาพ FITNESS_BOOTCAMP #6", "Wellness FITNESS_BOOTCAMP #6", "S", 1, "0926110884", 2, 12.9267, 100.8654, "wellness", 1, NOW(), NOW()),
(253, "MERCH_FITNESS_BOOTCAMP_07", "สุขภาพ FITNESS_BOOTCAMP #7", "Wellness FITNESS_BOOTCAMP #7", "S", 1, "0937345451", 2, 12.9274, 100.8667, "wellness", 1, NOW(), NOW()),
(254, "MERCH_FITNESS_BOOTCAMP_08", "สุขภาพ FITNESS_BOOTCAMP #8", "Wellness FITNESS_BOOTCAMP #8", "S", 1, "0948580018", 2, 12.9281, 100.868, "wellness", 1, NOW(), NOW()),
(255, "MERCH_FITNESS_BOOTCAMP_09", "สุขภาพ FITNESS_BOOTCAMP #9", "Wellness FITNESS_BOOTCAMP #9", "S", 1, "0959814585", 2, 12.9288, 100.8693, "wellness", 1, NOW(), NOW()),
(256, "MERCH_ICE_BATH_01", "สุขภาพ ICE_BATH #1", "Wellness ICE_BATH #1", "XL", 1, "0962049152", 2, 12.9305, 100.8716, "wellness", 1, NOW(), NOW()),
(257, "MERCH_ICE_BATH_02", "สุขภาพ ICE_BATH #2", "Wellness ICE_BATH #2", "E", 1, "0973283719", 2, 12.9312, 100.8628, "wellness", 1, NOW(), NOW()),
(258, "MERCH_ICE_BATH_03", "สุขภาพ ICE_BATH #3", "Wellness ICE_BATH #3", "M", 1, "0984518286", 2, 12.9319, 100.8641, "wellness", 1, NOW(), NOW()),
(259, "MERCH_ICE_BATH_04", "สุขภาพ ICE_BATH #4", "Wellness ICE_BATH #4", "S", 1, "0995752853", 2, 12.9326, 100.8654, "wellness", 1, NOW(), NOW()),
(260, "MERCH_ICE_BATH_05", "สุขภาพ ICE_BATH #5", "Wellness ICE_BATH #5", "S", 1, "0806987420", 2, 12.9232, 100.8667, "wellness", 1, NOW(), NOW()),
(261, "MERCH_ICE_BATH_06", "สุขภาพ ICE_BATH #6", "Wellness ICE_BATH #6", "S", 1, "0818221987", 2, 12.9239, 100.868, "wellness", 1, NOW(), NOW()),
(262, "MERCH_ICE_BATH_07", "สุขภาพ ICE_BATH #7", "Wellness ICE_BATH #7", "S", 1, "0829456554", 2, 12.9246, 100.8693, "wellness", 1, NOW(), NOW()),
(263, "MERCH_ICE_BATH_08", "สุขภาพ ICE_BATH #8", "Wellness ICE_BATH #8", "S", 1, "0831691121", 2, 12.9253, 100.8706, "wellness", 1, NOW(), NOW()),
(264, "MERCH_ICE_BATH_09", "สุขภาพ ICE_BATH #9", "Wellness ICE_BATH #9", "S", 1, "0842925688", 2, 12.926, 100.8719, "wellness", 1, NOW(), NOW()),
(265, "MERCH_ICE_BATH_10", "สุขภาพ ICE_BATH #10", "Wellness ICE_BATH #10", "S", 1, "0854160255", 2, 12.9267, 100.8631, "wellness", 1, NOW(), NOW()),
(266, "MERCH_HOTEL_SPA_01", "สปา HOTEL_SPA #1", "Spa HOTEL_SPA #1", "XL", 1, "0865394822", 2, 12.9424, 100.8814, "spa", 1, NOW(), NOW()),
(267, "MERCH_HOTEL_SPA_02", "สปา HOTEL_SPA #2", "Spa HOTEL_SPA #2", "E", 1, "0876629389", 2, 12.9431, 100.8827, "spa", 1, NOW(), NOW()),
(268, "MERCH_HOTEL_SPA_03", "สปา HOTEL_SPA #3", "Spa HOTEL_SPA #3", "M", 1, "0887863956", 2, 12.9438, 100.884, "spa", 1, NOW(), NOW()),
(269, "MERCH_HOTEL_SPA_04", "สปา HOTEL_SPA #4", "Spa HOTEL_SPA #4", "S", 1, "0899098523", 2, 12.9445, 100.8853, "spa", 1, NOW(), NOW()),
(270, "MERCH_HOTEL_SPA_05", "สปา HOTEL_SPA #5", "Spa HOTEL_SPA #5", "S", 1, "0901333090", 2, 12.9452, 100.8866, "spa", 1, NOW(), NOW()),
(271, "MERCH_MIDRANGE_HOTEL_01", "โรงแรม MIDRANGE_HOTEL #1", "Hotel MIDRANGE_HOTEL #1", "XL", 1, "0912567657", 2, 12.9409, 100.8869, "hotel", 1, NOW(), NOW()),
(272, "MERCH_MIDRANGE_HOTEL_02", "โรงแรม MIDRANGE_HOTEL #2", "Hotel MIDRANGE_HOTEL #2", "E", 1, "0923802224", 2, 12.9416, 100.8781, "hotel", 1, NOW(), NOW()),
(273, "MERCH_MIDRANGE_HOTEL_03", "โรงแรม MIDRANGE_HOTEL #3", "Hotel MIDRANGE_HOTEL #3", "M", 1, "0935036791", 2, 12.9423, 100.8794, "hotel", 1, NOW(), NOW()),
(274, "MERCH_MIDRANGE_HOTEL_04", "โรงแรม MIDRANGE_HOTEL #4", "Hotel MIDRANGE_HOTEL #4", "S", 1, "0946271358", 2, 12.943, 100.8807, "hotel", 1, NOW(), NOW()),
(275, "MERCH_MIDRANGE_HOTEL_05", "โรงแรม MIDRANGE_HOTEL #5", "Hotel MIDRANGE_HOTEL #5", "S", 1, "0957505925", 2, 12.9336, 100.882, "hotel", 1, NOW(), NOW()),
(276, "MERCH_MIDRANGE_HOTEL_06", "โรงแรม MIDRANGE_HOTEL #6", "Hotel MIDRANGE_HOTEL #6", "S", 1, "0968740492", 2, 12.9343, 100.8833, "hotel", 1, NOW(), NOW()),
(277, "MERCH_BUDGET_HOTEL_01", "โรงแรม BUDGET_HOTEL #1", "Hotel BUDGET_HOTEL #1", "XL", 1, "0979975059", 2, 12.932, 100.8826, "hotel", 1, NOW(), NOW()),
(278, "MERCH_BUDGET_HOTEL_02", "โรงแรม BUDGET_HOTEL #2", "Hotel BUDGET_HOTEL #2", "E", 1, "0982209626", 2, 12.9327, 100.8839, "hotel", 1, NOW(), NOW()),
(279, "MERCH_BUDGET_HOTEL_03", "โรงแรม BUDGET_HOTEL #3", "Hotel BUDGET_HOTEL #3", "M", 1, "0993444193", 2, 12.9334, 100.8852, "hotel", 1, NOW(), NOW()),
(280, "MERCH_BUDGET_HOTEL_04", "โรงแรม BUDGET_HOTEL #4", "Hotel BUDGET_HOTEL #4", "S", 1, "0804678760", 2, 12.9341, 100.8764, "hotel", 1, NOW(), NOW()),
(281, "MERCH_BUDGET_HOTEL_05", "โรงแรม BUDGET_HOTEL #5", "Hotel BUDGET_HOTEL #5", "S", 1, "0815913327", 2, 12.9348, 100.8777, "hotel", 1, NOW(), NOW()),
(282, "MERCH_BUDGET_HOTEL_06", "โรงแรม BUDGET_HOTEL #6", "Hotel BUDGET_HOTEL #6", "S", 1, "0827147894", 2, 12.9355, 100.879, "hotel", 1, NOW(), NOW()),
(283, "MERCH_BUDGET_HOTEL_07", "โรงแรม BUDGET_HOTEL #7", "Hotel BUDGET_HOTEL #7", "S", 1, "0838382461", 2, 12.9362, 100.8803, "hotel", 1, NOW(), NOW()),
(284, "MERCH_LUXURY_HOTEL_01", "โรงแรม LUXURY_HOTEL #1", "Hotel LUXURY_HOTEL #1", "XL", 1, "0849617028", 2, 12.9459, 100.8846, "hotel", 1, NOW(), NOW()),
(285, "MERCH_LUXURY_HOTEL_02", "โรงแรม LUXURY_HOTEL #2", "Hotel LUXURY_HOTEL #2", "E", 1, "0851851595", 2, 12.9466, 100.8859, "hotel", 1, NOW(), NOW()),
(286, "MERCH_LUXURY_HOTEL_03", "โรงแรม LUXURY_HOTEL #3", "Hotel LUXURY_HOTEL #3", "M", 1, "0863086162", 2, 12.9473, 100.8872, "hotel", 1, NOW(), NOW()),
(287, "MERCH_LUXURY_HOTEL_04", "โรงแรม LUXURY_HOTEL #4", "Hotel LUXURY_HOTEL #4", "S", 1, "0874320729", 2, 12.948, 100.8885, "hotel", 1, NOW(), NOW()),
(288, "MERCH_LUXURY_HOTEL_05", "โรงแรม LUXURY_HOTEL #5", "Hotel LUXURY_HOTEL #5", "S", 1, "0885555296", 2, 12.9487, 100.8797, "hotel", 1, NOW(), NOW()),
(289, "MERCH_LUXURY_HOTEL_06", "โรงแรม LUXURY_HOTEL #6", "Hotel LUXURY_HOTEL #6", "S", 1, "0896789863", 2, 12.9393, 100.881, "hotel", 1, NOW(), NOW()),
(290, "MERCH_LUXURY_HOTEL_07", "โรงแรม LUXURY_HOTEL #7", "Hotel LUXURY_HOTEL #7", "S", 1, "0908024430", 2, 12.94, 100.8823, "hotel", 1, NOW(), NOW()),
(291, "MERCH_LUXURY_HOTEL_08", "โรงแรม LUXURY_HOTEL #8", "Hotel LUXURY_HOTEL #8", "S", 1, "0919258997", 2, 12.9407, 100.8836, "hotel", 1, NOW(), NOW()),
(292, "MERCH_FAMILY_RESORT_01", "โรงแรม FAMILY_RESORT #1", "Hotel FAMILY_RESORT #1", "XL", 1, "0921493564", 2, 12.9174, 100.8759, "hotel", 1, NOW(), NOW()),
(293, "MERCH_FAMILY_RESORT_02", "โรงแรม FAMILY_RESORT #2", "Hotel FAMILY_RESORT #2", "E", 1, "0932728131", 2, 12.9181, 100.8772, "hotel", 1, NOW(), NOW()),
(294, "MERCH_FAMILY_RESORT_03", "โรงแรม FAMILY_RESORT #3", "Hotel FAMILY_RESORT #3", "M", 1, "0943962698", 2, 12.9188, 100.8785, "hotel", 1, NOW(), NOW()),
(295, "MERCH_FAMILY_RESORT_04", "โรงแรม FAMILY_RESORT #4", "Hotel FAMILY_RESORT #4", "S", 1, "0955197265", 2, 12.9195, 100.8798, "hotel", 1, NOW(), NOW()),
(296, "MERCH_FAMILY_RESORT_05", "โรงแรม FAMILY_RESORT #5", "Hotel FAMILY_RESORT #5", "S", 1, "0966431832", 2, 12.9202, 100.871, "hotel", 1, NOW(), NOW()),
(297, "MERCH_FAMILY_RESORT_06", "โรงแรม FAMILY_RESORT #6", "Hotel FAMILY_RESORT #6", "S", 1, "0977666399", 2, 12.9209, 100.8723, "hotel", 1, NOW(), NOW()),
(298, "MERCH_FAMILY_RESORT_07", "โรงแรม FAMILY_RESORT #7", "Hotel FAMILY_RESORT #7", "S", 1, "0988900966", 2, 12.9216, 100.8736, "hotel", 1, NOW(), NOW()),
(299, "MERCH_FAMILY_RESORT_08", "โรงแรม FAMILY_RESORT #8", "Hotel FAMILY_RESORT #8", "S", 1, "0991135533", 2, 12.9223, 100.8749, "hotel", 1, NOW(), NOW()),
(300, "MERCH_FAMILY_RESORT_09", "โรงแรม FAMILY_RESORT #9", "Hotel FAMILY_RESORT #9", "S", 1, "0802370100", 2, 12.923, 100.8762, "hotel", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(301, "MERCH_COWORKING_01", "ออฟฟิศ COWORKING #1", "Office COWORKING #1", "XL", 1, "0813604667", 2, 12.9427, 100.8855, "office", 1, NOW(), NOW()),
(302, "MERCH_COWORKING_02", "ออฟฟิศ COWORKING #2", "Office COWORKING #2", "E", 1, "0824839234", 2, 12.9434, 100.8868, "office", 1, NOW(), NOW()),
(303, "MERCH_COWORKING_03", "ออฟฟิศ COWORKING #3", "Office COWORKING #3", "M", 1, "0836073801", 2, 12.934, 100.878, "office", 1, NOW(), NOW()),
(304, "MERCH_COWORKING_04", "ออฟฟิศ COWORKING #4", "Office COWORKING #4", "S", 1, "0847308368", 2, 12.9347, 100.8793, "office", 1, NOW(), NOW()),
(305, "MERCH_COWORKING_05", "ออฟฟิศ COWORKING #5", "Office COWORKING #5", "S", 1, "0858542935", 2, 12.9354, 100.8806, "office", 1, NOW(), NOW()),
(306, "MERCH_COWORKING_06", "ออฟฟิศ COWORKING #6", "Office COWORKING #6", "S", 1, "0869777502", 2, 12.9361, 100.8819, "office", 1, NOW(), NOW()),
(307, "MERCH_COWORKING_07", "ออฟฟิศ COWORKING #7", "Office COWORKING #7", "S", 1, "0872012069", 2, 12.9368, 100.8832, "office", 1, NOW(), NOW()),
(308, "MERCH_COWORKING_08", "ออฟฟิศ COWORKING #8", "Office COWORKING #8", "S", 1, "0883246636", 2, 12.9375, 100.8845, "office", 1, NOW(), NOW()),
(309, "MERCH_COWORKING_09", "ออฟฟิศ COWORKING #9", "Office COWORKING #9", "S", 1, "0894481203", 2, 12.9382, 100.8858, "office", 1, NOW(), NOW()),
(310, "MERCH_COWORKING_10", "ออฟฟิศ COWORKING #10", "Office COWORKING #10", "S", 1, "0905715770", 2, 12.9389, 100.8871, "office", 1, NOW(), NOW()),
(311, "MERCH_VAN_GUIDE_01", "ขนส่ง VAN_GUIDE #1", "Transport VAN_GUIDE #1", "XL", 1, "0916950337", 2, 12.9346, 100.8753, "transport", 1, NOW(), NOW()),
(312, "MERCH_VAN_GUIDE_02", "ขนส่ง VAN_GUIDE #2", "Transport VAN_GUIDE #2", "E", 1, "0928184904", 2, 12.9353, 100.8766, "transport", 1, NOW(), NOW()),
(313, "MERCH_VAN_GUIDE_03", "ขนส่ง VAN_GUIDE #3", "Transport VAN_GUIDE #3", "M", 1, "0939419471", 2, 12.936, 100.8779, "transport", 1, NOW(), NOW()),
(314, "MERCH_VAN_GUIDE_04", "ขนส่ง VAN_GUIDE #4", "Transport VAN_GUIDE #4", "S", 1, "0941654038", 2, 12.9367, 100.8792, "transport", 1, NOW(), NOW()),
(315, "MERCH_VAN_GUIDE_05", "ขนส่ง VAN_GUIDE #5", "Transport VAN_GUIDE #5", "S", 1, "0952888605", 2, 12.9374, 100.8805, "transport", 1, NOW(), NOW()),
(316, "MERCH_SUNSET_YACHT_01", "ทัวร์ SUNSET_YACHT #1", "Tour SUNSET_YACHT #1", "XL", 1, "0964123172", 2, 12.9301, 100.8658, "tour", 1, NOW(), NOW()),
(317, "MERCH_SUNSET_YACHT_02", "ทัวร์ SUNSET_YACHT #2", "Tour SUNSET_YACHT #2", "E", 1, "0975357739", 2, 12.9308, 100.8671, "tour", 1, NOW(), NOW()),
(318, "MERCH_SUNSET_YACHT_03", "ทัวร์ SUNSET_YACHT #3", "Tour SUNSET_YACHT #3", "M", 1, "0986592306", 2, 12.9214, 100.8684, "tour", 1, NOW(), NOW()),
(319, "MERCH_SUNSET_YACHT_04", "ทัวร์ SUNSET_YACHT #4", "Tour SUNSET_YACHT #4", "S", 1, "0997826873", 2, 12.9221, 100.8596, "tour", 1, NOW(), NOW()),
(320, "MERCH_SUNSET_YACHT_05", "ทัวร์ SUNSET_YACHT #5", "Tour SUNSET_YACHT #5", "S", 1, "0809061440", 2, 12.9228, 100.8609, "tour", 1, NOW(), NOW()),
(321, "MERCH_SUNSET_YACHT_06", "ทัวร์ SUNSET_YACHT #6", "Tour SUNSET_YACHT #6", "S", 1, "0811296007", 2, 12.9235, 100.8622, "tour", 1, NOW(), NOW()),
(322, "MERCH_SAFE_RIDE_01", "ขนส่ง SAFE_RIDE #1", "Transport SAFE_RIDE #1", "XL", 1, "0822530574", 2, 12.9322, 100.8795, "transport", 1, NOW(), NOW()),
(323, "MERCH_SAFE_RIDE_02", "ขนส่ง SAFE_RIDE #2", "Transport SAFE_RIDE #2", "E", 1, "0833765141", 2, 12.9329, 100.8808, "transport", 1, NOW(), NOW()),
(324, "MERCH_SAFE_RIDE_03", "ขนส่ง SAFE_RIDE #3", "Transport SAFE_RIDE #3", "M", 1, "0844999708", 2, 12.9336, 100.8821, "transport", 1, NOW(), NOW()),
(325, "MERCH_SAFE_RIDE_04", "ขนส่ง SAFE_RIDE #4", "Transport SAFE_RIDE #4", "S", 1, "0856234275", 2, 12.9343, 100.8834, "transport", 1, NOW(), NOW()),
(326, "MERCH_SAFE_RIDE_05", "ขนส่ง SAFE_RIDE #5", "Transport SAFE_RIDE #5", "S", 1, "0867468842", 2, 12.935, 100.8847, "transport", 1, NOW(), NOW()),
(327, "MERCH_SAFE_RIDE_06", "ขนส่ง SAFE_RIDE #6", "Transport SAFE_RIDE #6", "S", 1, "0878703409", 2, 12.9357, 100.8759, "transport", 1, NOW(), NOW()),
(328, "MERCH_SAFE_RIDE_07", "ขนส่ง SAFE_RIDE #7", "Transport SAFE_RIDE #7", "S", 1, "0889937976", 2, 12.9364, 100.8772, "transport", 1, NOW(), NOW()),
(329, "MERCH_BALI_HAI_PIER_01", "ขนส่ง BALI_HAI_PIER #1", "Transport BALI_HAI_PIER #1", "XL", 1, "0892172543", 2, 12.9261, 100.8615, "transport", 1, NOW(), NOW()),
(330, "MERCH_BALI_HAI_PIER_02", "ขนส่ง BALI_HAI_PIER #2", "Transport BALI_HAI_PIER #2", "E", 1, "0903407110", 2, 12.9268, 100.8628, "transport", 1, NOW(), NOW()),
(331, "MERCH_BALI_HAI_PIER_03", "ขนส่ง BALI_HAI_PIER #3", "Transport BALI_HAI_PIER #3", "M", 1, "0914641677", 2, 12.9275, 100.8641, "transport", 1, NOW(), NOW()),
(332, "MERCH_BALI_HAI_PIER_04", "ขนส่ง BALI_HAI_PIER #4", "Transport BALI_HAI_PIER #4", "S", 1, "0925876244", 2, 12.9181, 100.8654, "transport", 1, NOW(), NOW()),
(333, "MERCH_BALI_HAI_PIER_05", "ขนส่ง BALI_HAI_PIER #5", "Transport BALI_HAI_PIER #5", "S", 1, "0937110811", 2, 12.9188, 100.8667, "transport", 1, NOW(), NOW()),
(334, "MERCH_BALI_HAI_PIER_06", "ขนส่ง BALI_HAI_PIER #6", "Transport BALI_HAI_PIER #6", "S", 1, "0948345378", 2, 12.9195, 100.868, "transport", 1, NOW(), NOW()),
(335, "MERCH_BALI_HAI_PIER_07", "ขนส่ง BALI_HAI_PIER #7", "Transport BALI_HAI_PIER #7", "S", 1, "0959579945", 2, 12.9202, 100.8592, "transport", 1, NOW(), NOW()),
(336, "MERCH_BALI_HAI_PIER_08", "ขนส่ง BALI_HAI_PIER #8", "Transport BALI_HAI_PIER #8", "S", 1, "0961814512", 2, 12.9209, 100.8605, "transport", 1, NOW(), NOW()),
(337, "MERCH_KOH_LARN_01", "ชายหาด KOH_LARN #1", "Beach KOH_LARN #1", "XL", 1, "0973049079", 2, 12.9156, 100.7838, "beach", 1, NOW(), NOW()),
(338, "MERCH_KOH_LARN_02", "ชายหาด KOH_LARN #2", "Beach KOH_LARN #2", "E", 1, "0984283646", 2, 12.9163, 100.7851, "beach", 1, NOW(), NOW()),
(339, "MERCH_KOH_LARN_03", "ชายหาด KOH_LARN #3", "Beach KOH_LARN #3", "M", 1, "0995518213", 2, 12.917, 100.7864, "beach", 1, NOW(), NOW()),
(340, "MERCH_KOH_LARN_04", "ชายหาด KOH_LARN #4", "Beach KOH_LARN #4", "S", 1, "0806752780", 2, 12.9177, 100.7877, "beach", 1, NOW(), NOW()),
(341, "MERCH_KOH_LARN_05", "ชายหาด KOH_LARN #5", "Beach KOH_LARN #5", "S", 1, "0817987347", 2, 12.9184, 100.789, "beach", 1, NOW(), NOW()),
(342, "MERCH_KOH_LARN_06", "ชายหาด KOH_LARN #6", "Beach KOH_LARN #6", "S", 1, "0829221914", 2, 12.9191, 100.7802, "beach", 1, NOW(), NOW()),
(343, "MERCH_KOH_LARN_07", "ชายหาด KOH_LARN #7", "Beach KOH_LARN #7", "S", 1, "0831456481", 2, 12.9198, 100.7815, "beach", 1, NOW(), NOW()),
(344, "MERCH_KOH_LARN_08", "ชายหาด KOH_LARN #8", "Beach KOH_LARN #8", "S", 1, "0842691048", 2, 12.9205, 100.7828, "beach", 1, NOW(), NOW()),
(345, "MERCH_KOH_LARN_09", "ชายหาด KOH_LARN #9", "Beach KOH_LARN #9", "S", 1, "0853925615", 2, 12.9212, 100.7841, "beach", 1, NOW(), NOW()),
(346, "MERCH_SCOOTER_RENTAL_01", "ขนส่ง SCOOTER_RENTAL #1", "Transport SCOOTER_RENTAL #1", "XL", 1, "0865160182", 2, 12.9399, 100.8804, "transport", 1, NOW(), NOW()),
(347, "MERCH_SCOOTER_RENTAL_02", "ขนส่ง SCOOTER_RENTAL #2", "Transport SCOOTER_RENTAL #2", "E", 1, "0876394749", 2, 12.9305, 100.8817, "transport", 1, NOW(), NOW()),
(348, "MERCH_SCOOTER_RENTAL_03", "ขนส่ง SCOOTER_RENTAL #3", "Transport SCOOTER_RENTAL #3", "M", 1, "0887629316", 2, 12.9312, 100.883, "transport", 1, NOW(), NOW()),
(349, "MERCH_SCOOTER_RENTAL_04", "ขนส่ง SCOOTER_RENTAL #4", "Transport SCOOTER_RENTAL #4", "S", 1, "0898863883", 2, 12.9319, 100.8843, "transport", 1, NOW(), NOW()),
(350, "MERCH_SCOOTER_RENTAL_05", "ขนส่ง SCOOTER_RENTAL #5", "Transport SCOOTER_RENTAL #5", "S", 1, "0901098450", 2, 12.9326, 100.8755, "transport", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(351, "MERCH_SCOOTER_RENTAL_06", "ขนส่ง SCOOTER_RENTAL #6", "Transport SCOOTER_RENTAL #6", "S", 1, "0912333017", 2, 12.9333, 100.8768, "transport", 1, NOW(), NOW()),
(352, "MERCH_SCOOTER_RENTAL_07", "ขนส่ง SCOOTER_RENTAL #7", "Transport SCOOTER_RENTAL #7", "S", 1, "0923567584", 2, 12.934, 100.8781, "transport", 1, NOW(), NOW()),
(353, "MERCH_SCOOTER_RENTAL_08", "ขนส่ง SCOOTER_RENTAL #8", "Transport SCOOTER_RENTAL #8", "S", 1, "0934802151", 2, 12.9347, 100.8794, "transport", 1, NOW(), NOW()),
(354, "MERCH_SCOOTER_RENTAL_09", "ขนส่ง SCOOTER_RENTAL #9", "Transport SCOOTER_RENTAL #9", "S", 1, "0946036718", 2, 12.9354, 100.8807, "transport", 1, NOW(), NOW()),
(355, "MERCH_SCOOTER_RENTAL_10", "ขนส่ง SCOOTER_RENTAL #10", "Transport SCOOTER_RENTAL #10", "S", 1, "0957271285", 2, 12.9361, 100.882, "transport", 1, NOW(), NOW()),
(356, "MERCH_BIKE_ROUTE_01", "ทัวร์ BIKE_ROUTE #1", "Tour BIKE_ROUTE #1", "XL", 1, "0968505852", 2, 12.9318, 100.8783, "tour", 1, NOW(), NOW()),
(357, "MERCH_BIKE_ROUTE_02", "ทัวร์ BIKE_ROUTE #2", "Tour BIKE_ROUTE #2", "E", 1, "0979740419", 2, 12.9325, 100.8796, "tour", 1, NOW(), NOW()),
(358, "MERCH_BIKE_ROUTE_03", "ทัวร์ BIKE_ROUTE #3", "Tour BIKE_ROUTE #3", "M", 1, "0981974986", 2, 12.9332, 100.8708, "tour", 1, NOW(), NOW()),
(359, "MERCH_BIKE_ROUTE_04", "ทัวร์ BIKE_ROUTE #4", "Tour BIKE_ROUTE #4", "S", 1, "0993209553", 2, 12.9339, 100.8721, "tour", 1, NOW(), NOW()),
(360, "MERCH_BIKE_ROUTE_05", "ทัวร์ BIKE_ROUTE #5", "Tour BIKE_ROUTE #5", "S", 1, "0804444120", 2, 12.9346, 100.8734, "tour", 1, NOW(), NOW()),
(361, "MERCH_WATER_SPORTS_01", "ทัวร์ WATER_SPORTS #1", "Tour WATER_SPORTS #1", "XL", 1, "0815678687", 2, 12.9242, 100.8647, "tour", 1, NOW(), NOW()),
(362, "MERCH_WATER_SPORTS_02", "ทัวร์ WATER_SPORTS #2", "Tour WATER_SPORTS #2", "E", 1, "0826913254", 2, 12.9249, 100.866, "tour", 1, NOW(), NOW()),
(363, "MERCH_WATER_SPORTS_03", "ทัวร์ WATER_SPORTS #3", "Tour WATER_SPORTS #3", "M", 1, "0838147821", 2, 12.9256, 100.8673, "tour", 1, NOW(), NOW()),
(364, "MERCH_WATER_SPORTS_04", "ทัวร์ WATER_SPORTS #4", "Tour WATER_SPORTS #4", "S", 1, "0849382388", 2, 12.9263, 100.8686, "tour", 1, NOW(), NOW()),
(365, "MERCH_WATER_SPORTS_05", "ทัวร์ WATER_SPORTS #5", "Tour WATER_SPORTS #5", "S", 1, "0851616955", 2, 12.927, 100.8699, "tour", 1, NOW(), NOW()),
(366, "MERCH_WATER_SPORTS_06", "ทัวร์ WATER_SPORTS #6", "Tour WATER_SPORTS #6", "S", 1, "0862851522", 2, 12.9277, 100.8611, "tour", 1, NOW(), NOW()),
(367, "MERCH_FULL_DAY_YACHT_01", "ทัวร์ FULL_DAY_YACHT #1", "Tour FULL_DAY_YACHT #1", "XL", 1, "0874086089", 2, 12.9254, 100.8614, "tour", 1, NOW(), NOW()),
(368, "MERCH_FULL_DAY_YACHT_02", "ทัวร์ FULL_DAY_YACHT #2", "Tour FULL_DAY_YACHT #2", "E", 1, "0885320656", 2, 12.9261, 100.8627, "tour", 1, NOW(), NOW()),
(369, "MERCH_FULL_DAY_YACHT_03", "ทัวร์ FULL_DAY_YACHT #3", "Tour FULL_DAY_YACHT #3", "M", 1, "0896555223", 2, 12.9268, 100.864, "tour", 1, NOW(), NOW()),
(370, "MERCH_FULL_DAY_YACHT_04", "ทัวร์ FULL_DAY_YACHT #4", "Tour FULL_DAY_YACHT #4", "S", 1, "0907789790", 2, 12.9275, 100.8653, "tour", 1, NOW(), NOW()),
(371, "MERCH_FULL_DAY_YACHT_05", "ทัวร์ FULL_DAY_YACHT #5", "Tour FULL_DAY_YACHT #5", "S", 1, "0919024357", 2, 12.9282, 100.8666, "tour", 1, NOW(), NOW()),
(372, "MERCH_FULL_DAY_YACHT_06", "ทัวร์ FULL_DAY_YACHT #6", "Tour FULL_DAY_YACHT #6", "S", 1, "0921258924", 2, 12.9289, 100.8679, "tour", 1, NOW(), NOW()),
(373, "MERCH_FULL_DAY_YACHT_07", "ทัวร์ FULL_DAY_YACHT #7", "Tour FULL_DAY_YACHT #7", "S", 1, "0932493491", 2, 12.9296, 100.8591, "tour", 1, NOW(), NOW()),
(374, "MERCH_CITY_PASS_01", "บริการ CITY_PASS #1", "Service CITY_PASS #1", "XL", 1, "0943728058", 2, 12.9383, 100.8764, "service", 1, NOW(), NOW()),
(375, "MERCH_CITY_PASS_02", "บริการ CITY_PASS #2", "Service CITY_PASS #2", "E", 1, "0954962625", 2, 12.939, 100.8777, "service", 1, NOW(), NOW()),
(376, "MERCH_CITY_PASS_03", "บริการ CITY_PASS #3", "Service CITY_PASS #3", "M", 1, "0966197192", 2, 12.9296, 100.879, "service", 1, NOW(), NOW()),
(377, "MERCH_CITY_PASS_04", "บริการ CITY_PASS #4", "Service CITY_PASS #4", "S", 1, "0977431759", 2, 12.9303, 100.8803, "service", 1, NOW(), NOW()),
(378, "MERCH_CITY_PASS_05", "บริการ CITY_PASS #5", "Service CITY_PASS #5", "S", 1, "0988666326", 2, 12.931, 100.8816, "service", 1, NOW(), NOW()),
(379, "MERCH_CITY_PASS_06", "บริการ CITY_PASS #6", "Service CITY_PASS #6", "S", 1, "0999900893", 2, 12.9317, 100.8829, "service", 1, NOW(), NOW()),
(380, "MERCH_CITY_PASS_07", "บริการ CITY_PASS #7", "Service CITY_PASS #7", "S", 1, "0802135460", 2, 12.9324, 100.8842, "service", 1, NOW(), NOW()),
(381, "MERCH_CITY_PASS_08", "บริการ CITY_PASS #8", "Service CITY_PASS #8", "S", 1, "0813370027", 2, 12.9331, 100.8754, "service", 1, NOW(), NOW()),
(382, "MERCH_NONG_NOOCH_01", "ท่องเที่ยว NONG_NOOCH #1", "Attraction NONG_NOOCH #1", "XL", 1, "0824604594", 2, 12.7648, 100.9317, "attraction", 1, NOW(), NOW()),
(383, "MERCH_NONG_NOOCH_02", "ท่องเที่ยว NONG_NOOCH #2", "Attraction NONG_NOOCH #2", "E", 1, "0835839161", 2, 12.7655, 100.933, "attraction", 1, NOW(), NOW()),
(384, "MERCH_NONG_NOOCH_03", "ท่องเที่ยว NONG_NOOCH #3", "Attraction NONG_NOOCH #3", "M", 1, "0847073728", 2, 12.7662, 100.9343, "attraction", 1, NOW(), NOW()),
(385, "MERCH_NONG_NOOCH_04", "ท่องเที่ยว NONG_NOOCH #4", "Attraction NONG_NOOCH #4", "S", 1, "0858308295", 2, 12.7669, 100.9356, "attraction", 1, NOW(), NOW()),
(386, "MERCH_NONG_NOOCH_05", "ท่องเที่ยว NONG_NOOCH #5", "Attraction NONG_NOOCH #5", "S", 1, "0869542862", 2, 12.7676, 100.9369, "attraction", 1, NOW(), NOW()),
(387, "MERCH_NONG_NOOCH_06", "ท่องเที่ยว NONG_NOOCH #6", "Attraction NONG_NOOCH #6", "S", 1, "0871777429", 2, 12.7683, 100.9382, "attraction", 1, NOW(), NOW()),
(388, "MERCH_NONG_NOOCH_07", "ท่องเที่ยว NONG_NOOCH #7", "Attraction NONG_NOOCH #7", "S", 1, "0883011996", 2, 12.769, 100.9395, "attraction", 1, NOW(), NOW()),
(389, "MERCH_NONG_NOOCH_08", "ท่องเที่ยว NONG_NOOCH #8", "Attraction NONG_NOOCH #8", "S", 1, "0894246563", 2, 12.7697, 100.9307, "attraction", 1, NOW(), NOW()),
(390, "MERCH_NONG_NOOCH_09", "ท่องเที่ยว NONG_NOOCH #9", "Attraction NONG_NOOCH #9", "S", 1, "0905481130", 2, 12.7603, 100.932, "attraction", 1, NOW(), NOW()),
(391, "MERCH_SANCTUARY_TRUTH_01", "ท่องเที่ยว SANCTUARY_TRUTH #1", "Attraction SANCTUARY_TRUTH #1", "XL", 1, "0916715697", 2, 12.967, 100.8873, "attraction", 1, NOW(), NOW()),
(392, "MERCH_SANCTUARY_TRUTH_02", "ท่องเที่ยว SANCTUARY_TRUTH #2", "Attraction SANCTUARY_TRUTH #2", "E", 1, "0927950264", 2, 12.9677, 100.8886, "attraction", 1, NOW(), NOW()),
(393, "MERCH_SANCTUARY_TRUTH_03", "ท่องเที่ยว SANCTUARY_TRUTH #3", "Attraction SANCTUARY_TRUTH #3", "M", 1, "0939184831", 2, 12.9684, 100.8899, "attraction", 1, NOW(), NOW()),
(394, "MERCH_SANCTUARY_TRUTH_04", "ท่องเที่ยว SANCTUARY_TRUTH #4", "Attraction SANCTUARY_TRUTH #4", "S", 1, "0941419398", 2, 12.9691, 100.8912, "attraction", 1, NOW(), NOW()),
(395, "MERCH_SANCTUARY_TRUTH_05", "ท่องเที่ยว SANCTUARY_TRUTH #5", "Attraction SANCTUARY_TRUTH #5", "S", 1, "0952653965", 2, 12.9698, 100.8925, "attraction", 1, NOW(), NOW()),
(396, "MERCH_SANCTUARY_TRUTH_06", "ท่องเที่ยว SANCTUARY_TRUTH #6", "Attraction SANCTUARY_TRUTH #6", "S", 1, "0963888532", 2, 12.9705, 100.8938, "attraction", 1, NOW(), NOW()),
(397, "MERCH_SANCTUARY_TRUTH_07", "ท่องเที่ยว SANCTUARY_TRUTH #7", "Attraction SANCTUARY_TRUTH #7", "S", 1, "0975123099", 2, 12.9712, 100.885, "attraction", 1, NOW(), NOW()),
(398, "MERCH_SANCTUARY_TRUTH_08", "ท่องเที่ยว SANCTUARY_TRUTH #8", "Attraction SANCTUARY_TRUTH #8", "S", 1, "0986357666", 2, 12.9719, 100.8863, "attraction", 1, NOW(), NOW()),
(399, "MERCH_SANCTUARY_TRUTH_09", "ท่องเที่ยว SANCTUARY_TRUTH #9", "Attraction SANCTUARY_TRUTH #9", "S", 1, "0997592233", 2, 12.9726, 100.8876, "attraction", 1, NOW(), NOW()),
(400, "MERCH_SANCTUARY_TRUTH_10", "ท่องเที่ยว SANCTUARY_TRUTH #10", "Attraction SANCTUARY_TRUTH #10", "S", 1, "0808826800", 2, 12.9733, 100.8889, "attraction", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(401, "MERCH_ART_IN_PARADISE_01", "ท่องเที่ยว ART_IN_PARADISE #1", "Attraction ART_IN_PARADISE #1", "XL", 1, "0811061367", 2, 12.945, 100.8882, "attraction", 1, NOW(), NOW()),
(402, "MERCH_ART_IN_PARADISE_02", "ท่องเที่ยว ART_IN_PARADISE #2", "Attraction ART_IN_PARADISE #2", "E", 1, "0822295934", 2, 12.9457, 100.8895, "attraction", 1, NOW(), NOW()),
(403, "MERCH_ART_IN_PARADISE_03", "ท่องเที่ยว ART_IN_PARADISE #3", "Attraction ART_IN_PARADISE #3", "M", 1, "0833530501", 2, 12.9464, 100.8908, "attraction", 1, NOW(), NOW()),
(404, "MERCH_ART_IN_PARADISE_04", "ท่องเที่ยว ART_IN_PARADISE #4", "Attraction ART_IN_PARADISE #4", "S", 1, "0844765068", 2, 12.937, 100.882, "attraction", 1, NOW(), NOW()),
(405, "MERCH_ART_IN_PARADISE_05", "ท่องเที่ยว ART_IN_PARADISE #5", "Attraction ART_IN_PARADISE #5", "S", 1, "0855999635", 2, 12.9377, 100.8833, "attraction", 1, NOW(), NOW()),
(406, "MERCH_FROST_ICE_01", "ท่องเที่ยว FROST_ICE #1", "Attraction FROST_ICE #1", "XL", 1, "0867234202", 2, 12.9014, 100.8526, "attraction", 1, NOW(), NOW()),
(407, "MERCH_FROST_ICE_02", "ท่องเที่ยว FROST_ICE #2", "Attraction FROST_ICE #2", "E", 1, "0878468769", 2, 12.9021, 100.8539, "attraction", 1, NOW(), NOW()),
(408, "MERCH_FROST_ICE_03", "ท่องเที่ยว FROST_ICE #3", "Attraction FROST_ICE #3", "M", 1, "0889703336", 2, 12.9028, 100.8552, "attraction", 1, NOW(), NOW()),
(409, "MERCH_FROST_ICE_04", "ท่องเที่ยว FROST_ICE #4", "Attraction FROST_ICE #4", "S", 1, "0891937903", 2, 12.9035, 100.8565, "attraction", 1, NOW(), NOW()),
(410, "MERCH_FROST_ICE_05", "ท่องเที่ยว FROST_ICE #5", "Attraction FROST_ICE #5", "S", 1, "0903172470", 2, 12.9042, 100.8578, "attraction", 1, NOW(), NOW()),
(411, "MERCH_FROST_ICE_06", "ท่องเที่ยว FROST_ICE #6", "Attraction FROST_ICE #6", "S", 1, "0914407037", 2, 12.9049, 100.8591, "attraction", 1, NOW(), NOW()),
(412, "MERCH_VIEWPOINT_01", "ท่องเที่ยว VIEWPOINT #1", "Attraction VIEWPOINT #1", "XL", 1, "0925641604", 2, 12.9176, 100.8543, "attraction", 1, NOW(), NOW()),
(413, "MERCH_VIEWPOINT_02", "ท่องเที่ยว VIEWPOINT #2", "Attraction VIEWPOINT #2", "E", 1, "0936876171", 2, 12.9183, 100.8556, "attraction", 1, NOW(), NOW()),
(414, "MERCH_VIEWPOINT_03", "ท่องเที่ยว VIEWPOINT #3", "Attraction VIEWPOINT #3", "M", 1, "0948110738", 2, 12.919, 100.8569, "attraction", 1, NOW(), NOW()),
(415, "MERCH_VIEWPOINT_04", "ท่องเที่ยว VIEWPOINT #4", "Attraction VIEWPOINT #4", "S", 1, "0959345305", 2, 12.9197, 100.8582, "attraction", 1, NOW(), NOW()),
(416, "MERCH_VIEWPOINT_05", "ท่องเที่ยว VIEWPOINT #5", "Attraction VIEWPOINT #5", "S", 1, "0961579872", 2, 12.9204, 100.8595, "attraction", 1, NOW(), NOW()),
(417, "MERCH_VIEWPOINT_06", "ท่องเที่ยว VIEWPOINT #6", "Attraction VIEWPOINT #6", "S", 1, "0972814439", 2, 12.9211, 100.8608, "attraction", 1, NOW(), NOW()),
(418, "MERCH_VIEWPOINT_07", "ท่องเที่ยว VIEWPOINT #7", "Attraction VIEWPOINT #7", "S", 1, "0984049006", 2, 12.9218, 100.8621, "attraction", 1, NOW(), NOW()),
(419, "MERCH_BEACH_ROAD_01", "ชายหาด BEACH_ROAD #1", "Beach BEACH_ROAD #1", "XL", 1, "0995283573", 2, 12.9334, 100.8764, "beach", 1, NOW(), NOW()),
(420, "MERCH_BEACH_ROAD_02", "ชายหาด BEACH_ROAD #2", "Beach BEACH_ROAD #2", "E", 1, "0806518140", 2, 12.9341, 100.8676, "beach", 1, NOW(), NOW()),
(421, "MERCH_BEACH_ROAD_03", "ชายหาด BEACH_ROAD #3", "Beach BEACH_ROAD #3", "M", 1, "0817752707", 2, 12.9348, 100.8689, "beach", 1, NOW(), NOW()),
(422, "MERCH_BEACH_ROAD_04", "ชายหาด BEACH_ROAD #4", "Beach BEACH_ROAD #4", "S", 1, "0828987274", 2, 12.9355, 100.8702, "beach", 1, NOW(), NOW()),
(423, "MERCH_BEACH_ROAD_05", "ชายหาด BEACH_ROAD #5", "Beach BEACH_ROAD #5", "S", 1, "0831221841", 2, 12.9362, 100.8715, "beach", 1, NOW(), NOW()),
(424, "MERCH_BEACH_ROAD_06", "ชายหาด BEACH_ROAD #6", "Beach BEACH_ROAD #6", "S", 1, "0842456408", 2, 12.9369, 100.8728, "beach", 1, NOW(), NOW()),
(425, "MERCH_BEACH_ROAD_07", "ชายหาด BEACH_ROAD #7", "Beach BEACH_ROAD #7", "S", 1, "0853690975", 2, 12.9376, 100.8741, "beach", 1, NOW(), NOW()),
(426, "MERCH_BEACH_ROAD_08", "ชายหาด BEACH_ROAD #8", "Beach BEACH_ROAD #8", "S", 1, "0864925542", 2, 12.9383, 100.8754, "beach", 1, NOW(), NOW()),
(427, "MERCH_3MERMAIDS_01", "คาเฟ่ 3MERMAIDS #1", "Cafe 3MERMAIDS #1", "XL", 1, "0876160109", 2, 12.916, 100.8647, "cafe", 1, NOW(), NOW()),
(428, "MERCH_3MERMAIDS_02", "คาเฟ่ 3MERMAIDS #2", "Cafe 3MERMAIDS #2", "E", 1, "0887394676", 2, 12.9167, 100.8559, "cafe", 1, NOW(), NOW()),
(429, "MERCH_3MERMAIDS_03", "คาเฟ่ 3MERMAIDS #3", "Cafe 3MERMAIDS #3", "M", 1, "0898629243", 2, 12.9174, 100.8572, "cafe", 1, NOW(), NOW()),
(430, "MERCH_3MERMAIDS_04", "คาเฟ่ 3MERMAIDS #4", "Cafe 3MERMAIDS #4", "S", 1, "0909863810", 2, 12.9181, 100.8585, "cafe", 1, NOW(), NOW()),
(431, "MERCH_3MERMAIDS_05", "คาเฟ่ 3MERMAIDS #5", "Cafe 3MERMAIDS #5", "S", 1, "0912098377", 2, 12.9188, 100.8598, "cafe", 1, NOW(), NOW()),
(432, "MERCH_3MERMAIDS_06", "คาเฟ่ 3MERMAIDS #6", "Cafe 3MERMAIDS #6", "S", 1, "0923332944", 2, 12.9195, 100.8611, "cafe", 1, NOW(), NOW()),
(433, "MERCH_3MERMAIDS_07", "คาเฟ่ 3MERMAIDS #7", "Cafe 3MERMAIDS #7", "S", 1, "0934567511", 2, 12.9101, 100.8624, "cafe", 1, NOW(), NOW()),
(434, "MERCH_3MERMAIDS_08", "คาเฟ่ 3MERMAIDS #8", "Cafe 3MERMAIDS #8", "S", 1, "0945802078", 2, 12.9108, 100.8637, "cafe", 1, NOW(), NOW()),
(435, "MERCH_3MERMAIDS_09", "คาเฟ่ 3MERMAIDS #9", "Cafe 3MERMAIDS #9", "S", 1, "0957036645", 2, 12.9115, 100.865, "cafe", 1, NOW(), NOW()),
(436, "MERCH_SHELL_TANGKE_01", "ร้านอาหาร SHELL_TANGKE #1", "Restaurant SHELL_TANGKE #1", "XL", 1, "0968271212", 2, 12.9572, 100.8842, "restaurant", 1, NOW(), NOW()),
(437, "MERCH_SHELL_TANGKE_02", "ร้านอาหาร SHELL_TANGKE #2", "Restaurant SHELL_TANGKE #2", "E", 1, "0979505779", 2, 12.9579, 100.8855, "restaurant", 1, NOW(), NOW()),
(438, "MERCH_SHELL_TANGKE_03", "ร้านอาหาร SHELL_TANGKE #3", "Restaurant SHELL_TANGKE #3", "M", 1, "0981740346", 2, 12.9586, 100.8868, "restaurant", 1, NOW(), NOW()),
(439, "MERCH_SHELL_TANGKE_04", "ร้านอาหาร SHELL_TANGKE #4", "Restaurant SHELL_TANGKE #4", "S", 1, "0992974913", 2, 12.9593, 100.8881, "restaurant", 1, NOW(), NOW()),
(440, "MERCH_SHELL_TANGKE_05", "ร้านอาหาร SHELL_TANGKE #5", "Restaurant SHELL_TANGKE #5", "S", 1, "0804209480", 2, 12.96, 100.8894, "restaurant", 1, NOW(), NOW()),
(441, "MERCH_SHELL_TANGKE_06", "ร้านอาหาร SHELL_TANGKE #6", "Restaurant SHELL_TANGKE #6", "S", 1, "0815444047", 2, 12.9607, 100.8907, "restaurant", 1, NOW(), NOW()),
(442, "MERCH_SHELL_TANGKE_07", "ร้านอาหาร SHELL_TANGKE #7", "Restaurant SHELL_TANGKE #7", "S", 1, "0826678614", 2, 12.9614, 100.892, "restaurant", 1, NOW(), NOW()),
(443, "MERCH_SHELL_TANGKE_08", "ร้านอาหาร SHELL_TANGKE #8", "Restaurant SHELL_TANGKE #8", "S", 1, "0837913181", 2, 12.9621, 100.8832, "restaurant", 1, NOW(), NOW()),
(444, "MERCH_SHELL_TANGKE_09", "ร้านอาหาร SHELL_TANGKE #9", "Restaurant SHELL_TANGKE #9", "S", 1, "0849147748", 2, 12.9628, 100.8845, "restaurant", 1, NOW(), NOW()),
(445, "MERCH_SHELL_TANGKE_10", "ร้านอาหาร SHELL_TANGKE #10", "Restaurant SHELL_TANGKE #10", "S", 1, "0851382315", 2, 12.9635, 100.8858, "restaurant", 1, NOW(), NOW()),
(446, "MERCH_NAKLUA_SEAFOOD_01", "ร้านอาหาร NAKLUA_SEAFOOD #1", "Restaurant NAKLUA_SEAFOOD #1", "XL", 1, "0862616882", 2, 12.9662, 100.8881, "restaurant", 1, NOW(), NOW()),
(447, "MERCH_NAKLUA_SEAFOOD_02", "ร้านอาหาร NAKLUA_SEAFOOD #2", "Restaurant NAKLUA_SEAFOOD #2", "E", 1, "0873851449", 2, 12.9669, 100.8894, "restaurant", 1, NOW(), NOW()),
(448, "MERCH_NAKLUA_SEAFOOD_03", "ร้านอาหาร NAKLUA_SEAFOOD #3", "Restaurant NAKLUA_SEAFOOD #3", "M", 1, "0885086016", 2, 12.9575, 100.8907, "restaurant", 1, NOW(), NOW()),
(449, "MERCH_NAKLUA_SEAFOOD_04", "ร้านอาหาร NAKLUA_SEAFOOD #4", "Restaurant NAKLUA_SEAFOOD #4", "S", 1, "0896320583", 2, 12.9582, 100.892, "restaurant", 1, NOW(), NOW()),
(450, "MERCH_NAKLUA_SEAFOOD_05", "ร้านอาหาร NAKLUA_SEAFOOD #5", "Restaurant NAKLUA_SEAFOOD #5", "S", 1, "0907555150", 2, 12.9589, 100.8933, "restaurant", 1, NOW(), NOW());

INSERT INTO `merchant` (`merchant_id`, `merchant_code`, `merchant_name_th`, `merchant_name_en`, `default_tier_code`, `is_active`, `phone`, `price_level`, `lat`, `lng`, `service_tags`, `cluster_id`, `created_at`, `updated_at`) VALUES
(451, "MERCH_PEACH_01", "สถานที่จัดงาน PEACH #1", "Venue PEACH #1", "XL", 1, "0918789717", 2, 12.9076, 100.8535, "venue", 1, NOW(), NOW()),
(452, "MERCH_PEACH_02", "สถานที่จัดงาน PEACH #2", "Venue PEACH #2", "E", 1, "0921024284", 2, 12.9083, 100.8548, "venue", 1, NOW(), NOW()),
(453, "MERCH_PEACH_03", "สถานที่จัดงาน PEACH #3", "Venue PEACH #3", "M", 1, "0932258851", 2, 12.909, 100.8561, "venue", 1, NOW(), NOW()),
(454, "MERCH_PEACH_04", "สถานที่จัดงาน PEACH #4", "Venue PEACH #4", "S", 1, "0943493418", 2, 12.9097, 100.8574, "venue", 1, NOW(), NOW()),
(455, "MERCH_PEACH_05", "สถานที่จัดงาน PEACH #5", "Venue PEACH #5", "S", 1, "0954727985", 2, 12.9104, 100.8587, "venue", 1, NOW(), NOW()),
(456, "MERCH_PEACH_06", "สถานที่จัดงาน PEACH #6", "Venue PEACH #6", "S", 1, "0965962552", 2, 12.9111, 100.86, "venue", 1, NOW(), NOW()),
(457, "MERCH_ROYAL_CLIFF_01", "โรงแรม ROYAL_CLIFF #1", "Hotel ROYAL_CLIFF #1", "XL", 1, "0977197119", 2, 12.9118, 100.8603, "hotel", 1, NOW(), NOW()),
(458, "MERCH_ROYAL_CLIFF_02", "โรงแรม ROYAL_CLIFF #2", "Hotel ROYAL_CLIFF #2", "E", 1, "0988431686", 2, 12.9125, 100.8616, "hotel", 1, NOW(), NOW()),
(459, "MERCH_ROYAL_CLIFF_03", "โรงแรม ROYAL_CLIFF #3", "Hotel ROYAL_CLIFF #3", "M", 1, "0999666253", 2, 12.9132, 100.8528, "hotel", 1, NOW(), NOW()),
(460, "MERCH_ROYAL_CLIFF_04", "โรงแรม ROYAL_CLIFF #4", "Hotel ROYAL_CLIFF #4", "S", 1, "0801900820", 2, 12.9139, 100.8541, "hotel", 1, NOW(), NOW()),
(461, "MERCH_ROYAL_CLIFF_05", "โรงแรม ROYAL_CLIFF #5", "Hotel ROYAL_CLIFF #5", "S", 1, "0813135387", 2, 12.9146, 100.8554, "hotel", 1, NOW(), NOW()),
(462, "MERCH_ROYAL_CLIFF_06", "โรงแรม ROYAL_CLIFF #6", "Hotel ROYAL_CLIFF #6", "S", 1, "0824369954", 2, 12.9052, 100.8567, "hotel", 1, NOW(), NOW()),
(463, "MERCH_ROYAL_CLIFF_07", "โรงแรม ROYAL_CLIFF #7", "Hotel ROYAL_CLIFF #7", "S", 1, "0835604521", 2, 12.9059, 100.858, "hotel", 1, NOW(), NOW());


-- ── 5.20 Place-Merchant Links (463 rows) ──
INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NOW(), NOW()),
(1, 2, 0, 2, NOW(), NOW()),
(1, 3, 0, 3, NOW(), NOW()),
(1, 4, 0, 4, NOW(), NOW()),
(1, 5, 0, 5, NOW(), NOW()),
(1, 6, 0, 6, NOW(), NOW()),
(2, 7, 1, 1, NOW(), NOW()),
(2, 8, 0, 2, NOW(), NOW()),
(2, 9, 0, 3, NOW(), NOW()),
(2, 10, 0, 4, NOW(), NOW()),
(2, 11, 0, 5, NOW(), NOW()),
(2, 12, 0, 6, NOW(), NOW()),
(2, 13, 0, 7, NOW(), NOW()),
(3, 14, 1, 1, NOW(), NOW()),
(3, 15, 0, 2, NOW(), NOW()),
(3, 16, 0, 3, NOW(), NOW()),
(3, 17, 0, 4, NOW(), NOW()),
(3, 18, 0, 5, NOW(), NOW()),
(3, 19, 0, 6, NOW(), NOW()),
(3, 20, 0, 7, NOW(), NOW()),
(3, 21, 0, 8, NOW(), NOW()),
(4, 22, 1, 1, NOW(), NOW()),
(4, 23, 0, 2, NOW(), NOW()),
(4, 24, 0, 3, NOW(), NOW()),
(4, 25, 0, 4, NOW(), NOW()),
(4, 26, 0, 5, NOW(), NOW()),
(4, 27, 0, 6, NOW(), NOW()),
(4, 28, 0, 7, NOW(), NOW()),
(4, 29, 0, 8, NOW(), NOW()),
(4, 30, 0, 9, NOW(), NOW()),
(5, 31, 1, 1, NOW(), NOW()),
(5, 32, 0, 2, NOW(), NOW()),
(5, 33, 0, 3, NOW(), NOW()),
(5, 34, 0, 4, NOW(), NOW()),
(5, 35, 0, 5, NOW(), NOW()),
(5, 36, 0, 6, NOW(), NOW()),
(5, 37, 0, 7, NOW(), NOW()),
(5, 38, 0, 8, NOW(), NOW()),
(5, 39, 0, 9, NOW(), NOW()),
(5, 40, 0, 10, NOW(), NOW()),
(6, 41, 1, 1, NOW(), NOW()),
(6, 42, 0, 2, NOW(), NOW()),
(6, 43, 0, 3, NOW(), NOW()),
(6, 44, 0, 4, NOW(), NOW()),
(6, 45, 0, 5, NOW(), NOW()),
(7, 46, 1, 1, NOW(), NOW()),
(7, 47, 0, 2, NOW(), NOW()),
(7, 48, 0, 3, NOW(), NOW()),
(7, 49, 0, 4, NOW(), NOW()),
(7, 50, 0, 5, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(7, 51, 0, 6, NOW(), NOW()),
(8, 52, 1, 1, NOW(), NOW()),
(8, 53, 0, 2, NOW(), NOW()),
(8, 54, 0, 3, NOW(), NOW()),
(8, 55, 0, 4, NOW(), NOW()),
(8, 56, 0, 5, NOW(), NOW()),
(8, 57, 0, 6, NOW(), NOW()),
(8, 58, 0, 7, NOW(), NOW()),
(9, 59, 1, 1, NOW(), NOW()),
(9, 60, 0, 2, NOW(), NOW()),
(9, 61, 0, 3, NOW(), NOW()),
(9, 62, 0, 4, NOW(), NOW()),
(9, 63, 0, 5, NOW(), NOW()),
(9, 64, 0, 6, NOW(), NOW()),
(9, 65, 0, 7, NOW(), NOW()),
(9, 66, 0, 8, NOW(), NOW()),
(10, 67, 1, 1, NOW(), NOW()),
(10, 68, 0, 2, NOW(), NOW()),
(10, 69, 0, 3, NOW(), NOW()),
(10, 70, 0, 4, NOW(), NOW()),
(10, 71, 0, 5, NOW(), NOW()),
(10, 72, 0, 6, NOW(), NOW()),
(10, 73, 0, 7, NOW(), NOW()),
(10, 74, 0, 8, NOW(), NOW()),
(10, 75, 0, 9, NOW(), NOW()),
(11, 76, 1, 1, NOW(), NOW()),
(11, 77, 0, 2, NOW(), NOW()),
(11, 78, 0, 3, NOW(), NOW()),
(11, 79, 0, 4, NOW(), NOW()),
(11, 80, 0, 5, NOW(), NOW()),
(11, 81, 0, 6, NOW(), NOW()),
(11, 82, 0, 7, NOW(), NOW()),
(11, 83, 0, 8, NOW(), NOW()),
(11, 84, 0, 9, NOW(), NOW()),
(11, 85, 0, 10, NOW(), NOW()),
(12, 86, 1, 1, NOW(), NOW()),
(12, 87, 0, 2, NOW(), NOW()),
(12, 88, 0, 3, NOW(), NOW()),
(12, 89, 0, 4, NOW(), NOW()),
(12, 90, 0, 5, NOW(), NOW()),
(13, 91, 1, 1, NOW(), NOW()),
(13, 92, 0, 2, NOW(), NOW()),
(13, 93, 0, 3, NOW(), NOW()),
(13, 94, 0, 4, NOW(), NOW()),
(13, 95, 0, 5, NOW(), NOW()),
(13, 96, 0, 6, NOW(), NOW()),
(14, 97, 1, 1, NOW(), NOW()),
(14, 98, 0, 2, NOW(), NOW()),
(14, 99, 0, 3, NOW(), NOW()),
(14, 100, 0, 4, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(14, 101, 0, 5, NOW(), NOW()),
(14, 102, 0, 6, NOW(), NOW()),
(14, 103, 0, 7, NOW(), NOW()),
(15, 104, 1, 1, NOW(), NOW()),
(15, 105, 0, 2, NOW(), NOW()),
(15, 106, 0, 3, NOW(), NOW()),
(15, 107, 0, 4, NOW(), NOW()),
(15, 108, 0, 5, NOW(), NOW()),
(15, 109, 0, 6, NOW(), NOW()),
(15, 110, 0, 7, NOW(), NOW()),
(15, 111, 0, 8, NOW(), NOW()),
(16, 112, 1, 1, NOW(), NOW()),
(16, 113, 0, 2, NOW(), NOW()),
(16, 114, 0, 3, NOW(), NOW()),
(16, 115, 0, 4, NOW(), NOW()),
(16, 116, 0, 5, NOW(), NOW()),
(16, 117, 0, 6, NOW(), NOW()),
(16, 118, 0, 7, NOW(), NOW()),
(16, 119, 0, 8, NOW(), NOW()),
(16, 120, 0, 9, NOW(), NOW()),
(17, 121, 1, 1, NOW(), NOW()),
(17, 122, 0, 2, NOW(), NOW()),
(17, 123, 0, 3, NOW(), NOW()),
(17, 124, 0, 4, NOW(), NOW()),
(17, 125, 0, 5, NOW(), NOW()),
(17, 126, 0, 6, NOW(), NOW()),
(17, 127, 0, 7, NOW(), NOW()),
(17, 128, 0, 8, NOW(), NOW()),
(17, 129, 0, 9, NOW(), NOW()),
(17, 130, 0, 10, NOW(), NOW()),
(18, 131, 1, 1, NOW(), NOW()),
(18, 132, 0, 2, NOW(), NOW()),
(18, 133, 0, 3, NOW(), NOW()),
(18, 134, 0, 4, NOW(), NOW()),
(18, 135, 0, 5, NOW(), NOW()),
(19, 136, 1, 1, NOW(), NOW()),
(19, 137, 0, 2, NOW(), NOW()),
(19, 138, 0, 3, NOW(), NOW()),
(19, 139, 0, 4, NOW(), NOW()),
(19, 140, 0, 5, NOW(), NOW()),
(19, 141, 0, 6, NOW(), NOW()),
(20, 142, 1, 1, NOW(), NOW()),
(20, 143, 0, 2, NOW(), NOW()),
(20, 144, 0, 3, NOW(), NOW()),
(20, 145, 0, 4, NOW(), NOW()),
(20, 146, 0, 5, NOW(), NOW()),
(20, 147, 0, 6, NOW(), NOW()),
(20, 148, 0, 7, NOW(), NOW()),
(21, 149, 1, 1, NOW(), NOW()),
(21, 150, 0, 2, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(21, 151, 0, 3, NOW(), NOW()),
(21, 152, 0, 4, NOW(), NOW()),
(21, 153, 0, 5, NOW(), NOW()),
(21, 154, 0, 6, NOW(), NOW()),
(21, 155, 0, 7, NOW(), NOW()),
(21, 156, 0, 8, NOW(), NOW()),
(22, 157, 1, 1, NOW(), NOW()),
(22, 158, 0, 2, NOW(), NOW()),
(22, 159, 0, 3, NOW(), NOW()),
(22, 160, 0, 4, NOW(), NOW()),
(22, 161, 0, 5, NOW(), NOW()),
(22, 162, 0, 6, NOW(), NOW()),
(22, 163, 0, 7, NOW(), NOW()),
(22, 164, 0, 8, NOW(), NOW()),
(22, 165, 0, 9, NOW(), NOW()),
(23, 166, 1, 1, NOW(), NOW()),
(23, 167, 0, 2, NOW(), NOW()),
(23, 168, 0, 3, NOW(), NOW()),
(23, 169, 0, 4, NOW(), NOW()),
(23, 170, 0, 5, NOW(), NOW()),
(23, 171, 0, 6, NOW(), NOW()),
(23, 172, 0, 7, NOW(), NOW()),
(23, 173, 0, 8, NOW(), NOW()),
(23, 174, 0, 9, NOW(), NOW()),
(23, 175, 0, 10, NOW(), NOW()),
(24, 176, 1, 1, NOW(), NOW()),
(24, 177, 0, 2, NOW(), NOW()),
(24, 178, 0, 3, NOW(), NOW()),
(24, 179, 0, 4, NOW(), NOW()),
(24, 180, 0, 5, NOW(), NOW()),
(25, 181, 1, 1, NOW(), NOW()),
(25, 182, 0, 2, NOW(), NOW()),
(25, 183, 0, 3, NOW(), NOW()),
(25, 184, 0, 4, NOW(), NOW()),
(25, 185, 0, 5, NOW(), NOW()),
(25, 186, 0, 6, NOW(), NOW()),
(26, 187, 1, 1, NOW(), NOW()),
(26, 188, 0, 2, NOW(), NOW()),
(26, 189, 0, 3, NOW(), NOW()),
(26, 190, 0, 4, NOW(), NOW()),
(26, 191, 0, 5, NOW(), NOW()),
(26, 192, 0, 6, NOW(), NOW()),
(26, 193, 0, 7, NOW(), NOW()),
(27, 194, 1, 1, NOW(), NOW()),
(27, 195, 0, 2, NOW(), NOW()),
(27, 196, 0, 3, NOW(), NOW()),
(27, 197, 0, 4, NOW(), NOW()),
(27, 198, 0, 5, NOW(), NOW()),
(27, 199, 0, 6, NOW(), NOW()),
(27, 200, 0, 7, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(27, 201, 0, 8, NOW(), NOW()),
(28, 202, 1, 1, NOW(), NOW()),
(28, 203, 0, 2, NOW(), NOW()),
(28, 204, 0, 3, NOW(), NOW()),
(28, 205, 0, 4, NOW(), NOW()),
(28, 206, 0, 5, NOW(), NOW()),
(28, 207, 0, 6, NOW(), NOW()),
(28, 208, 0, 7, NOW(), NOW()),
(28, 209, 0, 8, NOW(), NOW()),
(28, 210, 0, 9, NOW(), NOW()),
(29, 211, 1, 1, NOW(), NOW()),
(29, 212, 0, 2, NOW(), NOW()),
(29, 213, 0, 3, NOW(), NOW()),
(29, 214, 0, 4, NOW(), NOW()),
(29, 215, 0, 5, NOW(), NOW()),
(29, 216, 0, 6, NOW(), NOW()),
(29, 217, 0, 7, NOW(), NOW()),
(29, 218, 0, 8, NOW(), NOW()),
(29, 219, 0, 9, NOW(), NOW()),
(29, 220, 0, 10, NOW(), NOW()),
(30, 221, 1, 1, NOW(), NOW()),
(30, 222, 0, 2, NOW(), NOW()),
(30, 223, 0, 3, NOW(), NOW()),
(30, 224, 0, 4, NOW(), NOW()),
(30, 225, 0, 5, NOW(), NOW()),
(31, 226, 1, 1, NOW(), NOW()),
(31, 227, 0, 2, NOW(), NOW()),
(31, 228, 0, 3, NOW(), NOW()),
(31, 229, 0, 4, NOW(), NOW()),
(31, 230, 0, 5, NOW(), NOW()),
(31, 231, 0, 6, NOW(), NOW()),
(32, 232, 1, 1, NOW(), NOW()),
(32, 233, 0, 2, NOW(), NOW()),
(32, 234, 0, 3, NOW(), NOW()),
(32, 235, 0, 4, NOW(), NOW()),
(32, 236, 0, 5, NOW(), NOW()),
(32, 237, 0, 6, NOW(), NOW()),
(32, 238, 0, 7, NOW(), NOW()),
(33, 239, 1, 1, NOW(), NOW()),
(33, 240, 0, 2, NOW(), NOW()),
(33, 241, 0, 3, NOW(), NOW()),
(33, 242, 0, 4, NOW(), NOW()),
(33, 243, 0, 5, NOW(), NOW()),
(33, 244, 0, 6, NOW(), NOW()),
(33, 245, 0, 7, NOW(), NOW()),
(33, 246, 0, 8, NOW(), NOW()),
(34, 247, 1, 1, NOW(), NOW()),
(34, 248, 0, 2, NOW(), NOW()),
(34, 249, 0, 3, NOW(), NOW()),
(34, 250, 0, 4, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(34, 251, 0, 5, NOW(), NOW()),
(34, 252, 0, 6, NOW(), NOW()),
(34, 253, 0, 7, NOW(), NOW()),
(34, 254, 0, 8, NOW(), NOW()),
(34, 255, 0, 9, NOW(), NOW()),
(35, 256, 1, 1, NOW(), NOW()),
(35, 257, 0, 2, NOW(), NOW()),
(35, 258, 0, 3, NOW(), NOW()),
(35, 259, 0, 4, NOW(), NOW()),
(35, 260, 0, 5, NOW(), NOW()),
(35, 261, 0, 6, NOW(), NOW()),
(35, 262, 0, 7, NOW(), NOW()),
(35, 263, 0, 8, NOW(), NOW()),
(35, 264, 0, 9, NOW(), NOW()),
(35, 265, 0, 10, NOW(), NOW()),
(36, 266, 1, 1, NOW(), NOW()),
(36, 267, 0, 2, NOW(), NOW()),
(36, 268, 0, 3, NOW(), NOW()),
(36, 269, 0, 4, NOW(), NOW()),
(36, 270, 0, 5, NOW(), NOW()),
(37, 271, 1, 1, NOW(), NOW()),
(37, 272, 0, 2, NOW(), NOW()),
(37, 273, 0, 3, NOW(), NOW()),
(37, 274, 0, 4, NOW(), NOW()),
(37, 275, 0, 5, NOW(), NOW()),
(37, 276, 0, 6, NOW(), NOW()),
(38, 277, 1, 1, NOW(), NOW()),
(38, 278, 0, 2, NOW(), NOW()),
(38, 279, 0, 3, NOW(), NOW()),
(38, 280, 0, 4, NOW(), NOW()),
(38, 281, 0, 5, NOW(), NOW()),
(38, 282, 0, 6, NOW(), NOW()),
(38, 283, 0, 7, NOW(), NOW()),
(39, 284, 1, 1, NOW(), NOW()),
(39, 285, 0, 2, NOW(), NOW()),
(39, 286, 0, 3, NOW(), NOW()),
(39, 287, 0, 4, NOW(), NOW()),
(39, 288, 0, 5, NOW(), NOW()),
(39, 289, 0, 6, NOW(), NOW()),
(39, 290, 0, 7, NOW(), NOW()),
(39, 291, 0, 8, NOW(), NOW()),
(40, 292, 1, 1, NOW(), NOW()),
(40, 293, 0, 2, NOW(), NOW()),
(40, 294, 0, 3, NOW(), NOW()),
(40, 295, 0, 4, NOW(), NOW()),
(40, 296, 0, 5, NOW(), NOW()),
(40, 297, 0, 6, NOW(), NOW()),
(40, 298, 0, 7, NOW(), NOW()),
(40, 299, 0, 8, NOW(), NOW()),
(40, 300, 0, 9, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(41, 301, 1, 1, NOW(), NOW()),
(41, 302, 0, 2, NOW(), NOW()),
(41, 303, 0, 3, NOW(), NOW()),
(41, 304, 0, 4, NOW(), NOW()),
(41, 305, 0, 5, NOW(), NOW()),
(41, 306, 0, 6, NOW(), NOW()),
(41, 307, 0, 7, NOW(), NOW()),
(41, 308, 0, 8, NOW(), NOW()),
(41, 309, 0, 9, NOW(), NOW()),
(41, 310, 0, 10, NOW(), NOW()),
(42, 311, 1, 1, NOW(), NOW()),
(42, 312, 0, 2, NOW(), NOW()),
(42, 313, 0, 3, NOW(), NOW()),
(42, 314, 0, 4, NOW(), NOW()),
(42, 315, 0, 5, NOW(), NOW()),
(43, 316, 1, 1, NOW(), NOW()),
(43, 317, 0, 2, NOW(), NOW()),
(43, 318, 0, 3, NOW(), NOW()),
(43, 319, 0, 4, NOW(), NOW()),
(43, 320, 0, 5, NOW(), NOW()),
(43, 321, 0, 6, NOW(), NOW()),
(44, 322, 1, 1, NOW(), NOW()),
(44, 323, 0, 2, NOW(), NOW()),
(44, 324, 0, 3, NOW(), NOW()),
(44, 325, 0, 4, NOW(), NOW()),
(44, 326, 0, 5, NOW(), NOW()),
(44, 327, 0, 6, NOW(), NOW()),
(44, 328, 0, 7, NOW(), NOW()),
(45, 329, 1, 1, NOW(), NOW()),
(45, 330, 0, 2, NOW(), NOW()),
(45, 331, 0, 3, NOW(), NOW()),
(45, 332, 0, 4, NOW(), NOW()),
(45, 333, 0, 5, NOW(), NOW()),
(45, 334, 0, 6, NOW(), NOW()),
(45, 335, 0, 7, NOW(), NOW()),
(45, 336, 0, 8, NOW(), NOW()),
(46, 337, 1, 1, NOW(), NOW()),
(46, 338, 0, 2, NOW(), NOW()),
(46, 339, 0, 3, NOW(), NOW()),
(46, 340, 0, 4, NOW(), NOW()),
(46, 341, 0, 5, NOW(), NOW()),
(46, 342, 0, 6, NOW(), NOW()),
(46, 343, 0, 7, NOW(), NOW()),
(46, 344, 0, 8, NOW(), NOW()),
(46, 345, 0, 9, NOW(), NOW()),
(47, 346, 1, 1, NOW(), NOW()),
(47, 347, 0, 2, NOW(), NOW()),
(47, 348, 0, 3, NOW(), NOW()),
(47, 349, 0, 4, NOW(), NOW()),
(47, 350, 0, 5, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(47, 351, 0, 6, NOW(), NOW()),
(47, 352, 0, 7, NOW(), NOW()),
(47, 353, 0, 8, NOW(), NOW()),
(47, 354, 0, 9, NOW(), NOW()),
(47, 355, 0, 10, NOW(), NOW()),
(48, 356, 1, 1, NOW(), NOW()),
(48, 357, 0, 2, NOW(), NOW()),
(48, 358, 0, 3, NOW(), NOW()),
(48, 359, 0, 4, NOW(), NOW()),
(48, 360, 0, 5, NOW(), NOW()),
(49, 361, 1, 1, NOW(), NOW()),
(49, 362, 0, 2, NOW(), NOW()),
(49, 363, 0, 3, NOW(), NOW()),
(49, 364, 0, 4, NOW(), NOW()),
(49, 365, 0, 5, NOW(), NOW()),
(49, 366, 0, 6, NOW(), NOW()),
(50, 367, 1, 1, NOW(), NOW()),
(50, 368, 0, 2, NOW(), NOW()),
(50, 369, 0, 3, NOW(), NOW()),
(50, 370, 0, 4, NOW(), NOW()),
(50, 371, 0, 5, NOW(), NOW()),
(50, 372, 0, 6, NOW(), NOW()),
(50, 373, 0, 7, NOW(), NOW()),
(51, 374, 1, 1, NOW(), NOW()),
(51, 375, 0, 2, NOW(), NOW()),
(51, 376, 0, 3, NOW(), NOW()),
(51, 377, 0, 4, NOW(), NOW()),
(51, 378, 0, 5, NOW(), NOW()),
(51, 379, 0, 6, NOW(), NOW()),
(51, 380, 0, 7, NOW(), NOW()),
(51, 381, 0, 8, NOW(), NOW()),
(52, 382, 1, 1, NOW(), NOW()),
(52, 383, 0, 2, NOW(), NOW()),
(52, 384, 0, 3, NOW(), NOW()),
(52, 385, 0, 4, NOW(), NOW()),
(52, 386, 0, 5, NOW(), NOW()),
(52, 387, 0, 6, NOW(), NOW()),
(52, 388, 0, 7, NOW(), NOW()),
(52, 389, 0, 8, NOW(), NOW()),
(52, 390, 0, 9, NOW(), NOW()),
(53, 391, 1, 1, NOW(), NOW()),
(53, 392, 0, 2, NOW(), NOW()),
(53, 393, 0, 3, NOW(), NOW()),
(53, 394, 0, 4, NOW(), NOW()),
(53, 395, 0, 5, NOW(), NOW()),
(53, 396, 0, 6, NOW(), NOW()),
(53, 397, 0, 7, NOW(), NOW()),
(53, 398, 0, 8, NOW(), NOW()),
(53, 399, 0, 9, NOW(), NOW()),
(53, 400, 0, 10, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(54, 401, 1, 1, NOW(), NOW()),
(54, 402, 0, 2, NOW(), NOW()),
(54, 403, 0, 3, NOW(), NOW()),
(54, 404, 0, 4, NOW(), NOW()),
(54, 405, 0, 5, NOW(), NOW()),
(55, 406, 1, 1, NOW(), NOW()),
(55, 407, 0, 2, NOW(), NOW()),
(55, 408, 0, 3, NOW(), NOW()),
(55, 409, 0, 4, NOW(), NOW()),
(55, 410, 0, 5, NOW(), NOW()),
(55, 411, 0, 6, NOW(), NOW()),
(56, 412, 1, 1, NOW(), NOW()),
(56, 413, 0, 2, NOW(), NOW()),
(56, 414, 0, 3, NOW(), NOW()),
(56, 415, 0, 4, NOW(), NOW()),
(56, 416, 0, 5, NOW(), NOW()),
(56, 417, 0, 6, NOW(), NOW()),
(56, 418, 0, 7, NOW(), NOW()),
(57, 419, 1, 1, NOW(), NOW()),
(57, 420, 0, 2, NOW(), NOW()),
(57, 421, 0, 3, NOW(), NOW()),
(57, 422, 0, 4, NOW(), NOW()),
(57, 423, 0, 5, NOW(), NOW()),
(57, 424, 0, 6, NOW(), NOW()),
(57, 425, 0, 7, NOW(), NOW()),
(57, 426, 0, 8, NOW(), NOW()),
(58, 427, 1, 1, NOW(), NOW()),
(58, 428, 0, 2, NOW(), NOW()),
(58, 429, 0, 3, NOW(), NOW()),
(58, 430, 0, 4, NOW(), NOW()),
(58, 431, 0, 5, NOW(), NOW()),
(58, 432, 0, 6, NOW(), NOW()),
(58, 433, 0, 7, NOW(), NOW()),
(58, 434, 0, 8, NOW(), NOW()),
(58, 435, 0, 9, NOW(), NOW()),
(59, 436, 1, 1, NOW(), NOW()),
(59, 437, 0, 2, NOW(), NOW()),
(59, 438, 0, 3, NOW(), NOW()),
(59, 439, 0, 4, NOW(), NOW()),
(59, 440, 0, 5, NOW(), NOW()),
(59, 441, 0, 6, NOW(), NOW()),
(59, 442, 0, 7, NOW(), NOW()),
(59, 443, 0, 8, NOW(), NOW()),
(59, 444, 0, 9, NOW(), NOW()),
(59, 445, 0, 10, NOW(), NOW()),
(60, 446, 1, 1, NOW(), NOW()),
(60, 447, 0, 2, NOW(), NOW()),
(60, 448, 0, 3, NOW(), NOW()),
(60, 449, 0, 4, NOW(), NOW()),
(60, 450, 0, 5, NOW(), NOW());

INSERT INTO `place_merchant` (`place_id`, `merchant_id`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(61, 451, 1, 1, NOW(), NOW()),
(61, 452, 0, 2, NOW(), NOW()),
(61, 453, 0, 3, NOW(), NOW()),
(61, 454, 0, 4, NOW(), NOW()),
(61, 455, 0, 5, NOW(), NOW()),
(61, 456, 0, 6, NOW(), NOW()),
(62, 457, 1, 1, NOW(), NOW()),
(62, 458, 0, 2, NOW(), NOW()),
(62, 459, 0, 3, NOW(), NOW()),
(62, 460, 0, 4, NOW(), NOW()),
(62, 461, 0, 5, NOW(), NOW()),
(62, 462, 0, 6, NOW(), NOW()),
(62, 463, 0, 7, NOW(), NOW());


-- ============================================================
-- FINALIZE
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================================
-- VERIFICATION QUERIES (run after import)
-- ============================================================
-- SELECT COUNT(*) AS total_tables FROM information_schema.tables WHERE table_schema = 'u504097778_platform';
-- SELECT 'countries' AS tbl, COUNT(*) AS cnt FROM countries
-- UNION ALL SELECT 'clusters', COUNT(*) FROM clusters
-- UNION ALL SELECT 'applications', COUNT(*) FROM applications
-- UNION ALL SELECT 'modules', COUNT(*) FROM modules
-- UNION ALL SELECT 'journey', COUNT(*) FROM journey
-- UNION ALL SELECT 'place', COUNT(*) FROM place
-- UNION ALL SELECT 'journey_step', COUNT(*) FROM journey_step
-- UNION ALL SELECT 'merchant', COUNT(*) FROM merchant
-- UNION ALL SELECT 'place_merchant', COUNT(*) FROM place_merchant
-- UNION ALL SELECT 'users', COUNT(*) FROM users;
-- SELECT * FROM vw_api_journey_onecall_with_merchants_stats_final LIMIT 5;
