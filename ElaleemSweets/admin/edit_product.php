<?php
include "../db/db.php"; // الاتصال بقاعدة البيانات

if(!isset($_GET['id'])){
    die("المنتج غير محدد!");
}

$id = $_GET['id'];

// جلب بيانات المنتج من قاعدة البيانات
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product){
    die("المنتج غير موجود!");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تعديل المنتج - لوحة الإدارة</title>
<link rel="stylesheet" href="Admin_style.css">
<style>
form {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: #f9f9f9;
}
form label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}
form input[type="text"],
form textarea,
form select {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #aaa;
}
form button {
    padding: 10px 20px;
    background-color: #800020;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
form button:hover {
    background-color: #a00030;
}
</style>
</head>
<body>

<h1 style="text-align:center;">تعديل المنتج</h1>

<form action="update_product.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

    <label>اسم المنتج:</label>
    <input type="text" name="name" value="<?php echo $product['name']; ?>" required>

    <label>السعر:</label>
    <input type="text" name="price" value="<?php echo $product['price']; ?>" required>

    <label>القسم:</label>
    <select name="category" required>
        <option value="eastern" <?php if($product['category']=='eastern') echo "selected"; ?>>الحلويات الشرقية</option>
        <option value="moroccan" <?php if($product['category']=='moroccan') echo "selected"; ?>>الحلويات المغربية</option>
        <option value="nuts" <?php if($product['category']=='nuts') echo "selected"; ?>>اللوزيات</option>
        <option value="chocolate" <?php if($product['category']=='chocolate') echo "selected"; ?>>الشكلاطة</option>
        <option value="cakes" <?php if($product['category']=='cakes') echo "selected"; ?>>الكيكات</option>
        <option value="juices" <?php if($product['category']=='juices') echo "selected"; ?>>العصائر الطبيعية</option>
        <option value="tort" <?php if($product['category']=='tort') echo "selected"; ?>>التورتات</option>
    </select>

    <label>الوصف:</label>
    <textarea name="description" rows="4"><?php echo $product['description']; ?></textarea>

    <label>الصورة الحالية:</label>
    <img src="../images/<?php echo $product['image']; ?>" width="150" alt="<?php echo $product['name']; ?>">

    <label>تغيير الصورة:</label>
    <input type="file" name="image">

    <button type="submit">حفظ التعديلات</button>
</form>

</body>
</html>