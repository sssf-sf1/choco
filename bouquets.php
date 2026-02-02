<?php
require 'php/config.php';
$where = [];
$where[] = "category = 'bouquet'";

if (!empty($_GET['type'])) {
    $types = "'" . implode("','", $_GET['type']) . "'";
    $where[] = "bouquet_type IN ($types)";
}

if (!empty($_GET['price_min'])) {
    $where[] = "price >= " . (int)$_GET['price_min'];
}

if (!empty($_GET['price_max'])) {
    $where[] = "price <= " . (int)$_GET['price_max'];
}

if (!empty($_GET['size'])) {
    $sizes = [];
    foreach ($_GET['size'] as $size) {
        if ($size === 'small') $sizes[] = "berries < 15";
        if ($size === 'medium') $sizes[] = "berries BETWEEN 15 AND 25";
        if ($size === 'large') $sizes[] = "berries > 25";
    }
    if ($sizes) {
        $where[] = '(' . implode(' OR ', $sizes) . ')';
    }
}

if (!empty($_GET['chocolate'])) {
    $choco = "'" . implode("','", $_GET['chocolate']) . "'";
    $where[] = "chocolate IN ($choco)";
}
$order = "";
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] === 'price-asc') $order = " ORDER BY price ASC";
    if ($_GET['sort'] === 'price-desc') $order = " ORDER BY price DESC";
}

$sql = "SELECT * FROM products WHERE " . implode(" AND ", $where) . $order;
$result = $conn->query($sql);
$totalProducts = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Букеты — Chocoberry Place</title>
     <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="shortcut icon" href="favicon.ico">
     <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/set.css">
    <script src="./js/jquery3.7.1.js"></script>
</head>
<body>

<div id="header"></div>
<div class="container">
    <div class="breadcrumbs">
        <a href="index.php">Главная</a> <span>></span> <a href="#">Каталог</a> <span>></span> <span>Букеты</span>
    </div>
</div>
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Букеты из клубники в шоколаде</h1>
        <p class="page-subtitle">Изысканные композиции из свежей клубники в шоколаде</p>
    </div>
