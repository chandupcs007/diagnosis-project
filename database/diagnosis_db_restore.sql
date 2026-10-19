-- ============================================================
-- Diagnosis Project - Database Restore Script
-- Database: diagnosis_db
-- Generated from Laravel migrations + seeders
-- ============================================================

CREATE DATABASE IF NOT EXISTS `diagnosis_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `diagnosis_db`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `visits`;
DROP TABLE IF EXISTS `persons`;
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `migrations`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- users (+ auth tables from default Laravel migration)
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `specialization` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text,
  `qualification` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- persons (patients)
-- ------------------------------------------------------------
CREATE TABLE `persons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(255) DEFAULT NULL,
  `medical_history` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- menu_items (sidebar navigation)
-- ------------------------------------------------------------
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `parent_id` int DEFAULT 0,
  `order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- visits (patient diagnosis visits)
-- ------------------------------------------------------------
CREATE TABLE `visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visit_number` varchar(255) NOT NULL,
  `patient_id` bigint unsigned NOT NULL,
  `doctor_id` bigint unsigned NOT NULL,
  `department` varchar(255) NOT NULL,
  `symptoms` text,
  `diagnosis` text,
  `prescription` text,
  `status` enum('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visits_visit_number_unique` (`visit_number`),
  KEY `visits_patient_id_foreign` (`patient_id`),
  KEY `visits_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `persons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `visits_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- cache, jobs (required by Laravel config)
-- ------------------------------------------------------------
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- Password for all doctor accounts: password123
-- ============================================================

INSERT INTO `users` (`name`, `email`, `password`, `role`, `specialization`, `phone`, `qualification`, `address`, `email_verified_at`, `created_at`, `updated_at`) VALUES
('Dr. Sarah Johnson', 'sarah.johnson@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Cardiology', '+1-555-0101', 'MD, Cardiology', '123 Medical Center, Healthcare City', NOW(), NOW(), NOW()),
('Dr. Michael Chen', 'michael.chen@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Neurology', '+1-555-0102', 'MD, Neurology, PhD', '456 Brain Center, Neuro Street', NOW(), NOW(), NOW()),
('Dr. Emily Rodriguez', 'emily.rodriguez@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Pediatrics', '+1-555-0103', 'MD, Pediatrics', '789 Children Hospital, Kids Avenue', NOW(), NOW(), NOW()),
('Dr. James Wilson', 'james.wilson@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Orthopedics', '+1-555-0104', 'MD, Orthopedic Surgery', '321 Bone Center, Joint Street', NOW(), NOW(), NOW()),
('Dr. Lisa Thompson', 'lisa.thompson@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Dermatology', '+1-555-0105', 'MD, Dermatology', '654 Skin Clinic, Beauty Road', NOW(), NOW(), NOW()),
('Dr. Robert Brown', 'robert.brown@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'General Medicine', '+1-555-0106', 'MD, General Medicine', '987 Main Hospital, General Street', NOW(), NOW(), NOW()),
('Dr. Amanda Davis', 'amanda.davis@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Ophthalmology', '+1-555-0107', 'MD, Ophthalmology', '147 Vision Center, Eye Street', NOW(), NOW(), NOW()),
('Dr. David Miller', 'david.miller@hospital.com', '$2y$10$ZqCPOi0dW2AgybBiRVS8yu2gHkb4e4/Xme7gIcH23kha/esyGl9Ei', 'doctor', 'Dentistry', '+1-555-0108', 'DDS, Dental Surgery', '258 Dental Clinic, Smile Avenue', NOW(), NOW(), NOW());

INSERT INTO `menu_items` (`name`, `icon`, `route`, `parent_id`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
('Dashboard', 'fas fa-fw fa-tachometer-alt', 'dashboard', 0, 1, 1, NOW(), NOW()),
('Patients', 'fas fa-fw fa-user-injured', 'patients.index', 0, 2, 1, NOW(), NOW()),
('Diagnosis', 'fas fa-fw fa-diagnoses', 'diagnosis.index', 0, 3, 1, NOW(), NOW()),
('Reports', 'fas fa-fw fa-chart-bar', 'reports.index', 0, 4, 1, NOW(), NOW());

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_10_25_104409_create_persons_table', 1),
('2025_10_25_111409_create_menu_items_table', 1),
('2025_10_25_150935_create_visits_table', 1),
('2025_10_25_152833_add_role_and_specialization_to_users_table', 1);
