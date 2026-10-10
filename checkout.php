<?php

session_start();

require_once "config.php";

// فقط کاربر لاگین شده
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}


// بررسی وجود سبد خرید
if (
    !isset($_SESSION["cart"]) ||
    empty($_SESSION["cart"])
) {
    header("Location: cart.php");
    exit;
}


$cart = $_SESSION["cart"];

try {

    // شروع Transaction
    $conn->begin_transaction();


    /*
     * گرفتن محصولات سبد از دیتابیس
     */

    $ids = array_keys($cart);

    $placeholders = implode(
        ",",
        array_fill(0, count($ids), "?")
    );

    $types = str_repeat("i", count($ids));


    $stmt = $conn->prepare(
        "SELECT id, name, price
         FROM products
         WHERE id IN ($placeholders)"
    );

    $stmt->bind_param($types, ...$ids);

    $stmt->execute();

    $result = $stmt->get_result();


    $products = [];

    while ($product = $result->fetch_assoc()) {

        $products[$product["id"]] = $product;

    }

    $stmt->close();


    /*
     * محاسبه قیمت نهایی
     */

    $total_price = 0;


    foreach ($cart as $product_id => $quantity) {

        if (!isset($products[$product_id])) {
            throw new Exception("یکی از محصولات دیگر وجود ندارد.");
        }


        if ($quantity <= 0) {
            throw new Exception("تعداد محصول نامعتبر است.");
        }


        $total_price +=
            $products[$product_id]["price"] * $quantity;
    }


    /*
     * ایجاد سفارش
     */

    $user_id = $_SESSION["user_id"];


    $stmt = $conn->prepare(
        "INSERT INTO orders
        (user_id, total_price)
        VALUES (?, ?)"
    );


    $stmt->bind_param(
        "id",
        $user_id,
        $total_price
    );


    $stmt->execute();


    // گرفتن شماره سفارش
    $order_id = $conn->insert_id;


    $stmt->close();


    /*
     * ذخیره محصولات سفارش
     */

    $stmt = $conn->prepare(
        "INSERT INTO order_items
        (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)"
    );


    foreach ($cart as $product_id => $quantity) {

        $price = $products[$product_id]["price"];


        $stmt->bind_param(
            "iiid",
            $order_id,
            $product_id,
            $quantity,
            $price
        );


        $stmt->execute();
    }


    $stmt->close();


    /*
     * تایید Transaction
     */

    $conn->commit();


    /*
     * خالی کردن سبد خرید
     */

    $_SESSION["cart"] = [];


    /*
     * ذخیره شماره سفارش برای صفحه موفقیت
     */

    $_SESSION["last_order_id"] = $order_id;


    header("Location: order_success.php");
    exit;


} catch (Exception $e) {

    /*
     * اگر مشکلی پیش آمد،
     * تمام تغییرات برگردانده می‌شوند.
     */

    $conn->rollback();

    die(
        "خطا در ثبت سفارش: " .
        htmlspecialchars($e->getMessage())
    );
}

?>