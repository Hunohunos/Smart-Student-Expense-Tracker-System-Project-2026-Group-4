<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data    = json_decode(file_get_contents('php://input'), true);
    $name    = trim($data['name'] ?? '');
    $email   = trim($data['email'] ?? '');
    $message = trim($data['message'] ?? '');

    if (!$name || !$email || !$message) {
        jsonResponse(['error' => 'All feedback fields are required'], 400);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO feedback (name_feedback, email_feedback, message_feedback, date_feedback)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $message, time()]);
    jsonResponse(['message' => 'Feedback submitted. Thank you!']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    requireAuth();
    if ($_SESSION['user_types'] !== 'admin') {
        jsonResponse(['error' => 'Forbidden'], 403);
    }

    $stmt = $pdo->query(
        'SELECT name_feedback AS name,
                email_feedback AS email,
                message_feedback AS message,
                date_feedback AS date
         FROM feedback
         ORDER BY date_feedback DESC'
    );
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['date'] = date('Y-m-d', (int) $row['date']);
    }
    jsonResponse($rows);
}