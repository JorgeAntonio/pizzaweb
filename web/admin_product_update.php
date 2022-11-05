<?php

include 'config.php';

ob_start();
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id))
    header('location:admin_login.php');

if (isset($_POST['update_product'])){

    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ? WHERE id = ?");
    $update_product->execute([$name, $price, $pid]);
    $message[] = 'product updated successfully!';

    if (!empty($image)){
        if ($image_size > 2000000){
            $message[] = 'image size is too large!';
        }else{
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $pid]);
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            $message[] = 'image updated successfully!';
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
    <title>update product</title>

    <!--font awesome cdn link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!--custom admin style link-->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="update-product">
    <?php
        $update_id = $_GET['update'];
        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_product->execute([$update_id]);
        if ($select_product->rowCount() > 0){
        while ($fetch_products = $select_product->fetch(PDO::FETCH_ASSOC)){
    ?>
        <h1 class="heading">update product</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="pid" value="<?php echo $fetch_products['id'];?>">
            <input type="hidden" name="old_image" value="<?php echo $fetch_products['image'];?>">
            <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
            <input type="text" name="name" placeholder="product name" class="box" maxlength="100" value="<?php echo $fetch_products['name'];?>" required>
            <input type="number" min="0" max="9999999999" name="price" placeholder="product price" class="box"
                   maxlength="100" onkeypress="if (this.value.length == 10)" value="<?php echo $fetch_products['price'];?>" required>
            <input type="file" name="image" class="box" accept="image/jpeg, image/jpg, image/png">
            <div class="flex-btn">
                <input type="submit" name="update_product" class="btn" value="update product">
                <a href="admin_products.php" class="option-btn">Go back</a>
            </div>
        </form>
    <?php
            }
        }else{
            echo '<h2 class="empty">no data found!</h2>';
        }
    ?>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>