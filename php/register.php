<?php
session_start();
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    $errors = [];
    
    if (!$name) $errors[] = "Введите имя";
    if (!$surname) $errors[] = "Введите фамилию";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Введите корректный email";
    if (strlen($password) < 6) $errors[] = "Пароль должен быть не менее 6 символов";
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Пользователь с таким email уже существует";
    }
    $stmt->close();
    
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $surname, $email, $phone, $hash);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            header('Location: ../index.php');
            exit;
        } else {
            $_SESSION['error'] = "Ошибка регистрации: " . $mysqli->error;
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
        $_SESSION['old'] = $_POST;
    }
    
    header('Location: ../index.php#authModal');
    exit;
}