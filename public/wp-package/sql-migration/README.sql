-- ============================================================
-- Ngon Thi Hoa WordPress - Custom Database Tables v2.0
-- ============================================================
-- Run these queries AFTER activating the plugin (plugin creates
-- the tables automatically on activation). Use these scripts
-- for manual setup, backup restore, or dev environment seeding.
-- ============================================================

-- NOTE: Replace `wp_` with your actual WordPress table prefix.

-- ── 1. Reservations ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `wp_nth_reservations` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(30) NOT NULL,
    `reservation_date` date NOT NULL,
    `reservation_time` time NOT NULL,
    `number_of_guests` int(11) NOT NULL DEFAULT 1,
    `notes` text DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'pending',
    `language` varchar(5) DEFAULT 'vi',
    `ip_address` varchar(45) DEFAULT NULL,
    `source` varchar(50) DEFAULT 'website',
    `admin_notes` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `reservation_date` (`reservation_date`),
    KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2. Contact Submissions ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `wp_nth_contacts` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(30) DEFAULT NULL,
    `subject` varchar(200) DEFAULT NULL,
    `message` text NOT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'unread',
    `language` varchar(5) DEFAULT 'vi',
    `ip_address` varchar(45) DEFAULT NULL,
    `admin_notes` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3. Job Applications ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `wp_nth_applications` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `applicant_name` varchar(100) NOT NULL,
    `applicant_email` varchar(100) NOT NULL,
    `applicant_phone` varchar(30) DEFAULT NULL,
    `job_id` bigint(20) DEFAULT NULL,
    `job_title` varchar(200) DEFAULT NULL,
    `cv_file` varchar(500) DEFAULT NULL,
    `cover_letter` text DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'pending',
    `language` varchar(5) DEFAULT 'vi',
    `ip_address` varchar(45) DEFAULT NULL,
    `admin_notes` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `job_id` (`job_id`),
    KEY `applicant_email` (`applicant_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 4. Analytics (optional — internal page views) ────────────────────────────
CREATE TABLE IF NOT EXISTS `wp_nth_analytics` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `page_url` varchar(500) NOT NULL,
    `page_type` varchar(50) DEFAULT 'page',
    `referrer` varchar(500) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `language` varchar(5) DEFAULT 'vi',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `page_url` (`page_url`(191)),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Useful queries ────────────────────────────────────────────────────────────

-- Count pending reservations today
-- SELECT COUNT(*) FROM wp_nth_reservations
-- WHERE DATE(reservation_date) = CURDATE() AND status = 'pending';

-- Monthly reservation summary
-- SELECT DATE_FORMAT(reservation_date, '%Y-%m') as month,
--        COUNT(*) as bookings, SUM(number_of_guests) as guests
-- FROM wp_nth_reservations
-- WHERE status != 'cancelled'
-- GROUP BY month ORDER BY month DESC;

-- Export all reservations
-- SELECT name, email, phone, reservation_date, reservation_time,
--        number_of_guests, notes, status, created_at
-- FROM wp_nth_reservations ORDER BY reservation_date DESC;
