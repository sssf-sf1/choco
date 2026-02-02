<?php 
require 'check_admin.php'; 
require 'db.php'; 

// Получаем статистику
$orders_count = $mysqli->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$products_count = $mysqli->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$users_count = $mysqli->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Админка — Панель</title>
<link rel="stylesheet" href="assets/style.css">
<style>
body {font-family: Arial, sans-serif; background:#fff7f7;}
h2 {margin-bottom:20px;}
.dashboard {display:flex; gap:20px; flex-wrap:wrap;}
.card {background:#fff0f0;border:1px solid #b30000;padding:20px;border-radius:10px;flex:1 1 200px;text-align:center;box-shadow:2px 2px 8px rgba(0,0,0,0.1);}
.card h3 {margin:10px 0;}
.card a {display:inline-block;margin-top:10px;padding:5px 10px;background:#b30000;color:#fff;text-decoration:none;border-radius:5px;}
.card a:hover {background:#d60000;}
</style>
</head>
<body>

<h2>Панель администратора</h2>

<div class="dashboard">
    <div class="card">
        <h3>Заказы</h3>
        <p><strong><?= $orders_count ?></strong></p>
        <a href="orders.php">Перейти</a>
    </div>
    <div class="card">
        <h3>Продукты</h3>
        <p><strong><?= $products_count ?></strong></p>
        <a href="products.php">Перейти</a>
    </div>
    <div class="card">
        <h3>Пользователи</h3>
        <p><strong><?= $users_count ?></strong></p>
        <a href="users.php">Перейти</a>
    </div>
</div>

</body>
</html>
