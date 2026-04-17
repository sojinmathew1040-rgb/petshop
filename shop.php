<?php
require_once 'db.php';
include 'header.php';

$shop_products = $pdo->query("
    SELECT p.*, 
    (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) as main_image 
    FROM products p 
    ORDER BY id DESC
")->fetchAll();

$wishlist_items = $_SESSION['wishlist'] ?? [];
?>

<style>
    /* Apple-Level Pro style overrides for shop.php */
    body {
        background: #fbfbfd;
        padding-top: 64px;
    }

    .shop-hero {
        text-align: center;
        padding: 80px 20px 40px;
    }

    .shop-hero h1 {
        font-size: 56px;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #1d1d1f;
        margin-bottom: 10px;
    }

    .shop-hero p {
        font-size: 21px;
        font-weight: 500;
        color: #86868b;
        letter-spacing: -0.01em;
    }

    .shop-wrapper {
        max-width: 1400px;
        margin: 40px auto 80px auto;
        padding: 0 40px;
        display: flex;
        gap: 60px;
        align-items: flex-start;
    }

    .sidebar {
        width: 260px;
        flex-shrink: 0;
        position: sticky;
        top: 100px;
    }

    .sidebar h3 {
        font-size: 19px;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 20px;
        letter-spacing: -0.01em;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 40px;
    }

    /* Custom Checkmark matching Apple forms */
    .custom-checkbox {
        position: relative;
        padding-left: 32px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        color: #86868b;
        transition: color 0.3s;
        user-select: none;
        display: flex;
        align-items: center;
    }

    .custom-checkbox:hover {
        color: #1d1d1f;
    }

    .custom-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 20px;
        width: 20px;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 6px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .custom-checkbox input:checked~.checkmark {
        background-color: #1d1d1f;
        border-color: #1d1d1f;
        box-shadow: none;
    }

    .custom-checkbox input:checked~.checkmark:after {
        display: block;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 7px;
        top: 3px;
        width: 4px;
        height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .shop-content {
        flex: 1;
        min-width: 0;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .pro-card {
        background: #fff;
        padding: 30px;
        border-radius: 32px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pro-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.08);
    }

    .pro-image {
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
    }

    .pro-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }

    .pro-card:hover .pro-image img {
        transform: scale(1.05);
    }

    .pro-info {
        text-align: center;
    }

    .pro-info h4 {
        font-size: 20px;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
        letter-spacing: -0.01em;
    }

    .pro-info .price {
        font-size: 17px;
        font-weight: 500;
        color: #86868b;
    }

    .pro-info .old-price {
        text-decoration: line-through;
        margin-right: 8px;
        color: #ccc;
    }

    .add-btn {
        width: 100%;
        background: #f5f5f7;
        color: #1d1d1f;
        border: none;
        padding: 14px 0;
        border-radius: 980px;
        font-weight: 600;
        font-size: 15px;
        margin-top: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .pro-card:hover .add-btn {
        background: #1d1d1f;
        color: #fff;
    }

    .add-btn[disabled] {
        background: #f5f5f7;
        color: #ccc;
        cursor: not-allowed;
    }

    .wish-icon {
        position: absolute;
        top: 25px;
        right: 25px;
        z-index: 10;
        font-size: 20px;
        cursor: pointer;
        transition: transform 0.3s;
        opacity: 0.8;
    }

    .wish-icon:hover {
        transform: scale(1.2);
        opacity: 1;
    }

    .wish-icon.active {
        color: #ff3b30;
        opacity: 1;
    }

    .pro-badge {
        position: absolute;
        top: 25px;
        left: 25px;
        background: #eefaf0;
        color: #28a745;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media(max-width: 992px) {
        .shop-wrapper {
            flex-direction: column;
            padding: 0 20px;
        }

        .sidebar {
            width: 100%;
            position: static;
        }

        .shop-hero h1 {
            font-size: 40px;
        }
    }
</style>

<div class="shop-hero">
    <div
        style="display: inline-flex; align-items: center; justify-content: center; gap: 12px; background: rgba(0,0,0,0.03); padding: 8px 20px; border-radius: 100px; font-size: 13px; font-weight: 500; letter-spacing: 0.02em; margin-bottom: 24px; border: 1px solid rgba(0,0,0,0.05); user-select: none;">
        <a href="index.php"
            style="color: #86868b; text-decoration: none; display: flex; align-items: center; transition: color 0.3s;"
            onmouseover="this.style.color='#1d1d1f'" onmouseout="this.style.color='#86868b'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Home
        </a>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d2d2d7" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
        <span style="color: #1d1d1f; font-weight: 600;">Shop</span>
    </div>
    <h1>Our Collection.</h1>
    <p>Discover the finest kennels and premium accessories.</p>
</div>

<div class="shop-wrapper">
    <aside class="sidebar">
        <h3>Categories</h3>
        <div class="filter-group">
            <label class="custom-checkbox"><input type="checkbox" class="cat-filter" value="all" checked><span
                    class="checkmark"></span>All Products</label>
            <label class="custom-checkbox"><input type="checkbox" class="cat-filter" value="dog"><span
                    class="checkmark"></span>Dog Kennels</label>
            <label class="custom-checkbox"><input type="checkbox" class="cat-filter" value="cat"><span
                    class="checkmark"></span>Cat Houses</label>
            <label class="custom-checkbox"><input type="checkbox" class="cat-filter" value="bird"><span
                    class="checkmark"></span>Bird Cages</label>
            <label class="custom-checkbox"><input type="checkbox" class="cat-filter" value="accessories"><span
                    class="checkmark"></span>Accessories</label>
        </div>

        <h3>Price Range</h3>
        <div class="filter-group">
            <label class="custom-checkbox"><input type="checkbox" class="price-filter" value="under-2000"><span
                    class="checkmark"></span>Under ₹2000</label>
            <label class="custom-checkbox"><input type="checkbox" class="price-filter" value="2000-5000"><span
                    class="checkmark"></span>₹2000 - ₹5000</label>
            <label class="custom-checkbox"><input type="checkbox" class="price-filter" value="5000-10000"><span
                    class="checkmark"></span>₹5000 - ₹10000</label>
            <label class="custom-checkbox"><input type="checkbox" class="price-filter" value="over-10000"><span
                    class="checkmark"></span>Over ₹10000</label>
        </div>
    </aside>

    <div class="shop-content">
        <div class="product-grid">
            <?php foreach ($shop_products as $p):
                $in_wishlist = in_array($p['id'], $wishlist_items);
                $price_cat = 'under-2000';
                if ($p['price'] >= 2000 && $p['price'] <= 5000)
                    $price_cat = '2000-5000';
                elseif ($p['price'] > 5000 && $p['price'] <= 10000)
                    $price_cat = '5000-10000';
                elseif ($p['price'] > 10000)
                    $price_cat = 'over-10000';
                ?>
                <div class="pro-card" data-cat="<?= htmlspecialchars($p['category']) ?>" data-price="<?= $price_cat ?>"
                    onclick="window.location='product.php?id=<?= $p['id'] ?>'">

                    <div class="wish-icon <?= $in_wishlist ? 'active' : '' ?>"
                        onclick="toggleWishlist(event, <?= $p['id'] ?>, this)">
                        <?= $in_wishlist ? '❤️' : '🤍' ?>
                    </div>
                    <?php if ($p['badge']): ?>
                        <span class="pro-badge"><?= htmlspecialchars($p['badge']) ?></span>
                    <?php endif; ?>

                    <div class="pro-image">
                        <img src="<?= htmlspecialchars($p['main_image'] ?? 'assets/images/16.jpeg') ?>"
                            alt="<?= htmlspecialchars($p['title']) ?>">
                    </div>

                    <div class="pro-info">
                        <h4><?= htmlspecialchars($p['title']) ?></h4>
                        <div class="rating" style="color: #d6a86c; font-size: 14px; margin-bottom: 8px;">
                            <?= str_repeat('★', $p['rating']) . str_repeat('☆', 5 - $p['rating']) ?>
                        </div>
                        <p class="price">
                            <?php if ($p['old_price']): ?><span
                                    class="old-price">₹<?= number_format($p['old_price'], 2) ?></span><?php endif; ?>
                            ₹<?= number_format($p['price'], 2) ?>
                        </p>
                        <button class="add-btn" <?= $p['stock_status'] == 'Sold Out' ? 'disabled' : '' ?>
                            onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>)">
                            <?= $p['stock_status'] == 'Sold Out' ? 'Sold Out' : 'Add to Bag' ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const catFilters = document.querySelectorAll(".cat-filter");
        const priceFilters = document.querySelectorAll(".price-filter");
        const products = document.querySelectorAll(".pro-card");

        function filterProducts() {
            let activeCats = Array.from(catFilters).filter(cb => cb.checked).map(cb => cb.value);
            let activePrices = Array.from(priceFilters).filter(cb => cb.checked).map(cb => cb.value);

            const showAllCats = activeCats.includes("all") || activeCats.length === 0;
            const ignorePrice = activePrices.length === 0;

            products.forEach(p => {
                const productCat = p.getAttribute("data-cat") || "";
                const productPrice = p.getAttribute("data-price") || "";

                let catMatch = showAllCats ? true : activeCats.some(cat => productCat.includes(cat));
                let priceMatch = ignorePrice ? true : activePrices.includes(productPrice);

                p.style.display = (catMatch && priceMatch) ? "flex" : "none";
            });
        }

        catFilters.forEach(cb => cb.addEventListener("change", function () {
            if (this.value === "all" && this.checked) {
                catFilters.forEach(f => { if (f.value !== "all") f.checked = false; });
            } else if (this.value !== "all" && this.checked) {
                document.querySelector(".cat-filter[value='all']").checked = false;
            }
            filterProducts();
        }));

        priceFilters.forEach(cb => cb.addEventListener("change", filterProducts));
    });
</script>

<?php include 'footer.php'; ?>