<?php

require_once "config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // بررسی خالی نبودن فیلدها
    if ($username === "" || $email === "" || $password === "") {
        $message = "لطفاً همه فیلدها را پر کنید.";
        $message_type = "error";
    }

    // بررسی ایمیل
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "ایمیل وارد شده معتبر نیست.";
        $message_type = "error";
    }

    // بررسی طول رمز عبور
    elseif (strlen($password) < 6) {
        $message = "رمز عبور باید حداقل ۶ کاراکتر باشد.";
        $message_type = "error";
    }

    else {

        // بررسی اینکه نام کاربری یا ایمیل قبلاً ثبت نشده باشد
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

            // رمز عبور را به صورت امن Hash می‌کنیم
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // ذخیره کاربر در دیتابیس
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

                $message = "ثبت نام با موفقیت انجام شد! 🎉";
                $message_type = "success";

                // خالی کردن فرم
                $username = "";
                $email = "";

            } else {

                $message = "خطایی هنگام ثبت نام رخ داد.";
                $message_type = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ثبت نام</title>

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

        .register-box {
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

        .message {
            padding: 10px;
            border-radius: 7px;
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

<div class="register-box">

    <h2>📝 ثبت نام</h2>

    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <label for="username">نام کاربری</label>

        <input
            type="text"
            id="username"
            name="username"
            value="<?php echo htmlspecialchars($username ?? ""); ?>"
            required
        >

        <label for="email">ایمیل</label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($email ?? ""); ?>"
            required
        >

        <label for="password">رمز عبور</label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">
            ثبت نام
        </button>

    </form>

</div>

</body>
</html>