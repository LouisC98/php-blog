<?php
        require_once __DIR__ . '/database/database.php';
        require_once __DIR__ . '/database/security.php';
        $currentUser = isLoggedIn();

        if (!$currentUser) {
            header('Location: /');
        }



        /**
         * @var ArticleDAO
         */

        // $pdo = require_once './database.php';
        // $statementCreateOne = $pdo->prepare('
        //     INSERT INTO article (title, category, content, image) VALUES (:title, :category, :content, :image)
        // ');
        // $statementUpdateOne = $pdo->prepare('
        //     UPDATE article SET title=:title, category=:category, content=:content, image=:image WHERE id=:id
        // ');
        // $statementReadOne = $pdo->prepare('SELECT * FROM article WHERE id=:id');

        $articleDAO = require_once './database/models/ArticleDAO.php';


const ERROR_REQUIRED = "Veuillez renseigner ce champ";
const ERROR_TITLE_TOO_SHORT = "Le titre est trop court";
const ERROR_CONTENT_TOO_SHORT = "L'article est trop court";
const ERROR_IMAGE_URL = "L'image doit etre une URL valide";

// $filename = __DIR__ . '/data/articles.json';
// $articles = [];
$category = '';


$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];

// if (file_exists($filename)) {
//     $articles = json_decode(file_get_contents($filename), true) ?? [];
// }

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if ($id) {
    // $articleIdx = array_search($id, array_column($articles, 'id'));
    // $article = $articles[$articleIdx];

    // $statementReadOne->bindValue(':id', $id);
    // $statementReadOne->execute();
    // $article = $statementReadOne->fetch();

    $article = $articleDAO->getOne($id);

    if ($article['author'] !== $currentUser['id']) {
        header('Location: /');
    }

    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_POST = filter_input_array(INPUT_POST, [
        'title' => FILTER_SANITIZE_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content' => [
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'flag' => FILTER_FLAG_NO_ENCODE_QUOTES
        ]
    ]);

    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? '';
    $category = $_POST['category'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!$title) {
        $errors['title'] = ERROR_REQUIRED;
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = ERROR_TITLE_TOO_SHORT;
    }

    if (!$image) {
        $errors['image'] = ERROR_REQUIRED;
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors['image'] = ERROR_IMAGE_URL;
    }

    if (!$category) {
        $errors['category'] = ERROR_REQUIRED;
    }

    if (!$content) {
        $errors['content'] = ERROR_REQUIRED;
    } elseif (mb_strlen($content) < 20) {
        $errors['content'] = ERROR_CONTENT_TOO_SHORT;
    }

    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {

        if ($id) {
            //on réécris l'article dans la database avec les nouvelles données
            // $articles['title'] = $title;
            // $articles['image'] = $image;
            // $articles['category'] = $category;
            // $articles['content'] = $content;

            // $statementUpdateOne->bindValue(':title', $title);
            // $statementUpdateOne->bindValue(':category', $category);
            // $statementUpdateOne->bindValue(':image', $image);
            // $statementUpdateOne->bindValue(':content', $content);
            // $statementUpdateOne->bindValue(':id', $id);
            // $statementUpdateOne->execute();

            $articleDAO->updateOne([
                'title' => $title,
                'category' => $category,
                'content' => $content,
                'image' => $image,
                'id' => $id,
                'author' => $currentUser['id']
            ]);
        } else {
            //On creer un nouvelle article
            // $articles = [...$articles, [
            //     'title' => $title,
            //     'image' => $image,
            //     'category' => $category,
            //     'content' => $content,
            //     'id' => time()
            // ]];

            // $statementCreateOne->bindValue(':title', $title);
            // $statementCreateOne->bindValue(':category', $category);
            // $statementCreateOne->bindValue(':image', $image);
            // $statementCreateOne->bindValue(':content', $content);
            // $statementCreateOne->execute();

            $articleDAO->createOne([
                'title' => $title,
                'category' => $category,
                'content' => $content,
                'image' => $image,
                'author' => $currentUser['id']
            ]);
        }

        // file_put_contents($filename, json_encode($articles));
        header('Location: /');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/form-article.css">
    <title>
        <?= $id ? 'Éditer' : 'Créer' ?> un article
    </title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php'  ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1><?= $id ? 'Modifier' : 'Écrire' ?> un article</h1>
                <form action="/form-article.php<?= $id ? "?id=$id" : '' ?>" method="POST">
                    <div class="form-control">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="<?= $title ?? '' ?>">
                        <?php if ($errors['title']) : ?>
                            <p class="text-danger"><?= $errors['title'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value="<?= $image ?? '' ?>">
                        <?php if ($errors['image']) : ?>
                            <p class="text-danger"><?= $errors['image'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="category">Categorie</label>
                        <select name="category" id="category" value>
                            <option <?= !$category || $category === 'Technologie' ? 'selected' : '' ?> value="Technologie">Technologie</option>
                            <option <?= !$category || $category === 'Nature' ? 'selected' : '' ?> value="Nature">Nature</option>
                            <option <?= !$category || $category === 'Politique' ? 'selected' : '' ?> value="Politique">Politique</option>
                        </select>
                        <?php if ($errors['category']) : ?>
                            <p class="text-danger"><?= $errors['category'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="content">Contenu</label>
                        <textarea name="content" id="content"><?= $content ?? '' ?></textarea>
                        <?php if ($errors['content']) : ?>
                            <p class="text-danger"><?= $errors['content'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-action">
                        <a href="/" type="button" class="btn btn-secondary">Annuler</a>
                        <button class="btn btn-primary" type="submit"><?= $id ? 'Sauvegarder' : 'Publier' ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php require_once 'includes/footer.php'  ?>
    </div>
</body>

</html>