</section>
<section class="catalog">
    <div class="container">

        <div class="catalog-content">

            <!-- Фильтры -->
            <aside class="filters horizontal-filters">
                <form id="catalog-filters" method="GET">
                    <div class="filters-header">
                        <h2 class="filters-title">Фильтры</h2>
                        <div class="filter-actions">
                            <button type="submit" class="apply-filters">Применить фильтры</button>
                            <a href="?" class="reset-filters">Сбросить</a>
                        </div>
                    </div>

                    <div class="filter-sections">

                        <div class="filter-group">
                            <h3 class="filter-title">Тип букета</h3>
                            <label><input type="checkbox" name="type[]" value="flowers"> С цветами</label>
                            <label><input type="checkbox" name="type[]" value="no-flowers"> Без цветов</label>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Цена, ₽</h3>
                            <input type="number" name="price_min" placeholder="От" value="<?= $_GET['price_min'] ?? '' ?>">
                            <input type="number" name="price_max" placeholder="До" value="<?= $_GET['price_max'] ?? '' ?>">
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Размер</h3>
                            <label><input type="checkbox" name="size[]" value="small"> Маленький</label>
                            <label><input type="checkbox" name="size[]" value="medium"> Средний</label>
                            <label><input type="checkbox" name="size[]" value="large"> Большой</label>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Шоколад</h3>
                            <label><input type="checkbox" name="chocolate[]" value="milk"> Молочный</label>
                            <label><input type="checkbox" name="chocolate[]" value="dark"> Тёмный</label>
                            <label><input type="checkbox" name="chocolate[]" value="white"> Белый</label>
                        </div>

                        <div class="sort-options">
                            <h3 class="filter-title">Сортировка</h3>
                            <select name="sort" onchange="location='?sort='+this.value">
                                <option value="">Сортировать</option>
                                <option value="price-asc">По возрастанию цены</option>
                                <option value="price-desc">По убыванию цены</option>
                            </select>
                        </div>

                    </div>
                </form>
            </aside>

            <!-- Секция товаров -->
            <main class="products-section">
                <div class="products-header">
                    <div class="products-count">Найдено <?= $totalProducts ?> товаров</div>
                </div>

                <div class="products-grid">
                    <?php if($totalProducts > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="product-card">
                                <div class="product-image" style="background-image: url('<?= $row['img'] ?>')"></div>
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    
                                    <?php if (!empty($row['description'])): ?>
                                    <div class="description-container">
                                        <div class="product-description" id="desc_<?php echo $row['id']; ?>">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </div>
                                        <?php if (strlen($row['description']) > 60): ?>
                                        <button class="read-more-btn" data-target="desc_<?php echo $row['id']; ?>">
                                            Подробнее <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-price">
                                        <?php echo number_format($row['price'], 0, '.', ' '); ?> ₽
                                    </div>
                                    
                                    <button class="product-btn add-to-cart"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                            data-price="<?php echo $row['price']; ?>"
                                            data-img="<?php echo $row['img']; ?>">
                                        В корзину
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Нет букетов по выбранным фильтрам.</p>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>
</section>

<!-- Модальное окно авторизации -->
<div id="authModal" class="modal-overlay" style="display: none;">
    <div class="auth-modal">
        <button id="closeAuthModal" class="close-modal">&times;</button>
        <div class="auth-tabs">
            <div class="auth-tab active" data-tab="login">Вход</div>
            <div class="auth-tab" data-tab="register">Регистрация</div>
        </div>
        
        <!-- Форма входа -->
        <form class="auth-form active" data-form="login" method="POST" action="php/login.php">
            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email" placeholder="Введите email" required>
            </div>
            <div class="form-group">
                <label for="loginPassword">Пароль</label>
                <div style="position: relative;">
                    <input type="password" id="loginPassword" name="password" placeholder="Введите пароль" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="auth-submit">Войти</button>
            <div class="auth-links">
                <span class="auth-link" data-switch="register">Нет аккаунта? Зарегистрироваться</span>
            </div>
        </form>
        
        <!-- Форма регистрации -->
        <form class="auth-form" data-form="register" method="POST" action="php/register.php">
            <div class="form-group">
                <label for="registerName">Имя</label>
                <input type="text" id="registerName" name="name" placeholder="Введите имя" required>
            </div>
            <div class="form-group">
                <label for="registerSurname">Фамилия</label>
                <input type="text" id="registerSurname" name="surname" placeholder="Введите фамилию" required>
            </div>
            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input type="email" id="registerEmail" name="email" placeholder="Введите email" required>
            </div>
            <div class="form-group">
                <label for="registerPhone">Номер телефона</label>
                <input type="text" id="registerPhone" name="phone" class="mask-phone" placeholder="+7 (___) ___-__-__" required>
            </div>
            <div class="form-group">
                <label for="registerPassword">Пароль</label>
                <div style="position: relative;">
                    <input type="password" id="registerPassword" name="password" placeholder="Введите пароль" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="auth-submit">Зарегистрироваться</button>
            <div class="auth-links">
                <span class="auth-link" data-switch="login">Уже есть аккаунт? Войти</span>
            </div>
        </form>
    </div>
</div>

<div id="footer"></div>

<script>
    $(function() {
        $("#header").load("php/header.php", function() {
            // После загрузки header инициализируем кнопки авторизации
            setTimeout(initAuthButtons, 100);
        });
        $("#footer").load("html/footer.html");
        
        // Загружаем маску для телефона
        if (typeof $.fn.mask !== 'undefined') {
            $('.mask-phone').mask('+7 (999) 999-99-99');
        }
        
        // Инициализация модального окна
        initModal();
        
        // Инициализация кнопок авторизации
        initAuthButtons();
        
        // Инициализация кнопок "Подробнее"
        initReadMoreButtons();
        
        // Инициализация обработчика корзины
        initCartHandler();
    });
    
    function initAuthButtons() {
        // Обработчик для кнопок "Войти" и "Регистрация" в header
        $(document).on('click', '.auth-btn, .open-modal', function(e) {
            e.preventDefault();
            var tab = $(this).data('tab');
            
            // Показываем модальное окно
            $('#authModal').fadeIn(300).css({
                'display': 'flex'
            });
            $('body').css('overflow', 'hidden');
            
            // Переключаем на нужную вкладку
            if (tab) {
                $('.auth-tab').removeClass('active');
                $('.auth-tab[data-tab="' + tab + '"]').addClass('active');
                
                $('.auth-form').removeClass('active');
                $('.auth-form[data-form="' + tab + '"]').addClass('active');
            }
            
            // Фокус на поле email в зависимости от вкладки
            setTimeout(function() {
                if (tab === 'login') {
                    $('#loginEmail').focus();
                } else if (tab === 'register') {
                    $('#registerName').focus();
                }
            }, 300);
        });
    }
    
    function initModal() {
        // Закрытие модального окна
        $(document).on('click', '.close-modal, .modal-overlay', function(e) {
            if ($(e.target).hasClass('modal-overlay') || $(e.target).hasClass('close-modal')) {
                $('.modal-overlay').fadeOut(300);
                $('body').css('overflow', 'auto');
            }
        });

        // Переключение между вкладками
        $(document).on('click', '.auth-tab', function() {
            var tab = $(this).data('tab');
            
            $('.auth-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.auth-form').removeClass('active');
            $('.auth-form[data-form="' + tab + '"]').addClass('active');
        });

        // Переключение между формами по ссылкам
        $(document).on('click', '.auth-link[data-switch]', function(e) {
            e.preventDefault();
            var tab = $(this).data('switch');
            
            $('.auth-tab').removeClass('active');
            $('.auth-tab[data-tab="' + tab + '"]').addClass('active');
            
            $('.auth-form').removeClass('active');
            $('.auth-form[data-form="' + tab + '"]').addClass('active');
        });

        // Показать/скрыть пароль
        $(document).on('click', '.toggle-password', function() {
            var input = $(this).parent().find('input');
            var icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Закрытие по ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.modal-overlay').fadeOut(300);
                $('body').css('overflow', 'auto');
            }
        });
    }
    
    function initReadMoreButtons() {
        // Обработчик для кнопок "Подробнее"
        $(document).on('click', '.read-more-btn', function() {
            const targetId = $(this).data('target');
            const description = $('#' + targetId);
            const button = $(this);
            
            if (description.hasClass('expanded')) {
                // Сворачиваем
                description.removeClass('expanded');
                button.html('Подробнее <i class="fas fa-chevron-down"></i>');
                button.removeClass('expanded');
            } else {
                // Разворачиваем
                description.addClass('expanded');
                button.html('Скрыть <i class="fas fa-chevron-up"></i>');
                button.addClass('expanded');
            }
        });
        
        // Автоматически скрываем кнопку "Подробнее", если текст короткий
        setTimeout(function() {
            $('.product-description').each(function() {
                const description = $(this);
                const container = description.parent();
                const button = container.find('.read-more-btn');
                
                // Проверяем высоту текста
                if (description[0].scrollHeight <= description[0].clientHeight + 5) {
                    button.hide();
                }
            });
        }, 500);
    }
    
    function initCartHandler() {
        // Обработчик для кнопок "В корзину" на странице букетов
        $(document).on("click", ".add-to-cart", function() {
            let productId = $(this).data("id");
            let button = $(this);

            // Сначала проверяем авторизацию
            $.ajax({
                url: "php/check_auth.php",
                type: "GET",
                dataType: "json",
                success: function(authRes) {
                    if (authRes.authenticated) {
                        // Если пользователь авторизован, добавляем товар
                        $.ajax({
                            url: "php/add_to_cart.php",
                            type: "POST",
                            dataType: "json",
                            data: { id: productId },
                            success: function(res) {
                                if (res.status === "success") {
                                    alert("Товар добавлен в корзину!");
                                    // Обновляем счетчик в корзине
                                    if ($('#cartCount').length) {
                                        $('#cartCount').text(res.cartCount);
                                    }
                                } else {
                                    alert(res.message || "Ошибка при добавлении товара");
                                }
                            },
                            error: function() {
                                alert("Ошибка соединения с сервером");
                            }
                        });
                    } else {
                        // Если не авторизован, показываем уведомление и открываем окно входа
                        alert("Для добавления товара в корзину необходимо войти или зарегистрироваться");
                        
                        // Показываем модальное окно
                        $('#authModal').fadeIn(300).css({
                            'display': 'flex'
                        });
                        $('body').css('overflow', 'hidden');
                        
                        // Показываем таб с логином
                        $('.auth-tab').removeClass('active');
                        $('.auth-tab[data-tab="login"]').addClass('active');
                        $('.auth-form').removeClass('active');
                        $('.auth-form[data-form="login"]').addClass('active');
                        
                        // Фокус на поле email
                        setTimeout(function() {
                            $('#loginEmail').focus();
                        }, 300);
                    }
                },
                error: function() {
                    alert("Ошибка проверки авторизации");
                }
            });
        });
    }
