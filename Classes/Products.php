<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        if (empty($this->name) || empty($this->price)) {
            return "Product name and price are required.";
        }

        if (!is_numeric($this->price) || $this->price < 0) {
            return "Price must be a valid number.";
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    name = :name,
                    description = :description,
                    price = :price";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id, name, description, price, added_at
                  FROM " . $this->table_name . "
                  ORDER BY added_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>