<?php

session_start();

require_once "config.php";

// بررسی ورود
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$order_id = intval($_GET["id"] ?? 0);

if ($order_id <= 0) {
    header("Location: my_orders.php");
    exit;
}

$user_id = $_SESSION["user_id"];


// گرفتن اطلاعات سفارش
$stmt = $conn->prepare(
    "SELECT id, total_price, created_at
     FROM orders
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $order_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: my_orders.php");
    exit;
}

$order = $result->fetch_assoc();

$stmt->close();


// گرفتن محصولات سفارش
$stmt = $conn->prepare(
    "SELECT
        order_items.quantity,
        order_items.price,
        products.name,
        products.image
     FROM order_items
     INNER JOIN products
        ON order_items.product_id = products.id
     WHERE order_items.order_id = ?"
);

$stmt->bind_param("i", $order_id);

$stmt->execute();

$items = $stmt->get_result();

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
        جزئیات سفارش
    </title>


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

            background: #3498db;

            padding: 10px 15px;

            border-radius: 7px;
        }


        .container {

            max-width: 900px;

            margin: 40px auto;

            padding: 0 20px;
        }


        h1 {

            text-align: center;

            margin-bottom: 25px;
        }


        .order-summary {

            background: white;

            padding: 20px;

            border-radius: 12px;

            margin-bottom: 20px;

            display: flex;

            justify-content: space-between;

            gap: 20px;
        }


        .items {

            margin-top: 20px;
        }


        .item {

            background: white;

            padding: 20px;

            border-radius: 12px;

            margin-bottom: 15px;

            display: flex;

            align-items: center;

            gap: 20px;
        }


        .item img {

            width: 100px;

            height: 100px;

            object-fit: cover;

            border-radius: 10px;
        }


        .item-info {

            flex: 1;
        }


        .item-name {

            font-size: 19px;

            font-weight: bold;

            margin-bottom: 10px;
        }


        .item-line {

            color: #666;

            margin: 5px 0;
        }


        .item-total {

            font-weight: bold;

            font-size: 17px;
        }


        .total-box {

            background: white;

            padding: 25px;

            border-radius: 12px;

            text-align: center;

            margin-top: 25px;

            font-size: 23px;

            font-weight: bold;
        }


        @media (max-width: 600px) {

            .order-summary {

                flex-direction: column;

            }

            .item {

                flex-direction: column;

                text-align: center;

            }

        }

    </style>

</head>


<body>


<header>

    <div>
        📦 سفارش #
        <?php echo $order["id"]; ?>
    </div>


    <a href="my_orders.php">
        ← سفارش‌های من
    </a>

</header>


<main class="container">


    <h1>
        جزئیات سفارش
    </h1>


    <div class="order-summary">

        <div>

            شماره سفارش:

            <strong>
                #<?php echo $order["id"]; ?>
            </strong>

        </div>


        <div>

            تاریخ:

            <?php
            echo $order["created_at"];
            ?>

        </div>

    </div>


    <div class="items">


        <?php while ($item = $items->fetch_assoc()): ?>


            <?php

            $item_total =
                $item["price"] *
                $item["quantity"];

            ?>


            <div class="item">


                <?php if (!empty($item["image"])): ?>

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $item["image"]
                            );
                        ?>"
                        alt="<?php
                            echo htmlspecialchars(
                                $item["name"]
                            );
                        ?>"
                    >

                <?php else: ?>

                    <div style="
                        width:100px;
                        height:100px;
                        background:#ddd;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        border-radius:10px;
                    ">
                        بدون تصویر
                    </div>

                <?php endif; ?>


                <div class="item-info">


                    <div class="item-name">

                        <?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                        ?>

                    </div>


                    <div class="item-line">

                        قیمت واحد:

                        <?php
                        echo number_format(
                            $item["price"]
                        );
                        ?>

                        تومان

                    </div>


                    <div class="item-line">

                        تعداد:

                        <?php
                        echo $item["quantity"];
                        ?>

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


            </div>


        <?php endwhile; ?>


    </div>


    <div class="total-box">

        جمع کل سفارش:

        <?php
        echo number_format(
            $order["total_price"]
        );
        ?>

        تومان

    </div>


</main>


</body>

</html>

<?php

$stmt->close();

?>