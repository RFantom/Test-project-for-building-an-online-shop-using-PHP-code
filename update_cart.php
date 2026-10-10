<?php

session_start();

// فقط کاربران وارد شده
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// اگر سبد خرید وجود نداشت
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// دریافت اطلاعات
$product_id = intval($_POST["product_id"] ?? 0);
$action = $_POST["action"] ?? "";

// بررسی محصول
if ($product_id > 0 && isset($_SESSION["cart"][$product_id])) {

    if ($action === "increase") {

        // افزایش تعداد
        $_SESSION["cart"][$product_id]++;

    } elseif ($action === "decrease") {

        // کاهش تعداد
        $_SESSION["cart"][$product_id]--;

        // اگر تعداد صفر شد، محصول حذف شود
        if ($_SESSION["cart"][$product_id] <= 0) {

            unset($_SESSION["cart"][$product_id]);

        }
    }
}

// برگشت به سبد
header("Location: cart.php");
exit;

?>