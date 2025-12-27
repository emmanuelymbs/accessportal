<?php
session_start();

/*
TEMPORARY ADMIN PASSWORD
(we will hash this later)
*/
$ADMIN_PASSWORD = "123456";

if (!isset($_POST['admin_password'])) {
    header("Location: login.php");
    exit;
}

if ($_POST['admin_password'] === $ADMIN_PASSWORD) {
    $_SESSION['admin_logged_in'] = true;
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
