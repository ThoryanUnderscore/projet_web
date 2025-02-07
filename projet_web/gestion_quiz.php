<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user']; // Contient les infos de l'utilisateur connecté
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire des quiz</title>
    <link rel="stylesheet" type="text/css" href="./public/css/styles.css">
    <link rel="stylesheet" href="public/css/navbar.css">
</head>
<body>

<nav>
    <div class="nav-left">
        <a href="dashboard.php" class="logo">
            <img src="public/images/quizzeo_sansfond.png" alt="Logo">
        </a>
    </div>

    <div class="nav-right">
        <?php if ($user['type'] !== 'user'): ?>
            <a href="gestion_quiz.php" class="profile">Gestion des Quiz</a>
        <?php endif; ?>
        <a href="logout.php" class="logout">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h1>Gestionnaire des quiz</h1>

    <h2>Ajouter un Quiz</h2>
    <form action="server.php" method="POST">
        <input type="text" name="title" placeholder="Titre du quiz" required>
        <button type="submit" name="addQuiz">Ajouter</button>
    </form>

    <h2>Liste des Quiz</h2>
    <div id="quizList">
        <?php include 'server.php'; displayQuizzes(); ?>
    </div>
</div>

</body>
</html>
