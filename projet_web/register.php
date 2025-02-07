<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password_confirm = trim($_POST["password_confirm"]);
    $type = $_POST["type"];

    if ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $result = registerUser($nom, $prenom, $email, $password, $type);
        if ($result) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Quizzeo</title>
    <link rel="stylesheet" href="./public/css/formulaire.css">
</head>
<body>
    <div class="form-container">
        <h2>Inscription</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="register.php" method="POST">
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
            <select name="type" required>
                <option value="user">Utilisateur</option>
                <option value="school">École</option>
                <option value="entreprise">Entreprise</option>
            </select>
            <button type="submit" class="btn">S'inscrire</button>
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </form>
        
    </div>
</body>
</html>
