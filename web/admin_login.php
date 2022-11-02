<?php

include 'config.php';

ob_start();
session_start();

if(isset($_POST['login'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
    $select_admin->execute([$name, $pass]);
    $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if($select_admin->rowCount() > 0){
        $_SESSION['admin_id'] = $row['id'];
        header('location:admin_page.php');
    }else{
        $message[] = 'incorrect username or password!';
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
    <title>admin login</title>

    <!--font awesome cdn link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!--custom admin style link-->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php

if (isset($message)){
    foreach ($message as $message){
        echo '<div class="message">
                <span>'.$message.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>';
    }
}

?>

    <section class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>
            <p>defaul username = <span>admin</span> & password = <span>111</span> </p>
            <label>
                <input type="text" name="name" placeholder="enter your username" maxlength="20"
                       required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            </label>
            <label>
                <input type="password" name="pass" placeholder="enter your password" maxlength="20"
                       required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            </label>
            <input type="submit" class="btn" value="login now" name="login">
        </form>
    </section>
</body>
</html>