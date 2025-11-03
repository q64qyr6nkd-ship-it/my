<?php
session_start();
include "../db/db.php";

// تحقق صلاحية المدير
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'admin')){
    header("Location: ../admin/auth/login.php");
    exit;
}

// === 1. جلب المنتجات المخفية ===
$hiddenStmt = $conn->prepare("SELECT * FROM products WHERE available = 0 ORDER BY id DESC");
$hiddenStmt->execute();
$hiddenProducts = $hiddenStmt->fetchAll(PDO::FETCH_ASSOC);

// === 2. حساب أكثر 10 منتجات مبيعاً ===
$salesStmt = $conn->prepare("SELECT products FROM orders WHERE status='delivered'");
$salesStmt->execute();
$allOrders = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

$sales = [];
foreach($allOrders as $order){
    $products = json_decode($order['products'], true);
    foreach($products as $p){
        $id = $p['product_id'];
        $sales[$id]['name'] = $p['name'];
        $sales[$id]['quantity'] = ($sales[$id]['quantity'] ?? 0) + $p['quantity'];
        $sales[$id]['total'] = ($sales[$id]['total'] ?? 0) + ($p['quantity'] * $p['price']);
    }
}

// ترتيب حسب الكمية
usort($sales, function($a, $b){
    return $b['quantity'] - $a['quantity'];
});
$top10 = array_slice($sales, 0, 10);

// === 3. بيانات المبيعات اليومية للرسم البياني ===
$chartStmt = $conn->prepare("SELECT DATE(order_date) as day, SUM(total_price) as total_sales 
                             FROM orders WHERE status='delivered' GROUP BY DATE(order_date) ORDER BY day ASC");
$chartStmt->execute();
$chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);
$chartLabels = [];
$chartValues = [];
foreach($chartData as $row){
    $chartLabels[] = $row['day'];
    $chartValues[] = $row['total_sales'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة المبيعات - حلويات العالم</title>
<link rel="stylesheet" href="../css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.container { max-width: 1200px; margin: 40px auto; padding: 20px; }
h1 { text-align: center; margin-bottom: 30px; }
.table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
.table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: center; }
.table th { background-color: #800020; color: #fff; }
.button { padding: 6px 12px; background-color: #800020; color: #fff; border: none; cursor: pointer; border-radius: 5px; }
.button:hover { background-color: #a00030; }
</style>
</head>
<body>

<header class="header">
  <div class="logo">حلويات العالم</div>
  <nav class="navbar">
    <ul>
      <li><a href="../index.php">الرئيسية</a></li>
      <li><a href="admin.php">لوحة الإدارة</a></li>
      <li><a href="admin_sales.php">لوحة المبيعات</a></li>
    </ul>
  </nav>
</header>

<div class="container">
    <h1>المنتجات المخفية</h1>
    <table class="table">
        <tr>
            <th>المنتج</th>
            <th>السعر</th>
            <th>الكمية</th>
            <th>إظهار المنتج</th>
        </tr>
        <?php foreach($hiddenProducts as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo $p['price']; ?></td>
            <td><?php echo $p['quantity']; ?></td>
            <td>
                <button class="show-btn button" data-id="<?php echo $p['id']; ?>">إظهار</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h1>أكثر 10 منتجات مبيعاً</h1>
    <table class="table">
        <tr>
            <th>المنتج</th>
            <th>الكمية المباعة</th>
            <th>الإجمالي</th>
        </tr>
        <?php foreach($top10 as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo $p['quantity']; ?></td>
            <td><?php echo $p['total']; ?> د.ل</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h1>المبيعات اليومية</h1>
    <canvas id="salesChart" style="max-width:100%;"></canvas>
</div>

<script>
// إعادة تفعيل المنتج المخفي
document.querySelectorAll('.show-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const id = btn.dataset.id;
        fetch('show_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        }).then(res=>res.json()).then(data=>{
            if(data.success){
                alert('✅ تم إظهار المنتج');
                location.reload();
            } else {
                alert('❌ خطأ: '+data.message);
            }
        });
    });
});

// Chart.js
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'إجمالي المبيعات اليومية',
            data: <?php echo json_encode($chartValues); ?>,
            borderColor: '#800020',
            backgroundColor: 'rgba(128,0,32,0.2)',
            tension: 0.3
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

</body>
</html>