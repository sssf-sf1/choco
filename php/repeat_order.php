<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Требуется авторизация']);
    exit();
}

$order_id = $_POST['order_id'] ?? 0;

if (!$order_id) {
    echo json_encode(['status' => 'error', 'message' => 'Не указан номер заказа']);
    exit();
}

try {
    // Получаем информацию о заказе
    $order_sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user']['id']);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if ($order_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Заказ не найден']);
        exit();
    }
    
    // Получаем товары из заказа
    $items_sql = "SELECT oi.*, p.img, p.title FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.id 
                  WHERE order_id = ?";
    $stmt_items = $conn->prepare($items_sql);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    
    // Инициализируем корзину, если еще не существует
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $added_count = 0;
    
    while ($item = $items_result->fetch_assoc()) {
        $product_id = $item['product_id'];
        
        // Проверяем, есть ли уже такой товар в корзине
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $product_id) {
                // Для букетов: просто увеличиваем количество
                if ($item['is_bouquet']) {
                    $cart_item['qty'] += $item['quantity'];
                    $found = true;
                    break;
                }
                // Для других товаров: проверяем совпадают ли дополнения
                else {
                    $cart_addons = $cart_item['addons'] ?? [];
                    $item_addons = json_decode($item['addons'], true) ?? [];
                    
                    if (count($cart_addons) === count($item_addons)) {
                        $match = true;
                        foreach ($item_addons as $key => $value) {
                            if (!isset($cart_addons[$key])) {
                                $match = false;
                                break;
                            }
                        }
                        
                        if ($match && $cart_item['berry_qty'] == $item['berry_qty']) {
                            $cart_item['qty'] += $item['quantity'];
                            $found = true;
                            break;
                        }
                    }
                }
            }
        }
        
        // Если товар не найден в корзине, добавляем его
        if (!$found) {
            // Получаем информацию о товаре
            $product_sql = "SELECT * FROM products WHERE id = ?";
            $stmt_product = $conn->prepare($product_sql);
            $stmt_product->bind_param("i", $product_id);
            $stmt_product->execute();
            $product_result = $stmt_product->get_result();
            $product = $product_result->fetch_assoc();
            
            if ($product) {
                $cart_item = [
                    'id' => $product_id,
                    'title' => $product['title'],
                    'img' => $product['img'],
                    'qty' => $item['quantity'],
                    'price' => $item['price'] / $item['quantity'], // цена за единицу
                    'base_price' => $product['price'] // базовая цена из БД
                ];
                
                // Для букетов
                if ($item['is_bouquet']) {
                    $cart_item['is_bouquet'] = true;
                }
                // Для наборов и других товаров
                else {
                    $cart_item['berry_qty'] = $item['berry_qty'];
                    $cart_item['addons'] = json_decode($item['addons'], true) ?? [];
                    $cart_item['mold_type'] = $item['mold_type'];
                    
                    // Проверяем категорию для определения типа
                    if (in_array($product['category'] ?? '', ['gift', 'classic'])) {
                        $cart_item['is_set'] = true;
                    }
                }
                
                $_SESSION['cart'][] = $cart_item;
                $added_count++;
            }
        }
    }
    
    // Считаем общее количество товаров в корзине
    $cartCount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'];
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Товары из заказа #' . $order_id . ' добавлены в корзину',
        'cartCount' => $cartCount,
        'added' => $added_count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка при повторении заказа: ' . $e->getMessage()
    ]);
}
?>