<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "shop_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>