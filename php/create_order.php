<?php
session_start();
require_once "config.php";

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ---------------------------
// Получаем данные из POST
// ---------------------------
$customer_name    = trim($_POST['customer_name'] ?? '');
$customer_phone   = trim($_POST['customer_phone'] ?? '');
$customer_email   = trim($_POST['customer_email'] ?? '');
$delivery_method  = trim($_POST['delivery_method'] ?? 'pickup'); // pickup или delivery
$address          = trim($_POST['address'] ?? '');
$comment          = trim($_POST['comment'] ?? '');
$delivery_date    = trim($_POST['delivery_date'] ?? '');
$delivery_time    = trim($_POST['delivery_time_slot'] ?? '');
$user_id          = $_SESSION['user']['id'] ?? null;

// ---------------------------
// Получаем товары
// ---------------------------
$rawItems = $_POST['items'] ?? [];
$items = is_string($rawItems) ? json_decode($rawItems, true) : $rawItems;

// Проверка JSON
if (!is_array($items)) {
    echo json_encode(['status'=>'error', 'message'=>'Неверный формат данных корзины']);
    exit;
}

// ---------------------------
// Проверка обязательных полей
// ---------------------------
$missingFields = [];

if (!$customer_name) {
    $missingFields[] = 'Имя';
}

if (!$customer_phone) {
    $missingFields[] = 'Телефон';
}

if (empty($items)) {
    $missingFields[] = 'Корзина пуста';
}

// Проверка для доставки
if ($delivery_method === 'delivery') {
    if (!$address) {
        $missingFields[] = 'Адрес доставки';
    }
    if (!$delivery_date || !$delivery_time) {
        $missingFields[] = 'Дата или время доставки';
    }
}

// Проверка для самовывоза
if ($delivery_method === 'pickup') {
    if (!$delivery_date || !$delivery_time) {
        $missingFields[] = 'Дата или время самовывоза';
    }
}

// Если есть пропущенные поля, выводим ошибку
if (!empty($missingFields)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Не заполнены обязательные поля: ' . implode(', ', $missingFields)
    ]);
    exit;
}

// ---------------------------
// Считаем итоговую сумму
// ---------------------------
$total = 0;
foreach ($items as $item) {
    $total += floatval($item['price'] ?? 0);
}

// Стоимость доставки
$delivery_cost = ($delivery_method === 'delivery') ? 350 : 0;
$total += $delivery_cost;

// ---------------------------
// Сохраняем детали заказа
// ---------------------------
$order_details = json_encode($items, JSON_UNESCAPED_UNICODE);

// ---------------------------
// Начинаем транзакцию
// ---------------------------
$conn->begin_transaction();

try {
    // ---------------------------
    // Вставка заказа в таблицу orders
    // ---------------------------
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (customer_name, customer_phone, customer_email, user_id, total, delivery_method, address, comment, status, delivery_cost, order_details, delivery_date, delivery_time_slot) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssissssisss",
        $customer_name,
        $customer_phone,
        $customer_email,
        $user_id,
        $total,
        $delivery_method,
        $address,
        $comment,
        $delivery_cost,
        $order_details,
        $delivery_date,
        $delivery_time
    );

    if (!$stmt->execute()) {
        throw new Exception("Ошибка при создании заказа: " . $stmt->error);
    }
    
    // Получаем ID созданного заказа
    $order_id = $stmt->insert_id;
    $stmt->close();

    // ---------------------------
    // Сохраняем товары в таблицу order_items с названиями
    // ---------------------------
    foreach ($items as $item) {
        // Получаем название товара из таблицы products
        $product_id = intval($item['product_id'] ?? $item['id'] ?? 0);
        $product_title = '';
        
        if ($product_id > 0) {
            // Запрашиваем название товара из БД
            $title_stmt = $conn->prepare("SELECT title FROM products WHERE id = ?");
            $title_stmt->bind_param("i", $product_id);
            $title_stmt->execute();
            $title_result = $title_stmt->get_result();
            
            if ($title_row = $title_result->fetch_assoc()) {
                $product_title = $title_row['title'];
            }
            $title_stmt->close();
        }
        
        // Если название не нашлось, используем дефолтное
        if (empty($product_title)) {
            $product_title = $item['name'] ?? 'Товар #' . $product_id;
        }
        
        // Сохраняем детали товара (если есть дополнительные параметры)
        $details = null;
        if (isset($item['addons']) || isset($item['berry_qty']) || isset($item['mold_type'])) {
            $details_data = [];
            if (isset($item['berry_qty'])) $details_data['berry_qty'] = $item['berry_qty'];
            if (isset($item['addons'])) $details_data['addons'] = $item['addons'];
            if (isset($item['mold_type'])) $details_data['mold_type'] = $item['mold_type'];
            $details = json_encode($details_data, JSON_UNESCAPED_UNICODE);
        }
        
        // Вставляем товар в order_items
        $item_stmt = $conn->prepare("
            INSERT INTO order_items 
            (order_id, product_id, product_title, quantity, price, details) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $quantity = intval($item['quantity'] ?? $item['qty'] ?? 1);
        $price = floatval($item['price'] ?? 0);
        
        $item_stmt->bind_param(
            "iisids",
            $order_id,
            $product_id,
            $product_title,
            $quantity,
            $price,
            $details
        );
        
        if (!$item_stmt->execute()) {
            throw new Exception("Ошибка при сохранении товара: " . $item_stmt->error);
        }
        
        $item_stmt->close();
    }

    // ---------------------------
    // Фиксируем транзакцию
    // ---------------------------
    $conn->commit();

    // ---------------------------
    // Очищаем корзину и возвращаем успех
    // ---------------------------
    $_SESSION['cart'] = [];
    echo json_encode([
        'status' => 'success', 
        'message' => 'Заказ успешно оформлен!',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Откатываем транзакцию в случае ошибки
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}