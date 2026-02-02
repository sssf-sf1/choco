<?php
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$berry_qty = isset($_POST['berry_qty']) ? intval($_POST['berry_qty']) : null;
$addons = isset($_POST['addons']) ? json_decode($_POST['addons'], true) : [];
$mold_type = $_POST['mold_type'] ?? 'heart';

if ($id === null || !isset($_SESSION['cart'])) {
    echo json_encode(['status'=>'error', 'message'=>'Товар не найден']);
    exit;
}

foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['id'] == $id) {
        if ($berry_qty !== null) {
            $_SESSION['cart'][$key]['berry_qty'] = $berry_qty;
        }
        if (!empty($addons)) {
            $_SESSION['cart'][$key]['addons'] = $addons;
        }
        $_SESSION['cart'][$key]['mold_type'] = $mold_type;
        echo json_encode(['status'=>'success']);
        exit;
    }
}

echo json_encode(['status'=>'error','message'=>'Товар не найден']);
