<?php
require 'config.php';
requireAuth();

$userId = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT budget FROM user WHERE id_user = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    jsonResponse(['budget' => (float) $row['budget']]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $budget = (float) ($data['budget'] ?? 0);

    if ($budget <= 0) {
        jsonResponse(['error' => 'Budget must be greater than zero'], 400);
    }

    $stmt = $pdo->prepare('UPDATE user SET budget = ? WHERE id_user = ?');
    $stmt->execute([$budget, $userId]);
    jsonResponse(['message' => 'Budget updated']);
}