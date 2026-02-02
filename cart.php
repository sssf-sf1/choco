<?php
session_start();
require_once "php/config.php";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="shortcut icon" href="favicon.ico">
     <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/cart.css">
    <script src="js/jquery3.7.1.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div id="header"></div>

<section class="cart-content">
    <div class="container">
        <h2 class="section-title">Корзина</h2>

        <div id="successMessage" class="success-message"></div>
        <div id="errorMessage" class="error-message"></div>
        <div id="loadingOverlay" class="loading-overlay">
            <div class="spinner"></div>
            <div class="loading-text">Оформляем заказ...</div>
        </div>

        <?php if (empty($_SESSION["cart"])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Ваша корзина пуста</h3>
                <p>Добавьте товары, чтобы сделать заказ</p>
                <a href="index.php" class="btn-continue">Продолжить покупки</a>
            </div>
        <?php else: ?>
            <div class="cart-content-wrapper">
                <div class="cart-items" id="cartItems">
                    <?php 
                    $total_items_price = 0;
                    $total_addons_price = 0;

                    // Массив фиксированных цен для разных количеств ягод
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

                    foreach ($_SESSION["cart"] as $item): 
                        $product_id = $item["id"];
                        $product_details = [];
                        $is_bouquet = isset($item['is_bouquet']) ? $item['is_bouquet'] : false;
                        $berry_qty = isset($item['berry_qty']) ? $item['berry_qty'] : 9;
                        $is_set = false;
                        $item_addons_price = 0;

                        if ($conn) {
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                            $stmt->bind_param("i", $product_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                $product_details = $result->fetch_assoc();
                                if (!$is_bouquet) {
                                    $is_bouquet = ($product_details['category'] == 'bouquet');
                                }
                                $is_set = in_array($product_details['category'] ?? '', ['gift', 'classic']);
                            }
                            $stmt->close();
                        }

                        if ($is_bouquet) {
                            $berry_qty = 0;
                            $has_blueberries = $has_strawberries = $has_mold = '';
                            $mold_type = '';
                        } else {
                            $has_blueberries = !empty($item['addons']['blueberries']) ? 'checked' : '';
                            $has_strawberries = !empty($item['addons']['strawberries']) ? 'checked' : '';
                            $has_mold = !empty($item['addons']['mold']) ? 'checked' : '';
                            $mold_type = $item['mold_type'] ?? 'heart';
                        }

                        // Расчет цены товара
                        $item_base_price = 0;
                        if ($is_bouquet) {
                            $item_base_price = floatval($item['price']);
                        } elseif (isset($berry_prices[$berry_qty])) {
                            $item_base_price = $berry_prices[$berry_qty];
                        } else {
                            // Если количество ягод не из списка, берем ближайшее доступное
                            $available_qty = array_keys($berry_prices);
                            $closest_qty = 9; // значение по умолчанию
                            foreach ($available_qty as $qty) {
                                if ($qty <= $berry_qty) {
                                    $closest_qty = $qty;
                                } else {
                                    break;
                                }
                            }
                            $item_base_price = $berry_prices[$closest_qty];
                            $berry_qty = $closest_qty; // Обновляем количество ягод
                        }

                        // Расчет стоимости дополнений
                        if (!$is_bouquet && isset($item['addons'])) {
                            foreach ($item['addons'] as $addon => $addon_data) {
                                if (isset($addon_data['price'])) {
                                    $item_addons_price += floatval($addon_data['price']) * $item['qty'];
                                }
                            }
                        }

                        $item_total = $item_base_price * $item['qty'] + $item_addons_price;
                        $total_items_price += $item_total;
                        $total_addons_price += $item_addons_price;

                        $js_base_price = $item_base_price;
                    ?>
                        <div class="cart-item <?php echo $is_bouquet ? 'bouquet' : ''; ?> <?php echo $is_set ? 'set' : ''; ?>" 
                             data-id="<?= $item["id"] ?>" 
                             data-base-price="<?= $js_base_price ?>" 
                             data-is-bouquet="<?php echo $is_bouquet ? '1' : '0'; ?>"
                             data-is-set="<?php echo $is_set ? '1' : '0'; ?>"
                             data-berry-qty="<?= $berry_qty ?>">
                            <div class="cart-item-left">
                                <img src="<?= htmlspecialchars($item["img"]) ?>" alt="<?= htmlspecialchars($item["title"]) ?>" class="cart-item-img">
                            </div>
                            <div class="cart-item-center">
                                <h3 class="cart-item-title"><?= htmlspecialchars($item["title"]) ?></h3>
                                
                                <div class="product-details-info">
                                    <?php if (!empty($product_details)): ?>
                                        <div class="info-row">
                                            <span class="info-label">Категория:</span>
                                            <span class="info-value">
                                                <?php 
                                                if ($product_details['category'] == 'bouquet') echo 'Букет';
                                                elseif ($product_details['category'] == 'gift') echo 'Подарочный набор';
                                                elseif ($product_details['category'] == 'classic') echo 'Классический набор';
                                                else echo htmlspecialchars($product_details['category']);
                                                ?>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Шоколад:</span>
                                            <span class="info-value"><?= htmlspecialchars($product_details['chocolate'] ?? 'Молочный') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Вес:</span>
                                            <span class="info-value"><?= htmlspecialchars($product_details['weight'] ?? '0') ?> г</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!$is_bouquet): ?>
                                <div class="berry-selection">
                                    <h4>Количество ягод:</h4>
                                    <select class="berry-qty-select" data-id="<?= $item["id"] ?>">
                                        <?php 
                                        $available_qty = [3, 4, 6, 8, 9, 12, 16, 18, 20, 25, 30];
                                        foreach ($available_qty as $qty): 
                                            $price = $berry_prices[$qty];
                                        ?>
                                            <option value="<?= $qty ?>" 
                                                    data-price="<?= $price ?>" 
                                                    <?= $berry_qty == $qty ? 'selected' : '' ?>>
                                                <?= $qty ?> ягод — <?= number_format($price, 0, '.', ' ') ?> ₽
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!$is_bouquet): ?>
                                <div class="addons-section">
                                    <h4>Дополнительные опции:</h4>
                                    <div class="addon-option">
                                        <input type="checkbox" class="addon-checkbox" data-addon="blueberries" data-price="100" data-id="<?= $item["id"] ?>" id="blueberries_<?= $item["id"] ?>" <?= $has_blueberries ?>>
                                        <label for="blueberries_<?= $item["id"] ?>"><i class="fas fa-plus-circle"></i> Добавить свежую голубику (+100 ₽)</label>
                                    </div>
                                    <div class="addon-option">
                                        <input type="checkbox" class="addon-checkbox" data-addon="strawberries" data-price="50" data-id="<?= $item["id"] ?>" id="strawberries_<?= $item["id"] ?>" <?= $has_strawberries ?>>
                                        <label for="strawberries_<?= $item["id"] ?>"><i class="fas fa-plus-circle"></i> Добавить свежую нарезанную клубнику (+50 ₽)</label>
                                    </div>
                                    <div class="addon-option">
                                        <input type="checkbox" class="addon-checkbox" data-addon="mold" data-price="50" data-id="<?= $item["id"] ?>" id="mold_<?= $item["id"] ?>" <?= $has_mold ?>>
                                        <label for="mold_<?= $item["id"] ?>"><i class="fas fa-plus-circle"></i> Добавить шоколадный молд (+50 ₽)</label>
                                    </div>
                                    <div class="mold-selector" id="mold_selector_<?= $item["id"] ?>" style="display: <?= $has_mold ? 'block' : 'none' ?>;">
                                        <h5>Выберите форму молда:</h5>
                                        <div class="mold-options">
                                            <?php
                                            $molds = ['heart'=>'Сердце','star'=>'Звезда','flower'=>'Цветок','bear'=>'Медведь'];
                                            foreach ($molds as $value=>$label):
                                            ?>
                                            <div class="mold-option">
                                                <input type="radio" name="mold_type_<?= $item["id"] ?>" value="<?= $value ?>" id="<?= $value ?>_<?= $item["id"] ?>" <?= $mold_type==$value?'checked':'' ?> data-id="<?= $item["id"] ?>">
                                                <label for="<?= $value ?>_<?= $item["id"] ?>">
                                                    <img src="img/molds/<?= $value ?>.png" alt="<?= $label ?>" class="mold-img">
                                                    <span><?= $label ?></span>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="cart-item-controls">
                                    <button class="qty-btn minus" data-id="<?= $item["id"] ?>">−</button>
                                    <span class="cart-item-qty"><?= $item["qty"] ?></span>
                                    <button class="qty-btn plus" data-id="<?= $item["id"] ?>">+</button>
                                    <button class="cart-delete" data-id="<?= $item["id"] ?>"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="cart-item-right">
                                <div class="cart-item-price" id="price_<?= $item["id"] ?>" data-price="<?= $item_total ?>" data-qty="<?= $item["qty"] ?>">
                                    <?= number_format($item_total, 0, '.', ' ') ?> ₽
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-summary">
                    <h2 class="order-title">Ваш заказ</h2>

                    <div class="delivery-options">
                        <h3 class="delivery-title">Способ получения</h3>
                        <div class="delivery-option">
                            <input type="radio" name="delivery" value="pickup" id="pickup" checked>
                            <label for="pickup">Самовывоз (бесплатно)</label>
                        </div>
                        <div class="delivery-option">
                            <input type="radio" name="delivery" value="delivery" id="delivery">
                            <label for="delivery">Доставка (+350 ₽)</label>
                        </div>
                    </div>
                    
                    <!-- Поля для самовывоза (изначально скрыты) -->
                    <div id="pickupFields" class="delivery-section-fields">
                        <h3 class="delivery-title">Контактная информация для самовывоза</h3>
                        
                        <div class="form-group required">
                            <label for="customerName">Ваше имя *</label>
                            <input type="text" id="customerName" name="name" 
                                   value="<?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="customerPhone">Телефон *</label>
                            <input type="tel" id="customerPhone" name="phone" 
                                   placeholder="+7 (___) ___-__-__" 
                                   value="<?php echo isset($_SESSION['user']['phone']) ? htmlspecialchars($_SESSION['user']['phone']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="pickupDate">Дата самовывоза *</label>
                            <input type="date" id="pickupDate" name="pickup_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group required">
                            <label>Время самовывоза *</label>
                            <div id="pickupTimeSlots" class="time-slots">
                                <!-- Слоты времени будут сгенерированы JavaScript -->
                            </div>
                            <input type="hidden" id="selectedPickupTimeSlot" name="pickup_time">
                        </div>
                        
                        <div class="pickup-info">
                            <p><strong>Адрес самовывоза:</strong> г. Ижевск, ул. Ленина, д. 95</p>
                            <p><strong>Часы работы:</strong> ежедневно с 9:00 до 21:00</p>
                            <p><strong>Телефон:</strong> +7 (950) 834-50-20</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="orderComment">Комментарий к заказу</label>
                            <textarea id="orderComment" name="comment" placeholder="Особые пожелания, комментарии"></textarea>
                        </div>
                    </div>
                    
                    <!-- Поля для доставки (изначально скрыты) -->
                    <div id="deliveryFields" class="delivery-section-fields">
                        <h3 class="delivery-title">Контактная информация для доставки</h3>
                        
                        <div class="form-group required">
                            <label for="customerNameDelivery">Ваше имя *</label>
                            <input type="text" id="customerNameDelivery" name="delivery_name" 
                                   value="<?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="customerPhoneDelivery">Телефон *</label>
                            <input type="tel" id="customerPhoneDelivery" name="delivery_phone" 
                                   placeholder="+7 (___) ___-__-__" 
                                   value="<?php echo isset($_SESSION['user']['phone']) ? htmlspecialchars($_SESSION['user']['phone']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="deliveryAddress">Адрес доставки *</label>
                            <textarea id="deliveryAddress" name="address" placeholder="Улица, дом, квартира, подъезд, этаж" required></textarea>
                        </div>
                        
                        <div class="form-group required">
                            <label for="deliveryDate">Дата доставки *</label>
                            <input type="date" id="deliveryDate" name="delivery_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group required">
                            <label>Время доставки *</label>
                            <div id="deliveryTimeSlots" class="time-slots">
                                <!-- Слоты времени будут сгенерированы JavaScript -->
                            </div>
                            <input type="hidden" id="selectedDeliveryTimeSlot" name="delivery_time">
                        </div>
                        
                        <div class="form-group">
                            <label for="orderCommentDelivery">Комментарий к заказу</label>
                            <textarea id="orderCommentDelivery" name="delivery_comment" placeholder="Особые пожелания, комментарии"></textarea>
                        </div>
                        
                        <div class="delivery-info">
                            <p><strong>Стоимость доставки:</strong> 350 ₽</p>
                            <p><strong>Минимальный заказ для доставки:</strong> 1000 ₽</p>
                            <p><strong>Бесплатная доставка:</strong> от 5000 ₽</p>
                        </div>
                    </div>

                    <div class="order-details">
                        <div class="order-line">
                            <span>Товары:</span>
                            <span id="itemsTotal"><?= number_format($total_items_price, 0, '.', ' ') ?> ₽</span>
                        </div>
                        <div class="order-line">
                            <span>Дополнительные опции:</span>
                            <span id="addonsTotal"><?= number_format($total_addons_price, 0, '.', ' ') ?> ₽</span>
                        </div>
                        <div class="order-line">
                            <span>Доставка:</span>
                            <span id="deliveryCost">0 ₽</span>
                        </div>
                        <div class="order-line order-total">
                            <span>Итого:</span>
                            <span id="orderTotal"><?= number_format($total_items_price + $total_addons_price, 0, '.', ' ') ?> ₽</span>
                        </div>
                    </div>

                    <div class="order-note">
                        * Обязательные поля для заполнения
                    </div>

                    <button class="checkout-btn" id="checkoutBtn">Оформить заказ</button>
                    <p class="order-note" style="text-align: center; margin-top: 10px; font-size: 13px;">
                        Нажимая кнопку, вы соглашаетесь с <a href="#">условиями обработки персональных данных</a>
                    </p>
                </div>

            </div>
        <?php endif; ?>
    </div>
