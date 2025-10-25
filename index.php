<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Call the read method
$stmt = $user->read();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">OOP HP Lab</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="register.php">Register</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2>Registered Users</h2>

            <?php
            if ($num > 0) {
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Username</th>";
                echo "<th>Email</th>";
                echo "<th>Registered On</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                // Loop through the results
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    echo "<tr>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$username}</td>";
                    echo "<td>{$email}</td>";
                    echo "<td>{$created_at}</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<div class='alert alert-info'>No users found. Go register one!</div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>