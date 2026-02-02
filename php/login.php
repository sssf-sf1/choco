<?php
session_start();
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            header('Location: ../index.php');
            exit;
        } else {
            $_SESSION['error'] = "Неверный пароль";
        }
    } else {
        $_SESSION['error'] = "Пользователь не найден";
    }
    
    $_SESSION['old_email'] = $email;
    header('Location: ../index.php#authModal');
    exit;
}