<?php

require_once "admin_auth.php";
require_once "../config.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = $_POST["price"] ?? "";

    if ($name === "" || $description === "" || $price === "") {

        $message = "لطفا تمام اطلاعات را وارد کنید.";
        $message_type = "error";

    } else {

        $image_path = "";

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                $message = "آپلود عکس با مشکل مواجه شد.";
                $message_type = "error";

            } else {

                $allowed_types = [
                    "image/jpeg",
                    "image/png",
                    "image/webp",
                    "image/gif"
                ];

                $file_type = $_FILES["image"]["type"];

                if (!in_array($file_type, $allowed_types)) {

                    $message = "فرمت عکس باید JPG، PNG، WEBP یا GIF باشد.";
                    $message_type = "error";

                } elseif ($_FILES["image"]["size"] > 5 * 1024 * 1024) {

                    $message = "حجم عکس نباید بیشتر از 5 مگابایت باشد.";
                    $message_type = "error";

                } else {

                    $upload_dir = "../images/";

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    $file_name = uniqid("product_", true) . "." . $extension;

                    $target = $upload_dir . $file_name;

                    if (move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        $target
                    )) {

                        $image_path = "images/" . $file_name;

                    } else {

                        $message = "ذخیره عکس با مشکل مواجه شد.";
                        $message_type = "error";
                    }
                }
            }
        }

        if ($message === "") {

            $stmt = $conn->prepare(
                "INSERT INTO products
                (name, description, price, image)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssds",
                $name,
                $description,
                $price,
                $image_path
            );

            if ($stmt->execute()) {

                $stmt->close();

                header("Location: products.php");
                exit;

            } else {

                $message = "افزودن محصول با مشکل مواجه شد.";
                $message_type = "error";

                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>افزودن محصول</title>
        <style>
            * {
                box-sizing: border-box;
            }
            body {
                margin: 0;
                font-family: tahoma, arial, sans-serif;
                background: #f4f4f4;
                color: #222;
            }
            .header {
                background: #222;
                color: white;
                padding: 20px 40px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }
            .header h1 {
                margin: 0;
                font-size: 24px;
            }
            .header-buttons a {
                color: white;
                text-decoration: none;
                background: #444;
                padding: 10px 16px ;
                border-radius: 8px;
                white-space: nowrap;
            }
            .container {
                max-width: 700px;
                margin: 40px auto;
                padding: 0 20px;
            }
            .form-box {
                background: white;
                padding: 30px;
                border-radius: 14px;
                box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            }
            .form-box h2 {
                margin-top: 0;
                margin-bottom:25px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
            }
            .form-group input, .form-group textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-family: inherit;
                font-size: 15px;
            }
            .form-group textarea {
                min-height: 130px;
                resize: vertical;
            }
            .form-group input:focus, .form-group textarea:focus{
                outline: none;
                border-color: #222;
            }
            .submit-button {
                width: 100%;
                border: none;
                background: #222;
                color:white;
                padding: 13px;
                border-radius: 8px;
                font-family: inherit;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
            }
            .message {
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }
            .error {
                background: #f8d7da;
                color: #842029;
            }
            .image-help {
                color: #777;
                margin-top: 6px;
            }
            @media (max-width: 600px) {
                .header {
                    padding: 15px;
                }
                .header h1 {
                    font-size: 18px;
                }
                .form-box {
                    padding: 20px;
                }
            }
            </style>
    </head>
    <body>
        <header class="header">
            <h1>افزودن محصول➕</h1>
            <div class="header-buttons">
                <a href="dashboard.php">داشبورد🛠</a>
                <a href="products.php">محصولات📦</a>
            </div>
        </header>
        <main class="container">
            <div class="form-box">
                <h2>اطلاعات محصول</h2>
                <?php if ($message !== ""): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">نام محصول</label>
                            <input type="text" id="name" name="name" required valu="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>" >
                        </div>
                        <div class="form-group">
                            <label for="description">توضیحات محصول</label>
                            <textarea id="description" name="description" required><?php echo htmlspecialchars($_POST["description"] ?? ""); ?></textarea>

                        </div>
                        <div class="form-group">
                            <label for="price">
                                قیمت (تومان)
                            </label>
                            <input type="number" id="price" name="price" min="0" step="0.01" required value="<?php echo htmlspecialchars($_POST["price"] ?? ""); ?>">
                        </div>
                        <div class="form-group">
                            <label for="image">تصویر محصول</label>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                            <div class="image-help">
                                فرست های مجاز : JPG, PNG, WEBP, GIF - 5 MB حداکثر
                            </div>
                        </div>
                        <button type="submit" class="submit-button">افزودن محصول➕</button>
                        <a href="products.php" class="back-button">
                            بازگشت به محصولات ←
                        </a>
                    </form>
            </div>
        </main>
    </body>
</html>