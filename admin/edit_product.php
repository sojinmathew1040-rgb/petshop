<?php
require_once '../db.php';
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: product_manager.php');
    exit;
}

$product_id = $_GET['id'];

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_product'])) {
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $price = $_POST['price'];
        $old_price = $_POST['old_price'] ?: null;
        $category = $_POST['category'];
        $stock = (int) $_POST['stock_quantity'];

        $badge = '';
        $stock_status = 'In Stock';
        if ($stock <= 0) {
            $badge = 'sold-out';
            $stock_status = 'Sold Out';
        } elseif ($stock <= 5) {
            $badge = 'sale'; // Using sale color for limited stock warning
            $stock_status = 'Limited Stock';
        } elseif (!empty($_POST['badge'])) {
            $badge = $_POST['badge'];
        }

        $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, old_price=?, category=?, badge=?, stock_status=?, stock_quantity=? WHERE id=?");
        $stmt->execute([$title, $desc, $price, $old_price, $category, $badge, $stock_status, $stock, $product_id]);

        // Note: Full image edit logic is complex; for MVP we'll just allow data updates.
        // A robust image manager could be an additional feature later.

        header('Location: product_manager.php');
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: product_manager.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>🐶 WAGGY Pro</h2>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="hero_manager.php">Hero Slider</a></li>
                <li><a href="product_manager.php" class="active">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header-top">
                <h1>Edit Product #<?= $product['id'] ?></h1>
                <a href="product_manager.php" class="btn-primary" style="text-decoration:none;">Back to Products</a>
            </div>

            <div class="card">
                <h3>Update Details</h3>
                <form method="POST" style="margin-top:20px;">
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:2;">
                            <label>Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price"
                                value="<?= htmlspecialchars($product['price']) ?>" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Old Price (optional)</label>
                            <input type="number" step="0.01" name="old_price"
                                value="<?= htmlspecialchars($product['old_price'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"
                            rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="dog" <?= $product['category'] == 'dog' ? 'selected' : '' ?>>Dog</option>
                                <option value="cat" <?= $product['category'] == 'cat' ? 'selected' : '' ?>>Cat</option>
                                <option value="bird" <?= $product['category'] == 'bird' ? 'selected' : '' ?>>Bird</option>
                                <option value="accessories" <?= $product['category'] == 'accessories' ? 'selected' : '' ?>>
                                    Accessories</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Stock Qty</label>
                            <input type="number" name="stock_quantity"
                                value="<?= htmlspecialchars($product['stock_quantity'] ?? 0) ?>" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Badge</label>
                            <select name="badge">
                                <option value="" <?= $product['badge'] == '' ? 'selected' : '' ?>>Auto (Based on Stock)
                                </option>
                                <option value="new" <?= $product['badge'] == 'new' ? 'selected' : '' ?>>New</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_product" class="btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>