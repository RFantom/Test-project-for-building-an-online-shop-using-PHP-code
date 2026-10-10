<?php

session_start();


// بررسی ورود کاربر
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}


// گرفتن ID محصول
$product_id = intval($_GET["id"] ?? 0);


// حذف محصول از سبد
if ($product_id > 0 && isset($_SESSION["cart"][$product_id])) {

    unset($_SESSION["cart"][$product_id]);

}


// برگشت به سبد خرید
header("Location: cart.php");
exit;

?>