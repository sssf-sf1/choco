<?php
session_start();
require_once "php/config.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клубника в шоколаде - Chocoberry Place</title>
    <meta name="description" content="Клубника в шоколаде | Ижевск Изготовим и доставим в этот же день Дарим незабываемые эмоции вашим близким Заказать: +79508335025💫"</meta>
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <link rel="stylesheet" type="text/css" href="./css/grid.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script type="text/javascript" src="./js/jquery3.7.1.js"></script>
    <script type="text/javascript" src="./js/jquery.maskedinput.min.js"></script>
   
</head>
<body>
    <div id="header"></div>
    <div id="authModal" class="modal-overlay">
        <div class="auth-modal">
            <button id="closeAuthModal" class="close-modal">&times;</button>
            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="login">Вход</div>
                <div class="auth-tab" data-tab="register">Регистрация</div>
            </div>
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
                <div class="form-group" style="position: relative;">
                    <label for="registerPassword">Пароль</label>
                    <input type="password" id="registerPassword" name="password" placeholder="Введите пароль" required>
                    <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                </div>
                <button type="submit" class="auth-submit">Зарегистрироваться</button>
                <div class="auth-links">
                    <span class="auth-link" data-switch="login">Уже есть аккаунт? Войти</span>
                </div>
            </form>

            <form class="auth-form active" data-form="login" method="POST" action="php/login.php">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" placeholder="Введите email" required>
                </div>
                <div class="form-group" style="position: relative;">
                    <label for="loginPassword">Пароль</label>
                    <input type="password" id="loginPassword" name="password" placeholder="Введите пароль" required>
                    <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                </div>
                <button type="submit" class="auth-submit">Войти</button>
                <div class="auth-links">
                    <span class="auth-link" data-switch="register">Нет аккаунта? Зарегистрироваться</span>
                </div>
            </form>
        </div>
    </div>
    
        <nav class="main-nav">
        <div class="container">
            <!-- Внутри index.php где-то добавьте -->
<a href="cart.php" class="cart-btn" style="display: none;">
    <i class="fas fa-shopping-cart"></i>
    <span id="cartCount">0</span>
</a>
            <ul class="nav-menu">
                <li><a href="set.php">Наборы</a></li>
                <li><a href="bouquets.php">Букеты</a></li>
            </ul>
        </div>
    </nav>
   <section class="banner-wrapper">
    <div class="banner">
        <img src="./img/shapka_pk.png" alt="Сочная клубника в шоколаде">
    </div>
    <div class="banner-content">
        <h1>Сочная клубника в изысканном шоколаде</h1>
        <p>Создаём неповторимые вкусовые сочетания для ваших особенных моментов</p>
        <button class="banner-btn">Выбрать набор</button>
    </div>
