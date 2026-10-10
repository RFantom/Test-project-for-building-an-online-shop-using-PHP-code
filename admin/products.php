<?php

require_once "admin_auth.php";
require_once "../config.php";

$stmt = $conn->prepare(
    "SELECT id, name, description, price, image
     FROM products
     ORDER BY id DESC"
);

$stmt->execute();

$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>مدیریت محصولات</title>

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

        .store {
            background: #555;
            color: white;
        }

        .add-product {
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

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            padding-bottom: 15px;
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            background: #eee;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            margin: 0 0 10px;
            font-size: 19px;
        }

        .description {
            color: #666;
            line-height: 1.8;
            min-height: 55px;
            margin-bottom: 10px;
        }

        .price {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions a {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 11px 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .edit {
            background: #f0f0f0;
            color: #222;
            border: 1px solid #ccc;
        }

        .edit:hover {
            background: #ddd;
            transform: translateY(-2px);
        }

        .delete {
            background: #222;
            color: white;
        }

        .delete:hover {
            background: #444;
            transform: translateY(-2px);
        }

        .empty {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            color: #666;
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

            .actions {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

    <header class="header">

        <h1>🛠 مدیریت محصولات</h1>

        <div class="header-buttons">

            <a
                class="dashboard"
                href="dashboard.php"
            >
                📊 داشبورد
            </a>

            <a
                class="store"
                href="../products.php"
            >
                🏠 فروشگاه
            </a>

            <a
                class="add-product"
                href="add_product.php"
            >
                ➕ افزودن محصول
            </a>

        </div>

    </header>


    <main class="container">

        <div class="top-bar">

            <h2>
                لیست محصولات
            </h2>

            <span>
                تعداد محصولات:
                <strong><?php echo count($products); ?></strong>
            </span>

        </div>


        <?php if (empty($products)): ?>

            <div class="empty">
                هنوز هیچ محصولی ثبت نشده است.
            </div>

        <?php else: ?>

            <div class="products-grid">

                <?php foreach ($products as $product): ?>

                    <div class="product-card">

                        <?php if (!empty($product["image"])): ?>

                            <img
                                class="product-image"
                                src="../<?php echo htmlspecialchars($product["image"]); ?>"
                                alt="<?php echo htmlspecialchars($product["name"]); ?>"
                            >

                        <?php else: ?>

                            <div
                                class="product-image"
                                style="
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#888;
                                "
                            >
                                بدون تصویر
                            </div>

                        <?php endif; ?>


                        <div class="product-info">

                            <h3 class="product-name">
                                <?php echo htmlspecialchars($product["name"]); ?>
                            </h3>


                            <div class="description">

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $product["description"] ?? ""
                                    )
                                );
                                ?>

                            </div>


                            <div class="price">

                                <?php
                                echo number_format(
                                    (float) $product["price"]
                                );
                                ?>

                                تومان

                            </div>


                            <div class="actions">

                                <a
                                    class="edit"
                                    href="edit_product.php?id=<?php echo (int) $product["id"]; ?>"
                                >
                                    ✏️ ویرایش
                                </a>


                                <a
                                    class="delete"
                                    href="delete_product.php?id=<?php echo (int) $product["id"]; ?>"
                                    onclick="return confirm('آیا از حذف این محصول مطمئن هستید؟');"
                                >
                                    🗑 حذف
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

</body>

</html>