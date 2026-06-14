<?php
require 'config.php';

if (empty($_SESSION['id_user'])) {
    jsonResponse(['loggedIn' => false]);
}

$stmt = $pdo->prepare('SELECT id_user, name, email, user_types, budget FROM user WHERE id_user = ?');
$stmt->execute([$_SESSION['id_user']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    jsonResponse(['loggedIn' => false]);
}

jsonResponse([
    'loggedIn' => true,
    'user' => [
        'id'    => $user['id_user'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['user_types'],
        'budget'=> (float) $user['budget'],
    ]
]);