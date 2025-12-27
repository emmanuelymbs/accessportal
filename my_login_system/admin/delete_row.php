<?php
session_start();

/* PROTECT ADMIN */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

/* VALIDATE ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int) $_GET['id'];

/* DB CONNECTION */
$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    die("Database error");
}

/* DELETE RECORD */
$stmt = $conn->prepare("UPDATE login_attempts SET deleted = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->close();
$conn->close();

/* REDIRECT BACK */
header("Location: dashboard.php");
exit;
