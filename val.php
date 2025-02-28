<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();}
include("./include/connected.php");

$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

if (!isset($_SESSION['user_id'])) {
    header("Location: ./user/login.php");
    exit();
} else {
    $user_id = $_SESSION['user_id'];

    if (isset($product_id)) {
        // تحقق مما إذا كان المنتج موجودًا في السلة بالفعل
        $check = "SELECT quantity FROM cart WHERE product_id = ? AND user_id = ?";
        $stmt = $conn->prepare($check);
        $stmt->execute([$product_id, $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // المنتج موجود مسبقًا، قم بتحديث الكمية
            $update = "UPDATE cart SET quantity = quantity + ? WHERE product_id = ? AND user_id = ?";
            $stmt = $conn->prepare($update);
            $stmt->execute([$quantity, $product_id, $user_id]);

            // تحديث العدد في الجلسة
            $_SESSION['cart_count'] += $quantity;
        } else {
            // المنتج غير موجود، قم بإضافته
            $insert = $conn->prepare("INSERT INTO cart(user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$user_id, $product_id, $quantity]);

            // تحديث العدد في الجلسة
            $_SESSION['cart_count'] = ($_SESSION['cart_count'] ?? 0) + $quantity;
        }

        // إعادة توجيه المستخدم إلى الصفحة السابقة
        $previous_page = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: $previous_page");
        exit();
    }
}
?>
