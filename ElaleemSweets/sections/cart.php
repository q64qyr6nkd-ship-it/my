<?php
session_start();
include "../db/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../admin/auth/login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

// جلب المنتجات من قاعدة البيانات
try {
    $stmt = $conn->prepare("
        SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }

} catch(PDOException $e){
    echo "<p>حدث خطأ: " . $e->getMessage() . "</p>";
    $cart = [];
    $total = 0;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>السلة - حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">حلويات العالم</div>
  <nav class="navbar">
    <ul>
      <li><a href="../index.php">الرئيسية</a></li>
      <li><a href="../admin/admin.php">لوحة الإدارة</a></li>
    </ul>
  </nav>
</header>

<section class="cart-section">
  <h1>سلة المشتريات</h1>
  <?php if(!empty($cart)): ?>
    <table class="cart-table">
      <tr>
        <th>المنتج</th>
        <th>السعر</th>
        <th>الكمية</th>
        <th>الإجمالي</th>
        <th>إجراء</th>
      </tr>
      <?php foreach($cart as $item): ?>
        <tr data-id="<?php echo $item['cart_id']; ?>">
          <td><?php echo htmlspecialchars($item['name']); ?></td>
          <td><?php echo $item['price']; ?> د.ل</td>
          <td>
            <button class="update-btn decrease">-</button>
            <span><?php echo $item['quantity']; ?></span>
            <button class="update-btn increase">+</button>
          </td>
          <td><?php echo $item['price'] * $item['quantity']; ?> د.ل</td>
          <td><button class="remove-btn">حذف</button></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h3>الإجمالي الكلي: <span id="total"><?php echo $total; ?></span> د.ل</h3>
    <button id="confirm-order">تأكيد الطلب</button>
  <?php else: ?>
    <p>السلة فارغة.</p>
  <?php endif; ?>
</section>

<script>
// تحديث الكمية
document.querySelectorAll(".update-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const row = btn.closest("tr");
    const cartId = row.dataset.id;
    const action = btn.classList.contains("increase") ? "increase" : "decrease";
    fetch("update_cart.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "cart_id=" + cartId + "&action=" + action
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) location.reload();
    });
  });
});

// حذف المنتج
document.querySelectorAll(".remove-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const cartId = btn.closest("tr").dataset.id;
    fetch("update_cart.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "cart_id=" + cartId + "&action=remove"
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) location.reload();
    });
  });
});

// تأكيد الطلب
document.getElementById("confirm-order")?.addEventListener("click", () => {
  fetch("confirm_order.php", { method: "POST" })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      // التوجيه لفورم بيانات التوصيل بدلاً من مباشرة حالة الطلب
      window.location.href = "checkout.php";
    } else {
      alert(data.message);
    }
  });
});
</script>

</body>
</html>