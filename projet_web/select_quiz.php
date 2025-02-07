<?php
define("QUIZZES_INDEX_FILE", "quizzes.txt");

// Fonction pour charger la liste des quiz depuis l'index
function loadQuizList() {
    $quizzes = [];
    if (file_exists(QUIZZES_INDEX_FILE)) {
        $lines = file(QUIZZES_INDEX_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(";", $line);
            if (count($parts) >= 3) {
                $quizzes[] = [
                    "title"       => $parts[0],
                    "status"      => $parts[1],
                    "contentFile" => $parts[2]
                ];
            }
        }
    }
    return $quizzes;
}

$quizList = loadQuizList();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sélectionnez un Quiz</title>
    <link rel="stylesheet" href="./public/css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 60%;
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .quiz-list {
            list-style: none;
            padding: 0;
        }
        .quiz-item {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .quiz-item a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .quiz-item a:hover {
            color: #0056b3;
        }
        .status {
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Choisissez le Quiz à réaliser</h1>
        <?php if (empty($quizList)): ?>
            <p>Aucun quiz n'est disponible.</p>
        <?php else: ?>
            <ul class="quiz-list">
                <?php foreach ($quizList as $quiz): ?>
                    <li class="quiz-item">
                        <!-- Le lien passe le nom du fichier du quiz en paramètre GET -->
                        <a href="take_quiz.php?file=<?php echo urlencode($quiz['contentFile']); ?>">
                            <?php echo htmlspecialchars($quiz['title']); ?>
                        </a>
                        <div class="status">
                            Statut : <?php echo htmlspecialchars($quiz['status']); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>