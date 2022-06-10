<?php

$pdo = require_once __DIR__ . '/database/database.php';
$authDAO = require_once './database/security.php';

$sessionId = $_COOKIE['session'];

if ($sessionId) {
    // $statement = $pdo->prepare('DELETE FROM session WHERE id=:id');
    // $statement->bindValue(':id', $sessionId);
    // $statement->execute();
    // setcookie('session', '', time() - 1);

    $authDAO->logout($sessionId);

    header('Location: /');
}
