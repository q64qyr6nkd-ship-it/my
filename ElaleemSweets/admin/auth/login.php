<?php
session_start();
include "../../db/db.php";

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            // تسجيل الجلسة
            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // بعد تسجيل الدخول نعيد المستخدم للصفحة الرئيسية
            header("Location: ../../index.php");
            exit();
        } else {
            $error = "بيانات الدخول غير صحيحة";
        }
    } catch(PDOException $e){
        $error = "حدث خطأ: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول - حلويات العالم</title>
<link rel="stylesheet" href="../admin_style.css">
</head>
<body>

<div class="auth-container">
    <h1>تسجيل الدخول</h1>

    <?php if(!empty($error)): ?>
        <p class="error-msg"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" placeholder="example@email.com" required>

        <label>كلمة المرور:</label>
        <input type="password" name="password" placeholder="كلمة المرور" required>

        <button type="submit" class="auth-btn">تسجيل الدخول</button>
    </form>

    <p>ليس لديك حساب؟ <a href="register.php">سجل الآن</a></p>
</div>

</body>
</html>