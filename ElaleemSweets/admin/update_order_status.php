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
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$allowed = ['preparing','ready','delivering','delivered','reject'];

if($orderId<=0 || !in_array($status,$allowed,true)){
    echo json_encode(['success'=>false,'message'=>'بيانات غير صحيحة']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE orders SET status=?, reject_reason=NULL WHERE order_id=?");
    $stmt->execute([$status,$orderId]);

    echo json_encode(['success'=>true,'message'=>'تم تحديث الحالة']);
}catch(PDOException $e){
    echo json_encode(['success'=>false,'message'=>'خطأ في قاعدة البيانات: '.$e->getMessage()]);
}