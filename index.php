<?php

session_start();

require_once "config.php";

$message = "";
$message_type = "";

// اگر کاربر قبلاً وارد شده، مستقیم برود محصولات
if (isset($_SESSION["user_id"])) {
    header("Location: products.php");
    exit;
}


// مشخص کردن حالت صفحه
$mode = $_GET["mode"] ?? "login";


// =========================
// ثبت نام
// =========================

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "register") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $email === "" || $password === "") {

        $message = "لطفاً همه فیلدها را پر کنید.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "ایمیل وارد شده معتبر نیست.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "رمز عبور باید حداقل ۶ کاراکتر باشد.";
        $message_type = "error";

    } else {

        // بررسی وجود کاربر
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );

        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "این نام کاربری یا ایمیل قبلاً ثبت شده است.";
            $message_type = "error";

        } else {

            // Hash کردن رمز
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // ذخیره کاربر
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $username,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "ثبت نام با موفقیت انجام شد! حالا وارد شوید 🎉";
                $message_type = "success";

                // بعد از ثبت نام، فرم ورود نمایش داده شود
                $mode = "login";

            } else {

                $message = "خطایی هنگام ثبت نام رخ داد.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}


// =========================
// ورود
// =========================

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "login") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "لطفاً ایمیل و رمز عبور را وارد کنید.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "ایمیل وارد شده معتبر نیست.";
        $message_type = "error";

    } else {

        // پیدا کردن کاربر
        $stmt = $conn->prepare(
            "SELECT id, username, password
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // بررسی رمز
            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                header("Location: products.php");
                exit;

            } else {

                $message = "ایمیل یا رمز عبور اشتباه است.";
                $message_type = "error";
            }

        } else {

            $message = "ایمیل یا رمز عبور اشتباه است.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        ورود و ثبت نام
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f2f2f2;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;
        }

        .box {
            width: 380px;

            background: white;

            padding: 30px;

            border-radius: 15px;

            box-shadow:
                0 5px 25px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;

            margin-top: 0;

            margin-bottom: 25px;
        }

        /* دکمه های انتخاب */

        .tabs {
            display: flex;

            gap: 10px;

            margin-bottom: 25px;
        }

        .tabs a {
            flex: 1;

            text-align: center;

            padding: 11px;

            border-radius: 8px;

            text-decoration: none;

            background: #eee;

            color: #222;
        }

        .tabs a.active {
            background: #222;

            color: white;
        }

        label {
            display: block;

            margin-bottom: 7px;
        }

        input {
            width: 100%;

            padding: 12px;

            margin-bottom: 15px;

            border: 1px solid #ccc;

            border-radius: 8px;

            font-size: 15px;
        }

        button {
            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 8px;

            background: #222;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        button:hover {
            background: #444;
        }

        .message {
            padding: 10px;

            border-radius: 8px;

            margin-bottom: 15px;

            text-align: center;
        }

        .success {
            background: #d4edda;

            color: #155724;
        }

        .error {
            background: #f8d7da;

            color: #721c24;
        }

    </style>

</head>

<body>


<div class="box">

    <h1>
        🛍️ فروشگاه من
    </h1>


    <!-- انتخاب ورود / ثبت نام -->

    <div class="tabs">

        <a
            href="?mode=login"
            class="<?php echo $mode === "login" ? "active" : ""; ?>"
        >
            ورود
        </a>

        <a
            href="?mode=register"
            class="<?php echo $mode === "register" ? "active" : ""; ?>"
        >
            ثبت نام
        </a>

    </div>


    <!-- پیام -->

    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if ($mode === "register"): ?>


        <!-- ========================= -->
        <!-- فرم ثبت نام -->
        <!-- ========================= -->

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="register"
            >

            <label>
                نام کاربری
            </label>

            <input
                type="text"
                name="username"
                required
                maxlength="50"
            >


            <label>
                ایمیل
            </label>

            <input
                type="email"
                name="email"
                required
            >


            <label>
                رمز عبور
            </label>

            <input
                type="password"
                name="password"
                minlength="6"
                required
            >


            <button type="submit">
                ساخت حساب
            </button>

        </form>


    <?php else: ?>


        <!-- ========================= -->
        <!-- فرم ورود -->
        <!-- ========================= -->

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="login"
            >

            <label>
                ایمیل
            </label>

            <input
                type="email"
                name="email"
                required
            >


            <label>
                رمز عبور
            </label>

            <input
                type="password"
                name="password"
                required
            >


            <button type="submit">
                ورود
            </button>

        </form>


    <?php endif; ?>


</div>

</body>

</html>