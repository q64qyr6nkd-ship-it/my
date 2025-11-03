<?php
session_start();
include "../db/db.php";

header('Content-Type: application/json');

if(!isset($_SESSION['user'])){
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول']);
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    // جلب كل منتجات السلة للمستخدم
    $stmt = $conn->prepare("SELECT c.quantity, p.id AS product_id, p.name, p.price 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id=?");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$cartItems){
        echo json_encode(['success' => false, 'message' => 'السلة فارغة']);
        exit;
    }

    // تجهيز بيانات products و total_price
    $products = [];
    $totalPrice = 0;
    foreach($cartItems as $item){
        $products[] = [
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity']
        ];
        $totalPrice += $item['price'] * $item['quantity'];
    }

    $productsJson = json_encode($products, JSON_UNESCAPED_UNICODE);

    // إضافة الطلب إلى جدول orders مع الحالة الافتراضية "جاري التحضير"
    $stmt = $conn->prepare("INSERT INTO orders (user_id, products, total_price, status, order_date) 
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$userId, $productsJson, $totalPrice, 'preparing']); // تم تغيير القيمة هنا

    // مسح السلة بعد التأكيد
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->execute([$userId]);

    echo json_encode(['success' => true]);

} catch(PDOException $e){
    echo json_encode(['success' => false, 'message' => 'حدث خطأ: '.$e->getMessage()]);
}