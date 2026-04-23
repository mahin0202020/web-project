-- ============================================================
--  Paws & Pour – Complete Database Schema
--  File: db/database.sql
--  Run this in phpMyAdmin or MySQL CLI to set up the database
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `pawsandpour`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pawsandpour`;

-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name`  VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── PETS ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pets` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `name`       VARCHAR(80)  NOT NULL,
  `species`    ENUM('dog','cat','rabbit','bird','other') DEFAULT 'dog',
  `breed`      VARCHAR(100) DEFAULT NULL,
  `age`        VARCHAR(20)  DEFAULT NULL,
  `weight`     DECIMAL(5,2) DEFAULT NULL,
  `photo`      VARCHAR(255) DEFAULT NULL,
  `notes`      TEXT         DEFAULT NULL,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── RESERVATIONS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reservations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED NOT NULL,
  `pet_id`      INT UNSIGNED DEFAULT NULL,
  `date`        DATE         NOT NULL,
  `time_slot`   VARCHAR(40)  NOT NULL,
  `guests`      TINYINT UNSIGNED DEFAULT 1,
  `table_pref`  VARCHAR(50)  DEFAULT NULL,
  `status`      ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  `notes`       TEXT         DEFAULT NULL,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pet_id`)  REFERENCES `pets`(`id`)  ON DELETE SET NULL,
  INDEX `idx_user_date` (`user_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── PET ACTIVITY LOGS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pet_logs` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pet_id`      INT UNSIGNED NOT NULL,
  `log_type`    ENUM('feeding','walk','play','grooming','vet','note') DEFAULT 'note',
  `description` TEXT NOT NULL,
  `logged_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pet_id`) REFERENCES `pets`(`id`) ON DELETE CASCADE,
  INDEX `idx_pet_id` (`pet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── MENU ITEMS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category`        VARCHAR(60)   NOT NULL,
  `name`            VARCHAR(120)  NOT NULL,
  `description`     TEXT          DEFAULT NULL,
  `price`           DECIMAL(8,2)  NOT NULL,
  `is_pet_friendly` TINYINT(1)    DEFAULT 0,
  `emoji`           VARCHAR(10)   DEFAULT NULL,
  `available`       TINYINT(1)    DEFAULT 1,
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── SAMPLE MENU DATA ─────────────────────────────────────────
INSERT INTO `menu_items` (`category`, `name`, `description`, `price`, `is_pet_friendly`, `emoji`) VALUES
('Coffee',   'Caramel Macchiato',   'Espresso layered with vanilla syrup & steamed milk',              320.00, 0, '☕'),
('Coffee',   'Cold Brew',           '12-hour slow-brewed perfection, served over ice',                280.00, 0, '🧊'),
('Coffee',   'Puppuccino Latte',    'Our signature latte with a side puppuccino for your dog',        350.00, 1, '🐾'),
('Coffee',   'Hazelnut Flat White', 'Bold espresso with velvety steamed milk & hazelnut',             300.00, 0, '☕'),
('Coffee',   'Iced Mocha',          'Rich chocolate espresso over ice with whipped cream',            310.00, 0, '🍫'),
('Tea',      'Jasmine Green Tea',   'Delicate floral notes with premium green tea leaves',            220.00, 0, '🍵'),
('Tea',      'Chamomile Honey Tea', 'Calming chamomile with raw wildflower honey drizzle',            200.00, 0, '🌼'),
('Tea',      'Masala Chai',         'Spiced milk tea brewed the traditional way',                     180.00, 0, '🫖'),
('Bites',    'Avocado Toast',       'Sourdough, smashed avocado, cherry tomatoes, feta',              380.00, 0, '🥑'),
('Bites',    'Butter Croissant',    'Freshly baked, flaky French-style croissant',                   180.00, 0, '🥐'),
('Bites',    'Chicken Sandwich',    'Grilled chicken, lettuce, tomato on toasted brioche',            420.00, 0, '🥪'),
('Bites',    'Banana Walnut Cake',  'Moist homemade banana cake with walnut crumble topping',        250.00, 0, '🍰'),
('Pet',      'Pet Treat Platter',   'Assorted dog biscuits & cat treats — vet approved!',            150.00, 1, '🦴'),
('Pet',      'Puppuccino Cup',      'A small cup of whipped cream, just for your dog!',               80.00, 1, '🐶'),
('Specials', 'Owner + Pet Combo',   '1 beverage + 1 snack + 1 pet treat. Best value!',              520.00, 1, '🎉'),
('Specials', 'Weekend Brunch Box',  'Eggs, toast, salad, coffee — full brunch for two',              750.00, 0, '🍳');
