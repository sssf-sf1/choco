<?php require 'check_admin.php'; require 'db.php'; ?>

<?php
// Обновление статуса
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $mysqli->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

// Получение заказов
$result = $mysqli->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Получение товаров для каждого заказа
$order_items = [];
foreach ($orders as $order) {
    $stmt = $mysqli->prepare("
        SELECT oi.quantity, p.title, p.img, p.price 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $order_items[$order['id']] = $res;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Заказы</title>
<link rel="stylesheet" href="assets/style.css">
<style>
.order-card {border:1px solid #b30000;padding:10px;margin-bottom:20px;background:#fff0f0;}
.order-items img {width:50px;height:50px;margin-right:5px;vertical-align:middle;object-fit:cover;}
.order-items span {margin-right:15px; display:inline-block; margin-bottom:5px;}
</style>
</head>
<body>
<h2>Заказы</h2>

<?php foreach($orders as $o): ?>
<div class="order-card">
    <strong>Заказ #<?= $o['id'] ?> от <?= htmlspecialchars($o['customer_name']) ?> (<?= htmlspecialchars($o['customer_phone']) ?>)</strong><br>
    Email: <?= htmlspecialchars($o['customer_email']) ?><br>
    Адрес: <?= htmlspecialchars($o['address']) ?><br>
    Комментарий: <?= htmlspecialchars($o['comment']) ?><br>
    Способ доставки: <?= htmlspecialchars($o['delivery_method']) ?>, стоимость доставки: <?= $o['delivery_cost'] ?><br>
    <strong>Итого: <?= $o['total'] ?></strong><br>
    <div class="order-items">
        <strong>Товары:</strong><br>
        <?php foreach($order_items[$o['id']] as $item): ?>
            <span>
                <img src="../<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                <?= htmlspecialchars($item['title']) ?> x <?= $item['quantity'] ?> (<?= $item['price'] ?> ₽)
            </span>
        <?php endforeach; ?>
    </div>
    <form method="POST" style="margin-top:10px;">
        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
        <label>Статус: <input type="text" name="status" value="<?= htmlspecialchars($o['status']) ?>" required></label>
        <button type="submit" name="update_status">Обновить статус</button>
    </form>
</div>
<?php endforeach; ?>

<a href="index.php">Назад</a>
<script src="assets/script.js"></script>
</body>
</html>