</section>

    <section class="about">
        <div class="container">
            <h2 class="section-title">О компании</h2>
            <div class="about-content">
                <div class="about-text">
                    <p><span itemprop="description">Chocoberry Place — это мастерская по созданию изысканных десертов из свежайшей клубники в шоколаде.</span> Мы начали свой путь в <span itemprop="foundingDate">2017</span> году с маленькой домашней кондитерской и выросли в компанию с безупречной репутацией.</p>
                    <p>Мы тщательно отбираем только лучшие ягоды и используем исключительно качественный бельгийский шоколад. Каждый наш продукт создаётся с любовью и вниманием к деталям.</p>
                    <p>Наши клиенты — это те, кто ценит прекрасное, любит удивлять и быть окруженным вниманием. Мы помогаем вам создавать <a href="set.html" class="titl">моменты счастья</a> и делиться ими с близкими.</p>
                </div>
                <div class="about-image">
                    <img class="about" src="img/logo.webp" loading="lazy" alt="Chocoberry Place - клубника в шоколаде" itemprop="logo">
                </div>
            </div>
        </div>
    </section>
    <section class="top-sets">
        <div class="container">
            <h2 class="section-title">Популярные наборы клубники в шоколаде</h2>
            <div id="products"></div>
        </div>
    </section>
    <section class="reviews">
        <div class="container">
            <h2 class="section-title">Отзывы наших клиентов</h2>
            <div class="review-slider">
                <button class="slider-btn prev-btn" aria-label="Предыдущий отзыв">‹</button>
                <div class="slider-container">
                    <div class="review-track">
                        <div class="review-slide">
                            <div class="review-card">
                                <p class="review-text">"Заказывала букет из клубники на день рождения подруги. Была приятно удивлена вниманием к деталям и качеством продукции. Подруга в восторге, все гости были впечатлены!"</p>
                                <div class="review-author">- Екатерина</div>
                            </div>
                        </div>
                        <div class="review-slide">
                            <div class="review-card">
                                <p class="review-text">"Ежегодно заказываю клубнику в шоколаде на 8 марта для сотрудниц. Ни разу не было нареканий, только восторженные отзывы. Качество стабильно высокое, доставка всегда вовремя."</p>
                                <div class="review-author">- Дмитрий</div>
                            </div>
                        </div>
                        <div class="review-slide">
                            <div class="review-card">
                                <p class="review-text">"Попробовала практически все наборы Chocoberry Place. Это лучшая клубника в шоколаде, которую я ела! Сочетание свежей ягоды и качественного шоколада — просто божественно."</p>
                                <div class="review-author">- Анна</div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="slider-btn next-btn" aria-label="Следующий отзыв">›</button>
                <div class="slider-nav">
                    <div class="slider-dot active"></div>
                    <div class="slider-dot"></div>
                    <div class="slider-dot"></div>
                </div>
            </div>
        </div>
    </section>
    
    <div id="footer"></div>
    <script>
        
    $('.mask-phone').mask('+7 (999) 999-99-99');
    $(function() {
        $("#header").load("php/header.php");
        $("#products").load("php/products.php");
        $("#footer").load("html/footer.html");
        $(".banner-btn").click(function() {
            $('html, body').animate({
                scrollTop: $(".top-sets").offset().top
            }, 800);
        });
    });
    // В index.php после $("#products").load("php/products.php");
setTimeout(function() {
    // Инициализация кнопок "Подробнее" на главной
    $('.read-more-btn').click(function() {
        const targetId = $(this).data('target');
        const description = $('#' + targetId);
        const button = $(this);
        
        if (description.hasClass('expanded')) {
            description.removeClass('expanded');
            button.html('Подробнее <i class="fas fa-chevron-down"></i>');
            button.removeClass('expanded');
        } else {
            description.addClass('expanded');
            button.html('Скрыть <i class="fas fa-chevron-up"></i>');
            button.addClass('expanded');
        }
    });
}, 1000);// Добавление товара в корзину с проверкой авторизации
$(document).on('click', '.add-to-cart', function() {
    const productId = $(this).data('id');

    // Проверяем авторизацию через твой файл
    $.ajax({
        url: 'php/check_auth.php',
        type: 'GET',
        dataType: 'json',
        success: function(auth) {
            if (!auth.authenticated) {
                // Пользователь не вошёл – открываем окно авторизации
                $('#authModal').fadeIn();
                return;
            }

            // Если авторизован – добавляем товар в корзину
            $.ajax({
                url: 'php/add_to_cart.php',
                type: 'POST',
                data: { id: productId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Товар добавлен в корзину!');
                        updateCartCount();
                    } else {
                        alert(response.message || 'Ошибка добавления товара');
                    }
                },
                error: function() {
                    alert('Ошибка соединения с сервером');
                }
            });
        },
        error: function() {
            alert('Ошибка проверки авторизации');
        }
    });
});

    </script>
    <script src="js/cart.js"></script>
    <script src="js/slider_views.js"></script>
    <script src="js/modal.js"></script>
    <style>
        /* Базовые стили для блока отзывов */
