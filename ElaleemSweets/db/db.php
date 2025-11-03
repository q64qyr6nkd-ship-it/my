<?php
$host = "localhost";          // السيرفر المحلي
$user = "root";               // اسم المستخدم الافتراضي
$password = "";               // كلمة المرور (فارغة في XAMPP عادة)
$dbname = "ElaleemSweetsDB";  // اسم قاعدة البيانات

try {
    // إنشاء اتصال PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // ضبط وضع الأخطاء
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // رسالة خطأ واضحة إذا فشل الاتصال
    echo "<p>فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "</p>";
    exit();
}