<?php
header('Content-Type: application/json');
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';

$sql = "UPDATE products SET status='rejected' WHERE id=$id";
$response = $conn->query($sql)
    ? ["success" => true, "message" => "Product rejected"]
    : ["success" => false, "message" => "Error: " . $conn->error];

echo json_encode($response);
$conn->close();
?>