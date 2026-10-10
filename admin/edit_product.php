<?php

require_once "admin_auth.php";
require_once "../config.php";


/*
|--------------------------------------------------------------------------
| دریافت ID محصول
|--------------------------------------------------------------------------
*/

$product_id = intval($_GET["id"] ?? 0);

if ($product_id <= 0) {

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| دریافت اطلاعات محصول
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT id, name, description, price, image
     FROM products
     WHERE id = ?"
);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$stmt->close();


if (!$product) {

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| متغیرهای پیام
|--------------------------------------------------------------------------
*/

$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| ارسال فرم
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");

    $description = trim(
        $_POST["description"] ?? ""
    );

    $price = $_POST["price"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | بررسی اطلاعات
    |--------------------------------------------------------------------------
    */

    if ($name === "") {

        $message = "لطفاً نام محصول را وارد کنید.";
        $message_type = "error";

    } elseif ($price === "" || !is_numeric($price)) {

        $message = "لطفاً قیمت معتبر وارد کنید.";
        $message_type = "error";

    } elseif ((float) $price < 0) {

        $message = "قیمت نمی‌تواند منفی باشد.";
        $message_type = "error";

    } else {

        /*
        |--------------------------------------------------------------------------
        | تصویر فعلی
        |--------------------------------------------------------------------------
        */

        $image_path = $product["image"];


        /*
        |--------------------------------------------------------------------------
        | اگر تصویر جدید انتخاب شده باشد
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if (
                $_FILES["image"]["error"] !== UPLOAD_ERR_OK
            ) {

                $message = "در آپلود تصویر مشکلی به وجود آمد.";
                $message_type = "error";

            } else {

                $allowed_types = [
                    "image/jpeg",
                    "image/png",
                    "image/webp",
                    "image/gif"
                ];

                $file_type = $_FILES["image"]["type"];

                $file_size = $_FILES["image"]["size"];


                if (!in_array($file_type, $allowed_types, true)) {

                    $message = "فرمت عکس باید JPG، PNG، WEBP یا GIF باشد.";
                    $message_type = "error";

                } elseif ($file_size > 5 * 1024 * 1024) {

                    $message = "حجم عکس نباید بیشتر از 5 مگابایت باشد.";
                    $message_type = "error";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | ساخت پوشه images در صورت نبودن
                    |--------------------------------------------------------------------------
                    */

                    $upload_dir = "../images/";

                    if (!is_dir($upload_dir)) {

                        mkdir(
                            $upload_dir,
                            0755,
                            true
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | پسوند فایل
                    |--------------------------------------------------------------------------
                    */

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | نام تصادفی برای فایل
                    |--------------------------------------------------------------------------
                    */

                    $new_filename =
                        uniqid("product_", true)
                        . "."
                        . $extension;


                    $target_file =
                        $upload_dir . $new_filename;


                    /*
                    |--------------------------------------------------------------------------
                    | انتقال فایل
                    |--------------------------------------------------------------------------
                    */

                    if (
                        move_uploaded_file(
                            $_FILES["image"]["tmp_name"],
                            $target_file
                        )
                    ) {

                        $image_path =
                            "images/" . $new_filename;

                    } else {

                        $message =
                            "ذخیره تصویر جدید با مشکل مواجه شد.";

                        $message_type = "error";

                    }

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ذخیره اطلاعات در دیتابیس
        |--------------------------------------------------------------------------
        */

        if ($message === "") {

            $price = (float) $price;

            $stmt = $conn->prepare(
                "UPDATE products
                 SET name = ?,
                     description = ?,
                     price = ?,
                     image = ?
                 WHERE id = ?"
            );


            $stmt->bind_param(
                "ssdsi",
                $name,
                $description,
                $price,
                $image_path,
                $product_id
            );


            if ($stmt->execute()) {

                $message =
                    "محصول با موفقیت ویرایش شد.";

                $message_type = "success";


                /*
                |--------------------------------------------------------------------------
                | اطلاعات جدید را دوباره از دیتابیس می‌گیریم
                |--------------------------------------------------------------------------
                */

                $stmt->close();


                $stmt = $conn->prepare(
                    "SELECT id, name, description, price, image
                     FROM products
                     WHERE id = ?"
                );

                $stmt->bind_param(
                    "i",
                    $product_id
                );

                $stmt->execute();

                $result = $stmt->get_result();

                $product = $result->fetch_assoc();

                $stmt->close();

            } else {

                $message =
                    "ویرایش محصول انجام نشد.";

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ویرایش محصول</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .header {
            background: #222;
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-buttons a {
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .products-link {
            background: white;
            color: #222;
        }

        .dashboard-link {
            background: #555;
            color: white;
        }

        .container {
            max-width: 750px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .form-box h2 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
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

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #555;
        }

        .current-image {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .current-image img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message.success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .message.error {
            background: #f8d7da;
            color: #842029;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .buttons button,
        .buttons a {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-family: inherit;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .save {
            background: #198754;
            color: white;
        }

        .save:hover {
            background: #157347;
        }

        .cancel {
            background: #eee;
            color: #222;
        }

        .cancel:hover {
            background: #ddd;
        }

        @media (max-width: 600px) {

            .header {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .container {
                padding: 0 12px;
            }

            .form-box {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }

        }

    </style>

</head>


<body>


<header class="header">

    <h1>✏️ ویرایش محصول</h1>


    <div class="header-buttons">

        <a
            class="products-link"
            href="products.php"
        >
            📦 محصولات
        </a>

        <a
            class="dashboard-link"
            href="dashboard.php"
        >
            📊 داشبورد
        </a>

    </div>

</header>



<main class="container">

    <div class="form-box">

        <h2>
            ویرایش محصول شماره
            <?php echo (int) $product["id"]; ?>
        </h2>


        <?php if ($message !== ""): ?>

            <div
                class="message <?php echo htmlspecialchars($message_type); ?>"
            >
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-group">

                <label for="name">
                    نام محصول
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($product["name"]); ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label for="description">
                    توضیحات
                </label>

                <textarea
                    id="description"
                    name="description"
                ><?php echo htmlspecialchars($product["description"] ?? ""); ?></textarea>

            </div>



            <div class="form-group">

                <label for="price">
                    قیمت
                </label>

                <input
                    type="number"
                    id="price"
                    name="price"
                    min="0"
                    step="0.01"
                    value="<?php echo htmlspecialchars($product["price"]); ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    تصویر فعلی
                </label>


                <?php if (!empty($product["image"])): ?>

                    <div class="current-image">

                        <img
                            src="../<?php echo htmlspecialchars($product["image"]); ?>"
                            alt="<?php echo htmlspecialchars($product["name"]); ?>"
                        >

                    </div>

                <?php else: ?>

                    <p>
                        این محصول تصویر ندارد.
                    </p>

                <?php endif; ?>

            </div>



            <div class="form-group">

                <label for="image">
                    تصویر جدید
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                >

                <small>
                    در صورت انتخاب تصویر جدید، تصویر قبلی جایگزین می‌شود.
                    حداکثر حجم: 5 مگابایت
                </small>

            </div>



            <div class="buttons">

                <button
                    type="submit"
                    class="save"
                >
                    💾 ذخیره تغییرات
                </button>


                <a
                    href="products.php"
                    class="cancel"
                >
                    ❌ انصراف
                </a>

            </div>


        </form>

    </div>

</main>


</body>

</html>