<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

/* PROTECT ADMIN PAGE */
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "my_login_system");
if ($conn->connect_error) {
    die("Database connection failed");
}

/* FETCH DELETED RECORDS */
$sql = "SELECT id, username, password_plain, password_hash, ip, country, created_at
        FROM login_attempts
        WHERE deleted = 1
        ORDER BY id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Trash</title>
<style>
body { font-family: Arial; background:#f7f7f7; padding:20px; }
table { border-collapse: collapse; width:100%; background:#fff; }
th, td { padding:10px; border:1px solid #ccc; font-size:14px; }
th { background:#efefef; }
a {
    padding:6px 10px;
    background:#1877f2;
    color:#fff;
    text-decoration:none;
    border-radius:4px;
}
.restore {
    background:#27ae60;
}
.back {
    background:#555;
}
</style>
</head>
<body>

<h2>Trash (Deleted Records)</h2>

<a href="dashboard.php" class="back">← Back to Dashboard</a>

<table>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Password</th>
    <th>IP</th>
    <th>Country</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $formattedId = str_pad($row['id'], 5, "0", STR_PAD_LEFT);

        echo "<tr>
                  <td>{$formattedId}</td>
                  <td>{$row['username']}</td>
                  <td>{$row['password_plain']}</td>
                  <td>{$row['ip']}</td>
                  <td>{$row['country']}</td>
                  <td>{$row['created_at']}</td>
                  <td>
                      <a class='restore'
                         href='restore_row.php?id={$row['id']}'
                         onclick='return confirm(\"Restore this record?\");'>
                          Restore
                      </a>

                      <a href='hard_delete.php?id={$row['id']}'
                         class='delete-forever'
                         data-id='{$row['id']}'
                         style='background:#c0392b; margin-left:6px;'>
                         Delete Forever
                      </a>
                  </td>
              </tr>";

    }
} else {
    echo "<tr><td colspan='7'>Trash is empty</td></tr>";
}
?>

</table>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".delete-forever").forEach(link => {
        link.addEventListener("click", function (e) {
            const confirmed = confirm(
                "Permanently delete this record?\n\nThis cannot be undone!"
            );

            if (!confirmed) {
                e.preventDefault(); // STOP navigation
            }
        });
    });
});
</script>

</body>
</html>
