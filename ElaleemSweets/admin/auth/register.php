<?php
// بداية الملف مباشرة بدون أي فراغ أو سطر قبل <?php
session_start();
include "../../db/db.php"; // الاتصال بقاعدة البيانات

$errors = [];
$success = "";

// عند إرسال الفورم
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // التحقق من الحقول
    if(!$full_name) $errors[] = "الاسم الكامل مطلوب";
    if(!$email) $errors[] = "البريد الإلكتروني مطلوب";
    if(!$password) $errors[] = "كلمة المرور مطلوبة";
    if(!$phone) $errors[] = "رقم الهاتف مطلوب";

    // تحقق من وجود البريد مسبقًا
    if(empty($errors)){
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0){
            $errors[] = "البريد الإلكتروني مستخدم مسبقًا";
        }
    }

    // إضافة المستخدم
    if(empty($errors)){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())");
        $stmt->execute([$full_name, $email, $hashed_password, $phone]);
        $success = "تم التسجيل بنجاح! يمكنك تسجيل الدخول الآن.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل مستخدم جديد - حلويات العالم</title>
<link rel="stylesheet" href="../admin_style.css">
</head>
<body>

<div class="auth-container">
    <h1>تسجيل مستخدم جديد</h1>

    <?php if(!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>الاسم الكامل:</label>
        <input type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? '') ?>">

        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>كلمة المرور:</label>
        <input type="password" name="password" required>

        <label>رقم الهاتف:</label>
        <input type="text" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? '') ?>">

        <button type="submit">تسجيل</button>
    </form>

    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
</div>

</body>
</html>