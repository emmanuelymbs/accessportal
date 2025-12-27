<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: trash.php");
    exit;
}

$id = (int)$_GET['id'];

$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    die("Database connection failed");
}

$stmt = $conn->prepare("UPDATE login_attempts SET deleted = 0 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: trash.php");
exit;
