<?php
require_once 'db.php';
include 'header.php';

// Fetch hero slides
$hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
if (empty($hero_slides)) {
    $hero_slides = [['offer_text' => 'SAVE 10 - 20 % OFF', 'title_line1' => 'Best Destination', 'title_line2' => 'Your Pets', 'button_text' => 'SHOP NOW →', 'button_link' => 'shop.php', 'image_path' => 'assets/images/12.jpeg']];
}

// Fetch products for home (up to 3)
$home_products = $pdo->query("
    SELECT p.*, 
    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) as main_image 
    FROM products p 
    ORDER BY id DESC LIMIT 3
")->fetchAll();

$wishlist_items = $_SESSION['wishlist'] ?? [];
?>

<section class="hero">

    <div class="hero-slider">

        <div class="slides">
            <?php foreach ($hero_slides as $index => $slide): ?>
                <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                    <div class="hero-left">
                        <div class="hero-img-container">
                            <img src="<?= htmlspecialchars($slide['image_path'] ?? 'assets/images/12.jpeg') ?>"
                                class="hero-img">
                        </div>
                    </div>

                    <div class="hero-right">
                        <p class="offer"><?= htmlspecialchars($slide['offer_text']) ?></p>
                        <h1><?= htmlspecialchars($slide['title_line1']) ?> <br>
                            <span><?= htmlspecialchars($slide['title_line2']) ?></span>
                        </h1>
                        <button class="shop-btn"
                            onclick="window.location='<?= htmlspecialchars($slide['button_link']) ?>'"><?= htmlspecialchars($slide['button_text']) ?></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- DOTS -->
        <div class="dots">
            <?php foreach ($hero_slides as $index => $slide): ?>
                <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)"></span>
            <?php endforeach; ?>
        </div>

    </div>

</section>

<!-- FEATURES MARQUEE SECTION -->
<div class="features-marquee">
    <div class="marquee-content">
        <div class="marquee-item">🚚 Free Delivery Over ₹500</div>
        <div class="marquee-item">🛡️ Secure Payments</div>
        <div class="marquee-item">↩️ 30-Day Returns</div>
        <div class="marquee-item">🐾 Premium Pet Care</div>
        <div class="marquee-item">⭐ 5-Star Rated Service</div>
        <!-- Duplicated for seamless loop -->
        <div class="marquee-item">🚚 Free Delivery Over ₹500</div>
        <div class="marquee-item">🛡️ Secure Payments</div>
        <div class="marquee-item">↩️ 30-Day Returns</div>
        <div class="marquee-item">🐾 Premium Pet Care</div>
        <div class="marquee-item">⭐ 5-Star Rated Service</div>
    </div>
</div>

<!-- PRODUCT SECTION -->

<section class="product-section">
    <div class="product-header">
        <h2>Premium Pet Kennels</h2>
        <div class="product-filter">
            <button class="active">All</button>
            <button>Dog</button>
            <button>Cat</button>
            <button>Bird</button>
        </div>
        <a href="shop.php" class="shop-now-btn">Shop Now →</a> <!-- Link to main shop page -->
    </div>

    <div class="product-grid">
        <?php foreach ($home_products as $p):
            $in_wishlist = in_array($p['id'], $wishlist_items);
            ?>
            <div class="product-card" onclick="window.location='product.php?id=<?= $p['id'] ?>'">
                <!-- WISHLIST HEART -->
                <div class="wishlist-btn <?= $in_wishlist ? 'active' : '' ?>"
                    onclick="toggleWishlist(event, <?= $p['id'] ?>, this)">
                    <?= $in_wishlist ? '❤️' : '🤍' ?>
                </div>

                <div class="product-image">
                    <img src="<?= htmlspecialchars($p['main_image'] ?? 'assets/images/16.jpeg') ?>"
                        alt="<?= htmlspecialchars($p['title']) ?>">
                    <?php if ($p['badge']): ?>
                        <span class="badge <?= $p['badge'] ?>"><?= ucfirst($p['badge']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h4><?= htmlspecialchars($p['title']) ?></h4>
                    <div class="rating">
                        <?= str_repeat('★', $p['rating']) . str_repeat('☆', 5 - $p['rating']) ?>
                    </div>
                    <p class="price">
                        <?php if ($p['old_price']): ?>
                            <span class="old-price">₹<?= number_format($p['old_price'], 2) ?></span>
                        <?php endif; ?>
                        ₹<?= number_format($p['price'], 2) ?>
                    </p>
                    <button class="add-to-cart" <?= $p['stock_status'] == 'Sold Out' ? 'disabled' : '' ?>
                        onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>)">
                        <?= $p['stock_status'] == 'Sold Out' ? 'Sold Out' : 'Add to Cart' ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<!-- PROMOTIONAL BANNER -->
