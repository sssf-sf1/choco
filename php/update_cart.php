<?php
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if ($id === null || !isset($_SESSION['cart'])) {
    echo json_encode(['status'=>'error', 'message'=>'Товар не найден']);
    exit;
}

foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['id'] == $id) {
        if ($action === 'plus') {
            $_SESSION['cart'][$key]['qty']++;
            echo json_encode(['status'=>'success', 'itemQty'=>$_SESSION['cart'][$key]['qty']]);
            exit;
        } elseif ($action === 'minus') {
            $_SESSION['cart'][$key]['qty']--;
            if ($_SESSION['cart'][$key]['qty'] <= 0) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                echo json_encode(['status'=>'removed']);
                exit;
            }
            echo json_encode(['status'=>'success', 'itemQty'=>$_SESSION['cart'][$key]['qty']]);
            exit;
        }
    }
}

echo json_encode(['status'=>'error','message'=>'Товар не найден']);
