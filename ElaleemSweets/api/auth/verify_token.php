<?php
header('Content-Type: application/json');
require_once 'jwt_helper.php';

$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

if (!$token) {
    echo json_encode(["success" => false, "message" => "توكن مفقود"]);
    exit();
}

try {
    $decoded = JWT::decode($token, "secret_key");
    echo json_encode(["success" => true, "user" => $decoded]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "توكن غير صالح"]);
}
?>