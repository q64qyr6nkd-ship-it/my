<?php
header('Content-Type: application/json');
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$name = $data['name'] ?? '';
$price = $data['price'] ?? '';
$description = $data['description'] ?? '';

$sql = "INSERT INTO products (name, price, description) VALUES ('$name', '$price', '$description')";
$response = $conn->query($sql)
    ? ["success" => true, "message" => "Product added"]
    : ["success" => false, "message" => "Error: " . $conn->error];

echo json_encode($response);
$conn->close();
?>