<?php

require_once "admin_auth.php";
require_once "../config.php";


/*
|--------------------------------------------------------------------------
| دریافت سفارش‌ها
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        orders.id,
        orders.user_id,
        orders.total_price,
        orders.created_at,
        users.username,
        users.email
    FROM orders
    INNER JOIN users
        ON orders.user_id = users.id
    ORDER BY orders.id DESC
";

$result = $conn->query($sql);

$orders = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $orders[] = $row;

    }

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

    <title>مدیریت سفارش‌ها</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .header {
            background: #222;
            color: white;
            padding: 20px 30px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;
            flex-wrap: wrap;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-buttons a {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .dashboard {
            background: white;
            color: #222;
        }

        .products {
            background: #555;
            color: white;
        }

        .store {
            background: #198754;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;
            margin-bottom: 25px;

            flex-wrap: wrap;
        }

        .top-bar h2 {
            margin: 0;
        }

        .count {
            color: #666;
        }

        .orders-box {
            background: white;
            border-radius: 12px;
            overflow: hidden;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            background: #222;
            color: white;
            padding: 15px 12px;
            text-align: center;
            white-space: nowrap;
        }

        td {
            padding: 14px 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafafa;
        }

        .order-id {
            font-weight: bold;
        }

        .username {
            font-weight: bold;
        }

        .email {
            color: #666;
            font-size: 13px;
            direction: ltr;
        }

        .price {
            font-weight: bold;
        }

        .date {
            direction: ltr;
            white-space: nowrap;
        }

        .details-btn {
            display: inline-block;

            padding: 9px 14px;

            background: #222;
            color: white;

            border-radius: 7px;

            text-decoration: none;
            font-weight: bold;

            transition: 0.2s;
        }

        .details-btn:hover {
            background: #444;
            transform: translateY(-2px);
        }

        .empty {
            background: white;
            padding: 50px 20px;

            text-align: center;

            border-radius: 12px;

            color: #666;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.08);
        }

        @media (max-width: 600px) {

            .header {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .container {
                padding: 0 12px;
            }

        }

    </style>

</head>


<body>


<header class="header">

    <h1>📦 مدیریت سفارش‌ها</h1>


    <div class="header-buttons">

        <a
            class="dashboard"
            href="dashboard.php"
        >
            📊 داشبورد
        </a>


        <a
            class="products"
            href="products.php"
        >
            🛍 محصولات
        </a>


        <a
            class="store"
            href="../products.php"
        >
            🏠 فروشگاه
        </a>

    </div>

</header>



<main class="container">


    <div class="top-bar">

        <h2>
            لیست سفارش‌های کاربران
        </h2>


        <div class="count">

            تعداد سفارش‌ها:

            <strong>
                <?php echo count($orders); ?>
            </strong>

        </div>

    </div>



    <?php if (empty($orders)): ?>


        <div class="empty">

            📭

            <h3>
                هنوز سفارشی ثبت نشده است.
            </h3>

            <p>
                وقتی کاربران سفارشی ثبت کنند،
                سفارش‌ها در این قسمت نمایش داده می‌شوند.
            </p>

        </div>


    <?php else: ?>


        <div class="orders-box">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                شماره سفارش
                            </th>

                            <th>
                                کاربر
                            </th>

                            <th>
                                ایمیل
                            </th>

                            <th>
                                مبلغ سفارش
                            </th>

                            <th>
                                تاریخ
                            </th>

                            <th>
                                جزئیات
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($orders as $order): ?>

                            <tr>

                                <td class="order-id">

                                    #

                                    <?php
                                    echo (int) $order["id"];
                                    ?>

                                </td>


                                <td class="username">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["username"]
                                    );
                                    ?>

                                </td>


                                <td class="email">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["email"]
                                    );
                                    ?>

                                </td>


                                <td class="price">

                                    <?php
                                    echo number_format(
                                        (float) $order["total_price"]
                                    );
                                    ?>

                                    تومان

                                </td>


                                <td class="date">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["created_at"]
                                    );
                                    ?>

                                </td>


                                <td>

                                    <a
                                        class="details-btn"
                                        href="order_details.php?id=<?php echo (int) $order["id"]; ?>"
                                    >
                                        👁 مشاهده
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        </div>


    <?php endif; ?>


</main>


</body>

</html>