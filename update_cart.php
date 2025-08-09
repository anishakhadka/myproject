<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($product_id && isset($_SESSION['cart'][$product_id])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$product_id]['quantity'] -= 1;
            if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }
}

header('Location: cart.php');
exit;
