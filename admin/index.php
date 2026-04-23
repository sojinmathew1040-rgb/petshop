<?php
require_once '../db.php';
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Waggy Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>🐶 WAGGY Pro</h2>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="hero_manager.php">Hero Slider</a></li>
                <li><a href="category_manager.php">Categories</a></li>
                <li><a href="product_manager.php">Products</a></li>
                <li><a href="deal_manager.php">Deal Of The Day</a></li>
                <li><a href="testimonial_manager.php">Testimonials</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="review_manager.php">Reviews</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header-top">
                <h1>Overview</h1>
            </div>
            <div class="card">
                <h3>Welcome to Waggy Admin Pro</h3>
                <p style="color:#666; margin-top:10px;">Select a module from the left menu to manage the content of your
                    site.</p>
            </div>
        </div>
    </div>
</body>

</html>