<?php 
session_start();
require_once "config.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
$user_id =$_SESSION["user_id"];
$order_id = intval($_POST["order_id"] ?? 0);

if ($order_id <= 0) {
    header("Location: my_orders.php");
    exit();
}
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$stmt->close();
header("Location: my_orders.php");
exit();
?>