</script>

<style>
  /* Розовый фон только для фильтров */
.filters.horizontal-filters {
    padding: 30px;
    background-color: #fde4ec;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(122, 31, 61, 0.1);
    margin-bottom: 30px;
}

/* Остальной стиль фильтров */
/* Базовые стили фильтров */
.filter-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.apply-filters {
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff3366, #ff6699);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 51, 102, 0.2);
    flex: 1;
    min-width: 140px;
}

.apply-filters:hover {
    background: linear-gradient(135deg, #e60050, #ff3366);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(255, 51, 102, 0.3);
}

.apply-filters:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(255, 51, 102, 0.2);
}

.reset-filters {
    padding: 12px 24px;
    background: #f8f9fa;
    color: #7a1f3d;
    border: 2px solid #ffd1dc;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 140px;
}

.reset-filters:hover {
    background: #fff5f7;
    border-color: #ff3366;
    color: #ff3366;
    transform: translateY(-2px);
}

/* Заголовок фильтров */
.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

/* Контейнер фильтров */
.filter-sections {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Группа фильтров и сортировки */
.filter-group,
.sort-options {
    flex: 1 1 250px;
    background-color: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(122, 31, 61, 0.05);
    transition: all 0.3s ease;
}

.filter-group:hover,
.sort-options:hover {
    box-shadow: 0 8px 20px rgba(122, 31, 61, 0.1);
    transform: translateY(-2px);
}

/* Адаптивность */

/* 1024px - 769px (Небольшие ноутбуки и планшеты) */
@media (max-width: 1024px) {
    .filter-sections {
        gap: 15px;
    }
    
    .filter-group,
    .sort-options {
        flex: 1 1 220px;
        padding: 18px;
    }
}

/* 768px - 577px (Планшеты вертикально) */
@media (max-width: 768px) {
    .filters-header {
        margin-bottom: 15px;
    }
    
    .filter-sections {
        gap: 15px;
        flex-direction: column;
    }
    
    .filter-group,
    .sort-options {
        flex: 1 1 auto;
        width: 100%;
        padding: 18px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 12px 20px;
        font-size: 15px;
        min-width: 120px;
    }
}

/* 576px - 426px (Большие телефоны) */
@media (max-width: 576px) {
    .filters-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    
    .filter-sections {
        gap: 12px;
    }
    
    .filter-group,
    .sort-options {
        padding: 16px;
        border-radius: 12px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 12px 16px;
        font-size: 15px;
        min-width: 110px;
        border-radius: 20px;
    }
    
    .filter-actions {
        gap: 12px;
        margin-top: 15px;
    }
}

/* 425px - 376px (Средние телефоны) */
@media (max-width: 425px) {
    .filters-header {
        margin-bottom: 12px;
    }
    
    .filter-sections {
        gap: 10px;
    }
    
    .filter-group,
    .sort-options {
        padding: 14px;
        border-radius: 10px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 10px 14px;
        font-size: 14px;
        min-width: 100px;
        border-radius: 18px;
    }
    
    .filter-actions {
        gap: 10px;
        margin-top: 12px;
        flex-direction: column;
    }
    
    .apply-filters,
    .reset-filters {
        width: 100%;
        min-width: 0;
    }
}

/* 375px - 321px (Маленькие телефоны) */
@media (max-width: 375px) {
    .filters-header {
        gap: 10px;
    }
    
    .filter-group,
    .sort-options {
        padding: 12px;
        border-radius: 8px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 16px;
    }
    
    .filter-actions {
        gap: 8px;
        margin-top: 10px;
    }
}

/* 320px и меньше (Мини телефоны) */
@media (max-width: 320px) {
    .filters-header {
        margin-bottom: 8px;
    }
    
    .filter-sections {
        gap: 8px;
    }
    
    .filter-group,
    .sort-options {
        padding: 10px;
        border-radius: 6px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 8px 10px;
        font-size: 12px;
        border-radius: 14px;
    }
    
    .filter-actions {
        gap: 6px;
        margin-top: 8px;
    }
}

/* Дополнительные стили для содержимого фильтров (если нужно адаптировать) */
.filter-group h3,
.sort-options h3 {
    font-size: 18px;
    color: #7a1f3d;
    margin-bottom: 15px;
    font-weight: 600;
}

/* Адаптивность заголовков фильтров */
@media (max-width: 768px) {
    .filter-group h3,
    .sort-options h3 {
        font-size: 16px;
        margin-bottom: 12px;
    }
}

@media (max-width: 576px) {
    .filter-group h3,
    .sort-options h3 {
        font-size: 15px;
        margin-bottom: 10px;
    }
}

@media (max-width: 425px) {
    .filter-group h3,
    .sort-options h3 {
        font-size: 14px;
        margin-bottom: 8px;
    }
}

@media (max-width: 375px) {
    .filter-group h3,
    .sort-options h3 {
        font-size: 13px;
        margin-bottom: 6px;
    }
}

/* Адаптивность для чекбоксов/радиокнопок внутри фильтров */
.filter-option {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-option input[type="checkbox"],
.filter-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #ff3366;
}

.filter-option label {
    font-size: 15px;
    color: #333;
    cursor: pointer;
}

@media (max-width: 576px) {
    .filter-option {
        margin-bottom: 8px;
        gap: 6px;
    }
    
    .filter-option input[type="checkbox"],
    .filter-option input[type="radio"] {
        width: 16px;
        height: 16px;
    }
    
    .filter-option label {
        font-size: 14px;
    }
}

@media (max-width: 375px) {
    .filter-option {
        margin-bottom: 6px;
        gap: 5px;
    }
    
    .filter-option input[type="checkbox"],
    .filter-option input[type="radio"] {
        width: 14px;
        height: 14px;
    }
    
    .filter-option label {
        font-size: 13px;
    }
}

/* Адаптивность для выпадающих списков */
.filter-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ffd1dc;
    border-radius: 8px;
    font-size: 15px;
    color: #333;
    background: white;
    margin-top: 8px;
    cursor: pointer;
}

