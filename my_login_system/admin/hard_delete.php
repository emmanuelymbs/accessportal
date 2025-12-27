<?php
session_start();

/* PROTECT ADMIN PAGE */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: trash.php");
    exit;
}

$id = (int)$_GET['id'];

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    die("Database connection failed");
}

/* PERMANENT DELETE */
$stmt = $conn->prepare("DELETE FROM login_attempts WHERE id = ? AND deleted = 1");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: trash.php");
exit;
