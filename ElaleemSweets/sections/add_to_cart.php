<?php
session_start();
include "../db/db.php";

header('Content-Type: application/json');

if(!isset($_SESSION['user'])){
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول']);
    exit;
}

$userId = $_SESSION['user']['id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity   = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if($product_id <= 0 || $quantity <= 0){
        echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
        exit;
    }

    // جلب بيانات المنتج من قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$product){
        echo json_encode(['success' => false, 'message' => 'المنتج غير موجود']);
        exit;
    }

    try {
        // تحقق إذا المنتج موجود مسبقًا في قاعدة بيانات السلة للمستخدم
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
        $stmt->execute([$userId, $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if($existing){
            // تحديث الكمية
            $newQty = $existing['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
            $stmt->execute([$newQty, $existing['id']]);
        } else {
            // إضافة المنتج للسلة
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $product_id, $quantity]);
        }

        echo json_encode(['success' => true]);

    } catch(PDOException $e){
        echo json_encode(['success' => false, 'message' => 'حدث خطأ: '.$e->getMessage()]);
    }

    exit;
}

echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);