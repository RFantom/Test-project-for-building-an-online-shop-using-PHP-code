<?php 
require_once "admin_auth.php";
require_once "../config.php";
$result = $conn->query("SELECT COUNT(*) AS total FROM products");
$product_count = $result->fetch_assoc()["total"];
$result = $conn->query("SELECT COUNT(*) AS total FROM users");
$user_count = $result->fetch_assoc()["total"];
$result = $conn->query("SELECT COUNT(*) AS total FROM orders");
$order_count = $result->fetch_assoc()["total"];
$result = $conn->query("SELECT COALESCE(SUM(total_price), 0) AS total FROM orders");
$total_sales = $result->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>پنل مدیریت</title>
        <style>*{
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #f4f4f4;
            color: #222;
        }
        .header {
            background: #222;
            color: white;
            padding: 20px 40px;
            display:flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            flex: 1;
        }
        .header a {
            color: white;
            text-decoration: none;
            background: #444;
            padding: 10px 18px;
            border-radius: 8px;
            white-space: nowrap;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .welcome {
            margin-bottom: 30px;
        }
        .welcome h2 {
            margin-bottom: 5px;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        .card h3 {
            margin-top: 0;
            color: #666;
            font-size: 16px;
        }
        .number {
            font-size: 30px;
            font-weight: bold;
            margin-top: 15px;
        }
        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .menu a {
            background: white;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: #222;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            font-size: 18px;
            transition:0.2s;
        }
        .menu a:hover {
            transform: translateY(-3px);
        }
        @media (max-width: 800px){
            .cards {
                grid-template-columns: repeat(2,1fr);
            }
            .menu {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 500px){
            .cards {
                grid-template-columns: 1fr;
            }
            .header {
                padding: 15px;
                gap: 10px;
            }
            .header h1 {
                font-size: 18px;
            }
            .header a {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
        </style>
    </head>
    <body>
        <header class="header">
            <h1>پنل مدیریت فروشگاه🛠</h1>
            <a href="../products.php">فروشگاه🏠</a>
    </body>
    </header>
    <div class="container">
        <div class="welcome">
            <h2>خوش امدین👋</2>
            <p>
                از اینجا می توانید فروشگاه را مدیریت کنید.
            </p>
    </div>
    <div class="cards">
        <div class="card">
            <h3>محصولات📦</h3>
            <div class="number">
                <?php echo $product_count; ?>
            </div>
        </div>
        <div class="card">
            <h3>کاربران👤</h3>
            <div class="number">
                <?php echo $user_count; ?>
            </div>
        </div>
        <div class="card">
            <h3>سفارش ها🛒</h3>
            <div class="number">
                <?php echo $order_count; ?>
            </div>
        </div>
        <div class="card">
            <h3>فروش کل💰</h3>
            <div class="number">
                <?php echo number_format($total_sales); ?>
                تومان
            </div>
        </div>
    </div>
    <div class="menu">
        <a href="products.php">مدیریت محصولات📦</a>
        <a href="add_product.php">افزودن محصول➕</a>
        <a href="orders.php">مدیریت سفارش🛒</a>
        <a href="users.php">مدیرت کاربران👤</a>
        <a href="../products.php">مشاهده فروشگاه🏠</a>
        <a href="../logout.php">خروج از حساب🚪</a>
    </div>
    </div>
    </body>
</html>