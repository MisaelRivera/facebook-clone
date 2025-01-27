<?php
    include 'database/connection.php';
    include 'classes/users.php';
    include 'classes/posts.php';

    global $pdo;

    $loadFromUser = new User($pdo);
    $loadFromPost = new Post($pdo);

    define('BASE_URL', 'http://localhost/facebook');
?>