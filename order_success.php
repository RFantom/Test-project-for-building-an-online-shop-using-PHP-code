<?php

session_start();

require_once "config.php";

// فقط کاربر لاگین‌شده
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}


// بررسی وجود سفارش اخیر
if (!isset($_SESSION["last_order_id"])) {
    header("Location: products.php");
    exit;
}


$order_id = intval($_SESSION["last_order_id"]);


// گرفتن اطلاعات سفارش
$stmt = $conn->prepare(
    "SELECT id, total_price, created_at
     FROM orders
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $order_id,
    $_SESSION["user_id"]
);

$stmt->execute();

$result = $stmt->get_result();


// اگر سفارش پیدا نشد
if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: products.php");
    exit;
}


$order = $result->fetch_assoc();

$stmt->close();


// حذف شماره سفارش از Session
unset($_SESSION["last_order_id"]);

?>

<!DOCTYPE html>

<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        سفارش با موفقیت ثبت شد
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f4f4;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

        }


        .box {

            width: 450px;

            max-width: 90%;

            background: white;

            padding: 40px;

            border-radius: 15px;

            text-align: center;

            box-shadow:
                0 5px 25px rgba(0,0,0,0.1);

        }


        .success-icon {

            width: 80px;

            height: 80px;

            margin: 0 auto 20px;

            border-radius: 50%;

            background: #2ecc71;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 40px;

        }


        h1 {

            margin-bottom: 15px;

        }


        .message {

            color: #666;

            line-height: 1.8;

            margin-bottom: 25px;

        }


        .order-info {

            background: #f5f5f5;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 25px;

        }


        .order-row {

            display: flex;

            justify-content: space-between;

            padding: 8px 0;

        }


        .order-row.total {

            border-top: 1px solid #ddd;

            margin-top: 8px;

            padding-top: 15px;

            font-weight: bold;

            font-size: 18px;

        }


        .buttons {

            display: flex;

            gap: 10px;

        }


        .buttons a {

            flex: 1;

            padding: 12px;

            border-radius: 8px;

            text-decoration: none;

        }


        .products {

            background: #222;

            color: white;

        }


        .orders {

            background: #3498db;

            color: white;

        }


        @media (max-width: 500px) {

            .buttons {

                flex-direction: column;

            }

        }

    </style>

</head>


<body>


<div class="box">


    <div class="success-icon">
        ✓
    </div>


    <h1>
        سفارش با موفقیت ثبت شد 🎉
    </h1>


    <div class="message">

        ممنون از خرید شما!

        <br>

        سفارش شما با موفقیت در سیستم ثبت شد.

    </div>


    <div class="order-info">


        <div class="order-row">

            <span>
                شماره سفارش:
            </span>

            <strong>
                #<?php echo $order["id"]; ?>
            </strong>

        </div>


        <div class="order-row total">

            <span>
                مبلغ سفارش:
            </span>

            <span>

                <?php
                echo number_format(
                    $order["total_price"]
                );
                ?>

                تومان

            </span>

        </div>


    </div>


    <div class="buttons">


        <a
            class="products"
            href="products.php"
        >
            🛍️ ادامه خرید
        </a>


        <a
            class="orders"
            href="my_orders.php"
        >
            📦 سفارش‌های من
        </a>


    </div>


</div>


</body>

</html>