</section>

<div id="footer"></div>

<script>
$(document).ready(function() {
    // Загружаем header и footer
    $("#header").load("php/header.php"); 
    $("#footer").load("html/footer.html");

    // Массив фиксированных цен для ягод
    const BERRY_PRICES = {
        3: 565,
        4: 745,
        6: 1115,
        8: 1475,
        9: 1710,
        12: 2210,
        16: 2950,
        18: 3310,
        20: 3670,
        25: 4600,
        30: 5500
    };

    // Доступные количества ягод
    const AVAILABLE_BERRY_QTYS = [3, 4, 6, 8, 9, 12, 16, 18, 20, 25, 30];

    function generateTimeSlots(containerId, inputId, startHour, endHour, intervalMinutes=30) {
        const $container = $('#' + containerId);
        $container.empty();

        for(let h=startHour; h<endHour; h++){
            for(let m=0; m<60; m+=intervalMinutes){
                const time = ('0'+h).slice(-2) + ':' + ('0'+m).slice(-2);
                const slotId = containerId + '_' + time.replace(':','');
                const radio = `<div class="time-slot">
                    <input type="radio" name="${inputId}" id="${slotId}" value="${time}">
                    <label for="${slotId}">${time}</label>
                </div>`;
                $container.append(radio);
            }
        }

        // Выбор времени
        $container.find('input[type="radio"]').change(function(){
            $('#' + inputId).val($(this).val());
        });
    }

    // Слоты для самовывоза 10:00 - 20:00
    generateTimeSlots('pickupTimeSlots', 'selectedPickupTimeSlot', 10, 20, 30);
    // Слоты для доставки 10:00 - 20:00
    generateTimeSlots('deliveryTimeSlots', 'selectedDeliveryTimeSlot', 10, 20, 30);
// Функция для создания миниатюрного выпадающего блока времени
function generateCompactTimeDropdown(containerId, inputId, startHour, endHour, intervalMinutes=30) {
    const $container = $(`#${containerId}`);
    $container.empty();
    
    // Создаем структуру мини-дропдауна
    const $wrapper = $(`
        <div class="time-slots-wrapper">
            <button type="button" class="time-slots-trigger">
                <span class="placeholder">Выберите время</span>
                <span class="selected-time-text"></span>
                <span class="trigger-icon">▼</span>
            </button>
            <div class="time-slots-dropdown">
                <!-- Слоты времени будут добавлены здесь -->
            </div>
        </div>
    `);
    
    $container.append($wrapper);
    
    const $trigger = $wrapper.find('.time-slots-trigger');
    const $dropdown = $wrapper.find('.time-slots-dropdown');
    const $placeholder = $wrapper.find('.placeholder');
    const $selectedText = $wrapper.find('.selected-time-text');
    const $hiddenInput = $(`#${inputId}`);
    
    // Сначала скрываем выбранный текст
    $selectedText.hide();
    
    // Генерируем слоты времени
    for(let h = startHour; h < endHour; h++) {
        for(let m = 0; m < 60; m += intervalMinutes) {
            const time = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;
            const $slot = $(`
                <div class="time-slot" data-time="${time}">
                    ${time}
                </div>
            `);
            $dropdown.append($slot);
        }
    }
    
    // Обработчик выбора времени
    $dropdown.on('click', '.time-slot:not(.disabled)', function(e) {
        e.stopPropagation();
        const time = $(this).data('time');
        
        // Снимаем выделение со всех
        $dropdown.find('.time-slot').removeClass('selected');
        // Выделяем выбранный
        $(this).addClass('selected');
        
        // Обновляем тексты
        $placeholder.hide();
        $selectedText.text(time).show();
        $trigger.addClass('has-selected');
        $hiddenInput.val(time);
        
        // Закрываем дропдаун
        closeDropdown();
    });
    
    // Открытие/закрытие дропдауна
    $trigger.on('click', function(e) {
        e.stopPropagation();
        if ($dropdown.hasClass('active')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });
    
    // Закрытие по клику вне
    $(document).on('click', function(e) {
        if (!$wrapper.is(e.target) && $wrapper.has(e.target).length === 0) {
            closeDropdown();
        }
    });
    
    function openDropdown() {
        $dropdown.addClass('active');
        $trigger.addClass('active');
    }
    
    function closeDropdown() {
        $dropdown.removeClass('active');
        $trigger.removeClass('active');
    }
    
    // ESC для закрытия
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $dropdown.hasClass('active')) {
            closeDropdown();
        }
    });
    
    // Если уже есть выбранное время, показываем его
    if ($hiddenInput.val()) {
        const selectedTime = $hiddenInput.val();
        $placeholder.hide();
        $selectedText.text(selectedTime).show();
        $trigger.addClass('has-selected');
        $dropdown.find(`.time-slot[data-time="${selectedTime}"]`).addClass('selected');
    }
}

