<?php

include 'config.php';

ob_start();
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id))
    header('location:admin_login.php');

if (isset($_POST['update'])){
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $update_profile_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
    $update_profile_name->execute([$name, $admin_id]);

    $prev_pass = $_POST['prev_pass'];
    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $confirm_pass = sha1($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);
    $empty_pass = '';

    if ($old_pass != $empty_pass){
        if ($old_pass != $prev_pass){
            $message[] = 'your old password not matched!';
        }elseif($new_pass != $confirm_pass){
            $message[] = 'your new password and confirm password is not match!';
        }else{
            if ($new_pass != $empty_pass){
                $update_admin_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                $update_admin_pass->execute([$confirm_pass, $admin_id]);
                $message[] = 'your profile updated successfully!';
            }else{
                $message[] = 'please enter a new password!';
            }
        }
    }else{
        $message[] = 'please enter old password!';
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
    <title>admin profile update</title>

    <!--font awesome cdn link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!--custom admin style link-->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h3>Update Profile</h3>
        <input type="hidden" name="prev_pass" value="<?= $fetch_profile['password']; ?>">
        <label>
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="enter your username" maxlength="20"
                   required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <label>
            <input type="password" name="old_pass" placeholder="enter old your password" maxlength="20"
                   class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <label>
            <input type="password" name="new_pass" placeholder="enter new your password" maxlength="20"
                   class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <label>
            <input type="password" name="confirm_pass" placeholder="confirm new password" maxlength="20"
                   class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </label>
        <input type="submit" class="btn" value="update now" name="update">
    </form>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>