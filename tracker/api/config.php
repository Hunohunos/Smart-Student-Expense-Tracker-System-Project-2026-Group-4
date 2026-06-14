<?php
$host = 'sql209.infinityfree.com';
$db   = 'if0_42147623_tracker_system_db';
$user = 'if0_42147623';   // default XAMPP username
$pass = 'xbTx1CzqnOI3ECR';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Shared helper: send JSON and stop execution
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Shared helper: block unauthenticated requests
function requireAuth() {
    if (empty($_SESSION['id_user'])) {
        jsonResponse(['error' => 'Not authenticated'], 401);
    }
}