<?php

include 'config.php';

ob_start();
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id))
    header('location: admin_login.php');

if (isset($_POST['register'])){
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $password = sha1($_POST['pass']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    $confirm_password = sha1($_POST['cpass']);
    $confirm_password = filter_var($confirm_password, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
    $select_admin->execute([$name]);

    if ($select_admin->rowCount() > 0) {
        $message[] = 'this admin already exists!';
    }else{
        if ($password != $confirm_password) {
            $message[] = 'your password and confirm password is not match!';
        }else{
            $insert_admin = $conn->prepare("INSERT INTO `admin` (name, password) VALUES (?, ?)");
            $insert_admin->execute([$name, $confirm_password]);
            $message[] = 'new admin registered successfully!';
        }
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>register admin</title>

    <!--font awesome cdn link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!--custom admin style link-->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Register now</h3>
        <p>defaul username = <span>admin</span> & password = <span>111</span> </p>
        <label>
            <input type="text" name="name" placeholder="enter your username" maxlength="20"
                   required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <label>
            <input type="password" name="pass" placeholder="enter your password" maxlength="20"
                   required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <label>
            <input type="password" name="cpass" placeholder="confirm your password" maxlength="20"
                   required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <input type="submit" class="btn" value="register now" name="register">
    </form>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>