<?php

define("QUIZZES_FILE", "quizzes.txt");


function convertQuizToText($quiz) {
    $output = $quiz['title'] . ";";

    if (!empty($quiz['questions'])) {
        foreach ($quiz['questions'] as $question) {
            $questionText = isset($question['text']) ? $question['text'] : "";
            $choices = isset($question['choices']) ? implode("|", $question['choices']) : "";
            $correct = isset($question['correct']) ? $question['correct'] : "";

            // Concatenate with semicolons, keeping empty values intact
            $output .= "{$questionText};{$choices};{$correct};";
        }
    }

    return rtrim($output, ";"); // Only removes the last semicolon
}



/**
 * Convertir le texte d'un fichier (stocké sur une seule ligne sans espaces et sans étiquettes)
 * en un tableau associatif.
 * Format attendu :
 * title;questionText;choice1|choice2|...;correct;[...]
 * Le premier élément est le titre et chaque groupe de trois éléments représente une question.
 */
function convertTextToQuiz($text) {
    $parts = explode(";", $text);
    
    // Ensure empty values are preserved instead of being removed
    $quiz = ["title" => "", "questions" => []];

    if (!empty($parts)) {
        // First element is the quiz title
        $quiz['title'] = $parts[0];

        // Questions are grouped in sets of 3: text, choices, and correct answer
        $numParts = count($parts);
        for ($i = 1; $i < $numParts; $i += 3) {
            if (($i + 2) < $numParts) {
                $questionText = $parts[$i];
                $choicesStr = $parts[$i + 1];
                $correct = $parts[$i + 2];

                // Preserve empty choices by checking if the string is empty before exploding
                $choices = ($choicesStr !== "") ? explode("|", $choicesStr) : [];

                $quiz['questions'][] = [
                    "text" => $questionText,
                    "choices" => $choices,
                    "correct" => $correct
                ];
            }
        }
    }
    return $quiz;
}

// Charger les quiz depuis le fichier d'index
function loadQuizzes() {
    if (!file_exists(QUIZZES_FILE)) {
        return [];
    }
    $quizzes = [];
    $lines = file(QUIZZES_FILE, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $index => $line) {
        // Format attendu dans l'index : title;status;contentFile
        $parts = explode(";", $line);
        $title = $parts[0];
        $status = isset($parts[1]) ? $parts[1] : "encoursdecriture";
        $contentFile = isset($parts[2]) ? $parts[2] : "quiz_" . ($index + 1) . ".txt";
        $quizzes[] = [
            "id" => $index + 1,
            "title" => $title,
            "status" => $status,
            "contentFile" => $contentFile
        ];
    }
    return $quizzes;
}

// Enregistrer la liste des quiz dans le fichier d'index
function saveQuizzes($quizzes) {
    $fileContent = "";
    foreach ($quizzes as $quiz) {
        // Format d'enregistrement : title;status;contentFile
        $fileContent .= "{$quiz['title']};{$quiz['status']};{$quiz['contentFile']}\n";
    }
    file_put_contents(QUIZZES_FILE, $fileContent);
}

// Afficher les quiz avec boutons Modifier et Supprimer
function displayQuizzes() {
    $quizzes = loadQuizzes();
    foreach ($quizzes as $quiz) {
        echo "<div class='quiz-item'>
                <p><strong>Quiz :</strong> {$quiz['title']}</p>
                <p><strong>Status :</strong> 
                    <form action='server.php' method='POST' class='status-form' style='display:inline;'>
                        <input type='hidden' name='quizId' value='{$quiz['id']}'>
                        <select name='status' onchange='this.form.submit()'>
                            <option value='encoursdecriture' " . ($quiz['status'] === "encoursdecriture" ? "selected" : "") . ">En cours d'écriture</option>
                            <option value='lance' " . ($quiz['status'] === "lance" ? "selected" : "") . ">Lancé</option>
                            <option value='termine' " . ($quiz['status'] === "termine" ? "selected" : "") . ">Terminé</option>
                        </select>
                    </form>
                </p>
                <form action='server.php' method='GET' style='display:inline;'>
                    <input type='hidden' name='action' value='edit'>
                    <input type='hidden' name='quizId' value='{$quiz['id']}'>
                    <button type='submit'>Modifier</button>
                </form>
                <form action='server.php' method='POST' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='deleteQuiz' value='1'>
                    <input type='hidden' name='quizId' value='{$quiz['id']}'>
                    <button type='submit' onclick='return confirm(\"Voulez-vous vraiment supprimer ce quiz ?\")'>Supprimer</button>
                </form>
                <hr>
              </div>";
    }
}

// -----------------------------
// Traitement des actions
// -----------------------------

// Ajouter un quiz
if (isset($_POST['addQuiz'])) {
    $title = trim($_POST['title']);
    if ($title) {
        $quizzes = loadQuizzes();
        $newId = count($quizzes) + 1;
        $contentFile = "quiz_{$newId}.txt";
        // Créer un contenu par défaut pour le quiz (aucune question)
        $defaultContent = str_replace(" ", "", $title) . ";";
        file_put_contents($contentFile, $defaultContent);
        $quizzes[] = [
            "id" => $newId,
            "title" => str_replace(" ", "", $title),
            "status" => "encoursdecriture",
            "contentFile" => $contentFile
        ];
        saveQuizzes($quizzes);
    }
    header("Location: gestion_quiz.php");
    exit();
}

