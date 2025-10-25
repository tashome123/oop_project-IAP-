<?php
session_start();

require_once 'conf.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product->name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];

    $result = $product->create();

    if ($result === true) {
        $message = "<div class='alert alert-success'>Product added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $result . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php';  ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2>Add New Good or Service</h2>

            <?php echo $message; ?>

            <form action="add_product.php" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Product</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>