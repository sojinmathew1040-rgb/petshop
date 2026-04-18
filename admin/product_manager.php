<?php
require_once '../db.php';
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $price = $_POST['price'];
        $old_price = $_POST['old_price'] ?: null;
        $category = $_POST['category'];
        $stock = (int) $_POST['stock_quantity'];
        $is_trending = isset($_POST['is_trending']) ? 1 : 0;

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

        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, old_price, category, badge, stock_status, stock_quantity, is_trending) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $price, $old_price, $category, $badge, $stock_status, $stock, $is_trending]);
        $product_id = $pdo->lastInsertId();

        // Image uploads
        if (isset($_FILES['images'])) {
            $file_count = count($_FILES['images']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['images']['error'][$i] === 0) {
                    $filename = time() . '_' . $i . '_' . $_FILES['images']['name'][$i];
                    move_uploaded_file($_FILES['images']['tmp_name'][$i], '../uploads/' . $filename);
                    $img_path = 'uploads/' . $filename;
                    $pdo->prepare("INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?, ?, ?)")
                        ->execute([$product_id, $img_path, $i]);
                }
            }
        }
    } elseif (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];

        // Fetch images to delete from filesystem
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($images as $img) {
            $file_path = '../' . $img['image_path'];
            if (file_exists($file_path) && is_file($file_path)) {
                unlink($file_path);
            }
        }

        // Delete images from DB
        $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$product_id]);

        // Delete product
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
    }
    header('Location: product_manager.php');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Manager</title>
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
                <li><a href="category_manager.php">Categories</a></li>
                <li><a href="product_manager.php" class="active">Products</a></li>
                <li><a href="deal_manager.php">Deal Of The Day</a></li>
                <li><a href="testimonial_manager.php">Testimonials</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header-top">
                <h1>Product Manager</h1>
            </div>

            <div class="card">
                <h3>Add New Product</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top:20px;">
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:2;">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Old Price (optional)</label>
                            <input type="number" step="0.01" name="old_price">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Category</label>
                            <select name="category" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['name']) ?>">


                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Stock Qty</label>
                            <input type="number" name="stock_quantity" value="10" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Badge</label>
                            <select name="badge">
                                <option value="">Auto (Based on Stock)</option>
                                <option value="new">New</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1; display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="is_trending" id="is_trending" value="1"
                                style="width:20px;height:20px;cursor:pointer;">
                            <label for="is_trending" style="margin-bottom:0; cursor:pointer;">Trending</label>
                        </div>
                        <div class="form-group" style="flex:2;">
                            <label>3D Images</label>
                            <input type="file" name="images[]" multiple accept="image/*" required>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn-primary">Add Product</button>
                </form>
            </div>

            <div class="card">
                <h3>Current Products</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['title']) ?></td>
                                <td>₹<?= number_format($p['price'], 2) ?></td>
                                <td><?= isset($p['stock_quantity']) ? $p['stock_quantity'] : '10' ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-primary"
                                        style="padding: 6px 12px; text-decoration: none; font-size: 14px; border-radius: 5px; margin-right: 5px;">Edit</a>
                                    <form method="POST" style="display:inline;"
                                        onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <button type="submit" name="delete_product" class="btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>