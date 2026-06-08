<?php
/**
 * Database setup / upgrade — safe to re-run at any time.
 * http://localhost/Hospital%20-2.0/admin/setup.php
 */
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli('localhost', 'root', '', '', 3306);
if ($conn->connect_error) die('MySQL connection failed: ' . $conn->connect_error);

$steps = [];

// Helper: run one statement and log result
function run(mysqli $db, string $sql, string $label): void {
    global $steps;
    if ($db->query($sql)) $steps[] = "✅ $label";
    else                  $steps[] = "⚠️ $label — " . $db->error;
}

// ── 1. Database ───────────────────────────────────────────
run($conn, "CREATE DATABASE IF NOT EXISTS apex_hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci", 'Database apex_hospital');
$conn->select_db('apex_hospital');

// ── 2. Tables (each separately) ───────────────────────────
run($conn, "CREATE TABLE IF NOT EXISTS admin_users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(80)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    name       VARCHAR(120) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table admin_users');

run($conn, "CREATE TABLE IF NOT EXISTS appointments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80)  NOT NULL,
    last_name  VARCHAR(80)  NOT NULL,
    phone      VARCHAR(20)  NOT NULL,
    email      VARCHAR(120),
    department VARCHAR(80)  NOT NULL,
    pref_date  DATE         NOT NULL,
    message    TEXT,
    status     ENUM('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table appointments');

run($conn, "CREATE TABLE IF NOT EXISTS contacts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(120) NOT NULL,
    subject    VARCHAR(200) NOT NULL,
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table contacts');

run($conn, "CREATE TABLE IF NOT EXISTS doctors (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(120) NOT NULL,
    specialty      VARCHAR(120) NOT NULL,
    photo          VARCHAR(200) DEFAULT '',
    profile_url    VARCHAR(200) DEFAULT '',
    availability   VARCHAR(200) DEFAULT '',
    bio            TEXT,
    qualifications TEXT,
    phone          VARCHAR(30)  DEFAULT '',
    is_active      TINYINT(1)   DEFAULT 1,
    sort_order     INT          DEFAULT 0,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table doctors');

run($conn, "CREATE TABLE IF NOT EXISTS facilities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    description TEXT,
    image       VARCHAR(200) DEFAULT '',
    category    ENUM('medical','surgical') DEFAULT 'medical',
    is_active   TINYINT(1)  DEFAULT 1,
    sort_order  INT         DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table facilities');

run($conn, "CREATE TABLE IF NOT EXISTS testimonials (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    photo      VARCHAR(200) DEFAULT '',
    quote      TEXT         NOT NULL,
    rating     TINYINT(1)  DEFAULT 5,
    is_active  TINYINT(1)  DEFAULT 1,
    sort_order INT         DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table testimonials');

run($conn, "CREATE TABLE IF NOT EXISTS departments (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    is_active  TINYINT(1)  DEFAULT 1,
    sort_order INT         DEFAULT 0
) ENGINE=InnoDB", 'Table departments');

run($conn, "CREATE TABLE IF NOT EXISTS site_settings (
    `key`      VARCHAR(100) PRIMARY KEY,
    `value`    TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table site_settings');

// ── 3. Add missing columns to doctors (upgrade existing) ──
// Check each column individually before adding — compatible with all MySQL/MariaDB versions
$existing_cols = [];
$col_res = $conn->query("SHOW COLUMNS FROM doctors");
if ($col_res) while ($col = $col_res->fetch_assoc()) $existing_cols[] = $col['Field'];

$new_cols = [
    'bio'            => "ALTER TABLE doctors ADD COLUMN bio TEXT AFTER availability",
    'qualifications' => "ALTER TABLE doctors ADD COLUMN qualifications TEXT AFTER bio",
    'phone'          => "ALTER TABLE doctors ADD COLUMN phone VARCHAR(30) DEFAULT '' AFTER qualifications",
    'is_active'      => "ALTER TABLE doctors ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER phone",
    'sort_order'     => "ALTER TABLE doctors ADD COLUMN sort_order INT DEFAULT 0 AFTER is_active",
];
foreach ($new_cols as $col => $sql) {
    if (!in_array($col, $existing_cols)) {
        run($conn, $sql, "Column doctors.$col added");
    } else {
        $steps[] = "✅ Column doctors.$col already exists";
    }
}

// ── 4. Default admin user ─────────────────────────────────
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO admin_users (username,password,name) VALUES (?,?,'Administrator')");
$u = 'admin';
$stmt->bind_param('ss', $u, $hash);
$stmt->execute();
$steps[] = '✅ Admin user (admin / admin123)';

// ── 5. Seed doctors ───────────────────────────────────────
$docs = [
    ['Dr. Jaya Mishra',    'Gynecologist & Obstetrician', 'assets/img/jaya.jpg',     'Jaya.php',     'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Jaya Mishra is a highly experienced Gynecologist & Obstetrician dedicated to providing the best possible care.', "MBBS, MD / MS\nAdvanced Fellowship in Gynecology\n10+ Years Clinical Experience", '+91 9111XXXX13', 1],
    ['Dr. Rahul Tejankar', 'ENT Surgery',                 'assets/img/tejanakar.jpg','tejankar.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Rahul Tejankar is an expert ENT surgeon with extensive experience.',                                              "MBBS, MS (ENT)\nFellowship in ENT Surgery\n8+ Years Clinical Experience",        '+91 9111XXXX13', 2],
    ['Dr. P. M. Kumawat',  'Consultant Physician',        'assets/img/kumawat.jpg',  'kumawat.php',  'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. P. M. Kumawat is a seasoned consultant physician with deep expertise in internal medicine.',                          "MBBS, MD (Medicine)\n12+ Years Experience",                                     '+91 9111XXXX13', 3],
    ['Dr. Preeti Verma',   'Gynaecologist',               'assets/img/preeti.jpg',   'preeti.php',   'Mon–Sat: 10AM–2PM, 5PM–8PM', "Dr. Preeti Verma is a dedicated gynaecologist committed to women's health.",                                             "MBBS, MD (OBG)\n9+ Years Clinical Experience",                                  '+91 9111XXXX13', 4],
    ['Dr. Sonaniya',       'General Medicine',            'assets/img/sonaniya.jpg', 'sonaniya.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Sonaniya brings comprehensive general medicine expertise to Apex Health Care.',                                       "MBBS, MD\n7+ Years Clinical Experience",                                          '+91 9111XXXX13', 5],
    ['Dr. Katyayan',       'Specialist',                  'assets/img/katyayan.jpg', 'katyayan.php', 'Mon–Sat: 10AM–2PM, 5PM–8PM', 'Dr. Katyayan is a dedicated specialist providing excellent patient care.',                                               "MBBS, MD\n6+ Years Clinical Experience",                                          '+91 9111XXXX13', 6],
];
$sd = $conn->prepare("INSERT IGNORE INTO doctors (name,specialty,photo,profile_url,availability,bio,qualifications,phone,sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
foreach ($docs as $d) {
    $sd->bind_param('ssssssssi', $d[0],$d[1],$d[2],$d[3],$d[4],$d[5],$d[6],$d[7],$d[8]);
    $sd->execute();
}
$steps[] = '✅ Doctors seeded';

// ── 6. Seed facilities ────────────────────────────────────
$facs = [
    ['Cardiology',          'State-of-the-art heart care facility with 24/7 monitoring.',          'assets/img/cardiology.jpg',         'medical',  0],
    ['Operation Theatre',   'Advanced surgical suites equipped with modern technology.',            'assets/img/operation-theatre.jpg',  'surgical', 1],
    ['Pediatrics',          'Compassionate care for children and infants.',                        'assets/img/pediatrics.jpg',          'medical',  2],
    ['Emergency Care',      '24/7 trauma care and immediate medical response unit.',               'assets/img/emergency-care.jpg',      'medical',  3],
    ['Radiology & Imaging', 'High-precision diagnostic imaging, MRI, CT scans, and Ultrasound.',  'assets/img/radiology.jpg',           'medical',  4],
    ['General Surgery',     'Advanced minimally invasive and specialized surgical interventions.', 'assets/img/expert-surgeon.jpg',      'surgical', 5],
    ['Laboratory Services', 'Accurate clinical pathology and diagnostic testing laboratory.',      'assets/img/laboratory-services.jpg', 'medical',  6],
    ['Dental Care',         'Complete preventative dentistry, cosmetic, and oral surgical care.',  'assets/img/dental-care.jpg',         'medical',  7],
    ['Pharmacy',            'Well-stocked in-house pharmacy with qualified pharmacists on duty.',  'assets/img/pharmacy.jpg',            'medical',  8],
    ['Physical Therapy',    'Expert physiotherapy and rehabilitation for faster recovery.',        'assets/img/physical-therapy.jpg',    'medical',  9],
    ['Vaccination',         'Comprehensive immunisation services for children and adults.',        'assets/img/vaccination.jpg',         'medical',  10],
];
$sf = $conn->prepare("INSERT IGNORE INTO facilities (name,description,image,category,sort_order) VALUES (?,?,?,?,?)");
foreach ($facs as $f) {
    $sf->bind_param('ssssi', $f[0],$f[1],$f[2],$f[3],$f[4]);
    $sf->execute();
}
$steps[] = '✅ Facilities seeded';

// ── 7. Seed testimonials ──────────────────────────────────
$testi = [
    ['Rahul Sharma', 'assets/img/testimonial1.jpg', 'The care I received at Apex Health Care was phenomenal. The doctors are highly skilled and the staff made sure I was comfortable throughout my treatment.', 5, 0],
    ['Priya Singh',  'assets/img/testimonial2.jpg', 'Dr. Jaya Mishra is incredibly kind and professional. I had a wonderful experience delivering my baby here. The facilities are top-notch and very clean.',   5, 1],
    ['Anil Verma',   'assets/img/testimonial3.jpg', 'I visited for an emergency and the response time was amazing. Thank you to the entire emergency team for their swift and life-saving action.',             4, 2],
];
$st = $conn->prepare("INSERT IGNORE INTO testimonials (name,photo,quote,rating,sort_order) VALUES (?,?,?,?,?)");
foreach ($testi as $t) {
    $st->bind_param('sssii', $t[0],$t[1],$t[2],$t[3],$t[4]);
    $st->execute();
}
$steps[] = '✅ Testimonials seeded';

// ── 8. Seed departments ───────────────────────────────────
$depts = ['Cardiology','Neurology','Pediatrics','Orthopedics','Gynecology','ENT Surgery','Dental Care','Emergency Care','Radiology','General Medicine','Physical Therapy'];
$sdp = $conn->prepare("INSERT IGNORE INTO departments (name,sort_order) VALUES (?,?)");
foreach ($depts as $i => $d) {
    $sdp->bind_param('si', $d, $i);
    $sdp->execute();
}
$steps[] = '✅ Departments seeded';

// ── 9. Seed site_settings ─────────────────────────────────
$settings = [
    'hospital_name'    => 'Apex Health Care',
    'hospital_tagline' => 'Expert healthcare in Ujjain.',
    'hospital_address' => 'Apex Health Care, Freeganj, Ujjain, MP 456001',
    'hospital_phone'   => '+91 9111XXXX13',
    'hospital_email'   => 'apex.healthcare@gmail.com',
    'hospital_maps_url'=> 'https://maps.app.goo.gl/pRMKa3EmEvePvPgx8',
    'social_facebook'  => '#',
    'social_instagram' => '#',
    'social_twitter'   => '#',
    'social_youtube'   => '#',
    'hero_badge'       => '24/7 Emergency Service',
    'hero_title_line1' => 'Advanced Care.',
    'hero_title_line2' => 'Exceptional Health.',
    'hero_subtitle'    => 'Welcome to Apex Health Care, the premier medical facility in Ujjain. We are dedicated to providing world-class healthcare with state-of-the-art technology and a team of expert specialists.',
    'stat1_number'     => '25+',   'stat1_label' => 'Specialist Doctors',
    'stat2_number'     => '10k+',  'stat2_label' => 'Happy Patients',
    'stat3_number'     => '15+',   'stat3_label' => 'Years Experience',
    'stat4_number'     => '24/7',  'stat4_label' => 'Emergency Care',
    'footer_copyright' => '© 2025 Apex Health Care. All rights reserved.',
    'footer_city'      => 'Ujjain, Madhya Pradesh',
    'hours_weekday'    => 'Monday – Saturday: 8:00 AM – 9:00 PM',
    'hours_sunday'     => 'Sunday: 9:00 AM – 2:00 PM',
    'hours_emergency'  => '24/7 Emergency Available',
];
$ss = $conn->prepare("INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
foreach ($settings as $k => $v) {
    $ss->bind_param('ss', $k, $v);
    $ss->execute();
}
$steps[] = '✅ Site settings seeded';

// ── 10. Doctor portal tables ──────────────────────────────

run($conn, "CREATE TABLE IF NOT EXISTS doctor_accounts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id   INT NOT NULL,
    username    VARCHAR(80)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    is_active   TINYINT(1)   DEFAULT 1,
    last_login  DATETIME     DEFAULT NULL,
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB", 'Table doctor_accounts');

run($conn, "CREATE TABLE IF NOT EXISTS patients (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    first_name   VARCHAR(80)  NOT NULL,
    last_name    VARCHAR(80)  NOT NULL,
    dob          DATE,
    gender       ENUM('Male','Female','Other') DEFAULT 'Male',
    blood_group  VARCHAR(5)   DEFAULT '',
    phone        VARCHAR(20)  NOT NULL,
    email        VARCHAR(120) DEFAULT '',
    address      TEXT,
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table patients');

run($conn, "CREATE TABLE IF NOT EXISTS medical_records (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    patient_id    INT NOT NULL,
    doctor_id     INT NOT NULL,
    appointment_id INT DEFAULT NULL,
    visit_date    DATE NOT NULL,
    chief_complaint TEXT,
    diagnosis     TEXT,
    notes         TEXT,
    vitals_bp     VARCHAR(20)  DEFAULT '',
    vitals_pulse  VARCHAR(20)  DEFAULT '',
    vitals_temp   VARCHAR(20)  DEFAULT '',
    vitals_weight VARCHAR(20)  DEFAULT '',
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE
) ENGINE=InnoDB", 'Table medical_records');

run($conn, "CREATE TABLE IF NOT EXISTS prescriptions (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    record_id      INT NOT NULL,
    patient_id     INT NOT NULL,
    doctor_id      INT NOT NULL,
    medicine_name  VARCHAR(150) NOT NULL,
    dosage         VARCHAR(100) DEFAULT '',
    frequency      VARCHAR(100) DEFAULT '',
    duration       VARCHAR(80)  DEFAULT '',
    instructions   TEXT,
    created_at     DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (record_id)  REFERENCES medical_records(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id)        ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)         ON DELETE CASCADE
) ENGINE=InnoDB", 'Table prescriptions');

run($conn, "CREATE TABLE IF NOT EXISTS staff_messages (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    sender_type  ENUM('doctor','admin') NOT NULL,
    sender_id    INT NOT NULL,
    sender_name  VARCHAR(120) NOT NULL,
    receiver_type ENUM('doctor','admin','all') DEFAULT 'all',
    receiver_id  INT DEFAULT NULL,
    subject      VARCHAR(200) NOT NULL,
    body         TEXT         NOT NULL,
    is_read      TINYINT(1)   DEFAULT 0,
    parent_id    INT          DEFAULT NULL,
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB", 'Table staff_messages');

// Add patient_id & doctor_id to appointments if missing
$appt_cols = [];
$appt_res = $conn->query("SHOW COLUMNS FROM appointments");
if ($appt_res) while ($c = $appt_res->fetch_assoc()) $appt_cols[] = $c['Field'];
if (!in_array('patient_id', $appt_cols))
    run($conn, "ALTER TABLE appointments ADD COLUMN patient_id INT DEFAULT NULL AFTER message", 'Column appointments.patient_id');
else $steps[] = '✅ Column appointments.patient_id already exists';
if (!in_array('assigned_doctor_id', $appt_cols))
    run($conn, "ALTER TABLE appointments ADD COLUMN assigned_doctor_id INT DEFAULT NULL AFTER patient_id", 'Column appointments.assigned_doctor_id');
else $steps[] = '✅ Column appointments.assigned_doctor_id already exists';
if (!in_array('notes', $appt_cols))
    run($conn, "ALTER TABLE appointments ADD COLUMN notes TEXT DEFAULT NULL AFTER assigned_doctor_id", 'Column appointments.notes');
else $steps[] = '✅ Column appointments.notes already exists';

// Seed a demo doctor account (username: jaya, password: doctor123)
$dRow = $conn->query("SELECT id FROM doctors WHERE name LIKE '%Jaya%' LIMIT 1")->fetch_assoc();
if ($dRow) {
    $dHash = password_hash('doctor123', PASSWORD_DEFAULT);
    $dStmt = $conn->prepare("INSERT IGNORE INTO doctor_accounts (doctor_id,username,password) VALUES (?,?,?)");
    $dStmt->bind_param('iss', $dRow['id'], $u2, $dHash);
    $u2 = 'jaya';
    $dStmt->execute();
    $steps[] = '✅ Demo doctor account: username=jaya password=doctor123';
}

// Seed sample patients
$pts = [
    ['Ramesh','Sharma','1985-04-12','Male','B+','9876543210','ramesh@email.com','12 MG Road, Ujjain'],
    ['Sunita','Patel', '1992-07-22','Female','O+','9812345678','sunita@email.com','45 Freeganj, Ujjain'],
    ['Ankit', 'Verma', '2001-01-05','Male','A+','9988776655','ankit@email.com','7 Mahakal Marg, Ujjain'],
    ['Priya', 'Singh', '1978-11-30','Female','AB-','9765432109','priya@email.com','2 Tower Chowk, Ujjain'],
    ['Mohan', 'Yadav', '1965-08-15','Male','O-','9654321098','mohan@email.com','88 Dewas Gate, Ujjain'],
];
$spt = $conn->prepare("INSERT IGNORE INTO patients (first_name,last_name,dob,gender,blood_group,phone,email,address) VALUES (?,?,?,?,?,?,?,?)");
foreach ($pts as $p) { $spt->bind_param('ssssssss',$p[0],$p[1],$p[2],$p[3],$p[4],$p[5],$p[6],$p[7]); $spt->execute(); }
$steps[] = '✅ Sample patients seeded';

// ── Done ──────────────────────────────────────────────────
$allOk = !array_filter($steps, fn($s) => str_starts_with($s, '⚠️'));
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Setup – Apex Health Care</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:sans-serif;background:#f8fafc;padding:40px 16px;}
  .box{max-width:580px;margin:0 auto;background:#fff;border-radius:16px;padding:36px 40px;box-shadow:0 4px 32px rgba(0,0,0,.1);}
  h2{color:#0a1628;margin-bottom:24px;text-align:center;font-size:1.5rem;}
  .steps{margin-bottom:24px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;}
  .step{padding:9px 16px;border-bottom:1px solid #e2e8f0;font-size:.875rem;color:#2d3748;}
  .step:last-child{border-bottom:none;}
  .step.warn{background:#fef9c3;color:#854d0e;}
  .creds{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;margin-bottom:20px;font-size:.9rem;color:#15803d;text-align:center;}
  .warn-box{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:.85rem;color:#dc2626;text-align:center;}
  a.btn{display:inline-block;padding:12px 32px;background:#00c9a7;color:#0a1628;border-radius:50px;font-weight:700;text-decoration:none;font-size:.95rem;}
  .center{text-align:center;}
</style></head><body>
<div class="box">
  <h2><?= $allOk ? '✅' : '⚠️' ?> Database Setup <?= $allOk ? 'Complete' : 'Done (with warnings)' ?></h2>
  <div class="steps">
    <?php foreach($steps as $s): $warn=str_starts_with($s,'⚠️'); ?>
      <div class="step <?= $warn?'warn':'' ?>"><?= $s ?></div>
    <?php endforeach; ?>
  </div>
  <div class="creds">
    <strong>Admin Login:</strong>&nbsp;
    Username: <code>admin</code> &nbsp;|&nbsp; Password: <code>admin123</code>
  </div>
  <div class="warn-box">⚠️ Delete or restrict <strong>setup.php</strong> after setup is complete.</div>
  <div class="center"><a href="index.php" class="btn">Go to Admin Panel →</a></div>
</div>
</body></html>
