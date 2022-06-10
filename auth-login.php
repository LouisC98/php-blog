<?php

$pdo = require_once './database/database.php';
$authDAO = require_once './database/security.php';

const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_EMAIL_INVALID = "L'email n'est pas valide";
const ERROR_EMAIL_UNKNOWN = "L'email n'est pas enregistré";
const ERROR_PASSWORD_MISMATCH = "Le mot de passe n'est pas valide";


$errors = [

    'email' => '',
    'password' => '',

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = filter_input_array(INPUT_POST, [
        'email' => FILTER_SANITIZE_EMAIL,
        'password' => ''
    ]);



    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';


    if (!$email) {
        $errors['email'] = ERROR_REQUIRED;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = ERROR_EMAIL_INVALID;
    }

    if (!$password) {
        $errors['password'] = ERROR_REQUIRED;
    }


    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {

        // $statementUser = $pdo->prepare('SELECT * FROM user WHERE email=:email');
        // $statementUser->bindValue(':email', $email);
        // $statementUser->execute();
        // $user = $statementUser->fetch();

        $user = $authDAO->getUserFromEmail($email);

        if (!$user) {
            $errors['email'] = ERROR_EMAIL_UNKNOWN;
        } else {
            if (!password_verify($password, $user['password'])) {
                $errors['password'] = ERROR_PASSWORD_MISMATCH;
            } else {
                // $statementSession = $pdo->prepare('INSERT INTO session VALUES (DEFAULT, :userid)');
                // $statementSession->bindValue(':userid', $user['id']);
                // $statementSession->execute();

                // // Récupération de l'id de la session que l'on vient d'enregistrer dans la BDD
                // $sessionId = $pdo->lastInsertId();

                // // creer notre cookie
                // setcookie('session', $sessionId, time() + 60 * 60 * 24 * 14, '', '', false, true);

                $authDAO->login($user['id']);

                header('Location: /');
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <link rel="stylesheet" href="public/css/login.css">
    <title>Connexion</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1>Connexion</h1>
                <form action="/auth-login.php" method="POST">

                    <div class="form-control">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?= $email ?? '' ?>">
                        <?php if ($errors['email']) : ?>
                            <p class="text-danger"><?= $errors['email'] ?></p>
                        <?php endif ?>
                    </div>

                    <div class="form-control">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" value="<?= $password ?? '' ?>">
                        <?php if ($errors['password']) : ?>
                            <p class="text-danger"><?= $errors['password'] ?></p>
                        <?php endif ?>
                    </div>

                    <div class="form-action">
                        <a href="/" type="button" class="btn btn-secondary">Annuler</a>
                        <button class="btn btn-primary" type="submit">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>