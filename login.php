<?php
session_start();
require_once("../include/connected.php");

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني مطلوب ويجب أن يكون صالحًا.";
    }

    if (empty($password)) {
        $errors[] = "كلمة المرور مطلوبة.";
    }

    
    $query = "SELECT * FROM user WHERE email = :email";
    $result = $conn->prepare($query);
    $result->execute(['email' => $email]);
    $user = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $stored_password = $user['password'];
        
       
        if (password_verify($password, $stored_password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            if ($remember_me) {

                 setcookie('email', $email, time() + 60 * 60 * 24 * 30, '/', '', true, true); 
                 setcookie('passwoed', $password, time() + 60 * 60 * 24 * 30, '/', '', true, true); 
                }

            if ($user['email'] === "zayedhassantaha@gmail.com") {
                $_SESSION['EMAIL'] = $user['email'];
                echo '<script>alert("مرحبا بك أيها المدير، سوف يتم تحويلك إلى لوحة التحكم");</script>';
                header("REFRESH:2; URL=../admin/adminpanel.php");
                exit;
            } else {
                echo '<script>alert("مرحبا بك!"); window.location.href="../index.php";</script>';
            }
        } else {
            echo '<script>alert("كلمة المرور غير صحيحة.");</script>';
        }
    } else {
        echo '<script>alert("البريد الإلكتروني غير موجود.");</script>';
    }
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <link rel="stylesheet" href="/user/style.css">
</head>
<style>
    
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    
    
    }
    .container {
        width: 400px;
        background-color: #fff;
        margin: 80px auto;
        padding: 30px;
       border-radius: 8px;
       box-shadow: 0 0px 8px rgba(0, 0, 0, 0.2);
       text-align: center;
}h2 {
    color: hsl(276, 45%, 35%);
    /* margin-bottom: 20px; */
}

label {
    display: block;
    text-align: right;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    /* direction: rtl; */
}
input[type="checkbox"]{
    margin-bottom: 0;
    width: auto;
}

button {
    width: 100%;
    padding: 8px;
    background-color: hsl(276, 45%, 35%);
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}


</style>
<body>
   <div class="container">
        <h2>تسجيل الدخول</h2>
    
        <form action="login.php" method="POST">
            <label for="email"> البريدالإلكتروني</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" 
                   required><br>

            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required><br>

            <label for="remember_me">
                <input type="checkbox" id="remember_me" name="remember_me" 
                       <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?>> تذكرني
            </label><br>

            <button type="submit">تسجيل الدخول</button>
        </form>
        <p>ليس لديك حساب؟ <a href="registers.php">إنشاء حساب</a></p>
    </div>
</body>
</html>