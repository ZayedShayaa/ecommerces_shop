<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ./user/login.php");
    exit();
}

include("file/header.php");


// start delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    echo "<script>
        var confirmDelete = confirm('هل أنت متأكد من حذف المنتج؟');
        if (confirmDelete) {
            window.location.href = 'cart.php?delete_id=$product_id';
        } else {
            window.location.href = 'cart.php';
        }
    </script>";
}

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $product_id = $_GET['delete_id'];

    $deleteQuery = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $deleteQuery->execute([$_SESSION['user_id'], $product_id]);

    echo "<script>alert('تم حذف المنتج من السلة بنجاح'); window.location.href = 'cart.php';</script>";
}
// end delete
// if (isset($_POST['confirm_order'])) {
//     header("Location: checkout.php");
//     exit();
// }

$query = $conn->prepare("SELECT p.*, c.quantity FROM cart c JOIN product p ON c.product_id = p.id WHERE c.user_id = ?");
$query->execute([$_SESSION['user_id']]);
$cart_items = $query->fetchAll(PDO::FETCH_ASSOC);

// $_SESSION['cart_items'] = $cart_items;
?>


<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلة التسوق</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
    body {
    overflow-y: auto; /* السماح بالتمرير */
    min-height: 100vh; /* يضمن أن الصفحة تأخذ ارتفاع الشاشة بالكامل */
    margin: 0;
    padding: 0;
}

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
            overflow-x: hidden;
    
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        th {
            background-color: #f1f1f1;
        }

        .total {
            font-weight: bold;
            color: #007bff;
            
        }
        .divtotal{
            text-align: end;
            display: block;
        }

        .quantity {
            width: 60px;
        }

        .delete-icon {
            color: red;
            cursor: pointer;
        }

        .confirm-button {
            display: block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin: 20px auto 0;
            width: 150px;
        }

        .confirm-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>سلة التسوق</h1>
        <table>
            <thead>
                <tr>
                    <th>حذف</th>
                    <th>إجمالي السعر</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الاسم</th>
                    <th>صورة المنتج</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                foreach ($cart_items as $item) {
                    $subtotal = $item['proprice'] * $item['quantity'];
                    $total_price += $subtotal;
                ?>
                    <td><a href="cart.php?id=<?php echo $item['id']; ?>"><button type="submit" name="delete" value="" class="delete-icon" style="border:none; background:none;">
                                <i class="fas fa-trash"></i>
                            </button></a> </td>
                    <td class="total"><?php echo number_format($subtotal, 2); ?> &nbsp;</td>
                    <td><?= $item['quantity']; ?></td>
                    <td><?= $item['proprice']; ?> &nbsp;</td>
                    <td><?= $item['proname']; ?></td>
                    <td><img src="uploade/img/<?= $item['proimg']; ?>" alt="منتج" width="50"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="divtotal">
            <h3>الإجمالي: <span class="total"><?= number_format($total_price, 2); ?> &nbsp;</span></h3>
            <form action="checkout.php" method="GET">
    <button class="confirm-button" type="submit">تأكيد الطلب</button>
</form>


        </div>
    </div>
</body>
<br><br><br><br>
<?=include("file/footer.php");?>