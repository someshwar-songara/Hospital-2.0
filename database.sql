-- Database creation and seeding script for Apex Hospital

CREATE DATABASE IF NOT EXISTS `apex_hospital` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `apex_hospital`;

-- 1. admin_users
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(80)  NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `name`       VARCHAR(120) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. appointments
CREATE TABLE IF NOT EXISTS `appointments` (
    `id`                 INT AUTO_INCREMENT PRIMARY KEY,
    `first_name`         VARCHAR(80)  NOT NULL,
    `last_name`          VARCHAR(80)  NOT NULL,
    `phone`              VARCHAR(20)  NOT NULL,
    `email`              VARCHAR(120),
    `department`         VARCHAR(80)  NOT NULL,
    `pref_date`          DATE         NOT NULL,
    `message`            TEXT,
    `patient_id`         INT DEFAULT NULL,
    `assigned_doctor_id` INT DEFAULT NULL,
    `notes`              TEXT DEFAULT NULL,
    `status`             ENUM('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
    `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. contacts
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(120) NOT NULL,
    `email`      VARCHAR(120) NOT NULL,
    `subject`    VARCHAR(200) NOT NULL,
    `message`    TEXT         NOT NULL,
    `is_read`    TINYINT(1)   DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. doctors
CREATE TABLE IF NOT EXISTS `doctors` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `name`           VARCHAR(120) NOT NULL,
    `specialty`      VARCHAR(120) NOT NULL,
    `photo`          VARCHAR(200) DEFAULT '',
    `profile_url`    VARCHAR(200) DEFAULT '',
    `availability`   VARCHAR(200) DEFAULT '',
    `bio`            TEXT,
    `qualifications` TEXT,
    `phone`          VARCHAR(30)  DEFAULT '',
    `is_active`      TINYINT(1)   DEFAULT 1,
    `sort_order`     INT          DEFAULT 0,
    `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. facilities
CREATE TABLE IF NOT EXISTS `facilities` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(120) NOT NULL,
    `description` TEXT,
    `image`       VARCHAR(200) DEFAULT '',
    `category`    ENUM('medical','surgical') DEFAULT 'medical',
    `is_active`   TINYINT(1)  DEFAULT 1,
    `sort_order`  INT         DEFAULT 0,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(120) NOT NULL,
    `photo`          VARCHAR(200) DEFAULT '',
    `quote`      TEXT         NOT NULL,
    `rating`     TINYINT(1)  DEFAULT 5,
    `is_active`  TINYINT(1)  DEFAULT 1,
    `sort_order` INT         DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. departments
CREATE TABLE IF NOT EXISTS `departments` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(120) NOT NULL,
    `is_active`  TINYINT(1)  DEFAULT 1,
    `sort_order` INT         DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. site_settings
CREATE TABLE IF NOT EXISTS `site_settings` (
    `key`      VARCHAR(100) PRIMARY KEY,
    `value`    TEXT,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. doctor_accounts
CREATE TABLE IF NOT EXISTS `doctor_accounts` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `doctor_id`   INT NOT NULL,
    `username`    VARCHAR(80)  NOT NULL UNIQUE,
    `password`    VARCHAR(255) NOT NULL,
    `is_active`   TINYINT(1)   DEFAULT 1,
    `last_login`  DATETIME     DEFAULT NULL,
    `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. patients
CREATE TABLE IF NOT EXISTS `patients` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `first_name`   VARCHAR(80)  NOT NULL,
    `last_name`    VARCHAR(80)  NOT NULL,
    `dob`          DATE,
    `gender`       ENUM('Male','Female','Other') DEFAULT 'Male',
    `blood_group`  VARCHAR(5)   DEFAULT '',
    `phone`        VARCHAR(20)  NOT NULL,
    `email`        VARCHAR(120) DEFAULT '',
    `address`      TEXT,
    `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. medical_records
CREATE TABLE IF NOT EXISTS `medical_records` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id`     INT NOT NULL,
    `doctor_id`      INT NOT NULL,
    `appointment_id` INT DEFAULT NULL,
    `visit_date`     DATE NOT NULL,
    `chief_complaint` TEXT,
    `diagnosis`      TEXT,
    `notes`          TEXT,
    `vitals_bp`      VARCHAR(20)  DEFAULT '',
    `vitals_pulse`   VARCHAR(20)  DEFAULT '',
    `vitals_temp`    VARCHAR(20)  DEFAULT '',
    `vitals_weight`  VARCHAR(20)  DEFAULT '',
    `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`)  REFERENCES `doctors` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. prescriptions
CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `record_id`      INT NOT NULL,
    `patient_id`     INT NOT NULL,
    `doctor_id`      INT NOT NULL,
    `medicine_name`  VARCHAR(150) NOT NULL,
    `dosage`         VARCHAR(100) DEFAULT '',
    `frequency`      VARCHAR(100) DEFAULT '',
    `duration`       VARCHAR(80)  DEFAULT '',
    `instructions`   TEXT,
    `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`record_id`)  REFERENCES `medical_records` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`)        ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`)  REFERENCES `doctors` (`id`)         ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. staff_messages
CREATE TABLE IF NOT EXISTS `staff_messages` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `sender_type`  ENUM('doctor','admin') NOT NULL,
    `sender_id`    INT NOT NULL,
    `sender_name`  VARCHAR(120) NOT NULL,
    `receiver_type` ENUM('doctor','admin','all') DEFAULT 'all',
    `receiver_id`  INT DEFAULT NULL,
    `subject`      VARCHAR(200) NOT NULL,
    `body`         TEXT         NOT NULL,
    `is_read`      TINYINT(1)   DEFAULT 0,
    `parent_id`    INT          DEFAULT NULL,
    `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── SEEDING DATA ──

-- Default Admin User (Username: admin, Password: admin123)
INSERT IGNORE INTO `admin_users` (`username`, `password`, `name`) VALUES 
('admin', '$2y$10$tZ27dJk.N.JqV2K31l8cEe5g4E4N97H7sLd25H7Hw/fKx4vBfHjVq', 'Administrator');

-- Doctors
INSERT IGNORE INTO `doctors` (`name`, `specialty`, `photo`, `profile_url`, `availability`, `bio`, `qualifications`, `phone`, `sort_order`) VALUES
('Dr. Jaya Mishra',    'Gynecologist & Obstetrician', 'assets/img/jaya.jpg',     'Jaya.php',     'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Jaya Mishra is a highly experienced Gynecologist & Obstetrician dedicated to providing the best possible care.', 'MBBS, MD / MS\nAdvanced Fellowship in Gynecology\n10+ Years Clinical Experience', '+91 9111XXXX13', 1),
('Dr. Rahul Tejankar', 'ENT Surgery',                 'assets/img/tejanakar.jpg','tejankar.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Rahul Tejankar is an expert ENT surgeon with extensive experience.',                                              'MBBS, MS (ENT)\nFellowship in ENT Surgery\n8+ Years Clinical Experience',        '+91 9111XXXX13', 2),
('Dr. P. M. Kumawat',  'Consultant Physician',        'assets/img/kumawat.jpg',  'kumawat.php',  'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. P. M. Kumawat is a seasoned consultant physician with deep expertise in internal medicine.',                          'MBBS, MD (Medicine)\n12+ Years Experience',                                     '+91 9111XXXX13', 3),
('Dr. Preeti Verma',   'Gynaecologist',               'assets/img/preeti.jpg',   'preeti.php',   'Mon–Sat: 10AM–2PM, 5PM–8PM', "Dr. Preeti Verma is a dedicated gynaecologist committed to women's health.",                                             "MBBS, MD (OBG)\n9+ Years Clinical Experience",                                  '+91 9111XXXX13', 4),
('Dr. Sonaniya',       'General Medicine',            'assets/img/sonaniya.jpg', 'sonaniya.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Sonaniya brings comprehensive general medicine expertise to Apex Health Care.',                                       'MBBS, MD\n7+ Years Clinical Experience',                                          '+91 9111XXXX13', 5),
('Dr. Katyayan',       'Specialist',                  'assets/img/katyayan.jpg', 'katyayan.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Katyayan is a dedicated specialist providing excellent patient care.',                                               'MBBS, MD\n6+ Years Clinical Experience',                                          '+91 9111XXXX13', 6);

-- Facilities
INSERT IGNORE INTO `facilities` (`name`, `description`, `image`, `category`, `sort_order`) VALUES
('Cardiology',          'State-of-the-art heart care facility with 24/7 monitoring.',          'assets/img/cardiology.jpg',         'medical',  0),
('Operation Theatre',   'Advanced surgical suites equipped with modern technology.',            'assets/img/operation-theatre.jpg',  'surgical', 1),
('Pediatrics',          'Compassionate care for children and infants.',                        'assets/img/pediatrics.jpg',          'medical',  2),
('Emergency Care',      '24/7 trauma care and immediate medical response unit.',               'assets/img/emergency-care.jpg',      'medical',  3),
('Radiology & Imaging', 'High-precision diagnostic imaging, MRI, CT scans, and Ultrasound.',  'assets/img/radiology.jpg',           'medical',  4),
('General Surgery',     'Advanced minimally invasive and specialized surgical interventions.', 'assets/img/expert-surgeon.jpg',      'surgical', 5),
('Laboratory Services', 'Accurate clinical pathology and diagnostic testing laboratory.',      'assets/img/laboratory-services.jpg', 'medical',  6),
('Dental Care',         'Complete preventative dentistry, cosmetic, and oral surgical care.',  'assets/img/dental-care.jpg',         'medical',  7),
('Pharmacy',            'Well-stocked in-house pharmacy with qualified pharmacists on duty.',  'assets/img/pharmacy.jpg',            'medical',  8),
('Physical Therapy',    'Expert physiotherapy and rehabilitation for faster recovery.',        'assets/img/physical-therapy.jpg',    'medical',  9),
('Vaccination',         'Comprehensive immunisation services for children and adults.',        'assets/img/vaccination.jpg',         'medical',  10);

-- Testimonials
INSERT IGNORE INTO `testimonials` (`name`, `photo`, `quote`, `rating`, `sort_order`) VALUES
('Rahul Sharma', 'assets/img/testimonial1.jpg', 'The care I received at Apex Health Care was phenomenal. The doctors are highly skilled and the staff made sure I was comfortable throughout my treatment.', 5, 0),
('Priya Singh',  'assets/img/testimonial2.jpg', 'Dr. Jaya Mishra is incredibly kind and professional. I had a wonderful experience delivering my baby here. The facilities are top-notch and very clean.',   5, 1),
('Anil Verma',   'assets/img/testimonial3.jpg', 'I visited for an emergency and the response time was amazing. Thank you to the entire emergency team for their swift and life-saving action.',             4, 2);

-- Departments
INSERT IGNORE INTO `departments` (`name`, `sort_order`) VALUES
('Cardiology', 0), ('Neurology', 1), ('Pediatrics', 2), ('Orthopedics', 3), ('Gynecology', 4), ('ENT Surgery', 5), ('Dental Care', 6), ('Emergency Care', 7), ('Radiology', 8), ('General Medicine', 9), ('Physical Therapy', 10);

-- Site Settings
INSERT INTO `site_settings` (`key`, `value`) VALUES
('hospital_name', 'Apex Health Care'),
('hospital_tagline', 'Expert healthcare in Ujjain.'),
('hospital_address', 'Apex Health Care, Freeganj, Ujjain, MP 456001'),
('hospital_phone', '+91 9111XXXX13'),
('hospital_email', 'apex.healthcare@gmail.com'),
('hospital_maps_url', 'https://maps.app.goo.gl/pRMKa3EmEvePvPgx8'),
('social_facebook', '#'),
('social_instagram', '#'),
('social_twitter', '#'),
('social_youtube', '#'),
('hero_badge', '24/7 Emergency Service'),
('hero_title_line1', 'Advanced Care.'),
('hero_title_line2', 'Exceptional Health.'),
('hero_subtitle', 'Welcome to Apex Health Care, the premier medical facility in Ujjain. We are dedicated to providing world-class healthcare with state-of-the-art technology and a team of expert specialists.'),
('stat1_number', '25+'), ('stat1_label', 'Specialist Doctors'),
('stat2_number', '10k+'), ('stat2_label', 'Happy Patients'),
('stat3_number', '15+'), ('stat3_label', 'Years Experience'),
('stat4_number', '24/7'), ('stat4_label', 'Emergency Care'),
('footer_copyright', '© 2025 Apex Health Care. All rights reserved.'),
('footer_city', 'Ujjain, Madhya Pradesh'),
('hours_weekday', 'Monday – Saturday: 8:00 AM – 9:00 PM'),
('hours_sunday', 'Sunday: 9:00 AM – 2:00 PM'),
('hours_emergency', '24/7 Emergency Available')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

-- Demo Doctor Account (Username: jaya, Password: doctor123)
-- Targeted at doctor_id = 1 (Dr. Jaya Mishra)
INSERT IGNORE INTO `doctor_accounts` (`doctor_id`, `username`, `password`) VALUES 
(1, 'jaya', '$2y$10$W1z97W6BPuRAK5o30PwqNefJhXyZCWjaPxaEc/V/A.0Oe9Xuah8L6');

-- Sample Patients
INSERT IGNORE INTO `patients` (`id`, `first_name`, `last_name`, `dob`, `gender`, `blood_group`, `phone`, `email`, `address`) VALUES
(1, 'Ramesh', 'Sharma', '1985-04-12', 'Male',   'B+',  '9876543210', 'ramesh@email.com', '12 MG Road, Ujjain'),
(2, 'Sunita', 'Patel',  '1992-07-22', 'Female', 'O+',  '9812345678', 'sunita@email.com', '45 Freeganj, Ujjain'),
(3, 'Ankit',  'Verma',  '2001-01-05', 'Male',   'A+',  '9988776655', 'ankit@email.com',  '7 Mahakal Marg, Ujjain'),
(4, 'Priya',  'Singh',  '1978-11-30', 'Female', 'AB-', '9765432109', 'priya@email.com',  '2 Tower Chowk, Ujjain'),
(5, 'Mohan',  'Yadav',  '1965-08-15', 'Male',   'O-',  '9654321098', 'mohan@email.com',  '88 Dewas Gate, Ujjain');
