<header>
    <a href="/" class="logo">Blog APP</a>
    <ul class="header-menu">
        <li class="<?= $_SERVER['REQUEST_URI'] === '/add-article.php' ? 'active' : '' ?>">
            <a href="/add-article.php">Écrire un article</a>
        </li>
    </ul>
</header>