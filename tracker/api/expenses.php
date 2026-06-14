<?php
require 'config.php';
requireAuth();

$userId = $_SESSION['id_user'];
$isAdmin = ($_SESSION['user_types'] === 'admin');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Admin can fetch all expenses with ?admin=1
    if ($isAdmin && isset($_GET['admin'])) {
        $stmt = $pdo->query(
            'SELECT e.id_expense AS id,
                    e.name_expense AS title,
                    e.value_expense AS amount,
                    e.date_expense AS date,
                    c.name_categories AS category,
                    e.id_categories,
                    u.name AS user_name,
                    u.email AS user_email
             FROM expenses e
             JOIN categories c ON e.id_categories = c.id_categories
             JOIN user u ON e.user_expense = u.id_user
             ORDER BY e.date_expense DESC'
        );
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['date']   = date('c', (int) $row['date']);
            $row['amount'] = (float) $row['amount'];
        }
        jsonResponse($rows);
    }

    // Regular user: own expenses only
    $stmt = $pdo->prepare(
        'SELECT e.id_expense AS id,
                e.name_expense AS title,
                e.value_expense AS amount,
                e.date_expense AS date,
                c.name_categories AS category,
                e.id_categories
         FROM expenses e
         JOIN categories c ON e.id_categories = c.id_categories
         WHERE e.user_expense = ?
         ORDER BY e.date_expense DESC'
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['date']   = date('c', (int) $row['date']);
        $row['amount'] = (float) $row['amount'];
    }
    jsonResponse($rows);
}

if ($method === 'POST') {
    $data       = json_decode(file_get_contents('php://input'), true);
    $title      = trim($data['title'] ?? '');
    $amount     = (float) ($data['amount'] ?? 0);
    $categoryId = (int) ($data['id_categories'] ?? 0);
    $dateStr    = $data['date'] ?? '';
    $date       = $dateStr ? strtotime($dateStr) : time();

    if (!$title || $amount <= 0 || !$categoryId) {
        jsonResponse(['error' => 'Missing required fields'], 400);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO expenses (user_expense, name_expense, value_expense, date_expense, id_categories)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $amount, $date, $categoryId]);
    jsonResponse(['id' => $pdo->lastInsertId(), 'message' => 'Expense added']);
}

if ($method === 'PUT') {
    $data       = json_decode(file_get_contents('php://input'), true);
    $id         = (int) ($data['id'] ?? 0);
    $title      = trim($data['title'] ?? '');
    $amount     = (float) ($data['amount'] ?? 0);
    $categoryId = (int) ($data['id_categories'] ?? 0);
    $dateStr    = $data['date'] ?? '';
    $date       = $dateStr ? strtotime($dateStr) : time();

    $stmt = $pdo->prepare(
        'UPDATE expenses
         SET name_expense=?, value_expense=?, date_expense=?, id_categories=?
         WHERE id_expense=? AND user_expense=?'
    );
    $stmt->execute([$title, $amount, $date, $categoryId, $id, $userId]);
    jsonResponse(['message' => 'Expense updated']);
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int) ($data['id'] ?? 0);

    $stmt = $pdo->prepare('DELETE FROM expenses WHERE id_expense=? AND user_expense=?');
    $stmt->execute([$id, $userId]);
    jsonResponse(['message' => 'Expense deleted']);
}
