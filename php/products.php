<?php
require_once "config.php";
$sql_sets = "
    SELECT * FROM products
    WHERE category != 'bouquet'
    ORDER BY RAND()
    LIMIT 3
";

$result_sets = $conn->query($sql_sets);

$sql_bouquets = "
    SELECT * FROM products
    WHERE category = 'bouquet'
    ORDER BY RAND()
    LIMIT 3
";

$result_bouquets = $conn->query($sql_bouquets);

$products = [];

if ($result_sets && $result_sets->num_rows > 0) {
    while ($product = $result_sets->fetch_assoc()) {
        $products[] = $product;
    }
}
if ($result_bouquets && $result_bouquets->num_rows > 0) {
    while ($product = $result_bouquets->fetch_assoc()) {
        $products[] = $product;
    }
}
if (!empty($products)) {
    foreach ($products as $product) {
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?= htmlspecialchars($product['img']) ?>" loading="lazy" alt="<?= htmlspecialchars($product['alt']) ?>">
            </div>
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                
                <?php if (!empty($product['description'])): ?>
                <div class="description-container">
                    <div class="product-description" id="desc_main_<?= $product['id'] ?>">
                        <?= htmlspecialchars($product['description']) ?>
                    </div>
                    <?php if (strlen($product['description']) > 40): ?>
                    <button class="read-more-btn" data-target="desc_main_<?= $product['id'] ?>">
                        Подробнее <i class="fas fa-chevron-down"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="product-price"><?= htmlspecialchars($product['price']) ?> ₽</div>
                <button class="product-btn add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>Товары не найдены</p>";
}
?>
<style>
    /* Стили для описания товара */
.product-description {
    font-size: 14px;
    color: #666;
    margin: 10px 0;
    line-height: 1.5;
    max-height: 63px; /* 3 строки (14px * 1.5 * 3) */
    overflow: hidden;
    position: relative;
    transition: max-height 0.3s ease;
    text-align: left;
}

/* Если описание короткое, убираем градиент */
.product-description:not(.expanded)::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 30px;
    background: linear-gradient(to bottom, transparent, #fff);
    pointer-events: none;
}

/* Расширенное описание */
.product-description.expanded {
    max-height: 500px; /* Достаточно для длинного текста */
    overflow-y: auto;
}

.product-description.expanded::after {
    display: none;
}

/* Кнопка развернуть/свернуть */
.read-more-btn {
    display: inline-block;
    margin-top: 5px;
    font-size: 13px;
    color: #ff3366;
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 5px;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s ease;
}

.read-more-btn:hover {
    color: #e60050;
    text-decoration: underline;
}

.read-more-btn i {
    font-size: 10px;
    margin-left: 3px;
    transition: transform 0.3s ease;
}

.read-more-btn.expanded i {
    transform: rotate(180deg);
}

/* Стили для контейнера описания */
.description-container {
    margin: 8px 0;
    position: relative;
}

/* Для страниц set.php и bouquets.php - обновленная карточка товара */
.product-info {
    padding: 15px;
    text-align: center;
    position: relative;
    min-height: 180px;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 16px;
    color: #7a1f3d;
    margin-bottom: 8px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-price {
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 18px;
    color: #333;
}

/* Адаптивность */
@media (max-width: 768px) {
    .product-description {
        font-size: 13px;
        max-height: 58px; /* 3 строки (13px * 1.5 * 3) */
    }
    
    .product-title {
        font-size: 15px;
        min-height: 36px;
    }
}

/* Для главной страницы */
.top-sets .product-description {
    font-size: 13px;
    max-height: 39px; /* 2 строки (13px * 1.5 * 2) */
}

/* Для корзины - более подробное отображение */
.cart-item .product-description {
    max-height: none;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    margin: 5px 0;
}

.cart-item .product-description::after {
    display: none;
}

.cart-item .read-more-btn {
    display: none;
}

</style>