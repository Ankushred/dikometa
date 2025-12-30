<?php
session_start();

// 1. Force Login Check
function checkLogin() {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }
}

// 2. Protect Admin Pages (Kick out Members)
function checkAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: member_panel.php");
        exit();
    }
}

// 3. Redirect if already logged in
function redirectIfLoggedIn() {
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'member') {
            header("Location: member_panel.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}
?>