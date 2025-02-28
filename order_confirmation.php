<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./user/login.php");
    exit();
}

include("file/header.php");
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد الطلب</title>
    <style>
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
            text-align: center;
        }
        p {
            margin-bottom: 20px; /* إضافة مسافة أسفل النص */
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px; /* إضافة مسافة للزر */
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>شكرًا لك!</h2>
        <p>تم تأكيد طلبك بنجاح. سنتواصل معك قريبًا لتحديثات الشحن.</p>
        
        <a href="index.php" class="btn">العودة إلى الصفحة الرئيسية</a>
    </div>
</body>
</html>

<?= include("file/footer.php"); ?>
