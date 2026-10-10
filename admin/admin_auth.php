<?php 
session_start();
require_once "../config.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare(
    "SELECT role FROM users WHERE id = ?"
);
$stmt-> bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
if (!$user || $user["role"] !== "admin"){
    header("Location: ./products.php");
    exit;
}
?>