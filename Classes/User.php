<?php
class User {
    private $conn;
    private $table_name = "users";

    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
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

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    username = :username,
                    email = :email,
                    password_hash = :password_hash";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
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
                    id, username, email, created_at
                  FROM
                    " . $this->table_name . "
                  ORDER BY
                    created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>