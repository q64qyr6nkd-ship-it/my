<?php
session_start();
include "../db/db.php";

header('Content-Type: application/json');

if(!isset($_SESSION['user'])){
    echo json_encode(['success'=>false,'message'=>'يرجى تسجيل الدخول']);
    exit;
}

$userId = $_SESSION['user']['id'];
$cartId = $_POST['cart_id'] ?? null;
$action = $_POST['action'] ?? null;

if(!$cartId || !$action){
    echo json_encode(['success'=>false,'message'=>'بيانات غير صحيحة']);
    exit;
}

try {
    if($action === 'increase'){
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $userId]);
    } elseif($action === 'decrease'){
        // نقص الكمية فقط إذا كانت أكبر من 1
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ? AND user_id = ? AND quantity > 1");
        $stmt->execute([$cartId, $userId]);
    } elseif($action === 'remove'){
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $userId]);
    } else {
        echo json_encode(['success'=>false,'message'=>'إجراء غير صالح']);
        exit;
    }

    echo json_encode(['success'=>true]);

} catch(PDOException $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
