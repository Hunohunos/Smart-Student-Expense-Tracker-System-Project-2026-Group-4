<?php
require 'config.php';

$data     = json_decode(file_get_contents('php://input'), true);
$name     = trim($data['name'] ?? '');
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    jsonResponse(['error' => 'All fields are required'], 400);
}

// Password strength validation
if (strlen($password) < 8) {
    jsonResponse(['error' => 'Password must be at least 8 characters long'], 400);
}
if (!preg_match('/[A-Z]/', $password)) {
    jsonResponse(['error' => 'Password must contain at least one uppercase letter'], 400);
}
if (!preg_match('/[a-z]/', $password)) {
    jsonResponse(['error' => 'Password must contain at least one lowercase letter'], 400);
}
if (!preg_match('/[0-9]/', $password)) {
    jsonResponse(['error' => 'Password must contain at least one number'], 400);
}
if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    jsonResponse(['error' => 'Password must contain at least one symbol (e.g. @, #, !, $)'], 400);
}

// Check if email already exists
$stmt = $pdo->prepare('SELECT id_user FROM user WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonResponse(['error' => 'Email already registered'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare(
    'INSERT INTO user (name, email, password, user_types, budget) VALUES (?, ?, ?, ?, 1000.00)'
);
$stmt->execute([$name, $email, $hash, 'user']);

$newId = $pdo->lastInsertId();
$_SESSION['id_user']    = $newId;
$_SESSION['email']      = $email;
$_SESSION['user_types'] = 'user';

jsonResponse(['message' => 'Registered successfully', 'role' => 'user']);