<section class="promo-section fade-in">
    <div class="promo-content">
        <p class="promo-sub">UPTO 40% OFF</p>
        <h2 class="promo-title">Clearance Sale !!!</h2>
        <a href="shop.php" class="promo-btn">SHOP NOW →</a>
    </div>

    <div class="promo-image-box">
        <img src="assets/images/16.jpeg" alt="Dog Kennel">
    </div>
</section>

<!-- WAGGY IMPACT / STATS SECTION -->
<section class="stats-section fade-in">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">15,000+</div>
            <div class="stat-text">Happy Pets Served</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">99%</div>
            <div class="stat-text">Delivery Success Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">50+</div>
            <div class="stat-text">Premium Global Brands</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div class="stat-text">Expert Pet Care Support</div>
        </div>
    </div>
</section>

<!-- offer-section -->

<section class="offer-section fade-in">

    <!-- FLOATING ICONS -->
    <div class="floating-icon icon1">🐾</div>
    <div class="floating-icon icon2">🐾</div>
    <div class="floating-icon icon3">🐾</div>

    <div class="offer-container">

        <h2 class="offer-title">
            Get 20% Off On <br>
            <span>First Purchase</span>
        </h2>

        <form class="offer-form" id="offerForm">

            <div class="input-group">
                <input type="email" name="email" required>
                <label>Email Address</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label>Create Password</label>
            </div>

            <div class="input-group">
                <input type="password" name="repeat_password" required>
                <label>Repeat Password</label>
            </div>

            <button type="submit" id="submitBtn">
                <span class="btn-text">REGISTER NOW</span>
                <span class="spinner"></span>
            </button>

        </form>

        <div class="success-message" style="display:none; text-align:center; padding: 40px;">
            <p style="font-size: 24px; color: #28a745; margin-bottom: 10px;">✔ Registration Successful!</p>
            <p style="color: #666;">Your 20% discount is applied. Start shopping!</p>
        </div>

    </div>
</section>
<?php
include 'footer.php';
?>



<script>
    /* ===== FADE-IN (GLOBAL) ===== */
    const faders = document.querySelectorAll('.fade-in');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    });

    faders.forEach(el => observer.observe(el));


    /* ===== PROMO SECTION EFFECTS (SAFE) ===== */
    const promoSection = document.querySelector(".promo-section");
    const imgBox = document.querySelector(".promo-image-box");

    if (promoSection && imgBox) {

        /* PARALLAX */
        window.addEventListener("scroll", () => {
            const rect = promoSection.getBoundingClientRect();

            if (rect.top < window.innerHeight && rect.bottom > 0) {
                let offset = rect.top * -0.05;
                imgBox.style.setProperty('--parallax', offset + 'px');
            }
        });

        /* CURSOR LIGHT */
        promoSection.addEventListener("mousemove", (e) => {
            const rect = promoSection.getBoundingClientRect();

            promoSection.style.setProperty("--x", (e.clientX - rect.left) + "px");
            promoSection.style.setProperty("--y", (e.clientY - rect.top) + "px");
        });
    }
</script>
<script>
    const form = document.getElementById("offerForm");
    const successMsg = document.querySelector(".success-message");
    const btn = document.getElementById("submitBtn");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        btn.querySelector(".btn-text").textContent = "PROCESSING...";
        btn.disabled = true;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('ajax/offer_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (result.success) {
                form.style.display = "none";
                successMsg.style.display = "block";
            } else {
                alert(result.message);
                btn.querySelector(".btn-text").textContent = "REGISTER NOW";
                btn.disabled = false;
            }
        } catch (err) {
            alert("Network error.");
            btn.querySelector(".btn-text").textContent = "REGISTER NOW";
            btn.disabled = false;
        }
    });
</script>


</body>

</html>