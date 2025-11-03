<?php
session_start();
include "../db/db.php"; // الاتصال بقاعدة البيانات

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = 'chocolate' AND available = 1");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<p>خطأ في جلب المنتجات: " . $e->getMessage() . "</p>";
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>الشوكولاتة- حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header class="header">
    <div class="logo">حلويات العالم</div>
    <nav class="navbar">
        <ul>
            <li><a href="../index.php">الرئيسية</a></li>
            <li><a href="../admin/admin.php">لوحة الإدارة</a></li>
            <li class="cart">
                🛒 <a href="cart.php"><span id="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span></a>
            </li>
        </ul>
    </nav>
</header>

<section class="products-section">
    <h1>الشوكولاتة</h1>
    <div class="products-container">
        <?php if(!empty($products)): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="price"><?php echo $product['price']; ?> د.ل</p>
                    <p class="description"><?php echo $product['description']; ?></p>

                    <div class="quantity">
                        <span>1</span>
                        <button class="increase">+</button>
                        <button class="decrease">-</button>
                    </div>

                    <button class="add-to-cart" data-id="<?php echo $product['id']; ?>">أضف للسلة</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>لا توجد منتجات في هذا القسم</p>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>© 2025 حلويات العالم - جميع الحقوق محفوظة</p>
</footer>

<div class="cursor"></div>

<script>
const cartCount = document.querySelector("#cart-count");

// أزرار أضف للسلة
document.querySelectorAll(".add-to-cart").forEach(btn => {
    btn.addEventListener("click", () => {
        const productId = btn.getAttribute("data-id");
        const quantity = parseInt(btn.closest(".product-card").querySelector(".quantity span").textContent);

        fetch("add_to_cart.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "product_id=" + productId + "&quantity=" + quantity
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                let count = parseInt(cartCount.textContent);
                cartCount.textContent = count + quantity;
            } else {
                alert(data.message);
            }
        });
    });
});

// أزرار زيادة / نقصان الكمية
document.querySelectorAll(".increase").forEach(btn => {
    btn.addEventListener("click", () => {
        const span = btn.parentElement.querySelector("span");
        span.textContent = parseInt(span.textContent) + 1;
    });
});

document.querySelectorAll(".decrease").forEach(btn => {
    btn.addEventListener("click", () => {
        const span = btn.parentElement.querySelector("span");
        let val = parseInt(span.textContent);
        if(val > 1) span.textContent = val - 1;
    });
});
</script>

<script src="../js/cart.js"></script>
</body>
</html>