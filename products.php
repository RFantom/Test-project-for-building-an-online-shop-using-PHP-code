<?php

session_start();

require_once "config.php";

$is_admin =false;
if(isset($_SESSION["user_id"])){
    $user_id = $_SESSION["user_id"];
    $stmt= $conn->prepare("SELECT role FROM users WHERE id =?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    if ($user && $user["role"] === "admin"){
        $is_admin = true;
    }
}

// بررسی اینکه کاربر وارد شده یا نه
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// دریافت اطلاعات 4 محصول
$stmt = $conn->prepare(
    "SELECT id, name, description, price, image
     FROM products
     ORDER BY id DESC"
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>محصولات</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        /* Header */

        header {
            background: #222;
            color: white;

            padding: 18px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome {
            font-size: 18px;
        }

        .logout {
            background: #e74c3c;
            color: white;

            padding: 10px 18px;

            border-radius: 7px;

            text-decoration: none;
        }

        .logout:hover {
            background: #c0392b;
        }

        /* Main */

        .container {
            max-width: 1100px;

            margin: 40px auto;

            padding: 0 20px;
        }

        h1 {
            text-align: center;

            margin-bottom: 35px;
        }

        /* Products */

        .products {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;
        }

        .product {
            background: white;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 5px 15px rgba(0,0,0,0.08);

            transition: 0.2s;
        }

        .product:hover {
            transform: translateY(-5px);
        }

        .product img {
            width: 100%;

            height: 220px;

            object-fit: cover;

            display: block;
        }

        .product-info {
            padding: 18px;
        }

        .product-name {
            font-size: 20px;

            font-weight: bold;

            margin-bottom: 10px;
        }

        .description {
            color: #666;

            font-size: 14px;

            line-height: 1.7;

            min-height: 48px;
        }

        .price {
            margin-top: 15px;

            font-size: 18px;

            font-weight: bold;
        }
        .header-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cart {
            background: #3498db;
            color: white;
            padding: 10px 18px;
            border-radius: 7px;
            text-decoration: none;
            position: relative;
        }
        .cart:hover {
            background: #2980b9;
        }
        .cart-count {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            min-width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content : center;
            font-size: 12px;
            margin-right: 5px;
        }
        .add-cart {
            width: 100%;
            margin-top: 15px;
            padding: 11px;
            border-radius: 7px;
            background: #222;
            color: white;
            cursor: pointer;
            font-size: 15px;
        }
        .add-cart:hover {
            background: #444;
        }
        .orders {
            background: #27ae60;
            color: white;
            padding: 10px 18px;
            border-radius: 7px;
            text-decoration: none;
        }
        .orders:hover {
            background: #219150;
        }
        .admin-panel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            justify-self: center;
            gap: 6px;
            padding: 10px 16px;
            background: #222;
            color: white;
            border-radius: 7px;
            text-decoration: none;
        }
        .admin-panel:hover {
            background: #444;
        }

        /* Responsive */

        @media (max-width: 900px) {

            .products {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 500px) {

            .products {
                grid-template-columns: 1fr;
            }

            header {
                padding: 15px;

                flex-direction: column;

                gap: 15px;
            }

        }

    </style>

</head>

<body>

<header>

    <div class="welcome">

        👋 سلام
        <strong>
            <?php echo htmlspecialchars($_SESSION["username"]); ?>
        </strong>

    </div>
    <div class="header-buttons">
        <a class="cart" href="cart.php">
    🛒 سبد خرید

    <?php
    $cart_count = 0;

    if (isset($_SESSION["cart"])) {
        $cart_count = array_sum($_SESSION["cart"]);
    }
    ?>

    <?php if ($cart_count > 0): ?>

        <span class="cart-count">
            <?php echo $cart_count; ?>
        </span>

    <?php endif; ?>

</a>
<a class="orders" href="my_orders.php">
    📦سفارشات من
</a>
<?php if ($is_admin): ?>
<a class="admin-panel" href="admin/dashboard.php">
        پنل مدیریتی🛠
        </a>
        <?php endif; ?>
    <a class="logout" href="logout.php">
        خروج
    </a>
    </div>
</header>


<main class="container">

    <h1>
        🛍️ محصولات
    </h1>


    <div class="products">

        <?php while ($product = $result->fetch_assoc()): ?>

            <div class="product">

                <?php if (!empty($product["image"])): ?>

                    <img
                        src="<?php echo htmlspecialchars($product["image"]); ?>"
                        alt="<?php echo htmlspecialchars($product["name"]); ?>"
                    >

                <?php else: ?>

                    <div style="
                        height:220px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#ddd;
                    ">
                        بدون تصویر
                    </div>

                <?php endif; ?>


                <div class="product-info">

                    <div class="product-name">

                        <?php
                        echo htmlspecialchars($product["name"]);
                        ?>

                    </div>


                    <div class="description">

                        <?php
                        echo htmlspecialchars($product["description"]);
                        ?>

                    </div>
                    <div class="price">
                        <?php echo number_format($product["price"]); ?> تومان</div>

                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product["id"]; ?>">
                            <button type="submit" class="add-cart">افزودن به سبد خرید🛒</button>
                </form>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</main>

</body>

</html>

<?php

$stmt->close();

?>