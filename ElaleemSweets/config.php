<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";   // السيرفر المحلي
$username = "root";          // اسم المستخدم الافتراضي في XAMPP
$password = "";              // عادةً فارغة
$dbname = "elaleemsweetsdb";       // اسم قاعدة البيانات اللي أنشأتها

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
} else {
    // اختبار بسيط
    // echo "تم الاتصال بنجاح!";
}
?>