<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    exit;
}

$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    exit;
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=login_attempts.csv");

$output = fopen("php://output", "w");

fputcsv($output, ["ID", "Username", "Password Plain", "Password Hash", "IP", "Date"]);

$sql = "SELECT id, username, password_plain, password_hash, ip, created_at
        FROM login_attempts
        ORDER BY id ASC";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
