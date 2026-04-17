<?php
// db.php
session_start();

$db_file = __DIR__ . '/data/database.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper to check logged in admin
function isAdmin()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>