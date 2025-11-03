<?php
header('Content-Type: application/json');
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';
$name = $data['name'] ?? '';
$price = $data['price'] ?? '';
$description = $data['description'] ?? '';

$sql = "UPDATE products SET name='$name', price='$price', description='$description' WHERE id=$id";
$response = $conn->query($sql)
    ? ["success" => true, "message" => "Product updated"]
    : ["success" => false, "message" => "Error: " . $conn->error];

echo json_encode($response);
$conn->close();
?>