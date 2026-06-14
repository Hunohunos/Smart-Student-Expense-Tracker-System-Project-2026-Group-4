<?php
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Public: no auth needed — dropdown must load on expenses page
    $stmt = $pdo->query('SELECT id_categories AS id, name_categories AS name FROM categories ORDER BY name_categories');
    jsonResponse($stmt->fetchAll());
}

// Add / Delete requires any logged-in user (not admin-only)
requireAuth();

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    if (!$name) {
        jsonResponse(['error' => 'Category name is required'], 400);
    }
    // Check for duplicate
    $stmt = $pdo->prepare('SELECT id_categories FROM categories WHERE name_categories = ?');
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        jsonResponse(['error' => 'Category already exists'], 409);
    }
    $stmt = $pdo->prepare('INSERT INTO categories (name_categories) VALUES (?)');
    $stmt->execute([$name]);
    jsonResponse(['id' => (int)$pdo->lastInsertId(), 'name' => $name, 'message' => 'Category added']);
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int)($data['id'] ?? 0);
    if (!$id) {
        jsonResponse(['error' => 'Category ID required'], 400);
    }
    // Prevent deleting a category that is in use
    $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM expenses WHERE id_categories = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ((int)$row['cnt'] > 0) {
        jsonResponse(['error' => 'Cannot delete: category is used by existing expenses'], 409);
    }
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id_categories = ?');
    $stmt->execute([$id]);
    jsonResponse(['message' => 'Category deleted']);
}
