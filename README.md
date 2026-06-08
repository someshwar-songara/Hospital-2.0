# Apex Health Care - Hospital & Doctor Portal (v2.0)

A comprehensive hospital website and clinical management portal designed for **Apex Health Care**. It features a responsive public website for patients to book appointments, an admin control panel to manage website configurations, and a dedicated doctor portal to manage patients, medical records, prescriptions, and internal messaging.

---

## 🚀 Key Features

### 🌐 Public Website
- **Dynamic Homepage**: Features statistics, services, departments, facilities, testimonials, and contact information.
- **Appointment Booking**: Online scheduling form for patients to request appointments.
- **Contact Desk**: Support contact form with structured messaging.
- **Doctor Showcases**: Dedicated profile pages for registered medical specialists.

### 🩺 Doctor Portal
- **Clinical Dashboard**: Real-time view of daily stats, scheduled appointments, and recent medical messages.
- **Patient Management**: Complete record system of patient demographics, blood groups, history, and contact details.
- **Medical Records**: Visit-by-visit logs includingBP, pulse, temp, weight, chief complaints, diagnoses, and doctor notes.
- **Digital Prescriptions**: Generate medication sheets containing dosage, frequency, duration, and instructions.
- **Staff Messenger**: Secure internal chat/message center for doctors and admin staff.

### ⚙️ Admin Control Panel
- **Setup System**: One-click database installation and migration sync.
- **Website Settings**: Direct control over site branding, hero banners, stats, social links, and working hours.
- **Content Management**: Manage departments, doctor listings, testimonials, and surgical/medical facilities.
- **Appointments & Contact Managers**: Review, confirm, or complete patient requests.

---

## 🛠️ Tech Stack
- **Frontend**: HTML5, Vanilla CSS3 (Custom responsive themes), Javascript (ES6), Bootstrap 5.3
- **Backend**: PHP (Object-oriented & Procedural design)
- **Database**: MySQL / MariaDB (Relational design with Cascade Triggers)
- **Icons & Fonts**: FontAwesome 6, Google Fonts (Poppins & Inter)

---

## 📦 Installation & Setup

### 1. Prerequisites
- **XAMPP** (with PHP 8.0+ and MySQL) or equivalent local servers.
- **Git** (for code syncing).

### 2. Project Placement
Clone this repository into your local webroot (e.g., `C:/xampp/htdocs/` for Windows XAMPP):
```bash
cd C:/xampp/htdocs/
git clone https://github.com/someshwar-songara/Hospital-2.0.git "Hospital -2.0"
```

### 3. Database Initialization
1. Start **Apache** and **MySQL** via the XAMPP Control Panel.
2. Open your web browser and navigate to the automated database setup script:
   `http://localhost/Hospital%20-2.0/admin/setup.php`
3. The setup script will automatically:
   - Create the database `apex_hospital`.
   - Setup all 13 core relational tables and foreign keys.
   - Seed default settings, specialists, facilities, and initial portal logins.

*Alternatively, you can manually import the schema file [database.sql](database.sql) directly using phpMyAdmin.*

---

## 🔐 Credentials (Default Setup)

| Portal / Role | Access URL | Username | Password |
| :--- | :--- | :--- | :--- |
| **Administrator** | `http://localhost/Hospital%20-2.0/admin/login.php` | `admin` | `admin123` |
| **Doctor (Demo)** | `http://localhost/Hospital%20-2.0/doctor/login.php` | `jaya` | `doctor123` |

---

## 📂 Project Architecture
```
├── admin/                 # Admin Control Panel controllers and views
├── assets/                # Core stylesheets, images, and public assets
├── doctor/                # Doctor Portal dashboard and medical record system
├── includes/              # Shared components (Navbar, Footer, settings loader)
├── database.sql           # Database schema backup and seed data
└── setup.php              # Automated setup and schema sync engine
```

---

## 🔒 Security Recommendations
- **Important**: Once the setup is completed, restrict access to or delete the file `admin/setup.php` to prevent unauthorized database wipes.
- Update default passwords (`admin123` & `doctor123`) inside their respective portals during first use.
