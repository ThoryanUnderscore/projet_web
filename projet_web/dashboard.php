<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user']; // Contient les infos de l'utilisateur connecté

define("QUIZZES_FILE", "./quizzes.txt");

// Fonction pour charger les quiz depuis quizzes.txt
function loadQuizzes($search = '') {
    if (!file_exists(QUIZZES_FILE)) {
        return [];
    }

    $quizzes = [];
    $lines = file(QUIZZES_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $parts = explode(";", $line);
        if (count($parts) >= 2) { // Vérifie qu'on a bien un nom et un état
            $title = trim($parts[0]);
            $status = trim($parts[1]);

            // Si une recherche est effectuée, on filtre les quiz par titre
            if ($search === '' || stripos($title, $search) !== false) {
                $quizzes[] = ["title" => $title, "status" => $status];
            }
        }
    }

    return $quizzes;
}

// Récupération du paramètre de recherche (GET)
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$quizzes = loadQuizzes($searchQuery);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="public/css/dashboard.css">
    <link rel="stylesheet" href="public/css/navbar.css">
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="dashboard.php" class="logo">
                <img src="public/images/quizzeo_sansfond.png" alt="Logo">
            </a>
            <form method="GET" action="dashboard.php" class="search-form">
                <input type="text" name="search" placeholder="Recherchez un quiz" class="search" value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
        
        <div class="nav-right">
            <?php if ($user['type'] !== 'user'): ?>
                <a href="gestion_quiz.php" class="profile">Gestion des Quiz</a>
            <?php endif; ?>
            <a href="logout.php" class="logout">Déconnexion</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Bienvenue, <?= htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']); ?> !</h1>
        <p>Type de compte : <strong><?= htmlspecialchars($user['type']); ?></strong></p>
    </div>

    <div class="quiz-container">
        <h2>Vos Quiz</h2>

        <!-- Bouton pour accéder au fichier select_quiz.php -->
        <a href="select_quiz.php" class="btn-choisir-quiz">Choisir votre quiz</a>

        <?php if (!empty($quizzes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom du Quiz</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= htmlspecialchars($quiz['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun quiz trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>