@media (max-width: 576px) {
    .filter-group select {
        padding: 8px 10px;
        font-size: 14px;
        border-radius: 6px;
    }
}

@media (max-width: 375px) {
    .filter-group select {
        padding: 6px 8px;
        font-size: 13px;
        border-radius: 4px;
    }
}

/* Плавные переходы для лучшего UX */
.filter-group,
.sort-options,
.apply-filters,
.reset-filters,
.filter-option input,
.filter-group select {
    transition: all 0.2s ease;
}

/* Оптимизация для очень узких экранов */
@media (max-width: 280px) {
    .filter-group,
    .sort-options {
        padding: 8px;
        border-radius: 4px;
    }
    
    .apply-filters,
    .reset-filters {
        padding: 6px 8px;
        font-size: 11px;
        border-radius: 12px;
    }
    
    .filter-group h3,
    .sort-options h3 {
        font-size: 12px;
    }
    
    .filter-option label {
        font-size: 12px;
    }
    
    .filter-option input[type="checkbox"],
    .filter-option input[type="radio"] {
        width: 12px;
        height: 12px;
    }
}
.filter-group,
.sort-options {
    flex: 1 1 200px;
    background-color: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(122, 31, 61, 0.05);
}

.filter-group label,
.sort-options label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
    color: #333;
}

