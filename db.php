<?php
// db.php
session_start();

$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = '';
$mysql_dbname = 'waggy_db';

try {
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_dbname;charset=utf8mb4", $mysql_user, $mysql_pass);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper to check logged in admin
function isAdmin()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>