<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password, role FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $pass_db, $role);
    $stmt->fetch();
    $stmt->close();

    // Проверка пароля (без хеша)
    if ($id && $password === $pass_db && $role === 'admin') {
        $_SESSION['user_id'] = $id;
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль или недостаточно прав";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Вход в админку</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<form method="POST" class="form-login">
<h2>Вход в админку</h2>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Пароль" required>
<button type="submit">Войти</button>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</form>
</body>
</html>
