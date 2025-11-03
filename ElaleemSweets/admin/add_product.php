<?php
include "../db/db.php";

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // رفع الصورة
    $image = $_FILES['image']['name'];
    $target = "../images/".basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // إدخال البيانات في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, category, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $category, $image]);

    echo "<p class='success'>تم إضافة المنتج بنجاح!</p>";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>إضافة منتج جديد</title>
<link rel="stylesheet" href="Admin_style.css">
</head>
<body>
<div class="container">
<h1>إضافة منتج جديد</h1>

<form action="" method="post" enctype="multipart/form-data">
<label>اسم المنتج:</label>
<input type="text" name="name" required>

<label>السعر:</label>
<input type="number" step="0.01" name="price" required>

<label>القسم:</label>
<select name="category" required>
<option value="eastern">الحلويات الشرقية</option>
<option value="moroccan">الحلويات المغربية</option>
<option value="almond">اللوزيات</option>
<option value="chocolate">الشكلاطة</option>
<option value="cake">الكعكات</option>
<option value="juices">العصائر الطبيعية</option>
<option value="tort">التورتات</option>
</select>

<label>الوصف:</label>
<textarea name="description" required></textarea>

<label>صورة المنتج:</label>
<input type="file" name="image" accept="image/*" required>

<button type="submit" name="submit">إضافة المنتج</button>
</form>

<a href="admin.php" class="back-button">رجوع للوحة الإدارة</a>
</div>
</body>
</html>