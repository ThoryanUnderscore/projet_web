<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire des quiz</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

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
