<?php
session_start();
?>
<header class="header">
    <div class="container <?php echo isset($_SESSION['user']) ? 'header__top' : ''; ?>">
        <a href="index.php" class="logo">
            <img src="img/logo.svg" alt="ChocoBerry Place">
        </a>
       
        <?php if (isset($_SESSION['user'])): ?>
            <div class="user-menu">
                <a href="account.php" class="user-link" title="Личный кабинет">
                    <i class="fas fa-user"></i>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                </a>
                <a href="cart.php" class="cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $cartCount = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $cartCount += $item['qty'];
                        }
                    }
                    if ($cartCount > 0): ?>
                        <span id="cartCount" class="cart-count"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                <div class="btn__auth">
                    <a href="php/logout.php" class="logout-btn" title="Выход">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="logout-text">Выход</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="btn__auth">
                <span class="auth-btn open-modal" data-modal="authModal" data-tab="login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="auth-text">Войти</span>
                </span>
                <span class="auth-btn open-modal" data-modal="authModal" data-tab="register">
                    <i class="fas fa-user-plus"></i>
                    <span class="auth-text">Регистрация</span>
                </span>
            </div>
        <?php endif; ?>
    </div>
    
    <style>
        /* Базовые стили */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .container.header__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 35px;
            width: auto;
            transition: transform 0.3s;
        }
        
        .logo img:hover {
            transform: scale(1.05);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #ff3366;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .user-link:hover {
            color: #ff3366;
            background: #fff5f7;
        }
        
        .user-link i {
            font-size: 16px;
        }
        
        .user-name {
            font-size: 14px;
        }
        
        .cart-btn {
            position: relative;
            display: flex;
            align-items: center;
            color: #ff3366;
            font-size: 16px;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.3s;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .cart-btn:hover {
            color: #ff3366;
            background: #fff5f7;
        }
        
        .cart-count {
            position: absolute;
            top: -3px;
            right: -3px;
            background: #ff3366;
            color: white;
            font-size: 10px;
            font-weight: bold;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn__auth {
            display: flex;
            gap: 10px;
        }
        
        .auth-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            color: #7a1f3d;
            background: #fff5f7;
            border: 1px solid #ffd1dc;
            white-space: nowrap;
        }
        
        .auth-btn:hover {
            background: #ff3366;
            color: white;
            border-color: #ff3366;
        }
        
        .auth-btn i {
            font-size: 13px;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            color: #7a1f3d;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .logout-btn:hover {
            background: #ff3366;
            color: white;
            border-color: #ff3366;
        }
        
        /* Адаптивность */
        
        /* 768px - 481px (Планшеты вертикально) */
        @media (max-width: 768px) {
            .container.header__top {
                padding: 8px 12px;
            }
            
            .logo img {
                height: 20px;
            }
            
            .user-menu {
                gap: 10px;
            }
            
            .user-link {
                padding: 5px 8px;
                gap: 4px;
            }
            
            .user-name {
                font-size: 13px;
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .cart-btn {
                padding: 5px 6px;
                font-size: 15px;
            }
            
            .btn__auth {
                gap: 8px;
            }
            
            .auth-btn {
                padding: 5px 10px;
                font-size: 12px;
            }
            
            .logout-btn {
                padding: 5px 10px;
                font-size: 12px;
            }
        }
        
        /* 480px - 376px (Средние телефоны) */
        @media (max-width: 480px) {
            .container.header__top {
                padding: 6px 10px;
            }
            
            .logo img {
                height: 16px;
            }
            
            .user-menu {
                gap: 8px;
            }
            
           .user-name {
                display:none;
            }
            
            .cart-btn {
                padding: 4px 5px;
                font-size: 14px;
            }
            
            .cart-count {
                width: 14px;
                height: 14px;
                font-size: 9px;
                top: -2px;
                right: -2px;
            }
            
            .btn__auth {
                gap: 6px;
            }
            
            .auth-btn,
            .logout-btn {
                padding: 4px 8px;
                font-size: 11px;
                gap: 3px;
            }
            
            .auth-btn i,
            .logout-btn i {
                font-size: 11px;
            }
        }
        
        /* 375px - 321px (Маленькие телефоны) */
        @media (max-width: 375px) {
            .container.header__top {
                padding: 5px 8px;
            }
            
            .logo img {
                height: 14px;
            }
            
            .user-menu {
                gap: 6px;
            }
            
            .user-link {
                padding: 4px 6px;
                gap: 3px;
            }
            
            .user-name {
                display:none;
            }
            
            .user-link i {
                font-size: 14px;
            }
            
            .cart-btn {
                padding: 4px;
                font-size: 13px;
            }
            
            .cart-count {
                width: 13px;
                height: 13px;
                font-size: 8px;
            }
            
            .btn__auth {
                gap: 5px;
            }
            
            .auth-btn,
            .logout-btn {
                padding: 4px 6px;
                font-size: 10px;
                min-width: 60px;
            }
            
            .auth-text,
            .logout-text {
                display: none;
            }
            
            .auth-btn {
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 50%;
            }
            
            .logout-btn {
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 50%;
            }
            
            .auth-btn i,
            .logout-btn i {
                font-size: 12px;
                margin: 0;
            }
        }
        
        /* 320px и меньше (Мини телефоны) */
        @media (max-width: 320px) {
            .container.header__top {
                padding: 4px 6px;
            }
            
            .logo img {
                height: 12px;
            }
            
            .user-menu {
                gap: 4px;
            }
            
            .user-link {
                padding: 3px 5px;
            }
            
            .user-name {
                display:none;
            }
            
            .user-link i {
                font-size: 12px;
            }
            
            .cart-btn {
                padding: 3px;
                font-size: 12px;
            }
            
            .cart-count {
                width: 12px;
                height: 12px;
                font-size: 7px;
            }
            
            .btn__auth {
                gap: 4px;
            }
            
            .auth-btn,
            .logout-btn {
                width: 30px;
                height: 32px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .auth-btn i,
            .logout-btn i {
                font-size: 11px;
            }
        }
        
        /* Оптимизация для ультра-узких экранов */
        @media (max-width: 280px) {
            .logo img {
                height: 20px;
            }
            
            .user-menu {
                gap: 3px;
            }
            
            .user-link {
                padding: 2px 4px;
            }
            
            .user-name {
                max-width: 50px;
                font-size: 9px;
            }
            
            .cart-btn {
                padding: 2px;
                font-size: 11px;
            }
            
            .cart-count {
                width: 11px;
                height: 11px;
                font-size: 6px;
                top: -1px;
                right: -1px;
            }
            
            .auth-btn,
            .logout-btn {
                width: 30px;
                height: 30px;
            }
            
            .auth-btn i,
            .logout-btn i {
                font-size: 10px;
            }
        }
        
        /* Для неавторизованных пользователей - адаптация кнопок */
        @media (max-width: 375px) {
            .btn__auth:not(:has(.logout-btn)) .auth-btn {
                min-width: auto;
                width: auto;
                height: auto;
                padding: 4px 8px;
                border-radius: 20px;
            }
            
            .btn__auth:not(:has(.logout-btn)) .auth-text {
                display: inline;
                font-size: 10px;
            }
            
            .btn__auth:not(:has(.logout-btn)) .auth-btn i {
                font-size: 10px;
            }
        }
        
        @media (max-width: 320px) {
            .btn__auth:not(:has(.logout-btn)) .auth-btn {
                padding: 3px 6px;
                font-size: 9px;
            }
            
            .btn__auth:not(:has(.logout-btn)) .auth-text {
                font-size: 9px;
            }
            
            .btn__auth:not(:has(.logout-btn)) .auth-btn i {
                font-size: 9px;
            }
        }
    </style>
</header>