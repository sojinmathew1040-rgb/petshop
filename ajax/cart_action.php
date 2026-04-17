<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? (int) $input['product_id'] : 0;
$action = isset($input['action']) ? $input['action'] : 'add';
$qty = isset($input['qty']) ? (int) $input['qty'] : 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
} elseif ($action === 'update') {
    if ($qty > 0) {
        $_SESSION['cart'][$product_id] = $qty;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
} elseif ($action === 'remove') {
    unset($_SESSION['cart'][$product_id]);
}

$total_count = array_sum(array_values($_SESSION['cart']));

echo json_encode([
    'success' => true,
    'count' => $total_count
]);
