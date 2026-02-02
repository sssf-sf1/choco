<?php require 'check_admin.php'; require 'db.php'; ?>

<?php
// Добавление продукта
if (isset($_POST['add_product'])) {
    $title = $_POST['title'];
    $img = $_POST['img']; // в базе: "img/klubnika1.jpg"
    $alt = $_POST['alt'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $weight = $_POST['weight'];
    $chocolate = $_POST['chocolate'];
    $bouquet_type = $_POST['bouquet_type'];
    $berries = $_POST['berries'];

    $stmt = $mysqli->prepare("INSERT INTO products (title,img,alt,price,category,weight,chocolate,bouquet_type,berries) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssdsisss",$title,$img,$alt,$price,$category,$weight,$chocolate,$bouquet_type,$berries);
    $stmt->execute();
}

// Удаление продукта
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $mysqli->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

// Редактирование продукта
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $img = $_POST['img'];
    $alt = $_POST['alt'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $weight = $_POST['weight'];
    $chocolate = $_POST['chocolate'];
    $bouquet_type = $_POST['bouquet_type'];
    $berries = $_POST['berries'];

    $stmt = $mysqli->prepare("UPDATE products SET title=?,img=?,alt=?,price=?,category=?,weight=?,chocolate=?,bouquet_type=?,berries=? WHERE id=?");
    $stmt->bind_param("sssdsisssi",$title,$img,$alt,$price,$category,$weight,$chocolate,$bouquet_type,$berries,$id);
    $stmt->execute();
}

// Получение продуктов
$result = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Продукты</title>
<link rel="stylesheet" href="assets/style.css">
<style>
.product-card {border:1px solid #b30000;padding:10px;margin-bottom:20px;background:#fff0f0; display:flex; align-items:center;}
.product-card img {width:80px;height:80px;margin-right:15px; object-fit:cover;}
.product-info {flex-grow:1;}
.product-actions input, .product-actions select {margin-right:5px;margin-bottom:5px;}
</style>
</head>
<body>
<h2>Продукты</h2>

<h3>Добавить продукт</h3>
<form method="POST" class="product-form">
<input type="text" name="title" placeholder="Название" required>
<input type="text" name="img" placeholder="Путь к картинке, например img/klubnika1.jpg" required>
<input type="text" name="alt" placeholder="Alt" required>
<input type="number" step="0.01" name="price" placeholder="Цена" required>
<input type="text" name="category" placeholder="Категория" required>
<input type="text" name="weight" placeholder="Вес" required>
<input type="text" name="chocolate" placeholder="Шоколад" required>
<input type="text" name="bouquet_type" placeholder="Тип букета" required>
<input type="text" name="berries" placeholder="Ягоды" required>
<button type="submit" name="add_product">Добавить</button>
</form>

<h3>Список продуктов</h3>
<?php foreach($products as $p): ?>
<div class="product-card">
    <img src="../<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['alt']) ?>">
    <div class="product-info">
        <strong><?= htmlspecialchars($p['title']) ?></strong><br>
        Цена: <?= $p['price'] ?> ₽, Категория: <?= htmlspecialchars($p['category']) ?><br>
        Вес: <?= htmlspecialchars($p['weight']) ?>, Шоколад: <?= htmlspecialchars($p['chocolate']) ?><br>
        Букет: <?= htmlspecialchars($p['bouquet_type']) ?>, Ягоды: <?= htmlspecialchars($p['berries']) ?>
    </div>
    <div class="product-actions">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="text" name="title" value="<?= htmlspecialchars($p['title']) ?>" required>
            <input type="text" name="img" value="<?= htmlspecialchars($p['img']) ?>" required>
            <input type="text" name="alt" value="<?= htmlspecialchars($p['alt']) ?>" required>
            <input type="number" step="0.01" name="price" value="<?= $p['price'] ?>" required>
            <input type="text" name="category" value="<?= htmlspecialchars($p['category']) ?>" required>
            <input type="text" name="weight" value="<?= htmlspecialchars($p['weight']) ?>" required>
            <input type="text" name="chocolate" value="<?= htmlspecialchars($p['chocolate']) ?>" required>
            <input type="text" name="bouquet_type" value="<?= htmlspecialchars($p['bouquet_type']) ?>" required>
            <input type="text" name="berries" value="<?= htmlspecialchars($p['berries']) ?>" required>
            <button type="submit" name="edit_product">Редактировать</button>
        </form>
        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Удалить продукт?')">Удалить</a>
    </div>
</div>
<?php endforeach; ?>
<a href="index.php">Назад</a>
<script src="assets/script.js"></script>
</body>
</html>
