<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription / Connexion</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

    <div class="container">
        <h2 id="form-title">Inscription</h2>
        <form action="traitement.php" method="post" id="auth-form">
            
            <div class="form-group" id="nom-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>

            <div class="form-group" id="prenom-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="mdp">Mot de passe :</label>
                <input type="password" name="mdp" id="mdp" required>
            </div>

            <input type="hidden" name="action" value="inscription">
            <button type="submit" class="btn">S'inscrire</button>
        </form>

        <p class="switch-link">Déjà un compte ? <br><a href="#" id="switch-login">Se connecter</a></p>
    </div>

    <script src="./js/script.js"></script>
</body>
</html>
