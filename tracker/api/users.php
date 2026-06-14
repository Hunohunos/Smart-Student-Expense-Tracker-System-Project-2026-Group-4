<?php
require 'config.php';
requireAuth();

if ($_SESSION['user_types'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query(
        'SELECT u.id_user AS id,
                u.name,
                u.email,
                u.user_types AS role,
                u.budget,
                COUNT(e.id_expense) AS expense_count
         FROM user u
         LEFT JOIN expenses e ON e.user_expense = u.id_user
         GROUP BY u.id_user
         ORDER BY u.id_user DESC'
    );
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['id']            = (int)   $row['id'];
        $row['budget']        = (float) $row['budget'];
        $row['expense_count'] = (int)   $row['expense_count'];
    }
    jsonResponse($rows);
}

// Admin editing a user's name or password
if ($method === 'PUT') {
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (int) ($data['id'] ?? 0);
    $name   = trim($data['name'] ?? '');
    $newPwd = $data['password'] ?? '';

    if ($name) {
        $stmt = $pdo->prepare('UPDATE user SET name = ? WHERE id_user = ?');
        $stmt->execute([$name, $id]);
    }
    if ($newPwd) {
        $hash = password_hash($newPwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE id_user = ?');
        $stmt->execute([$hash, $id]);
    }
    jsonResponse(['message' => 'User updated']);
}

// Admin deleting a user
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($data['id'] ?? 0);

    if (!$id) {
        jsonResponse(['error' => 'Invalid user ID'], 400);
    }

    try {
        // Delete the user's expenses first to satisfy the FK constraint
        // (expenses.user_expense references user.id_user and has no ON DELETE CASCADE)
        $stmt = $pdo->prepare('DELETE FROM expenses WHERE user_expense = ?');
        $stmt->execute([$id]);

        $stmt = $pdo->prepare('DELETE FROM user WHERE id_user = ?');
        $stmt->execute([$id]);

        jsonResponse(['message' => 'User deleted']);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Could not delete user: ' . $e->getMessage()], 500);
    }
}