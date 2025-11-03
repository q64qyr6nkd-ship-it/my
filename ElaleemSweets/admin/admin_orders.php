<?php
session_start();
include "../db/db.php";

// تحقق صلاحية المدير
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../admin/auth/login.php");
    exit;
}

// جلب كل الطلبات
try {
    $stmt = $conn->prepare("SELECT * FROM orders ORDER BY order_id DESC");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e){
    echo "<p>حدث خطأ: ".$e->getMessage()."</p>";
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة إدارة الطلبات - حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.admin-orders { max-width: 1200px; margin: 40px auto; padding: 20px; }
.admin-orders h1 { text-align: center; margin-bottom: 30px; }
.orders-table { width: 100%; border-collapse: collapse; }
.orders-table th, .orders-table td { padding: 12px 15px; border: 1px solid #ddd; text-align: center; }
.orders-table th { background-color: #800020; color: #fff; }
.update-btn { background-color: #800020; color: #fff; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: 0.3s; }
.update-btn:hover { background-color: #a00030; }
.status-preparing { background-color: #f8d7da; }
.status-ready { background-color: #fff3cd; }
.status-delivering { background-color: #cce5ff; }
.status-delivered { background-color: #d4edda; }
.status-reject { background-color: #f5c6cb; }
.product-checkbox { margin-right: 5px; }
</style>
</head>
<body>

<header class="header">
  <div class="logo">حلويات العالم</div>
  <nav class="navbar">
    <ul>
      <li><a href="../index.php">الرئيسية</a></li>
      <li><a href="../admin/admin.php">لوحة الإدارة</a></li>
      <li><a href="admin_sales.php">لوحة المبيعات</a></li>
    </ul>
  </nav>
</header>

<section class="admin-orders">
    <h1>طلبات الزبائن</h1>

    <?php if(!empty($orders)): ?>
        <table class="orders-table">
            <tr>
                <th>رقم الطلب</th>
                <th>اسم المستخدم</th>
                <th>المنتجات</th>
                <th>السعر الكلي</th>
                <th>الحالة</th>
                <th>تاريخ الطلب</th>
                <th>تحديث الحالة</th>
            </tr>

            <?php foreach($orders as $order): ?>
                <?php
                // جلب اسم المستخدم
                $stmtUser = $conn->prepare("SELECT full_name FROM users WHERE id=?");
                $stmtUser->execute([$order['user_id']]);
                $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
                $userName = $user ? $user['full_name'] : 'مستخدم مجهول';

                // تحويل الحالة إلى كلاس للألوان
                $statusClass = '';
                switch($order['status']){
                    case 'preparing': $statusClass='status-preparing'; break;
                    case 'ready': $statusClass='status-ready'; break;
                    case 'delivering': $statusClass='status-delivering'; break;
                    case 'delivered': $statusClass='status-delivered'; break;
                    case 'reject': $statusClass='status-reject'; break;
                }

                $products = json_decode($order['products'], true);
                ?>
                <tr class="<?php echo $statusClass; ?>">
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($userName); ?></td>
                    <td>
                        <?php foreach($products as $p): ?>
                            <?php
                            $stmtP = $conn->prepare("SELECT name FROM products WHERE id=?");
                            $stmtP->execute([$p['product_id']]);
                            $pData = $stmtP->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <?php echo htmlspecialchars($pData['name']); ?> × <?php echo $p['quantity']; ?><br>
                        <?php endforeach; ?>
                    </td><td><?php echo $order['total_price']; ?> د.ل</td>
                    <td><?php echo $order['status']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td>
                        <form class="update-status-form" data-order="<?php echo $order['order_id']; ?>">
                            <select name="status">
                                <option value="preparing" <?php if($order['status']=="preparing") echo "selected"; ?>>جاري التحضير</option>
                                <option value="ready" <?php if($order['status']=="ready") echo "selected"; ?>>جاهز</option>
                                <option value="delivering" <?php if($order['status']=="delivering") echo "selected"; ?>>جاري التوصيل</option>
                                <option value="delivered" <?php if($order['status']=="delivered") echo "selected"; ?>>تم التوصيل</option>
                                <option value="reject" <?php if($order['status']=="reject") echo "selected"; ?>>مرفوض</option>
                            </select>
                            <button type="submit" class="update-btn">تحديث</button>
                        </form>
                        <div class="reject-products" id="reject-<?php echo $order['order_id']; ?>" style="display:none; margin-top:10px;">
                            <form class="reject-products-form" data-order="<?php echo $order['order_id']; ?>">
                                <?php foreach($products as $p): ?>
                                    <?php
                                    $stmtP = $conn->prepare("SELECT name FROM products WHERE id=?");
                                    $stmtP->execute([$p['product_id']]);
                                    $pData = $stmtP->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <label>
                                        <input type="checkbox" class="product-checkbox" name="reject_products[]" value="<?php echo $p['product_id']; ?>">
                                        <?php echo htmlspecialchars($pData['name']); ?>
                                    </label><br>
                                <?php endforeach; ?>
                                <button type="submit" class="update-btn" style="margin-top:5px;">تأكيد الرفض</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>لا يوجد طلبات حالياً.</p>
    <?php endif; ?>
</section>

<script>
// عرض قائمة المنتجات عند اختيار "مرفوض"
document.querySelectorAll('.update-status-form select').forEach(select => {
    select.addEventListener('change', function() {
        const orderId = this.closest('form').dataset.order;
        const rejectDiv = document.getElementById('reject-'+orderId);
        if(this.value === 'reject'){
            rejectDiv.style.display = 'block';
        } else {
            rejectDiv.style.display = 'none';
        }
    });
});

// تحديث حالة الطلبية
document.querySelectorAll('.update-status-form').forEach(form => {
    form.addEventListener('submit', e=>{
        e.preventDefault();
        const orderId = form.dataset.order;
        const status = form.querySelector('select[name="status"]').value;

        fetch("update_order_status.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "order_id=" + orderId + "&status=" + encodeURIComponent(status)
        })
        .then(res => res.json())
        .then(data=>{
            if(data.success){
                alert(data.message);
                location.reload();
            } else alert(data.message);
        });
    });
});

// تحديث المنتجات المرفوضة
document.querySelectorAll('.reject-products-form').forEach(form=>{
    form.addEventListener('submit', e=>{
        e.preventDefault();
        const orderId = form.dataset.order;
        const products = Array.from(form.querySelectorAll('input[name="reject_products[]"]:checked')).map(i=>i.value);

        if(products.length === 0){
            alert("اختر منتج أو أكثر للرفض");
            return;
        }

        fetch("reject_products.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: "order_id=" + orderId + "&products=" + encodeURIComponent(JSON.stringify(products))
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                alert(data.message);
                location.reload();
            } else alert(data.message);
        });
    });
});
</script>

</body>
</html>