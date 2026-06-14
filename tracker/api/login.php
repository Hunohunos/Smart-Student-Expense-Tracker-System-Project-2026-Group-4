<?php
require 'config.php';

$data     = json_decode(file_get_contents('php://input'), true);
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['error' => 'Invalid email or password'], 401);
}

$_SESSION['id_user']    = $user['id_user'];
$_SESSION['email']      = $user['email'];
$_SESSION['user_types'] = $user['user_types'];

jsonResponse(['message' => 'Login successful', 'role' => $user['user_types']]);