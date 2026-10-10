<?php

require_once "admin_auth.php";
require_once "../config.php";


/*
|--------------------------------------------------------------------------
| دریافت ID محصول
|--------------------------------------------------------------------------
*/

$product_id = intval($_GET["id"] ?? 0);

if ($product_id <= 0) {

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| پیدا کردن تصویر محصول
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT image
     FROM products
     WHERE id = ?"
);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| اگر محصول وجود نداشت
|--------------------------------------------------------------------------
*/

if (!$product) {

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| حذف محصول از دیتابیس
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "DELETE FROM products
     WHERE id = ?"
);

$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | حذف تصویر محصول از پوشه images
    |--------------------------------------------------------------------------
    */

    if (!empty($product["image"])) {

        $image_file = "../" . $product["image"];

        if (file_exists($image_file)) {

            unlink($image_file);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | برگشت به لیست محصولات
    |--------------------------------------------------------------------------
    */

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| اگر حذف انجام نشد
|--------------------------------------------------------------------------
*/

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

    <title>خطا</title>

    <style>

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            text-align: center;
            padding: 50px;
        }

        .box {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .error {
            color: #842029;
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background: #222;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

    </style>

</head>

<body>

    <div class="box">

        <div class="error">
            حذف محصول انجام نشد.
        </div>

        <a href="products.php">
            بازگشت به محصولات
        </a>

    </div>

</body>

</html>