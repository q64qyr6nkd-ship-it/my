<?php
session_start();
include "../db/db.php";

header('Content-Type: application/json');

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    echo json_encode(['success'=>false,'message'=>'غير مسموح']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false,'message'=>'طريقة غير مسموحة']);
    exit;
}

$orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$products = isset($_POST['products']) ? json_decode($_POST['products'],true) : [];

if($orderId<=0 || empty($products)){
    echo json_encode(['success'=>false,'message'=>'بيانات غير صحيحة']);
    exit;
}

try {
    foreach($products as $pid){
        $stmt = $conn->prepare("UPDATE products SET available=0 WHERE id=?");
        $stmt->execute([$pid]);
    }

    // ضع سبب الرفض للطلبية للزبون
    $stmt = $conn->prepare("UPDATE orders SET status='reject', reject_reason='بعض المنتجات غير متوفرة' WHERE order_id=?");
    $stmt->execute([$orderId]);

    echo json_encode(['success'=>true,'message'=>'تم رفض المنتجات المختارة']);
}catch(PDOException $e){
    echo json_encode(['success'=>false,'message'=>'خطأ في قاعدة البيانات: '.$e->getMessage()]);
}