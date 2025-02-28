<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./user/login.php");
    exit();
}

include("file/header.php");

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT p.*, c.quantity FROM cart c 
                         JOIN product p ON c.product_id = p.id 
                         WHERE c.user_id = ?");
$query->execute([$user_id]);
$cart_items = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$cart_items) {
    echo "<script>alert('سلتك فارغة! أضف منتجات أولاً.'); window.location.href = 'cart.php';</script>";
    exit();
}

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['proprice'] * $item['quantity'];
}

function sendWhatsAppMessage($phone_number, $cart_items, $total_price, $customer_name, $email, $address, $city, $zip_code, $payment_method) {
    $ultramsg_token = "b55kvtovojio43hx"; 
    $instance_id = "instance107089"; 
    $api_url = "https://api.ultramsg.com/$instance_id/messages/chat"; 

    $country_code = "+967"; 
    if (strpos($phone_number, "+") !== 0) { 
        $phone_number = $country_code . ltrim($phone_number, "0");
    }

    $message = "📜 *فاتورة الطلب* \n\n";
    $message .= "👤 *الاسم:* $customer_name \n";
    $message .= "📞 *رقم الهاتف:* $phone_number \n";
    $message .= "✉️ *البريد الإلكتروني:* $email \n";
    $message .= "🏠 *العنوان:* $address, $city - $zip_code \n";
    $message .= "\n🛍️ *المنتجات:* \n";

    foreach ($cart_items as $item) {
        $message .= "- " . $item['proname'] . " × " . $item['quantity'] . " = " . ($item['proprice'] * $item['quantity']) . " ريال \n";
    }

    $message .= "\n💰 *الإجمالي:* " . number_format($total_price, 2) . " ريال \n";
    $message .= "💳 *طريقة الدفع:* " . ($payment_method == 'cash_on_delivery' ? "الدفع عند الاستلام" : "إلكترونية") . "\n";
    $message .= "\n📦 *شكراً لتسوقك معنا! 😊*";

    $data = [
        'token' => $ultramsg_token,
        'to' => $phone_number,
        'body' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);
    return $result;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $shipping_address = $_POST['shipping_address'] ?? null;
    $city = $_POST['city'] ?? null;
    $zip_code = $_POST['zip_code'] ?? null; 
    $phone_number = $_POST['phone_number'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $credit_card_type = ($payment_method === 'credit_card') ? ($_POST['credit_card_type'] ?? null) : null;

    if ($shipping_address && $city && $zip_code && $phone_number && $payment_method) {
        $insertOrder = $conn->prepare("INSERT INTO orders (user_id, order_date, total_price, shipping_address, city, zip_code, phone_number, payment_method, credit_card_type) 
                                       VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)");
        $insertOrder->execute([$user_id, $total_price, $shipping_address, $city, $zip_code, $phone_number, $payment_method, $credit_card_type]);

        $queryUser = $conn->prepare("SELECT username, email FROM user WHERE id = ?");
        $queryUser->execute([$user_id]);
        $user = $queryUser->fetch(PDO::FETCH_ASSOC);
        $customer_name = $user['username'];
        $email = $user['email'];

        sendWhatsAppMessage($phone_number, $cart_items, $total_price, $customer_name, $email, $shipping_address, $city, $zip_code, $payment_method);

       $deleteCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $deleteCart->execute([$user_id]);

        echo "<script>alert('تم تأكيد طلبك بنجاح!'); window.location.href = 'order_confirmation.php';</script>";
                exit();
    } else {
        echo "<script>alert('جميع الحقول مطلوبة!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد الطلب</title>
     <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
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
        label { margin-bottom: 5px; font-size: 20px; }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        h3 { margin-bottom: 20px; }
        .btn:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>تأكيد الطلب</h2>
        <form action="checkout.php" method="POST">
            <label>عنوان الشحن</label>
            <input type="text" name="shipping_address" required>
            <label>المدينة</label>
            <input type="text" name="city" required>
            <label>الرمز البريدي</label>
            <input type="text" name="zip_code" required>
            <label>رقم الهاتف</label>
            <input type="text" name="phone_number" required>
            <label>طريقة الدفع</label>
            <select name="payment_method" required>
                <option value="cash_on_delivery">الدفع عند الاستلام</option>
                <option value="credit_card">بطاقة ائتمان</option>
                <option value="paypal">باي بال</option>
            </select>
            <h3>الإجمالي: <?= number_format($total_price, 2); ?> ريال</h3>
            <button type="submit" class="btn">تأكيد الطلب</button>
        </form>
    </div>
</body>
</html>
<br><br><br><br>
<?= include("file/footer.php"); ?>
