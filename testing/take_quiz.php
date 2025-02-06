<?php
if (!isset($_GET['file'])) {
    die("Aucun quiz sélectionné.");
}

$quizFile = $_GET['file'];
if (!file_exists($quizFile)) {
    die("Le fichier quiz n'existe pas : " . htmlspecialchars($quizFile));
}
// --------------------------------------
// Function to convert a single-line quiz text into an array
// --------------------------------------
function convertTextToQuiz($text) {
    // Split the text by semicolon.
    // Do NOT filter out empty parts so that empty values are preserved.
    $parts = explode(";", $text);
    
    // The first part is the quiz title.
    $quiz = [
        "title" => array_shift($parts),
        "questions" => []
    ];
    
    // Each question is represented by 3 fields: question text, choices, correct answer.
    while (count($parts) >= 3) {
        // Extract the next three fields.
        $questionText = array_shift($parts);
        $choicesStr   = array_shift($parts);
        $correct      = array_shift($parts);
        
        // If the choices field is not empty, split it by "|" into an array.
        $choices = ($choicesStr !== "") ? explode("|", $choicesStr) : [];
        
        $quiz['questions'][] = [
            "text"    => $questionText,
            "choices" => $choices,
            "correct" => $correct
        ];
    }
    return $quiz;
}

// --------------------------------------
// Specify the quiz file (adjust the path if needed)
// --------------------------------------
$quizFile = "quiz_1.txt";  // You can change this path to search in another folder (e.g., "data/quiz_1.txt")

if (!file_exists($quizFile)) {
    die("Quiz file not found at: " . $quizFile);
}

// Read the quiz file content (assumed to be stored on one single line)
$text = file_get_contents($quizFile);
$quiz = convertTextToQuiz($text);

// Initialize score variables.
$score = 0;
$total = count($quiz["questions"]);

// If the form has been submitted, process the answers.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($quiz["questions"] as $index => $question) {
        // Retrieve user's answer; if none provided, default to an empty string.
        $userAnswer = isset($_POST["answer_$index"]) ? $_POST["answer_$index"] : "";
        // Compare the user's answer with the correct answer (trim spaces).
        if (trim($userAnswer) === trim($question["correct"])) {
            $score++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($quiz["title"]); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .question {
            margin-bottom: 20px;
            text-align: left;
        }
        .question p {
            font-weight: bold;
        }
        .choices label {
            display: block;
            margin: 5px 0;
        }
        .score {
            font-size: 1.5em;
            margin-top: 20px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($quiz["title"]); ?></h1>
        <?php if ($_SERVER["REQUEST_METHOD"] !== "POST"): ?>
            <form method="post">
                <?php foreach ($quiz["questions"] as $index => $question): ?>
                    <div class="question">
                        <p>Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question["text"]); ?></p>
                        <div class="choices">
                        <?php if (!empty($question["choices"])): ?>
                            <?php foreach ($question["choices"] as $choice): ?>
                                <label>
                                    <input type="radio" name="answer_<?php echo $index; ?>" value="<?php echo htmlspecialchars($choice); ?>">
                                    <?php echo htmlspecialchars($choice); ?>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <label>
                                <input type="text" name="answer_<?php echo $index; ?>" placeholder="Votre réponse">
                            </label>
                        <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <input type="submit" value="Valider le Quiz">
            </form>
        <?php else: ?>
            <div class="score">
                <p>Votre score: <?php echo $score; ?> / <?php echo $total; ?></p>
            </div>
            <button onclick="window.location.href='select_quiz.php';">Retour à la page de quiz</button>
        <?php endif; ?>
    </div>
</body>
</html>
