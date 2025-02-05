<?php

define("QUIZZES_FILE", __DIR__ . ".\gestionnaire\quizzes.txt");

// Load quizzes from the text file
function loadQuizzes() {
    if (!file_exists(QUIZZES_FILE)) {
        return [];
    }

    $quizzes = [];
    $lines = file(QUIZZES_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $parts = explode(";", $line);
        if (count($parts) >= 2) {
            $quizzes[] = ["title" => $parts[0], "status" => $parts[1]];
        }
    }

    return $quizzes;
}

$quizzes = loadQuizzes();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord des Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tableau de Bord des Quiz</h1>
        <table>
            <tr>
                <th>Nom du Quiz</th>
                <th>Statut</th>
            </tr>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?= htmlspecialchars($quiz['title']) ?></td>
                    <td><?= htmlspecialchars($quiz['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
