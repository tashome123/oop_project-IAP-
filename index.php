<?php
session_start();
// 1. Include conf.php
require_once 'conf.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Product.php'; // 2. Include Product class

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// --- Get Users ---
$user = new User($db);
$user_stmt = $user->read();
$user_num = $user_stmt->rowCount();

// --- Get Products ---
$product = new Product($db);
$product_stmt = $product->read();
$product_num = $product_stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; // 3. Use the new navbar ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2>Registered Users</h2>
            <?php
            if ($user_num > 0) {
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'><tr><th>ID</th><th>Username</th><th>Email</th><th>Registered</th><th>Verified</th></tr></thead>";
                echo "<tbody>";
                while ($row = $user_stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $verified_status = $is_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>';
                    echo "<tr><td>{$id}</td><td>{$username}</td><td>{$email}</td><td>{$created_at}</td><td>{$verified_status}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-info'>No users found.</div>";
            }
            ?>
        </div>

        <div class="col-12 mt-5">
            <h2>Goods & Services</h2>
            <?php
            if ($product_num > 0) {
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'><tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Added On</th></tr></thead>";
                echo "<tbody>";
                while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    echo "<tr>";
                    echo "<td>{$id}</td>";
                    echo "<td>{$name}</td>";
                    echo "<td>{$description}</td>";
                    echo "<td>$" . number_format($price, 2) . "</td>";
                    echo "<td>{$added_at}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-info'>No products found. <a href='add_product.php'>Add one!</a></div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>