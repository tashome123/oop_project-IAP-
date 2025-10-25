<?php
session_start();
require_once 'conf.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$stmt = $user->read();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">OOP HP Lab</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <span class="navbar-text me-3">
                        Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
            </li>
            <li class="nav-item">
                <a class="btn btn-outline-light" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2>Registered Users (Task #10)</h2>

            <?php
            if ($num > 0) {
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Username</th>";
                echo "<th>Email</th>";
                echo "<th>Registered On</th>";
                echo "<th>Verified</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $verified_status = $is_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>';
                    echo "<tr>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$username}</td>";
                    echo "<td>{$email}</td>";
                    echo "<td>{$created_at}</td>";
                    echo "<td>{$verified_status}</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<div class='alert alert-info'>No users found.</div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>