.reviews {
    padding: 40px 0;
    background: linear-gradient(135deg, #fff5f7 0%, #fff 100%);
    position: relative;
    overflow: hidden;
}

.reviews .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.section-title {
    text-align: center;
    font-size: 32px;
    color: #7a1f3d;
    margin-bottom: 40px;
    font-weight: 700;
}

/* Контейнер слайдера */
.review-slider {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

/* Кнопки навигации */
.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: #ff3366;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    box-shadow: 0 4px 8px rgba(255, 51, 102, 0.2);
}

.slider-btn:hover {
    background: #e60050;
    transform: translateY(-50%) scale(1.1);
}

.slider-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.prev-btn {
    left: -20px;
}

.next-btn {
    right: -20px;
}

/* Контейнер для трека слайдов */
.slider-container {
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    background: white;
}

.review-track {
    display: flex;
    transition: transform 0.5s ease;
    will-change: transform;
}

.review-slide {
    flex: 0 0 100%;
    min-width: 100%;
    padding: 30px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px; /* Минимальная высота */
}

/* Карточка отзыва */
.review-card {
    text-align: center;
    padding: 30px;
    max-width: 600px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.review-text {
    font-size: 18px;
    line-height: 1.6;
    color: #333;
    margin-bottom: 20px;
    font-style: italic;
    position: relative;
    padding: 0 10px;
}

.review-text::before,
.review-text::after {
    content: '"';
    font-size: 32px;
    color: #ff3366;
    opacity: 0.3;
    position: absolute;
    font-family: Georgia, serif;
}

.review-text::before {
    top: -10px;
    left: -5px;
}

.review-text::after {
    bottom: -20px;
    right: -5px;
}

.review-author {
    font-size: 16px;
    font-weight: 600;
    color: #7a1f3d;
    margin-top: 10px;
}

/* Навигационные точки */
.slider-nav {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.slider-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #ddd;
    cursor: pointer;
    transition: all 0.3s;
}

.slider-dot:hover {
    background: #ff99bb;
}

.slider-dot.active {
    background: #ff3366;
    transform: scale(1.2);
}

/* Адаптивность */

/* 1024px - 769px (Небольшие ноутбуки и планшеты горизонтально) */
@media (max-width: 1024px) {
    .reviews {
        padding: 35px 0;
    }
    
    .section-title {
        font-size: 28px;
        margin-bottom: 35px;
    }
    
    .review-slider {
        max-width: 700px;
    }
    
    .review-slide {
        padding: 25px;
        min-height: 180px;
    }
    
    .review-card {
        padding: 25px;
    }
    
    .review-text {
        font-size: 17px;
        line-height: 1.5;
    }
}

/* 768px - 577px (Планшеты вертикально) */
@media (max-width: 768px) {
    .reviews {
        padding: 30px 0;
    }
    
    .reviews .container {
        padding: 0 15px;
    }
    
    .section-title {
        font-size: 26px;
        margin-bottom: 30px;
    }
    
    .review-slider {
        max-width: 90%;
    }
    
    .slider-btn {
        width: 35px;
        height: 35px;
        font-size: 18px;
    }
    
    .prev-btn {
        left: -15px;
    }
    
    .next-btn {
        right: -15px;
    }
    
    .review-slide {
        padding: 20px;
        min-height: 160px;
    }
    
    .review-card {
        padding: 20px;
    }
    
    .review-text {
        font-size: 16px;
        line-height: 1.5;
        padding: 0 5px;
    }
    
    .review-text::before,
    .review-text::after {
        font-size: 28px;
    }
    
    .review-author {
        font-size: 15px;
    }
}

/* 576px - 426px (Большие телефоны) */
@media (max-width: 576px) {
    .reviews {
        padding: 25px 0;
    }
    
    .section-title {
        font-size: 24px;
        margin-bottom: 25px;
    }
    
    .review-slider {
        max-width: 95%;
    }
    
    .slider-btn {
        width: 30px;
        height: 30px;
        font-size: 16px;
    }
    
    .prev-btn {
        left: -10px;
    }
    
    .next-btn {
        right: -10px;
    }
    
    .review-slide {
        padding: 15px;
        min-height: 140px;
    }
    
    .review-card {
        padding: 15px;
    }
    
    .review-text {
        font-size: 15px;
        line-height: 1.4;
        margin-bottom: 15px;
    }
    
    .review-text::before,
    .review-text::after {
        font-size: 24px;
    }
    
    .review-author {
        font-size: 14px;
    }
}

/* 425px - 376px (Средние телефоны) */
@media (max-width: 425px) {
    .reviews {
        padding: 20px 0;
    }
    
    .reviews .container {
        padding: 0 10px;
    }
    
    .section-title {
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    .review-slider {
        max-width: 100%;
    }
    
    .slider-btn {
        width: 28px;
        height: 28px;
        font-size: 14px;
        top: 40%; /* Сдвигаем выше из-за меньшей высоты */
    }
    
    .prev-btn {
        left: 5px;
    }
    
    .next-btn {
        right: 5px;
    }
    
    .slider-container {
        margin: 0 10px;
    }
    
    .review-slide {
        padding: 12px;
        min-height: 120px;
    }
    
    .review-card {
        padding: 12px;
    }
    
    .review-text {
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 12px;
    }
    
    .review-text::before {
        top: -8px;
        left: 0;
    }
    
    .review-text::after {
        bottom: -15px;
        right: 0;
    }
    
    .review-author {
        font-size: 13px;
        margin-top: 8px;
    }
    
    .slider-nav {
        margin-top: 15px;
    }
    
    .slider-dot {
        width: 8px;
        height: 8px;
    }
}

/* 375px - 321px (Маленькие телефоны) */
@media (max-width: 375px) {
    .reviews {
        padding: 15px 0;
    }
    
    .section-title {
        font-size: 20px;
        margin-bottom: 15px;
    }
    
    .review-slider {
        max-width: 100%;
    }
    
    .slider-btn {
        width: 25px;
        height: 25px;
        font-size: 12px;
        top: 35%;
    }
    
    .prev-btn {
        left: 3px;
    }
    
    .next-btn {
        right: 3px;
    }
    
    .slider-container {
        margin: 0 8px;
        border-radius: 10px;
    }
    
    .review-slide {
        padding: 10px;
        min-height: 110px;
    }
    
    .review-card {
        padding: 10px;
    }
    
    .review-text {
        font-size: 13px;
        line-height: 1.3;
        margin-bottom: 10px;
    }
    
    .review-text::before,
    .review-text::after {
        font-size: 20px;
    }
    
    .review-author {
        font-size: 12px;
    }
    
    .slider-nav {
        gap: 8px;
        margin-top: 12px;
    }
    
    .slider-dot {
        width: 7px;
        height: 7px;
    }
}

/* 320px и меньше (Мини телефоны) */
@media (max-width: 320px) {
    .reviews {
        padding: 12px 0;
    }
    
    .section-title {
        font-size: 18px;
        margin-bottom: 12px;
    }
    
    .slider-btn {
        width: 22px;
        height: 22px;
        font-size: 11px;
        top: 30%;
    }
    
    .prev-btn {
        left: 2px;
    }
    
    .next-btn {
        right: 2px;
    }
    
    .slider-container {
        margin: 0 5px;
    }
    
    .review-slide {
        padding: 8px;
        min-height: 100px;
    }
    
    .review-card {
        padding: 8px;
    }
    
    .review-text {
        font-size: 12px;
        line-height: 1.3;
        margin-bottom: 8px;
    }
    
    .review-text::before,
    .review-text::after {
        font-size: 18px;
    }
    
    .review-text::before {
        top: -5px;
    }
    
    .review-text::after {
        bottom: -10px;
    }
    
    .review-author {
        font-size: 11px;
    }
    
    .slider-nav {
        gap: 6px;
        margin-top: 10px;
    }
    
    .slider-dot {
        width: 6px;
        height: 6px;
    }
}
    </style>
  
<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.review-track');
    const slides = document.querySelectorAll('.review-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    let currentSlide = 0;
    const totalSlides = slides.length;
    
    // Функция обновления позиции слайдера
    function updateSlider() {
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        // Обновляем активную точку
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
    
    // Переход к следующему слайду
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
    }
    
    // Переход к предыдущему слайду
    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider();
    }
    
    // Обработчики для кнопок
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);
    
    // Обработчики для точек навигации
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentSlide = index;
            updateSlider();
        });
    });
    
    // Автопрокрутка (опционально)
    let slideInterval = setInterval(nextSlide, 5000);
    
    // Останавливаем автопрокрутку при наведении
    const sliderContainer = document.querySelector('.review-slider');
    sliderContainer.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });
    
    sliderContainer.addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 5000);
    });
    
    // Останавливаем автопрокрутку при касании на мобильных
    sliderContainer.addEventListener('touchstart', () => {
        clearInterval(slideInterval);
    });
});
</script>
</body>
</html>