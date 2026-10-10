<?php

session_start();

require_once "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$product_id = intval($_POST["product_id"] ?? 0);

if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: products.php");
    exit;
}

$stmt->close();

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_SESSION["cart"][$product_id])) {
    $_SESSION["cart"][$product_id]++;
} else {
    $_SESSION["cart"][$product_id] = 1;
}

header("Location: products.php");
exit;
?>