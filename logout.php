<?php

session_start();

// حذف تمام اطلاعات Session
$_SESSION = [];

// نابود کردن Session
session_destroy();

// برگشت به صفحه ورود
header("Location: login.php");
exit;

?>