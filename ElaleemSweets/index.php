<?php
session_start();
include "db/db.php";

$userName = "";
if(isset($_SESSION['user'])){
    $userName = $_SESSION['user']['full_name'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>حلويات العالم</title>

<style>
/* ===== عام ===== */
:root {
    --main-bg: #fdfaf6;
    --white: #ffffff;
    --dark-red: #4b1e1e;
    --light-gold: #f5e4c3;
    --shadow: rgba(0,0,0,0.1);
}

body {
    font-family: 'Cairo', sans-serif;
    background: var(--main-bg);
    margin: 0;
    padding: 0;
    color: var(--dark-red);
}

/* ===== الهيدر ===== */
.header {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    height: 60px;
    background-color: var(--dark-red);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    z-index: 1000;
}

.logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--white);
}

/* زر الثلاث شرطات */
#menu-toggle {
    cursor: pointer;
    width: 30px;
    height: 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

#menu-toggle span {
    display: block;
    height: 3px;
    background: var(--white);
    border-radius: 2px;
}

/* القائمة الجانبية */
.sidebar {
    position: fixed;
    top: 0;
    right: -250px;
    width: 250px;
    height: 100%;
    background-color: var(--white);
    box-shadow: -2px 0 5px rgba(0,0,0,0.3);
    transition: right 0.3s ease;
    z-index: 1200;
    padding-top: 60px;
}

.sidebar.active {
    right: 0;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.sidebar ul li a, .sidebar ul li span {
    text-decoration: none;
    color: #333;
    display: block;
}

/* واجهة الترحيب */
.hero {
    margin-top: 60px;
    height: 80vh;
    position: relative;
    overflow: hidden;
}

.hero-slider img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.hero-slider img.active {
    display: block;
}

.hero-content {
    position: absolute;
    top: 50%;
    right: 50%;
    transform: translate(50%, -50%);
    text-align: center;
    color: #000;
}

.hero-content h1 {
    font-size: 2rem;
    margin-bottom: 10px;
}

.hero-content p {
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.btn {
    padding: 10px 20px;
    background: var(--dark-red);
    color: var(--white);
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

/* الأقسام */
.sections {
    padding: 50px 20px;
    text-align: center;
}

.sections h2 {
    font-size: 2rem;
    margin-bottom: 30px;
}

.section-link {
    text-decoration: none;
    color: inherit;
    display: block;
    margin-bottom: 20px;
}

.section-box {
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
    cursor: pointer;
    background-color: #f5f5f5;
    color: #000;
    font-weight: bold;
    font-size: 1.2rem;
    background-size: cover;
    background-position: center;
}

.section-box:hover {
    background-color: #dcdcdc;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* فروعنا */
.branches {
    padding: 50px 20px;
    text-align: center;
}

.branches ul {
    list-style: none;
    padding: 0;
}

.branches ul li {
    margin: 10px 0;
}

/* تواصل معنا */
.contact {
    padding: 50px 20px;
    text-align: center;
}

/* الفوتر */
footer {
    padding: 20px;
    text-align: center;
    background-color: var(--dark-red);
    color: var(--white);
}

/* Media Queries */
@media(max-width:768px){
    .hero-content h1 { font-size: 1.5rem; }
    .hero-content p { font-size: 1rem; }
}
</style>
</head>
<body>

<!-- الهيدر -->
<header class="header">
    <div class="logo">حلويات العالم</div>
    <div id="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
</header>

<!-- القائمة الجانبية -->
<nav class="sidebar">
    <ul><li><a href="#hero">الرئيسية</a></li>
        <li><a href="#sections">الأقسام</a></li>
        <li><a href="#branches">فروعنا</a></li>
        <li><a href="#contact">تواصل معنا</a></li>
        <?php if(empty($userName)): ?>
            <li><a href="admin/auth/register.php">التسجيل</a></li>
            <li><a href="admin/auth/login.php">تسجيل الدخول</a></li>
        <?php else: ?>
            <li><span>مرحبا، <?php echo htmlspecialchars($userName); ?></span></li>
            <li><a href="admin/auth/logout.php">تسجيل الخروج</a></li>
        <?php endif; ?>
        <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <li><a href="admin/admin.php">لوحة الإدارة</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- واجهة الترحيب -->
<section class="hero" id="hero">
    <div class="hero-slider">
        <img src="images/eastern1.jpg" class="active">
        <img src="images/eastern2.jpg">
        <img src="images/eastern3.jpg">
    </div>
    <div class="hero-content">
        <h1>مرحبًا بكم في حلويات العالم</h1>
        <p>أفخم الحلويات الشرقية والغربية المصنوعة بأجود المكونات الطبيعية.</p>
        <a href="#sections" class="btn">استعرض الأقسام</a>
    </div>
</section>

<!-- الأقسام -->
<section id="sections" class="sections">
    <h2>الأقسام</h2>
    <a href="sections/eastern.php" class="section-link"><div class="section-box" data-images="images/eastern1.jpg,images/eastern2.jpg">الحلويات الشرقية</div></a>
    <a href="sections/moroccan.php" class="section-link"><div class="section-box" data-images="images/moroccan1.jpg,images/moroccan2.jpg">الحلويات المغربية</div></a>
    <a href="sections/nuts.php" class="section-link"><div class="section-box" data-images="images/nuts1.jpg,images/nuts2.jpg">اللوزيات</div></a>
    <a href="sections/cakes.php" class="section-link"><div class="section-box" data-images="images/cakes1.jpg,images/cakes2.jpg">الكيكات</div></a>
</section>

<!-- فروعنا -->
<section id="branches" class="branches">
    <h2>فروعنا</h2>
    <ul>
        <li>الفرع الأول: تاجوراء الطريق الساحلي</li>
        <li>الفرع الثاني: زاوية الدهماني</li>
        <li>الفرع الثالث: شارع القاهرة</li>
        <li>الفرع الرابع: المنصورة</li>
    </ul>
</section>

<!-- تواصل معنا -->
<section id="contact" class="contact">
    <h2>تواصل معنا</h2>
    <p>📞 0922256698 | 📧 info@elaleemsweets.com</p>
</section>

<!-- الفوتر -->
<footer>
    <p>© 2025 حلويات العالم - جميع الحقوق محفوظة</p>
</footer>

<script>
// القائمة الجانبية
document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }

    // واجهة الترحيب - تغيير الصور كل 5 ثواني
    let heroImages = document.querySelectorAll(".hero-slider img");
    let currentHero = 0;
    setInterval(() => {
        heroImages[currentHero].classList.remove("active");
        currentHero = (currentHero + 1) % heroImages.length;
        heroImages[currentHero].classList.add("active");
    }, 5000);

    // الأقسام - تغيير الخلفية تلقائياً
    document.querySelectorAll(".section-box").forEach(box => {
        let images = box.dataset.images.split(",");
        let index = 0;
        setInterval(() => {
            box.style.backgroundImage = url(${images[index]});
            index = (index + 1) % images.length;
        }, 10000);
    });
});
</script>

</body>
</html>