<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        if (empty($this->email) || empty($this->password)) {
            return "Email and password are required.";
        }

        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['is_verified'] == 0) {
                return "Account not verified. Please check your email for the code.";
            }

            if (password_verify($this->password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                return true;
            } else {
                return "Incorrect password.";
            }
        } else {
            return "Email not found.";
        }
    }

    public function register() {

        if (empty($this->username) || empty($this->email) || empty($this->password)) {
            return "All fields are required.";
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        if (strlen($this->password) < 8) {
            return "Password must be at least 8 characters long.";
        }
        if ($this->isAlreadyExists()) {
            return "Username or Email already exists.";
        }

        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    username = :username,
                    email = :email,
                    password_hash = :password_hash,
                    verification_code = :verification_code,
                    is_verified = 0";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":verification_code", $verification_code);

        if ($stmt->execute()) {
            return $verification_code;
        }

        return false;
    }

    public function verifyAccount($email, $code) {
        if (empty($email) || empty($code)) {
            return "Email and code are required.";
        }

        $query = "SELECT id FROM " . $this->table_name . "
                  WHERE email = :email AND verification_code = :code AND is_verified = 0
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":code", $code);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $update_query = "UPDATE " . $this->table_name . "
                             SET is_verified = 1, verification_code = NULL
                             WHERE email = :email";

            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(":email", $email);

            if ($update_stmt->execute()) {
                return true;
            }
        }

        return "Invalid email or verification code.";
    }

    private function isAlreadyExists() {
        $query = "SELECT id FROM " . $this->table_name . "
                  WHERE username = :username OR email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT
                    id, username, email, created_at, is_verified
                  FROM
                    " . $this->table_name . "
                  ORDER BY
                    created_at DESC";

        // You were missing these lines
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }