<?php
session_start();
require 'php/config.php';

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Получаем историю заказов пользователя с детализацией
$orders_sql = "SELECT o.*,
                      (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) as items_total
               FROM orders o 
               WHERE o.user_id = ? 
               ORDER BY o.created_at DESC";
$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет — Chocoberry Place</title>
     <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="shortcut icon" href="favicon.ico">
     <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .account-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .account-header {
            background: linear-gradient(135deg, #ff85a3, rgb(145, 22, 63));
            color: white;
            padding: 40px 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .account-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .account-nav {
            display: flex;
            background: #fde4ec;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .nav-item {
            flex: 1;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            font-weight: bold;
            color: #7a1f3d;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .nav-item.active {
            background: white;
            color: #ff3366;
            border-bottom-color: #ff3366;
        }

        .nav-item:hover {
            background: white;
        }

        .account-content {
            display: none;
        }

        .account-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(122, 31, 61, 0.1);
            transition: transform 0.3s;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(122, 31, 61, 0.2);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
                align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #fde4ec;
        }

        .order-number {
            font-weight: bold;
            color: #ff3366;
            font-size: 18px;
        }

        .order-date {
            color: #666;
        }

        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .status-new { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-canceled { background: #f8d7da; color: #721c24; }

        .order-info {
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #7a1f3d;
            min-width: 150px;
        }

        .info-value {
            color: #333;
        }

        .order-items {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .items-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #7a1f3d;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #fde4ec;
        }

        .order-total {
            font-size: 20px;
            font-weight: bold;
            color: #7a1f3d;
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #ff3366;
            color: white;
        }

        .btn-primary:hover {
            background: #e60050;
        }

        .btn-secondary {
            background: #7a1f3d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a152d;
        }

        .empty-orders {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .empty-orders i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #fde4ec;
        }

        .profile-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(122, 31, 61, 0.1);
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: bold;
            color: #7a1f3d;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
        }

        .item-list {
            list-style: none;
            padding: 0;
        }

        .item-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .item-details {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .item-addons {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-footer {
                flex-direction: column;
                gap: 15px;
            }

            .order-actions {
                width: 100%;
                justify-content: center;
            }

            .info-row {
                flex-direction: column;
            }

            .info-label {
                min-width: auto;
                margin-bottom: 3px;
            }
        }



    </style>
    <script src="./js/jquery3.7.1.js"></script>
</head>
<body>

<div id="header"></div>

<div class="account-container">
    <div class="account-header">
        <h1>Личный кабинет</h1>
        <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
    </div>

    <div class="account-nav">
        <div class="nav-item active" data-tab="orders">Мои заказы</div>
        <div class="nav-item" data-tab="profile">Профиль</div>
    </div>

    <!-- Вкладка "Мои заказы" -->
    <div class="account-content active" id="orders-tab">
        <h2>История заказов</h2>
        
        <?php if ($orders_result->num_rows > 0): ?>
            <div class="orders-list">
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                    <?php
                    // Получаем детали заказа - берем название товара из order_items
                    $details_sql = "SELECT oi.*, p.title as product_name, p.img, p.price as base_price, 
                                           p.category, p.bouquet_type 
                                   FROM order_items oi 
                                   LEFT JOIN products p ON oi.product_id = p.id 
                                   WHERE oi.order_id = ?";
                    $stmt_details = $conn->prepare($details_sql);
                    $stmt_details->bind_param("i", $order['id']);
                    $stmt_details->execute();
                    $details_result = $stmt_details->get_result();
                    ?>
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-number">Заказ #<?php echo $order['id']; ?></div>
                                <div class="order-date">Оформлен: <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></div>
                            </div>
                            
                        </div>

                        <div class="order-info">
                            <div class="info-row">
                                <div class="info-label">Имя:</div>
                                <div class="info-value">
                                    <?php 
                                    $customer_name = $order['customer_name'];
                                    if (empty($customer_name) && isset($_SESSION['user']['name'])) {
                                        $customer_name = $_SESSION['user']['name'];
                                        if (!empty($_SESSION['user']['surname'])) {
                                            $customer_name .= ' ' . $_SESSION['user']['surname'];
                                        }
                                    }
                                    echo htmlspecialchars($customer_name);
                                    ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Телефон:</div>
                                <div class="info-value">
                                    <?php 
                                    $customer_phone = $order['customer_phone'];
                                    if (empty($customer_phone) && !empty($_SESSION['user']['phone'])) {
                                        $customer_phone = $_SESSION['user']['phone'];
                                    }
                                    echo htmlspecialchars($customer_phone ?? '');
                                    ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Способ получения:</div>
                                <div class="info-value">
                                    <?php 
                                    if ($order['delivery_method'] == 'delivery') {
                                        echo 'Доставка (' . number_format($order['delivery_cost'], 0, '.', ' ') . ' ₽)';
                                    } else {
                                        echo 'Самовывоз';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <?php if ($order['delivery_date']): ?>
                            <div class="info-row">
                                <div class="info-label">
                                    <?php echo $order['delivery_method'] == 'delivery' ? 'Дата доставки:' : 'Дата самовывоза:'; ?>
                                </div>
                                <div class="info-value">
                                    <?php 
                                    $delivery_date = date('d.m.Y', strtotime($order['delivery_date']));
                                    echo htmlspecialchars($delivery_date);
                                    ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($order['delivery_time_slot']): ?>
                            <div class="info-row">
                                <div class="info-label">
                                    <?php echo $order['delivery_method'] == 'delivery' ? 'Время доставки:' : 'Время самовывоза:'; ?>
                                </div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($order['delivery_time_slot']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($order['address'] && $order['delivery_method'] == 'delivery'): ?>
                            <div class="info-row">
                                <div class="info-label">Адрес доставки:</div>
                                <div class="info-value"><?php echo htmlspecialchars($order['address']); ?></div>
                            </div>
                            <?php elseif ($order['delivery_method'] == 'pickup' && !empty($order['delivery_address'])): ?>
                            <div class="info-row">
                                <div class="info-label">Адрес самовывоза:</div>
                                <div class="info-value"><?php echo htmlspecialchars($order['delivery_address']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($order['comment'])): ?>
                            <div class="info-row">
                                <div class="info-label">Комментарий:</div>
                                <div class="info-value"><?php echo htmlspecialchars($order['comment']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                      <div class="order-items">
    <div class="items-title">Состав заказа:</div>
    <ul class="item-list">
        <?php while ($item = $details_result->fetch_assoc()): ?>
            <?php
            // Декодируем дополнения из JSON
            $addons = json_decode($item['addons'] ?? '[]', true);
            $item_details = json_decode($item['details'] ?? '{}', true);
            
            // Берем название товара в порядке приоритета:
            // 1. Название из order_items (product_title) - сохраняемое при оформлении заказа
            // 2. Если product_title пустое, пытаемся получить название из products по product_id
            // 3. Стандартное название
            
            $product_name = '';
            
            // 1. Проверяем сохраненное название из order_items
            if (!empty($item['product_title'])) {
                $product_name = $item['product_title'];
            } else {
                // 2. Если сохраненного названия нет, пробуем получить из таблицы products
                $product_id = $item['product_id'];
                if ($product_id > 0) {
                    $stmt_product = $conn->prepare("SELECT title FROM products WHERE id = ?");
                    $stmt_product->bind_param("i", $product_id);
                    $stmt_product->execute();
                    $product_result = $stmt_product->get_result();
                    if ($product_row = $product_result->fetch_assoc()) {
                        $product_name = $product_row['title'];
                    }
                    $stmt_product->close();
                }
                
                // 3. Если не нашли название в БД, используем стандартное
                if (empty($product_name)) {
                    $product_name = 'Товар #' . $product_id;
                }
            }
            
            $item_total = $item['price'];
            $quantity = $item['quantity'];
            
            // Определяем, является ли товар букетом
            $isBouquet = false;
            
            // 1. Проверяем поле is_bouquet из details
            if (isset($item_details['is_bouquet'])) {
                $isBouquet = (bool)$item_details['is_bouquet'];
            }
            // 2. Проверяем поле category из таблицы products
            elseif (isset($item['category']) && $item['category'] === 'bouquet') {
                $isBouquet = true;
            }
            // 3. Проверяем bouquet_type
            elseif (isset($item['bouquet_type']) && $item['bouquet_type'] !== null) {
                $isBouquet = true;
            }
            
            // Массив фиксированных цен для ягод (должен быть объявлен в начале файла)
            // Если он не объявлен, объявляем локально
            $berry_prices = [
                3 => 565,
                4 => 745,
                6 => 1115,
                8 => 1475,
                9 => 1710,
                12 => 2210,
                16 => 2950,
                18 => 3310,
                20 => 3670,
                25 => 4600,
                30 => 5500
            ];
            
            $berry_qty = $item['berry_qty'] ?? 9;
            
            // Рассчитываем стоимость на основе фиксированных цен
            $berry_price = $berry_prices[$berry_qty] ?? 1710;
            
            // Рассчитываем стоимость дополнений
            $addons_cost = 0;
            if (is_array($addons)) {
                // Проверяем разные форматы данных о дополнениях
                if (isset($addons['blueberries'])) {
                    if (is_array($addons['blueberries'])) {
                        $addons_cost += isset($addons['blueberries']['price']) ? $addons['blueberries']['price'] : 100;
                    } else {
                        $addons_cost += $addons['blueberries'] ? 100 : 0;
                    }
                }
                if (isset($addons['strawberries'])) {
                    if (is_array($addons['strawberries'])) {
                        $addons_cost += isset($addons['strawberries']['price']) ? $addons['strawberries']['price'] : 50;
                    } else {
                        $addons_cost += $addons['strawberries'] ? 50 : 0;
                    }
                }
                if (isset($addons['mold'])) {
                    if (is_array($addons['mold'])) {
                        $addons_cost += isset($addons['mold']['price']) ? $addons['mold']['price'] : 50;
                    } else {
                        $addons_cost += $addons['mold'] ? 50 : 0;
                    }
                }
            }
            
            // Для букетов показываем просто цену
            if ($isBouquet) {
                ?>
                <li>
                    <div>
                        <strong><?php echo htmlspecialchars($product_name); ?></strong>
                        <div class="item-details">
                            Количество: <?php echo $quantity; ?> шт.
                            <br><em>Букет</em>
                        </div>
                    </div>
                    <div style="font-weight: bold; text-align: right;">
                        <?php echo number_format($item_total, 0, '.', ' '); ?> ₽
                        <div style="font-size: 12px; color: #666; font-weight: normal;">
                            <?php echo number_format($item_total / $quantity, 0, '.', ' '); ?> ₽ × <?php echo $quantity; ?>
                        </div>
                    </div>
                </li>
                <?php
            } else {
                // Для наборов показываем детализацию с фиксированными ценами
                ?>
                <li>
                    <div>
                        <strong><?php echo htmlspecialchars($product_name); ?></strong>
                        <div class="item-details">
                            Количество: <?php echo $quantity; ?> шт.
                            <?php if ($berry_qty > 0): ?>
                               
                            <?php endif; ?>
                            <?php if ($addons_cost > 0): ?>
                                <br>Дополнения: +<?php echo number_format($addons_cost, 0, '.', ' '); ?> ₽
                                <?php 
                                if (isset($addons['blueberries']) && 
                                   (is_array($addons['blueberries']) ? $addons['blueberries']['price'] > 0 : $addons['blueberries'])) {
                                    echo "(голубика) ";
                                }
                                if (isset($addons['strawberries']) && 
                                   (is_array($addons['strawberries']) ? $addons['strawberries']['price'] > 0 : $addons['strawberries'])) {
                                    echo "(клубника) ";
                                }
                                if (isset($addons['mold']) && 
                                   (is_array($addons['mold']) ? $addons['mold']['price'] > 0 : $addons['mold'])) {
                                    $mold_type = $item['mold_type'] ?? 'heart';
                                    $mold_names = ['heart' => 'сердце', 'star' => 'звезда', 'flower' => 'цветок', 'bear' => 'медведь'];
                                    echo "(молд " . ($mold_names[$mold_type] ?? $mold_type) . ")";
                                }
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="font-weight: bold; text-align: right;">
                        <?php echo number_format($item_total, 0, '.', ' '); ?> ₽
                        <div style="font-size: 12px; color: #666; font-weight: normal;">
                            
                        </div>
                    </div>
                </li>
                <?php
            }
            ?>
        <?php endwhile; ?>
    </ul>
</div>

                        <div class="order-footer">
                            <div class="order-total">
                                Итого: <?php echo number_format($order['total'], 0, '.', ' '); ?> ₽
                                <?php if ($order['delivery_method'] == 'delivery' && $order['delivery_cost'] > 0): ?>
                                <br><small style="font-size: 14px; color: #666;">
                                    (включая доставку: <?php echo number_format($order['delivery_cost'], 0, '.', ' '); ?> ₽)
                                </small>
                                <?php endif; ?>
                            </div>
                    
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h3>У вас пока нет заказов</h3>
                <p>Сделайте свой первый заказ в нашем магазине!</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Перейти к покупкам</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Вкладка "Профиль" -->
    <div class="account-content" id="profile-tab">
        <h2>Личная информация</h2>
        <div class="profile-info">
            <div class="info-group">
                <div class="info-label">Имя</div>
                <div class="info-value"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?></div>
            </div>
            
            <div class="info-group">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?></div>
            </div>
            <?php if (!empty($_SESSION['user']['phone'])): ?>
            <div class="info-group">
                <div class="info-label">Телефон</div>
                <div class="info-value"><?php echo htmlspecialchars($_SESSION['user']['phone']); ?></div>
            </div>
            <?php endif; ?>
            <button class="btn btn-primary" onclick="editProfile()">Редактировать профиль</button>
        </div>
    </div>
</div>

<div id="footer"></div>

<script>
    $(function() {
        $("#header").load("php/header.php");
        $("#footer").load("html/footer.html");

        // Переключение вкладок
        $('.nav-item').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.nav-item').removeClass('active');
            $(this).addClass('active');
            
            $('.account-content').removeClass('active');
            $(`#${tab}-tab`).addClass('active');
        });

        // Повторить заказ
        $(document).on('click', '.repeat-order', function() {
            const orderId = $(this).data('order-id');
            repeatOrder(orderId);
        });
    });

    function editProfile() {
        alert('Функция редактирования профиля будет реализована позже');
    }

    function repeatOrder(orderId) {
        if (confirm('Хотите повторить этот заказ? Все товары будут добавлены в корзину.')) {
            $.ajax({
                url: 'php/repeat_order.php',
                type: 'POST',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Товары добавлены в корзину!');
                        if ($('#cartCount').length) {
                            $('#cartCount').text(response.cartCount);
                        }
                        if (confirm('Перейти в корзину для оформления заказа?')) {
                            window.location.href = 'cart.php';
                        }
                    } else {
                        alert(response.message || 'Ошибка при повторе заказа');
                    }
                },
                error: function() {
                    alert('Ошибка соединения с сервером');
                }
            });
        }
    }
</script>

</body>
</html>