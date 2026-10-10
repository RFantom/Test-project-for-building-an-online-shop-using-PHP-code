<?php

session_start();

require_once "config.php";

// فقط کاربران لاگین‌شده
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// گرفتن سفارش‌های کاربر
$stmt = $conn->prepare(
    "SELECT id, total_price, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>سفارش‌های من</title>


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

            max-width: 900px;

            margin: 40px auto;

            padding: 0 20px;
        }


        h1 {

            text-align: center;

            margin-bottom: 30px;
        }


        .order {

            background: white;

            padding: 20px;

            margin-bottom: 15px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.08);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }


        .order-info {

            flex: 1;
        }


        .order-number {

            font-size: 20px;

            font-weight: bold;

            margin-bottom: 10px;
        }


        .order-date {

            color: #777;

            margin-bottom: 8px;
        }


        .order-price {

            font-weight: bold;

            font-size: 18px;
        }


        .details {

            background: #222;

            color: white;

            text-decoration: none;

            padding: 10px 16px;

            border-radius: 7px;

            white-space: nowrap;
        }


        .details:hover {

            background: #444;
        }


        .empty {

            background: white;

            padding: 50px 20px;

            text-align: center;

            border-radius: 12px;

            font-size: 20px;
        }


        .shop-button {

            display: inline-block;

            margin-top: 20px;

            padding: 11px 20px;

            background: #3498db;

            color: white;

            text-decoration: none;

            border-radius: 7px;
        }


        @media (max-width: 600px) {

            .order {

                flex-direction: column;

                align-items: stretch;

                text-align: center;
            }

            .details {

                display: block;

            }

        }
        .order-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .order-buttons form {
            margin: 0;
        }
        .delete {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 14px;
        }
        .delete:hover {
            background: #c0392b;
        }

    </style>

</head>


<body>


<header>

    <div>
        📦 سفارش‌های من
    </div>


    <a
        class="back"
        href="products.php"
    >
        ← محصولات
    </a>

</header>


<main class="container">


    <h1>
        سفارش‌های من
    </h1>


    <?php if ($result->num_rows === 0): ?>


        <div class="empty">

            📦 هنوز سفارشی ثبت نکرده‌اید.

            <br>

            <a
                class="shop-button"
                href="products.php"
            >
                🛍️ مشاهده محصولات
            </a>

        </div>


    <?php else: ?>


        <?php while ($order = $result->fetch_assoc()): ?>


            <div class="order">


                <div class="order-info">


                    <div class="order-number">

                        سفارش #

                        <?php
                        echo $order["id"];
                        ?>

                    </div>


                    <div class="order-date">

                        تاریخ ثبت:

                        <?php
                        echo $order["created_at"];
                        ?>

                    </div>


                    <div class="order-price">

                        مبلغ:

                        <?php
                        echo number_format(
                            $order["total_price"]
                        );
                        ?>

                        تومان

                    </div>


                </div>

            <div class="order-buttons">
                <a
                    class="details"
                    href="order_details.php?id=<?php
                        echo $order["id"];
                    ?>"
                >
                    مشاهده جزئیات
                </a>
                <form 
                method="POST"
                action="delete_order.php"
                onsubmit="return confirm('آیا از حذف این سفارش مطمئن هستید؟');">
                 <input type="hidden" name="order_id" value="<?php echo $order["id"]; ?>">
                  <button type="submit" class="delete">حذف سفارش🗑</button>
                </form>
                </div>
            </div>


        <?php endwhile; ?>


    <?php endif; ?>


</main>


</body>

</html>

<?php

$stmt->close();

?>