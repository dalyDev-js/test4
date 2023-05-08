<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');

  

include_once "db.php";

class Product
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getAllProducts()
    {
        $query = "SELECT * FROM products";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductBySKU($sku)
    {
        $query = "SELECT * FROM products WHERE SKU = :SKU";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":SKU", $sku);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveProduct($data)
    {
        $query = "INSERT INTO products(SKU, name, price, attribute, value) ";
        $query .=  "VALUES( :sku, :name, :price, :attribute, :value)";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":sku", $data->sku);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":price", $data->price);
        $stmt->bindParam(":attribute", $data->attribute);
        $stmt->bindParam(":value", $data->value);

        if ($stmt->execute()) {
            return ["status" => 1, "message" => "Data created."];
        } else {
            return ["status" => 0, "message" => "Failed to create data."];
        }
    }

    public function updateProduct($data)
    {
        $query = "UPDATE products SET SKU = :SKU, name = :name, price = :price, attribute = :attribute, value = :value ";
        $query .=  "WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":id", $data->id);
        $stmt->bindParam(":SKU", $data->SKU);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":price", $data->price);
        $stmt->bindParam(":attribute", $data->attribute);
        $stmt->bindParam(":value", $data->value);

        if ($stmt->execute()) {
            return ["status" => 1, "message" => "Data updated."];
        } else {
            return ["status" => 0, "message" => "Failed to update data."];
        }
    }

    public function deleteProduct($sku)
    {
        $query = "DELETE FROM products WHERE SKU = :SKU";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(":SKU", $sku);

        if ($stmt->execute()) {
            return ["status" => 1, "message" => "Data deleted."];
        } else {
            return ["status" => 0, "message" => "Failed to delete data."];
        }
    }
}

$initConnection = new DataBase("fdb1027.freehostingeu.com:3306", "4308662_assignment", "!Daly123", "4308662_assignment");
$connection = $initConnection->connect();

$product = new Product($connection);

$method = $_SERVER["REQUEST_METHOD"];
switch ($method) {
    case "GET":
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        if (isset($path[3])) {

            $sku = $path[3];
            $result = $product->getProductBySKU($sku);
            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Product not found."]);
            }
        } else {
            $result = $product->getAllProducts();
            echo json_encode($result);
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        $result = $product->saveProduct($data);
        echo json_encode($result);
        break;

    case "PUT":
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        if (isset($path[3])) {
            $sku = $path[3];
            $data = json_decode(file_get_contents("php://input"));
            $data->sku = $sku;
            $result = $product->updateProduct($data);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => 0, "message" => "Invalid request."]);
        }
        break;

    case "DELETE":
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        if (isset($path[3])) {
            $sku = $path[3];
            $result = $product->deleteProduct($sku);
            echo json_encode($result);
        } else {
            echo json_encode(["status" => 0, "message" => "Invalid request."]);
        }
        break;

    default:
        echo json_encode(["status" => 0, "message" => "Invalid request method."]);
        break;
}
