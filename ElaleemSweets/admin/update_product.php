<?php
include "../db/db.php";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // تحقق إذا تم رفع صورة جديدة
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $imageName = time() . "_" . $_FILES['image']['name'];
        $target = "../images/" . $imageName;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
            // تحديث كل شيء بما فيها الصورة
            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, description=?, image=? WHERE id=?");
            $stmt->execute([$name, $price, $category, $description, $imageName, $id]);
        }
    } else {
        // تحديث بدون تغيير الصورة
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, description=? WHERE id=?");
        $stmt->execute([$name, $price, $category, $description, $id]);
    }

    // إعادة التوجيه للوحة الإدارة
    header("Location: admin.php");
    exit();
} else {
    die("الوصول غير مسموح");
}
?>