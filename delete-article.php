<?php

// $filename = __DIR__ . "/data/articles.json";
// $articles = [];

/**
 * @var PDO
 */

$pdo = require_once './database.php';
$statement = $pdo->prepare('DELETE FROM article WHERE id=:id');

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {
    $statement->bindValue(':id', $id);
    $statement->execute();
}
// else {
//     if (file_exists($filename)) {
//         $articles = json_decode(file_get_contents($filename), true) ?? [];
//         $articleIdx = array_search($id, array_column($articles, 'id'));
//         array_splice($articles, $articleIdx, 1);
//         file_put_contents($filename, json_encode($articles));
//         header('Location: /');
//     }
// }
header('Location: /');