// Заменяем старые вызовы на новые:
generateCompactTimeDropdown('pickupTimeSlots', 'selectedPickupTimeSlot', 10, 20, 30);
generateCompactTimeDropdown('deliveryTimeSlots', 'selectedDeliveryTimeSlot', 10, 20, 30);
    // -------------------------------
    // Обновление цены одного товара
    function updateCartItemPrice($item) {
        const itemId = $item.data('id');
        const qty = parseInt($item.find('.cart-item-qty').text()) || 1;
        const isBouquet = $item.hasClass('bouquet');
        
        let totalPrice = 0;

        if (isBouquet) {
            // Букет — цена фиксированная
            const basePrice = parseFloat($item.data('base-price')) || 0;
            let addons = 0;
            $item.find('.addon-checkbox:checked').each(function(){ 
                addons += parseFloat($(this).data('price')) || 0; 
            });
            totalPrice = (basePrice + addons) * qty;
        } else {
            // Набор — цена из выбранного количества ягод
            const $select = $item.find('.berry-qty-select');
            const berryQty = parseInt($select.val()) || 9;
            const selectedOption = $select.find('option:selected');
            const berryPrice = parseFloat(selectedOption.data('price')) || BERRY_PRICES[berryQty] || 1710;
            
            let addons = 0;
            $item.find('.addon-checkbox:checked').each(function(){ 
                addons += parseFloat($(this).data('price')) || 0; 
            });
            
            totalPrice = (berryPrice + addons) * qty;
        }

        $('#price_' + itemId)
            .text(totalPrice.toLocaleString('ru-RU') + ' ₽')
            .data('price', totalPrice);
    }

    // -------------------------------
    // Обновление итогов корзины
    function updateCartTotals() {
        let itemsTotal = 0, addonsTotal = 0;
        
        $('.cart-item').each(function(){
            const $item = $(this);
            updateCartItemPrice($item);
            const price = parseFloat($item.find('.cart-item-price').data('price')) || 0;
            itemsTotal += price;

            $item.find('.addon-checkbox:checked').each(function(){
                const addonPrice = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($item.find('.cart-item-qty').text()) || 1;
                addonsTotal += addonPrice * qty;
            });
        });

        const deliveryCost = $('input[name="delivery"]:checked').val() === 'delivery' ? 350 : 0;
        const orderTotal = itemsTotal + deliveryCost;

        $('#itemsTotal').text(itemsTotal.toLocaleString('ru-RU') + ' ₽');
        $('#addonsTotal').text(addonsTotal.toLocaleString('ru-RU') + ' ₽');
        $('#deliveryCost').text(deliveryCost.toLocaleString('ru-RU') + ' ₽');
        $('#orderTotal').text(orderTotal.toLocaleString('ru-RU') + ' ₽');
    }

    // -------------------------------
    // Изменение количества товара
    $(document).on('click', '.qty-btn.plus', function(){
        const $item = $('.cart-item[data-id="' + $(this).data('id') + '"]');
        $item.find('.cart-item-qty').text(parseInt($item.find('.cart-item-qty').text()) + 1);
        updateCartTotals();
    });

    $(document).on('click', '.qty-btn.minus', function(){
        const $item = $('.cart-item[data-id="' + $(this).data('id') + '"]');
        const $qty = $item.find('.cart-item-qty');
        let qty = parseInt($qty.text());
        if (qty > 1) {
            $qty.text(qty - 1);
            updateCartTotals();
        } else {
            if (confirm('Удалить товар из корзины?')) {
                deleteCartItem($item.data('id'));
            }
        }
    });

    // -------------------------------
    // Удаление товара
    function deleteCartItem(id) {
        $.post('php/remove_from_cart.php', { id: id }, function(res) {
            if (res.status === 'success') {
                $('.cart-item[data-id="' + id + '"]').fadeOut(200, function() { 
                    $(this).remove(); 
                    updateCartTotals(); 
                    
                    // Если корзина пуста, перезагружаем страницу
                    if ($('.cart-item').length === 0) {
                        setTimeout(() => location.reload(), 300);
                    }
                });
            } else {
                alert(res.message || 'Ошибка при удалении товара');
            }
        }, 'json');
    }

    $(document).on('click', '.cart-delete', function() {
        const id = $(this).data('id');
        if (confirm('Удалить товар из корзины?')) {
            deleteCartItem(id);
        }
    });

    // -------------------------------
    // Изменения допов, ягод, способа доставки
    $(document).on('change', '.addon-checkbox, .berry-qty-select, input[name="delivery"]', function() {
        const id = $(this).closest('.cart-item').data('id');
        if (id) {
            // Сохраняем изменения в сессии
            saveCartChanges(id);
        }
        updateCartTotals();
    });

   // Показать/скрыть выбор молда — ТОЛЬКО при изменении чекбокса mold
