<?php
header('Content-Type: application/json');
include 'config.php';

$result = $conn->query("SELECT * FROM orders");
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode(["success" => true, "data" => $orders]);
$conn->close();
?>