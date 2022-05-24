<?php
$filename = __DIR__ . "/data/articles.json";
$articles = [];
$categories = [];

if (file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? [];
    $catmap = array_map(fn ($a) => $a['category'], $articles);
    //je creer un tableau associatif qui a pour clés les categorie et pour valeur le nombre d'article
    $categories = array_reduce($catmap, function ($acc, $cat) {
        if (isset($acc[$cat])) {
            $acc[$cat]++;
        } else {
            $acc[$cat] = 1;
        }
        return $acc;
    }, []);
    //je creer un tableau associatif qui a pour cle les categories et pour valeur tout les article de cette categorie

    $articlesPerCategories = array_reduce($articles, function ($acc, $article) {
        if (isset($acc[$article['category']])) {
            $acc[$article['category']] = [...$acc[$article['category']], $article];
        } else {
            $acc[$article['category']] = [$article];
        }
        return $acc;
    }, []);

    // echo "<pre>";
    // print_r($articlesPerCategories);
    // echo "</pre>";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/index.css">
    <title>Blog APP</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="category-container">
                <?php foreach ($categories as $cat => $num) : ?>
                    <h2><?= $cat ?></h2>
                    <div class="articles-container">
                        <?php foreach ($articlesPerCategories[$cat] as $article) : ?>
                            <div class="article block">
                                <div class="overflow">
                                    <div class="img-container" style="background-image: url(<?= $article['image'] ?>);"></div>
                                </div>
                                <h3><?= $article['title'] ?></h3>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>