$(document).on('change', '.addon-checkbox[data-addon="mold"]', function() {
    const id = $(this).data('id');
    const isChecked = $(this).is(':checked');

    if (isChecked) {
        $('#mold_selector_' + id).slideDown(200);
    } else {
        $('#mold_selector_' + id).slideUp(200);
    }

    saveCartChanges(id);
});

// Изменение типа молда – БЕЗ управления видимостью блока
$(document).on('change', 'input[name^="mold_type_"]', function(e) {
    e.stopPropagation();   // чтобы не всплывало событие
    const id = $(this).data('id');
    saveCartChanges(id);
});


    // -------------------------------
    // Сохранение изменений в корзине
    function saveCartChanges(id) {
        const $item = $('.cart-item[data-id="' + id + '"]');
        
        if ($item.length === 0) return;
        
        const isBouquet = $item.hasClass('bouquet');
        
        if (isBouquet) {
            // Для букетов сохраняем только дополнения
            const addons = {};
            $item.find('.addon-checkbox').each(function() {
                addons[$(this).data('addon')] = $(this).is(':checked');
            });
            
            $.post('php/update_cart_details.php', {
                id: id,
                addons: JSON.stringify(addons)
            });
        } else {
            // Для наборов сохраняем количество ягод и дополнения
            const berryQty = parseInt($item.find('.berry-qty-select').val()) || 9;
            const addons = {};
            $item.find('.addon-checkbox').each(function() {
                addons[$(this).data('addon')] = $(this).is(':checked');
            });
            const moldType = $item.find('input[name^="mold_type_' + id + '"]:checked').val() || 'heart';
            
            $.post('php/update_cart_details.php', {
                id: id,
                berry_qty: berryQty,
                addons: JSON.stringify(addons),
                mold_type: moldType
            });
        }
    }

    // -------------------------------
    // Переключение полей доставки/самовывоза
    function toggleDeliveryFields() {
        const method = $('input[name="delivery"]:checked').val();

        if (method === 'delivery') {
            $('#deliveryFields').show().find('input, textarea').prop('readonly', false).prop('required', true);
            $('#pickupFields').hide().find('input, textarea').prop('readonly', true).prop('required', false);
        } else {
            $('#pickupFields').show().find('input, textarea').prop('readonly', false).prop('required', true);
            $('#deliveryFields').hide().find('input, textarea').prop('readonly', true).prop('required', false);
        }
    }
    
    $('input[name="delivery"]').on('change', toggleDeliveryFields);
    toggleDeliveryFields(); // Инициализация при загрузке

    // Инициализация
    updateCartTotals();
    
    // ОСТАВЛЯЕМ ТОЛЬКО ОБРАБОТЧИК ИЗ cart_actions.js
    // УДАЛЯЕМ обработчик оформления заказа отсюда, он будет в cart_actions.js
});
</script>
<script src="js/cart_actions.js"></script>
<script>
// Дополнительный JS для улучшения адаптивности
$(document).ready(function() {
    // Адаптивное поведение для выбора времени
    function adaptTimeSlots() {
        if ($(window).width() <= 480) {
            // На мобилках уменьшаем количество колонок в mold-options
            $('.mold-options').css('grid-template-columns', 'repeat(2, 1fr)');
            
            // Уменьшаем размер молдов на мобилках
            $('.mold-img').css({
                'width': '35px',
                'height': '35px'
            });
        } else {
            // На десктопах возвращаем стандартный вид
            $('.mold-options').css('grid-template-columns', 'repeat(auto-fill, minmax(100px, 1fr))');
            $('.mold-img').css({
                'width': '50px',
                'height': '50px'
            });
        }
        
        // Адаптация картинок товаров
        if ($(window).width() <= 768) {
            $('.cart-item-img').css({
                'width': 'auto',
                'max-width': '250px',
                'height': 'auto'
            });
        }
    }
    
    // Вызываем при загрузке и изменении размера окна
    adaptTimeSlots();
    $(window).resize(adaptTimeSlots);
    
    // Улучшение UX на мобильных устройствах
    if ('ontouchstart' in window) {
        $('.qty-btn, .cart-delete').css('min-height', '44px');
        $('.addon-checkbox').css({
            'min-width': '44px',
            'min-height': '44px'
        });
    }
    
    // Плавная прокрутка к ошибкам на мобилках
    $(document).on('click', '#checkoutBtn', function() {
        if ($(window).width() <= 768) {
            const $error = $('#errorMessage:visible');
            if ($error.length) {
                $('html, body').animate({
                    scrollTop: $error.offset().top - 100
                }, 500);
            }
        }
    });
});
</script>
</body>
</html>