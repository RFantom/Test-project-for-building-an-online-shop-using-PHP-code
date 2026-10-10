<?php

session_start();

require_once "config.php";

// فقط کاربران وارد شده
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}


// اگر سبد خرید وجود نداشت
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


// گرفتن محصولات موجود در سبد
$cart = $_SESSION["cart"];

$products = [];


// اگر سبد خالی نیست
if (!empty($cart)) {

    $ids = array_keys($cart);

    // ساخت ?,?,? برای Prepared Statement
    $placeholders = implode(",", array_fill(0, count($ids), "?"));

    $types = str_repeat("i", count($ids));

    $stmt = $conn->prepare(
        "SELECT id, name, description, price, image
         FROM products
         WHERE id IN ($placeholders)"
    );

    $stmt->bind_param($types, ...$ids);

    $stmt->execute();

    $result = $stmt->get_result();

    while ($product = $result->fetch_assoc()) {

        $products[] = $product;

    }

    $stmt->close();
}


// محاسبه جمع کل
$total = 0;

foreach ($products as $product) {

    $id = $product["id"];

    $quantity = $cart[$id] ?? 0;

    $total += $product["price"] * $quantity;
}

?>

<!DOCTYPE html>

<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>سبد خرید</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f4f4;
        }


        header {

            background: #222;

            color: white;

            padding: 18px 30px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }


        header a {

            color: white;

            text-decoration: none;

            padding: 10px 15px;

            border-radius: 7px;
        }


        .back {

            background: #3498db;
        }


        .container {

            max-width: 1000px;

            margin: 40px auto;

            padding: 0 20px;
        }


        h1 {

            text-align: center;

            margin-bottom: 30px;
        }


        .cart-item {

            background: white;

            padding: 20px;

            margin-bottom: 15px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            gap: 20px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.08);
        }


        .cart-item img {

            width: 120px;

            height: 120px;

            object-fit: cover;

            border-radius: 10px;
        }


        .product-info {

            flex: 1;
        }


        .product-name {

            font-size: 20px;

            font-weight: bold;

            margin-bottom: 10px;
        }


        .unit-price {

            color: #666;

            margin-bottom: 10px;
        }


        .quantity {

            font-size: 16px;

            margin-bottom: 10px;
        }


        .item-total {

            font-weight: bold;

            font-size: 18px;
        }


        .remove {

            background: #e74c3c;

            color: white;

            padding: 9px 14px;

            border-radius: 7px;

            text-decoration: none;
        }


        .remove:hover {

            background: #c0392b;
        }


        .total-box {

            background: white;

            padding: 25px;

            border-radius: 12px;

            margin-top: 25px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.08);
        }


        .total {

            font-size: 24px;

            font-weight: bold;

            margin-bottom: 20px;
        }


        .empty {

            background: white;

            padding: 40px;

            text-align: center;

            border-radius: 12px;

            font-size: 20px;
        }


        @media (max-width: 600px) {

            .cart-item {

                flex-direction: column;

                text-align: center;
            }

            .cart-item img {

                width: 180px;

                height: 180px;
            }

        }
        .quantity {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 8px;
}

.quantity-control form {
    margin: 0;
}

.quantity-control button {
    width: 32px;
    height: 32px;

    border: none;
    border-radius: 6px;

    background: #222;
    color: white;

    font-size: 20px;

    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-control button:hover {
    background: #444;
}

.quantity-number {
    min-width: 30px;

    text-align: center;

    font-size: 18px;

    font-weight: bold;
}

    </style>

</head>


<body>


<header>

    <div>
        🛒 سبد خرید
    </div>


    <a
        class="back"
        href="products.php"
    >
        ← ادامه خرید
    </a>

</header>


<main class="container">

    <h1>
        سبد خرید شما
    </h1>


    <?php if (empty($products)): ?>

        <div class="empty">

            🛒 سبد خرید شما خالی است.

            <br><br>

            <a href="products.php">
                مشاهده محصولات
            </a>

        </div>


    <?php else: ?>


        <?php foreach ($products as $product): ?>

            <?php

            $id = $product["id"];

            $quantity = $cart[$id];

            $item_total =
                $product["price"] * $quantity;

            ?>


            <div class="cart-item">


                <?php if (!empty($product["image"])): ?>

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $product["image"]
                            );
                        ?>"
                        alt="<?php
                            echo htmlspecialchars(
                                $product["name"]
                            );
                        ?>"
                    >

                <?php else: ?>

                    <div style="
                        width:120px;
                        height:120px;
                        background:#ddd;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        border-radius:10px;
                    ">
                        بدون تصویر
                    </div>

                <?php endif; ?>


                <div class="product-info">

                    <div class="product-name">

                        <?php
                        echo htmlspecialchars(
                            $product["name"]
                        );
                        ?>

                    </div>


                    <div class="unit-price">

                        قیمت واحد:

                        <?php
                        echo number_format(
                            $product["price"]
                        );
                        ?>

                        تومان

                    </div>


                    <div class="quantity">

    <span>تعداد:</span>

    <div class="quantity-control">

        <!-- کم کردن -->

        <form method="POST" action="update_cart.php">

            <input
                type="hidden"
                name="product_id"
                value="<?php echo $id; ?>"
            >

            <input
                type="hidden"
                name="action"
                value="decrease"
            >

            <button type="submit">
                −
            </button>

        </form>


        <!-- تعداد -->

        <span class="quantity-number">
            <?php echo $quantity; ?>
        </span>


        <!-- اضافه کردن -->

        <form method="POST" action="update_cart.php">

            <input
                type="hidden"
                name="product_id"
                value="<?php echo $id; ?>"
            >

            <input
                type="hidden"
                name="action"
                value="increase"
            >

            <button type="submit">
                +
            </button>

        </form>

    </div>

</div>


                    <div class="item-total">

                        مبلغ:

                        <?php
                        echo number_format(
                            $item_total
                        );
                        ?>

                        تومان

                    </div>

                </div>


                <a
                    class="remove"
                    href="remove_cart.php?id=<?php
                        echo $id;
                    ?>"
                >
                    حذف
                </a>


            </div>


        <?php endforeach; ?>


        <div class="total-box">

            <div class="total">

                جمع کل:

                <?php
                echo number_format($total);
                ?>

                تومان

            </div>


            <form method="POST" action="checkout.php">

    <button
        type="submit"
        style="
            width:100%;
            padding:13px;
            border:none;
            border-radius:8px;
            background:#222;
            color:white;
            font-size:16px;
            cursor:pointer;
        "
    >
        ادامه و ثبت سفارش
    </button>

</form>

        </div>


    <?php endif; ?>


</main>


</body>

</html>