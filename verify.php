<?php
session_start();
require_once 'conf.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($email)) {
    $message = "<div class='alert alert-danger'>No email provided.</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $email_posted = $_POST['email'];

    $result = $user->verifyAccount($email_posted, $code);

    if ($result === true) {
        $message = "<div class='alert alert-success'>Account verified! You can now log in.</div>";
        echo '<meta http-equiv="refresh" content="3;url=login.php">';
    } else {
        $message = "<div class='alert alert-danger'>" . $result . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2>Verify Your Account</h2>
            <p>A verification code was "sent" to your email. Please enter it below.</p>

            <?php echo $message; ?>

            <form action="verify.php" method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="mb-3">
                    <label for="code" class="form-label">Verification Code (6 digits)</label>
                    <input type="text" class="form-control" id="code" name="code" maxlength="6" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify</button>
            </form>
            <div class="text-center mt-3">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>