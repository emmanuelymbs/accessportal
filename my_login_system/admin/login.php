<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>

<h2>Admin Login</h2>

<?php
if (isset($_GET['error'])) {
    echo "<p style='color:red;'>Wrong password</p>";
}
?>

<form method="POST" action="auth.php">
    <input type="password" name="admin_password" placeholder="Admin password" required>
    <br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
