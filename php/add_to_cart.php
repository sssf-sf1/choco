<?php
session_start();
header('Content-Type: application/json');
require_once "config.php";

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
if ($qty < 1) $qty = 1;

if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid id']);
    exit;
}

$stmt = $conn->prepare("SELECT id, title, img, alt, price, category, berries FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Product not found']);
    exit;
}

$product = $res->fetch_assoc();
$stmt->close();

// Определяем тип товара
$is_bouquet = ($product['category'] === 'bouquet');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    // Если товар уже есть в корзине, увеличиваем количество
    $_SESSION['cart'][$id]['qty'] += $qty;
} else {
    // Формируем данные товара
    $product_data = [
        'id' => (int)$product['id'],
        'title' => $product['title'],
        'img' => $product['img'],
        'alt' => $product['alt'],
        'price' => (float)$product['price'], // Используем цену из БД
        'qty' => $qty,
        'category' => $product['category'],
        'berries' => (int)$product['berries'] ?: 0
    ];
    
    if ($is_bouquet) {
        // Для букета: фиксированная цена
        $product_data['is_bouquet'] = true;
        $product_data['price'] = (float)$product['price'];
    } else {
        // Для наборов: настраиваемая цена
        $product_data['is_bouquet'] = false;
        $product_data['base_price'] = 90; // Базовая стоимость набора
        $product_data['berry_qty'] = 9; // Начальное количество ягод
        $product_data['addons'] = [];
        $product_data['mold_type'] = 'heart';
        
        // Начальная цена: база + 9 ягод
        $berry_qty = 9;
        $berry_price = $berry_qty * 180;
        $product_data['price'] = 90 + $berry_price;
    }
    
    $_SESSION['cart'][$id] = $product_data;
}

// Подсчитываем общее количество товаров в корзине
$cartCount = 0;
$cartSum = 0.0;
foreach ($_SESSION['cart'] as $it) {
    $cartCount += $it['qty'];
    $cartSum += $it['price'] * $it['qty'];
}

echo json_encode([
    'status' => 'success',
    'cartCount' => $cartCount,
    'cartSum' => round($cartSum, 2)
]);
?>