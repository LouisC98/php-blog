<?php
require_once __DIR__ . '/database/database.php';
require_once __DIR__ . '/database/security.php';

$currentUser = isLoggedIn();

$articleDAO = require_once __DIR__ . '/database/models/ArticleDAO.php';

$articles = [];

if (!$currentUser) {
    header('Location: /auth-login.php');
}

$articles = $articleDAO->fetchUserArticles($currentUser['id']);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/profile.css">
    <title>Ma page</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <h1>Mon espace</h1>
            <h2>Mes informations</h2>
            <div class="info-container">
                <ul>
                    <li>
                        <strong>Prénom: </strong>
                        <p><?= $currentUser['firstname'] ?></p>
                    </li>
                    <li>
                        <strong>Nom: </strong>
                        <p><?= $currentUser['lastname'] ?></p>
                    </li>
                    <li>
                        <strong>Email: </strong>
                        <p><?= $currentUser['email'] ?></p>
                    </li>
                </ul>
            </div>
            <h2>Mes articles</h2>
            <div class="articles-list">
                <ul>
                    <?php foreach ($articles as $article) : ?>
                        <li>
                            <span><?= substr($article['title'], 0, 100) . ' ...' ?></span>
                            <div class="article-action">
                                <a class="btn btn-small" href="/delete-article.php?id=<?= $article['id'] ?>">Supprimer</a>
                                <a class="btn btn-primary" href="/form-article.php?id=<?= $article['id'] ?>">Éditer</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>