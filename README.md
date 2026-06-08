# Apex Health Care – Hospital Management System (v2.0)

Apex Health Care is a modern, responsive Hospital Management System built with PHP and MySQL. It features a public patient-facing website, an administrative dashboard, and a specialized portal for doctors to manage patient visits, medical records, and prescriptions.

---

## 🌟 Key Features

### 🏢 Public Website
- **Dynamic Homepage**: Hero highlights, stats counter, services carousel, and patient testimonials.
- **Appointment Booking**: Complete booking system for scheduling visits.
- **Interactive Pages**: About us, departments overview, facility highlights, and a contact form.
- **Individual Doctor Profiles**: Detailed pages showcasing medical qualifications, specialties, and clinical timings.

### 🛡️ Administrative Portal (`/admin`)
- **Control Panel**: Overview of pending appointments, messages, and site statistics.
- **Appointments Management**: Approve, complete, or cancel incoming appointment requests.
- **Clinical Directory**: Add, update, and manage doctor profiles, specialties, and active statuses.
- **Site Configuration**: Edit general hospital settings like address, support phone/email, emergency hours, and social media handles.

### 🩺 Doctor Portal (`/doctor`)
- **Personalized Clinical Dashboard**: Shows assigned appointments, recent patient visits, and inbox messages.
- **Electronic Health Records (EHR)**:
  - Add and manage detailed patient profiles.
  - Record chief complaints, diagnoses, clinical notes, and patient vitals (BP, temperature, pulse, weight).
- **Digital Prescriptions**: Generate itemized prescriptions specifying medicine name, dosage, frequency, duration, and special instructions.
- **Internal Messaging**: Secure internal messaging system to communicate with the administrative team and other doctors.

---

## 🛠️ Technology Stack
- **Backend**: PHP 8.x
- **Database**: MySQL (MariaDB)
- **Frontend**: Bootstrap 5.x, Vanilla CSS3, FontAwesome 6, Google Fonts
- **Database Wrapper**: Native PHP MySQLi API

---

## 🚀 Setup & Installation

### Prerequisites
- **XAMPP** (or any local server environment containing Apache, PHP 8.x, and MySQL/MariaDB)

### Step-by-Step Installation

1. **Clone & Place Project**:
   Place the project folder inside your local server directory:
   ```bash
   C:\xampp\htdocs\Hospital -2.0
   ```

2. **Start Local Servers**:
   Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**.

3. **Initialize the Database**:
   You can initialize and populate the database in one of two ways:
   
   * **Method A (Recommended - Automated)**:
     Simply visit the setup script in your web browser:
     ```
     http://localhost/Hospital -2.0/admin/setup.php
     ```
     This script will automatically create the database `apex_hospital`, configure all required tables/indexes/relations, and populate them with standard seed data and default accounts.

   * **Method B (Manual)**:
     Import the [database.sql](database.sql) file directly using **phpMyAdmin** (`http://localhost/phpmyadmin`) or your MySQL client of choice.

4. **Run the Application**:
   Navigate to the public home page:
   ```
   http://localhost/Hospital -2.0/index.php
   ```

---

## 🔑 Default Credentials

### 1. Administrative Portal (`/admin`)
- **URL**: `http://localhost/Hospital -2.0/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

### 2. Doctor Portal (`/doctor`)
- **URL**: `http://localhost/Hospital -2.0/doctor/login.php`
- **Username**: `jaya`
- **Password**: `doctor123`

---

## 📂 Project Directory Structure

```text
Hospital -2.0/
├── admin/               # Administrative portal pages & styles
├── assets/              # Public site assets (images, stylesheets)
├── doctor/              # Doctor portal pages & styles
├── includes/            # Common website page components (headers, footers)
├── database.sql         # SQL schema and seed data file
├── index.php            # Main homepage
├── about.php            # About us page
├── book.php             # Appointment request page
├── contact.php          # Contact form page
└── facilities.php       # Hospital facilities list
```

---
*Developed with ❤️ for Apex Health Care.*
