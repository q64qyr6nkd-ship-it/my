<?php
session_start();
include "../db/db.php";

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// جلب آخر طلب للمستخدم
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_id DESC LIMIT 1");
$stmt->execute([$userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    header("Location: index.php");
    exit;
}

// معالجة الفورم عند الضغط على "التالي"
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $city = trim($_POST['city']);
    $address = trim($_POST['address']);
    $landmark = trim($_POST['landmark']);
    $phone = trim($_POST['phone']);

    // تحديث بيانات التوصيل في قاعدة البيانات
    $update = $conn->prepare("UPDATE orders SET delivery_city=?, delivery_address=?, delivery_landmark=?, delivery_phone=? WHERE order_id=?");
    $update->execute([$city, $address, $landmark, $phone, $order['order_id']]);

    // بعد حفظ البيانات، توجيه المستخدم لصفحة حالة الطلب
    header("Location: order_status.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بيانات التوصيل - حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="auth-container">
    <h1>بيانات التوصيل</h1>

    <form method="POST" class="auth-form">
        <label>المدينة:</label>
        <input type="text" name="city" placeholder="ادخل المدينة" required>

        <label>العنوان التفصيلي:</label>
        <textarea name="address" placeholder="ادخل العنوان بالكامل" required></textarea>

        <label>أقرب علامة دالة:</label>
        <input type="text" name="landmark" placeholder="مثال: بجانب المدرسة">

        <label>رقم للتواصل:</label>
        <input type="text" name="phone" placeholder="رقم الهاتف" required>

        <button type="submit" class="auth-btn">التالي</button>
    </form>
</div>

</body>
</html>