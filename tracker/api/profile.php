<?php
require 'config.php';
requireAuth();

$userId = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT name, email, budget FROM user WHERE id_user = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    $user['budget'] = (float) $user['budget'];
    jsonResponse($user);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Update name
    if (isset($data['name'])) {
        $name = trim($data['name']);
        if (!$name) jsonResponse(['error' => 'Name cannot be empty'], 400);
        $stmt = $pdo->prepare('UPDATE user SET name = ? WHERE id_user = ?');
        $stmt->execute([$name, $userId]);
        jsonResponse(['message' => 'Name updated']);
    }

    // Update password
    if (isset($data['newPassword'])) {
        $stmt = $pdo->prepare('SELECT password FROM user WHERE id_user = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!password_verify($data['currentPassword'], $row['password'])) {
            jsonResponse(['error' => 'Current password is incorrect'], 403);
        }

        $hash = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE id_user = ?');
        $stmt->execute([$hash, $userId]);
        jsonResponse(['message' => 'Password updated']);
    }
}