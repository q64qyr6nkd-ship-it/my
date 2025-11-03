<?php
include "../db/db.php"; // الاتصال بقاعدة البيانات

// التحقق من البحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// جلب جميع الأقسام الموجودة
try {
    $stmt = $conn->prepare("SELECT DISTINCT category FROM products ORDER BY category");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    echo "<p>خطأ في جلب الأقسام: " . $e->getMessage() . "</p>";
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>لوحة إدارة حلويات العالم</title>
<link rel="stylesheet" href="Admin_style.css">
</head>
<body>
<div class="container">

<h1>لوحة إدارة حلويات العالم</h1>

<!-- زر إضافة منتج جديد -->
<a href="add_product.php" class="add-button">إضافة منتج جديد</a>

<!-- نموذج البحث -->
<form method="GET" class="search-form">
    <input type="text" name="search" placeholder="ابحث عن منتج..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">بحث</button>
</form>

<?php if(!empty($categories)): ?>
    <?php foreach($categories as $category): ?>
        <?php
        // جلب المنتجات الخاصة بكل قسم مع البحث إذا تم
        if($search !== '') {
            $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND name LIKE ?");
            $stmt->execute([$category, "%$search%"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
            $stmt->execute([$category]);
        }
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <div class="category-section">
            <h2><?php echo htmlspecialchars($category); ?></h2>

            <?php if(!empty($products)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>السعر</th>
                            <th>القسم</th>
                            <th>الوصف</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td><img src="../images/<?php echo htmlspecialchars($product['image']); ?>" width="60" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['price']); ?> د.ل</td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td><?php echo htmlspecialchars($product['description']); ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">تعديل</a> |
                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('هل تريد حذف هذا المنتج؟');">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>لا توجد منتجات في هذا القسم.</p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">لا توجد أقسام في قاعدة البيانات</p>
<?php endif; ?>

</div>
</body>
</html>