.filter-group input[type="checkbox"] {
    margin-right: 10px;
    accent-color: #ff3366;
    cursor: pointer;
}

.filter-group input[type="number"] {
    width: calc(50% - 10px);
    padding: 8px 10px;
    margin-right: 10px;
    margin-top: 5px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

.filter-group input[type="number"]:last-child {
    margin-right: 0;
}

.filter-group select,
.sort-options select {
    width: 100%;
    padding: 8px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-top: 5px;
    cursor: pointer;
    background-color: #fff;
    color: #333;
    font-weight: 500;
}

/* Товары */
.products-section {
    margin-top: 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
}

.product-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(122, 31, 61, 0.05);
    transition: transform 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    width: 100%;
    height: 200px;
    background-size: cover;
    background-position: center;
}

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

.product-btn {
    background-color: #ff3366;
    color: #fff;
    border: none;
    border-radius: 25px;
    padding: 8px 20px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.product-btn:hover {
    background-color: #e60050;
}

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

/* Модальное окно */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.auth-modal {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 400px;
    width: 90%;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.close-modal {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

.auth-tabs {
    display: flex;
    margin-bottom: 20px;
    border-bottom: 2px solid #eee;
}

.auth-tab {
    padding: 10px 20px;
    cursor: pointer;
    font-weight: bold;
    color: #666;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.auth-tab.active {
    color: #ff3366;
    border-bottom-color: #ff3366;
}

.auth-form {
    display: none;
}

.auth-form.active {
    display: block;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
}

.auth-submit {
    width: 100%;
    padding: 12px;
    background-color: #ff3366;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.auth-submit:hover {
    background-color: #e60050;
}

.auth-links {
    margin-top: 15px;
    text-align: center;
}

.auth-link {
    color: #ff3366;
    cursor: pointer;
    text-decoration: underline;
}

.auth-link:hover {
    color: #e60050;
}
</style>

</body>
</html>