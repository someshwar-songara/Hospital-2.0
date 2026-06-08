<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function doctor_logged_in(): bool {
    return isset($_SESSION['doctor_id']);
}

function require_doctor(): void {
    if (!doctor_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function doctor_logout(): void {
    unset($_SESSION['doctor_id'], $_SESSION['doctor_name'], $_SESSION['doctor_specialty'], $_SESSION['doctor_photo']);
    header('Location: login.php');
    exit;
}
