<?php
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if ($id === null || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['status'=>'error', 'message'=>'Товар не найден']);
    exit;
}

$found = false;
foreach ($_SESSION['cart'] as $key => $item) {
    // Проверяем разные возможные варианты идентификаторов
    if (isset($item['id']) && $item['id'] == $id) {
        unset($_SESSION['cart'][$key]);
        $found = true;
        break;
    }
    // Проверяем product_id, если нет id
    elseif (isset($item['product_id']) && $item['product_id'] == $id) {
        unset($_SESSION['cart'][$key]);
        $found = true;
        break;
    }
    // Проверяем вложенный массив, если есть
    elseif (isset($item['item']) && isset($item['item']['id']) && $item['item']['id'] == $id) {
        unset($_SESSION['cart'][$key]);
        $found = true;
        break;
    }
    elseif (isset($item['item']) && isset($item['item']['product_id']) && $item['item']['product_id'] == $id) {
        unset($_SESSION['cart'][$key]);
        $found = true;
        break;
    }
}

if ($found) {
    // Переиндексация массива
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    echo json_encode([
        'status' => 'success',
        'message' => 'Товар удален из корзины',
        'cart_count' => count($_SESSION['cart'])
    ]);
} else {
    echo json_encode(['status'=>'error', 'message'=>'Товар не найден в корзине']);
}