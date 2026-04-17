<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$wishlist_count = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Waggy Pet Shop</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/addons.css">

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

    <!-- ===== HEADER ===== -->
    <header>

        <!-- TOP HEADER -->
        <div class="top-header">

            <a href="index.php" style="text-decoration:none; color:inherit;">
                <div class="logo">
                    🐶 <span>WAGGY</span>
                    <small>Pet Shop</small>
                </div>
            </a>

            <div class="search-box">
                <input type="text" placeholder="Search For More Than 10,000 Products">
                <button>🔍</button>
            </div>

            <div class="contact">
                <div>
                    Phone
                    <span>+980-34-984089</span>
                </div>
                <div>
                    Email
                    <span>waggy@gmail.com</span>
                </div>
            </div>

        </div>

        <!-- NAVBAR -->
        <nav class="navbar">

            <div class="hamburger" onclick="toggleMenu()">☰</div>

            <ul class="menu" id="menu">
                <!-- <li><a href="shop.php" style="text-decoration:none; color:inherit;">Shop by Category ▼</a></li> -->
                <li><a href="index.php" style="text-decoration:none; color:inherit;">Home</a></li>
                <li><a href="about.php" style="text-decoration:none; color:inherit;">About Us</a></li>
                <li><a href="shop.php" style="text-decoration:none; color:inherit;">Shop</a></li>
                <!-- <li><a href="#" style="text-decoration:none; color:inherit;">Blog</a></li> -->
                <li><a href="contact.php" style="text-decoration:none; color:inherit;">Contact</a></li>
                <!-- <li class="highlight"><a href="#" style="text-decoration:none; color:inherit;">GET PRO</a></li> -->
            </ul>

            <div class="icons">
                <div style="cursor:pointer;" onclick="window.location='account.php'">👤</div>
                <div class="wishlist-link" onclick="window.location='wishlist.php'"
                    style="position:relative; cursor:pointer;">❤ <span id="wishlist-count-badge"
                        style="position:absolute;top:-8px;right:-10px;background:#d6a86c;color:#fff;font-size:11px;padding:3px 6px;border-radius:50%;"><?= $wishlist_count ?></span>
                </div>
                <div class="cart" onclick="window.location='cart.php'" style="cursor:pointer;">🛒 <span
                        id="cart-count-badge"><?= $cart_count ?></span></div>
            </div>

        </nav>

    </header>