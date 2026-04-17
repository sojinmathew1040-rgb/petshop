<?php
require_once '../db.php';
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_slide'])) {
        $offer_text = $_POST['offer_text'];
        $title_line1 = $_POST['title_line1'];
        $title_line2 = $_POST['title_line2'];
        $button_text = $_POST['button_text'];
        $button_link = $_POST['button_link'];

        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $filename = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $filename);
            $image_path = 'uploads/' . $filename;
        }

        $stmt = $pdo->prepare("INSERT INTO hero_slides (offer_text, title_line1, title_line2, button_text, button_link, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$offer_text, $title_line1, $title_line2, $button_text, $button_link, $image_path]);
    } elseif (isset($_POST['delete_slide'])) {
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$_POST['slide_id']]);
    }
    header('Location: hero_manager.php');
    exit;
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Hero Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>🐶 WAGGY Pro</h2>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="hero_manager.php" class="active">Hero Slider</a></li>
                <li><a href="product_manager.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header-top">
                <h1>Hero Slider Manager</h1>
            </div>

            <div class="card">
                <h3>Add New Slide</h3>
                <form method="POST" enctype="multipart/form-data" style="margin-top:20px;">
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Offer Text</label>
                            <input type="text" name="offer_text" placeholder="e.g. SAVE 10 - 20 % OFF" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Image</label>
                            <input type="file" name="image" required accept="image/*">
                        </div>
                    </div>
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Title Line 1</label>
                            <input type="text" name="title_line1" placeholder="e.g. Best Destination" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Title Line 2 (Highlight)</label>
                            <input type="text" name="title_line2" placeholder="e.g. Your Pets" required>
                        </div>
                    </div>
                    <div style="display:flex; gap:20px;">
                        <div class="form-group" style="flex:1;">
                            <label>Button Text</label>
                            <input type="text" name="button_text" placeholder="e.g. SHOP NOW →" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Button Link</label>
                            <input type="text" name="button_link" placeholder="e.g. shop.php" required>
                        </div>
                    </div>
                    <button type="submit" name="add_slide" class="btn-primary">Add Slide</button>
                </form>
            </div>

            <div class="card">
                <h3>Current Slides</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Offer</th>
                            <th>Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slides as $slide): ?>
                            <tr>
                                <td><img src="../<?= htmlspecialchars($slide['image_path']) ?>" width="80" height="80"></td>
                                <td><?= htmlspecialchars($slide['offer_text']) ?></td>
                                <td><?= htmlspecialchars($slide['title_line1']) ?> <br> <span
                                        style="color:#c89b63"><?= htmlspecialchars($slide['title_line2']) ?></span></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="slide_id" value="<?= $slide['id'] ?>">
                                        <button type="submit" name="delete_slide" class="btn-danger">Delete</button>
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