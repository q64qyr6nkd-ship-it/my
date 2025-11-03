<?php
header('Content-Type: application/json');
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['order_id'] ?? '';
$status = $data['status'] ?? '';

$sql = "UPDATE orders SET status='$status' WHERE id=$id";
$response = $conn->query($sql)
    ? ["success" => true, "message" => "Order status updated"]
    : ["success" => false, "message" => "Error: " . $conn->error];

echo json_encode($response);
$conn->close();
?>