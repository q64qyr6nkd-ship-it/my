<?php
header('Content-Type: application/json');
include 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["success" => false, "message" => "Missing ID"]);
    exit();
}

$result = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $result->fetch_assoc();

echo json_encode(["success" => true, "data" => $product]);
$conn->close();
?>