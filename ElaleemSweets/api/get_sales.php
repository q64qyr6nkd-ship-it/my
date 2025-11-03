<?php
header('Content-Type: application/json');
include 'config.php';

$result = $conn->query("SELECT SUM(price) AS total_sales FROM orders WHERE status='completed'");
$sales = $result->fetch_assoc();

echo json_encode(["success" => true, "data" => $sales]);
$conn->close();
?>