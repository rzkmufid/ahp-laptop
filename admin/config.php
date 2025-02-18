<?php
// config.php
$host = 'localhost';
$dbname = 'laptop_ecommerce';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Start session for admin authentication
session_start();

// Function to check if admin is logged in
function isLoggedIn() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
};

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}