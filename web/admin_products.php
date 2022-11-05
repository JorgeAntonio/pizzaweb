<?php

include 'config.php';

ob_start();
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id))
    header('location:admin_login.php');

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    $select_product = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_product->execute([$name]);
    if ($select_product->rowCount() > 0){
        $message[] = 'this product already exists!';
    }elseif ($image_size > 2000000){
        $message[] = 'image size is too large!';
    }else{
        $insert_product = $conn->prepare("INSERT INTO `products` (name, price, image) VALUES (?, ?, ?)");
        $insert_product->execute([$name, $price, $image]);
        move_uploaded_file($image_tmp_name, $image_folder);
        $message[] = 'new product added successfully!';
    }
}

if (isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('uploaded_img/'.$fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    header('location:admin_products.php');
    $message[] = 'product deleted successfully!';
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>products</title>

    <!--font awesome cdn link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!--custom admin style link-->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="add-products">
    <h1 class="heading">Add product</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="product name" class="box" maxlength="100" required>
        <input type="number" min="0" max="9999999999" name="price" placeholder="product price" class="box"
               maxlength="100" onkeypress="if (this.value.length == 10)" required>
        <input type="file" name="image" class="box" accept="image/jpeg, image/jpg, image/png" required>
        <input type="submit" name="add_product" class="btn" value="add product">
    </form>
</section>

<section class="show-products">
    <h1 class="heading">Show products</h1>
    <div class="box-container">
        <?php
            $select_product = $conn->prepare("SELECT * FROM `products`");
            $select_product->execute();
            if ($select_product->rowCount() > 0){
                 while ($fetch_products = $select_product->fetch(PDO::FETCH_ASSOC)){
        ?>
        <div class="box">
            <div class="price">$<span><?php echo $fetch_products['price']; ?></span>/-</div>
            <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
            <div class="name"><?php echo $fetch_products['name']; ?></div>
            <div class="flex-btn">
                <a href="admin_product_update.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
                <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn"
                onclick="return confirm('delete this product?')">delete</a>
            </div>
        </div>
        <?php
                }
            }else{
                echo '<h2 class="empty">no data found!</h2>';
            }
        ?>
    </div>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>