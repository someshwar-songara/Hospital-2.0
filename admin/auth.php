<?php
session_start();

function is_logged_in(): bool {
    return isset($_SESSION['admin_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function logout(): void {
    session_destroy();
    header('Location: login.php');
    exit;
}
