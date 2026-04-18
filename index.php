<?php
require_once 'db.php';
include 'header.php';

// Fetch hero slides
$hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
if (empty($hero_slides)) {
    $hero_slides = [['offer_text' => 'SAVE 10 - 20 % OFF', 'title_line1' => 'Best Destination', 'title_line2' => 'Your Pets', 'button_text' => 'SHOP NOW →', 'button_link' => 'shop.php', 'image_path' => 'assets/images/12.jpeg']];
}

// Fetch Categories
$home_categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();

// Fetch Deal of the Day
$deal_of_the_day = $pdo->query("
    SELECT d.end_time, p.*,
    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) as main_image 
    FROM deal_of_the_day d 
    JOIN products p ON d.product_id = p.id 
    WHERE d.end_time > NOW()
    LIMIT 1
")->fetch();

// Fetch Trending Products
$trending_products = $pdo->query("
    SELECT p.*, 
    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) as main_image 
    FROM products p 
    WHERE p.is_trending = 1
    ORDER BY id DESC LIMIT 4
")->fetchAll();

// Fetch Testimonials
$home_testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3")->fetchAll();

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

<!-- CATEGORIES SECTION -->
<section class="category-section fade-in">
    <div class="category-header" style="text-align: center; margin-bottom: 40px;">
        <h2 style="font-size: 36px; font-weight: 600; color: #1d1d1f;">Shop by Category</h2>
    </div>
    <div class="category-grid">
        <?php foreach ($home_categories as $cat): ?>
            <div class="category-card" onclick="window.location='shop.php?category=<?= urlencode($cat['name']) ?>'">
                <img src="<?= htmlspecialchars($cat['image_path'] ?? 'assets/images/placeholder.jpg') ?>"
                    alt="<?= htmlspecialchars($cat['name']) ?>">
                <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- DEAL OF THE DAY -->
<?php if ($deal_of_the_day): ?>
    <section class="deal-section fade-in">
        <div class="deal-container">
            <div class="deal-image-container">
                <img src="<?= htmlspecialchars($deal_of_the_day['main_image'] ?? 'assets/images/placeholder.jpg') ?>"
                    alt="<?= htmlspecialchars($deal_of_the_day['title']) ?>">
                <div class="deal-badge-hot">SALE</div>
            </div>
            <div class="deal-content">
                <h3 class="deal-subtitle">DEAL OF THE DAY</h3>
                <h2 class="deal-title"><?= htmlspecialchars($deal_of_the_day['title']) ?></h2>
                <div class="deal-price">
                    <span class="old-price">₹<?= number_format((float) $deal_of_the_day['old_price'], 2) ?></span>
                    <span class="new-price">₹<?= number_format((float) $deal_of_the_day['price'], 2) ?></span>
                </div>

                <div class="deal-timer" data-endtime="<?= htmlspecialchars($deal_of_the_day['end_time']) ?>">
                    <div class="time-box"><span class="days">00</span><small>Days</small></div>
                    <div class="time-box"><span class="hours">00</span><small>Hrs</small></div>
                    <div class="time-box"><span class="minutes">00</span><small>Mins</small></div>
                    <div class="time-box"><span class="seconds">00</span><small>Secs</small></div>
                </div>

                <button class="deal-btn-pro" onclick="window.location='product.php?id=<?= $deal_of_the_day['id'] ?>'">Shop
                    Deal Now</button>
            </div>
        </div>
    </section>
<?php endif; ?>

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
<!-- TRENDING PRODUCTS -->
<?php if (!empty($trending_products)): ?>
    <section class="product-section fade-in" style="background: transparent; box-shadow: none; padding-top:0;">
        <div class="product-header">
            <h2>Trending Best Sellers</h2>
            <a href="shop.php" class="shop-now-btn" style="background:#f5f5f7; color:#1d1d1f; border:none;">View All →</a>
        </div>

        <div class="product-grid p-grid-4">
            <?php foreach ($trending_products as $p):
                $in_wishlist = in_array($p['id'], $wishlist_items);
                ?>
                <div class="product-card" onclick="window.location='product.php?id=<?= $p['id'] ?>'">
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
                                <span class="old-price">₹<?= number_format((float) $p['old_price'], 2) ?></span>
                            <?php endif; ?>
                            ₹<?= number_format((float) $p['price'], 2) ?>
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
<?php endif; ?>

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

<!-- TESTIMONIALS SECTION -->
<?php if (!empty($home_testimonials)): ?>
    <section class="testimonial-section fade-in">
        <div style="text-align:center; margin-bottom:50px;">
            <h2 style="font-size:36px; font-weight:600; color:#1d1d1f;">Happy Pets, Happy Owners</h2>
            <p style="color:#666; margin-top:10px;">Don't just take our word for it.</p>
        </div>
        <div class="testimonial-grid">
            <?php foreach ($home_testimonials as $t): ?>
                <div class="testimonial-card">
                    <div class="test-rating" style="color:#f5b301; font-size:20px; margin-bottom:15px;">
                        <?= str_repeat('★', $t['rating']) ?>
                    </div>
                    <p class="test-quote"
                        style="font-size:16px; color:#444; line-height:1.6; font-style:italic; margin-bottom:20px;">
                        "<?= htmlspecialchars($t['quote']) ?>"</p>
                    <div class="test-user" style="display:flex; align-items:center; gap:15px;">
                        <img src="<?= htmlspecialchars($t['image_path'] ?? 'assets/images/user-placeholder.jpg') ?>" alt="User"
                            style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
                        <div class="test-info">
                            <h4 style="font-size:16px; font-weight:600; color:#1d1d1f; margin:0;">
                                <?= htmlspecialchars($t['customer_name']) ?>
                            </h4>
                            <span style="font-size:13px; color:#888;">Verified Buyer</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

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

    /* ===== DEAL TIMER ===== */
    const dealTimer = document.querySelector('.deal-timer');
    if (dealTimer) {
        const endTime = new Date(dealTimer.dataset.endtime).getTime();

        const updateTimer = setInterval(() => {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                clearInterval(updateTimer);
                dealTimer.innerHTML = "<div style='font-weight:600; color:#ff3b30;'>Deal Expired</div>";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            dealTimer.querySelector('.days').innerText = String(days).padStart(2, '0');
            dealTimer.querySelector('.hours').innerText = String(hours).padStart(2, '0');
            dealTimer.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
            dealTimer.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
        }, 1000);
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