<?php

session_start();

require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message = "لطفاً ایمیل و رمز عبور را وارد کنید.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "ایمیل وارد شده معتبر نیست.";

    } else {

        // پیدا کردن کاربر با ایمیل
        $stmt = $conn->prepare(
            "SELECT id, username, password FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // بررسی رمز عبور
            if (password_verify($password, $user["password"])) {

                // ساخت Session
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                // انتقال به صفحه محصولات
                header("Location: products.php");
                exit;

            } else {

                $message = "ایمیل یا رمز عبور اشتباه است.";
            }

        } else {

            $message = "ایمیل یا رمز عبور اشتباه است.";
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

    <title>ورود</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 350px;

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
        }

        input {
            width: 100%;

            padding: 11px;

            margin-bottom: 15px;

            border: 1px solid #ccc;

            border-radius: 7px;

            box-sizing: border-box;
        }

        button {
            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 7px;

            background: #222;

            color: white;

            cursor: pointer;

            font-size: 16px;
        }

        button:hover {
            background: #444;
        }

        .error {
            background: #f8d7da;

            color: #721c24;

            padding: 10px;

            border-radius: 7px;

            margin-bottom: 15px;

            text-align: center;
        }

        .register-link {
            text-align: center;

            margin-top: 20px;
        }

        .register-link a {
            color: #222;

            text-decoration: none;

            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h2>🔐 ورود به حساب</h2>

    <?php if ($message !== ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <label for="email">
            ایمیل
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($email ?? ""); ?>"
            required
        >

        <label for="password">
            رمز عبور
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">
            ورود
        </button>

    </form>

    <div class="register-link">

        حساب کاربری ندارید؟

        <a href="register.php">
            ثبت نام کنید
        </a>

    </div>

</div>

</body>

</html>