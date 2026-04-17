<?php
// init_db.php
$db_file = __DIR__ . '/data/database.sqlite';

// create dir if not exists
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}
if (!is_dir(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0777, true);
}

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Admins
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )");

    // Insert default admin: admin / admin123
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admins (username, password) VALUES ('admin', '$hash')");
    }

    // 2. Hero Slides
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        offer_text TEXT,
        title_line1 TEXT,
        title_line2 TEXT,
        button_text TEXT,
        button_link TEXT,
        image_path TEXT,
        sort_order INTEGER DEFAULT 0
    )");

    // Insert dummy slides if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM hero_slides");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO hero_slides (offer_text, title_line1, title_line2, button_text, button_link, image_path, sort_order) VALUES ('SAVE 10 - 20 % OFF', 'Best Destination', 'Your Pets', 'SHOP NOW →', 'shop.php', 'assets/images/12.jpeg', 1)");
        $pdo->exec("INSERT INTO hero_slides (offer_text, title_line1, title_line2, button_text, button_link, image_path, sort_order) VALUES ('NEW COLLECTION', 'Comfort Living', 'Your Dogs', 'SHOP NOW →', 'shop.php', 'assets/images/13.jpeg', 2)");
        $pdo->exec("INSERT INTO hero_slides (offer_text, title_line1, title_line2, button_text, button_link, image_path, sort_order) VALUES ('LIMITED DEAL', 'Premium Homes', 'Your Pets', 'SHOP NOW →', 'shop.php', 'assets/images/14.jpeg', 3)");
    }

    // 3. Products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL,
        old_price REAL DEFAULT NULL,
        category TEXT,
        badge TEXT,
        rating INTEGER DEFAULT 5,
        stock_status TEXT DEFAULT 'In Stock',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert dummy product if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO products (title, description, price, old_price, category, badge) VALUES ('Grey Hoodle', 'Give your pet the ultimate comfort.', 18.00, 25.00, 'dog', 'new')");
        // product id = 1
    }

    // 4. Product Images (for 3D viewer)
    $pdo->exec("CREATE TABLE IF NOT EXISTS product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER,
        image_path TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    $stmt = $pdo->query("SELECT COUNT(*) FROM product_images");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO product_images (product_id, image_path, sort_order) VALUES (1, 'assets/images/16.jpeg', 1)");
    }

    echo "Database initialized successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>