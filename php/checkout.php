<?php
session_start();
require_once "config.php";
header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Корзина пуста']);
    exit;
}

// Получаем данные из POST
$orderData = json_decode($_POST['order'] ?? '{}', true);

if (!$orderData) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные данные заказа']);
    exit;
}

// Проверяем обязательные поля
$deliveryType = $orderData['delivery'] ?? 'pickup';
$name = trim($orderData['name'] ?? '');
$phone = trim($orderData['phone'] ?? '');
$address = trim($orderData['address'] ?? '');
$comment = trim($orderData['comment'] ?? '');

if (empty($name) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Заполните имя и телефон']);
    exit;
}

if ($deliveryType === 'delivery' && empty($address)) {
    echo json_encode(['status' => 'error', 'message' => 'Укажите адрес доставки']);
    exit;
}

// Подготовка данных для БД
$total = 0;
$itemsJson = json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE);

// Рассчитываем общую сумму
foreach ($_SESSION['cart'] as $item) {
    $total += ($item['price'] ?? 0) * ($item['qty'] ?? 1);
}

// Добавляем стоимость доставки
if ($deliveryType === 'delivery') {
    $total += 350;
}

try {
    // Вставляем заказ в БД
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, delivery_type, address, comment, items_json, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())");
    
    $stmt->bind_param("ssssssd", 
        $name,
        $phone,
        $deliveryType,
        $address,
        $comment,
        $itemsJson,
        $total
    );
    
    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        
        // Очищаем корзину
        $_SESSION['cart'] = [];
        
        echo json_encode([
            'status' => 'success', 
            'order_id' => $orderId,
            'message' => 'Заказ успешно оформлен!'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ошибка сохранения заказа']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}



$conn->close();
?>