// Modifier le statut d'un quiz
if (isset($_POST['quizId']) && isset($_POST['status']) && !isset($_POST['deleteQuiz']) && !isset($_POST['saveQuiz'])) {
    $quizId = intval($_POST['quizId']);
    $newStatus = $_POST['status'];
    $quizzes = loadQuizzes();
    foreach ($quizzes as &$quiz) {
        if ($quiz['id'] == $quizId) {
            $quiz['status'] = $newStatus;
            break;
        }
    }
    saveQuizzes($quizzes);
    header("Location: gestion_quiz.php");
    exit();
}

// Supprimer un quiz
if (isset($_POST['deleteQuiz']) && isset($_POST['quizId'])) {
    $quizId = intval($_POST['quizId']);
    $quizzes = loadQuizzes();
    $newQuizzes = [];
    foreach ($quizzes as $quiz) {
        if ($quiz['id'] == $quizId) {
            if (file_exists($quiz['contentFile'])) {
                unlink($quiz['contentFile']);
            }
            continue;
        }
        $newQuizzes[] = $quiz;
    }
    foreach ($newQuizzes as $index => &$quiz) {
        $quiz['id'] = $index + 1;
    }
    saveQuizzes($newQuizzes);
    header("Location: gestion_quiz.php");
    exit();
}

// Afficher le formulaire d'édition d'un quiz
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['quizId'])) {
    $quizId = intval($_GET['quizId']);
    $quizzes = loadQuizzes();
    $quizToEdit = null;
    foreach ($quizzes as $quiz) {
        if ($quiz['id'] == $quizId) {
            $quizToEdit = $quiz;
            break;
        }
    }
    if (!$quizToEdit) {
        echo "Quiz non trouvé.";
        exit();
    }
    // Charger le contenu détaillé du quiz depuis le fichier texte
    if (file_exists($quizToEdit['contentFile'])) {
        $textContent = file_get_contents($quizToEdit['contentFile']);
        $content = convertTextToQuiz($textContent);
    } else {
        $content = [
            "title" => $quizToEdit['title'],
            "questions" => []
        ];
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Modifier le Quiz</title>
        <link rel="stylesheet" href="./public/css/styles.css">
    </head>
    <body>
        <h2>Modifier le Quiz</h2>
        <form action="server.php" method="POST">
            <input type="hidden" name="quizId" value="<?php echo $quizId; ?>">
            <label>Nom du Quiz :</label><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($content['title']); ?>"><br><br>
            
            <h3>Questions :</h3>
            <div id="questions-container">
                <?php
                if (!empty($content['questions'])) {
                    foreach ($content['questions'] as $qIndex => $question) {
                        ?>
                        <div class="question-block" style="margin-bottom:20px; border:1px solid #ccc; padding:10px;">
                            <label>Question <?php echo $qIndex + 1; ?> :</label><br>
                            <input type="text" name="questions[<?php echo $qIndex; ?>][text]" value="<?php echo htmlspecialchars($question['text']); ?>"><br>
                            <label>Choix :</label><br>
                            <?php
                            if (!empty($question['choices'])) {
                                foreach ($question['choices'] as $cIndex => $choice) {
                                    echo "<input type='text' name='questions[{$qIndex}][choices][{$cIndex}]' value='" . htmlspecialchars($choice) . "'><br>";
                                }
                            }
                            ?>
                            <label>Bonne réponse :</label><br>
                            <input type="text" name="questions[<?php echo $qIndex; ?>][correct]" value="<?php echo htmlspecialchars($question['correct']); ?>"><br>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <button type="button" onclick="addQuestion()">Ajouter une question</button><br><br>
            <input type="submit" name="saveQuiz" value="Enregistrer les modifications">
        </form>
        
        <script>
            let questionCount = <?php echo !empty($content['questions']) ? count($content['questions']) : 0; ?>;
            function addQuestion() {
                const container = document.getElementById('questions-container');
                const div = document.createElement('div');
                div.className = 'question-block';
                div.style.marginBottom = '20px';
                div.style.border = '1px solid #ccc';
                div.style.padding = '10px';
                div.innerHTML = `
                    <label>Question ${questionCount + 1} :</label><br>
                    <input type="text" name="questions[${questionCount}][text]" value=""><br>
                    <label>Choix :</label><br>
                    <input type="text" name="questions[${questionCount}][choices][0]" value=""><br>
                    <input type="text" name="questions[${questionCount}][choices][1]" value=""><br>
                    <input type="text" name="questions[${questionCount}][choices][2]" value=""><br>
                    <input type="text" name="questions[${questionCount}][choices][3]" value=""><br>
                    <label>Bonne réponse :</label><br>
                    <input type="text" name="questions[${questionCount}][correct]" value=""><br>
                `;
                container.appendChild(div);
                questionCount++;
            }
        </script>
    </body>
    </html>
    <?php
    exit();
}

// Sauvegarder les modifications d'un quiz édité
if (isset($_POST['saveQuiz']) && isset($_POST['quizId'])) {
    $quizId = intval($_POST['quizId']);
    $newTitle = trim($_POST['title']);
    $newQuestions = isset($_POST['questions']) ? $_POST['questions'] : [];

    $quizzes = loadQuizzes();
    foreach ($quizzes as &$quiz) {
        if ($quiz['id'] == $quizId) {
            // Mettre à jour le titre (sans espaces)
            $quiz['title'] = str_replace(" ", " ", $newTitle);
            $content = [
                "title" => $newTitle,
                "questions" => $newQuestions
            ];
            $textContent = convertQuizToText($content);
            file_put_contents($quiz['contentFile'], $textContent);
            break;
        }
    }
    saveQuizzes($quizzes);
    header("Location: gestion_quiz.php");
    exit();
}
?>