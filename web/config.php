<?php

$db_name = 'mysql:host=pizzabd;dbname=PIZZAWEBSITE';
$username = 'MYSQL_USER';
$db_password = 'MYSQL_PASSWORD';

$conn = new PDO($db_name, $username, $db_password);
