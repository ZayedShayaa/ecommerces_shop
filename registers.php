<?php
require_once("../include/connected.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $remember_me = isset($_POST['remember_me']); 
    $errors = [];

    if (empty($username)) {
        $errors[] = "اسم المستخدم مطلوب.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "البريد الإلكتروني غير صالح.";
    }
    if (strlen($password) < 6) {
        $errors[] = "كلمة المرور يجب أن تتكون من 6 أحرف على الأقل.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        $errors[] = "كلمة المرور يجب أن تحتوي على حروف كبيرة، أرقام، ورموز.";
    }if (!preg_match('/@gmail\.com$/', $email) && !preg_match('/@hotmail\.com$/', $email)) {
       $errors[]= "البريد الإلكتروني يجب أن يكون من نطاق Gmail أو Hotmail."; 
       //  OR ((strpos($email, 'gmail.com') === false) && (strpos($email, 'hotmail.com') === false))
    
        
    
     } 
    
    if ($password !== $confirm_password) {
        $errors[] = "كلمتا المرور غير متطابقتين.";
    }
    if (empty($recaptcha_response)) {
        $errors[] = "الرجاء التحقق من أنك لست روبوت.";
    }

    //  reCAPTCHA
    if (empty($errors)) {
        $secret_key = "6LfszZoqAAAAANywDE6dis-U29gycQeQjijEzaic";
        $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
        $recaptcha_data = http_build_query([
            'secret' => $secret_key,
            'response' => $recaptcha_response,
        ]);

       @ $options = ['http' => ['method' => 'POST', 'content' => $recaptcha_data]];
       @ $context = stream_context_create($options);
       @ $recaptcha_verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify", false, $context);
       @ $recaptcha_result = json_decode($recaptcha_verify, true);

        if (intval($recaptcha_result["success"]) !== 1) {
            $errors[] = "فشل التحقق من reCAPTCHA. حاول مرة أخرى.";
        }
    }

    if (empty($errors)) {
        $query = "SELECT COUNT(*) FROM user WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($query);
        $stmt->execute(['username' => $username, 'email' => $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = "اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            try {
                $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);

                session_start();
                $_SESSION['username'] = $username;

              
                // if ($remember_me) {
                //     setcookie('username', $username, time() + 60 * 60 * 24 * 30, '/', '', true, true); 
                // }

                echo "<script>alert('تم إنشاء الحساب بنجاح!'); window.location.href = 'login.php';</script>";
                exit;
            } catch (PDOException $e) {
                echo "<script>alert('حدث خطأ: " . $e->getMessage() . "');</script>";
            }
        }
    }

    foreach ($errors as $error) {
        echo "<script>alert('$error');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>تسجيل الحساب</title>
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
        <img src="../images/light4.png" width="100">
        <h2>تسجيل حساب</h2>
        <form action="registers.php" method="post">
            <label for="username">اسم المستخدم</label>
            <input type="text" name="username" id="username" required><br>

            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" required><br>

            <label for="confirm_password">تأكيد كلمة المرور</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>

            <!-- <label for="remember_me">تذكرني <input type="checkbox" name="remember_me" id="remember_me"></label><br> -->
            <div class="g-recaptcha" data-sitekey="6LfszZoqAAAAAFSgiNzv6Bwy83T9NEiby1A1RNNw"></div>
            <button type="submit">إنشاء حساب</button>
        </form>
        <p>هل لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
    </div>
</body>
</html>