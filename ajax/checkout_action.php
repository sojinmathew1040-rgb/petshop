<?php
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$address = trim($data['address'] ?? '');

if (empty($name) || empty($email) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'All shipping fields are required.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Fetch Cart Products to calculate total accurately (server-side)
    $ids = array_keys($cart_items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_price = 0;
    $product_prices = [];
    foreach ($products as $p) {
        $p_id = $p['id'];
        $qty = $cart_items[$p_id];
        $price = $p['price'];
        $total_price += $price * $qty;
        $product_prices[$p_id] = $price;
    }

    // Apply Offer if active
    if (isset($_SESSION['offer_applied']) && $_SESSION['offer_applied'] === true) {
        $total_price -= ($total_price * 0.20);
        unset($_SESSION['offer_applied']); // One-time use per session
    }

    // 2. Create Order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, phone, shipping_address, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $email, $phone, $address, $total_price]);
    $order_id = $pdo->lastInsertId();

    // 3. Create Order Items
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $p_id => $qty) {
        $price = $product_prices[$p_id] ?? 0;
        $stmtItem->execute([$order_id, $p_id, $qty, $price]);
    }

    $pdo->commit();

    // 4. Clear Cart
    unset($_SESSION['cart']);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()]);
}
?>