<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Require the new config file FIRST
require_once 'conf.php';

// 2. Then require the other files
require 'vendor/autoload.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// 3. Database object is created (it will now see the constants)
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    $result = $user->register();

    if (is_string($result) && strlen($result) == 6) {
        $verification_code = $result;
        $mail = new PHPMailer(true);

        try {
            // 4. Use the new constants for email
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
            $mail->addAddress($user->email, $user->username);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Account - OOP Project';
            $mail->Body    = "Hello <strong>{$user->username}</strong>,<br><br>
                              Your verification code is: <strong>{$verification_code}</strong><br><br>
                              Please enter this code on the verification page.";
            $mail->AltBody = "Your verification code is: {$verification_code}";

            $mail->send();

            header("Location: verify.php?email=" . urlencode($user->email));
            exit;

        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>
                        Registration successful, but the email could not be sent.
                        Mailer Error: {$mail->ErrorInfo}
                        </div>";
        }

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
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2>User Registration</h2>

            <?php echo $message; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password (min 8 chars)</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>