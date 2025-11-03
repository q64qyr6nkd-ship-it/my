<?php
session_start();
include "../db/db.php";

if(!isset($_SESSION['user'])){
    header("Location: ../admin/auth/login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_id DESC LIMIT 1");
    $stmt->execute([$userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$order){
        $orderProducts = [];
        $total = 0;
        $status = '';
    } else {
        $orderProducts = json_decode($order['products'], true);
        $total = $order['total_price'];
        $status = $order['status'];

        $statusMap = [
            'preparing'=>'جاري التحضير',
            'ready'=>'جاهز',
            'delivering'=>'جاري التوصيل',
            'delivered'=>'تم التوصيل',
            'reject'=>'مرفوض'
        ];
        $displayStatus = $statusMap[$status] ?? $status;
    }
}catch(PDOException $e){
    $orderProducts=[];
    $total=0;
    $status='';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>حالة الطلب - حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.order-status { text-align:center; font-size:20px; margin-bottom:20px; font-weight:bold; color:#800020; }
.order-reason { color:red; text-align:center; font-weight:bold; margin-bottom:20px; }
.order-card { display:flex; margin-bottom:15px; border:1px solid #ccc; padding:10px; border-radius:8px; }
.order-card img { width:100px; height:100px; object-fit:cover; margin-right:15px; }
.order-details h4{ margin:0 0 10px 0; }
.total { text-align:center; font-size:18px; font-weight:bold; margin-top:20px; }
.confirm-btn { display:block; width:200px; margin:20px auto; text-align:center; background-color:#800020; color:#fff; padding:10px; border-radius:6px; text-decoration:none; }
</style>
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

<section class="order-section">
    <h1>حالة طلبك</h1>

    <?php if(!empty($orderProducts)): ?>
        <div class="order-status">حالة الطلب: <?php echo htmlspecialchars($displayStatus); ?></div>

        <?php foreach($orderProducts as $item): ?>
            <?php
            $stmt = $conn->prepare("SELECT image, name, available FROM products WHERE id=?");
            $stmt->execute([$item['product_id']]);
            $productData = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="order-card">
                <img src="../images/<?php echo htmlspecialchars($productData['image']); ?>" alt="<?php echo htmlspecialchars($productData['name']); ?>">
                <div class="order-details">
                    <h4><?php echo htmlspecialchars($productData['name']); ?></h4>
                    <p>السعر: <?php echo $item['price']; ?> د.ل</p>
                    <p>الكمية: <?php echo $item['quantity']; ?></p>
                    <p>الإجمالي: <?php echo $item['price'] * $item['quantity']; ?> د.ل</p>
                    <?php if($productData['available']==0): ?>
                        <p style="color:red;font-weight:bold;">❌ هذا المنتج مرفوض: الكمية غير متوفرة</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total">الإجمالي الكلي: <?php echo $total; ?> د.ل</div>
        <a href="../index.php" class="confirm-btn">العودة للأقسام</a>
    <?php else: ?>
        <p class="empty-order">لا يوجد طلبات حالياً.</p>
        <a href="../index.php" class="confirm-btn">استعراض الأقسام</a>
    <?php endif; ?>
</section>

<footer>
    <p>© 2025 حلويات العالم - جميع الحقوق محفوظة</p>
</footer>
</body>
</html>