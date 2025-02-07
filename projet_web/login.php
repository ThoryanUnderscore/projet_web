<?php
session_start();
require 'config.php';

// Générer un captcha aléatoire (calcul ou suite de caractères)
function generateCaptcha() {
    $captcha_type = rand(0, 1);
    if ($captcha_type == 0) {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $_SESSION['captcha'] = "$num1 + $num2";
        $_SESSION['captcha_answer'] = $num1 + $num2;
    } else {
        $_SESSION['captcha'] = strtoupper(substr(md5(rand()), 0, 6));
        $_SESSION['captcha_answer'] = $_SESSION['captcha'];
    }
}

// Initialiser le captcha au chargement
if (!isset($_SESSION['captcha'])) generateCaptcha();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $captcha_input = trim($_POST["captcha"]);

    if ($captcha_input == $_SESSION['captcha_answer']) {
        $user = checkUser($email, $password);

        if ($user) {
            // 🔥 Vérification correcte du statut (doit être "non" pour être désactivé)
            if (isset($user['is_active']) && $user['is_active'] === "non") {
                $error = "Votre compte est désactivé.";
            } else {
                $_SESSION["user"] = $user;
                // 🔥 Redirection admin / user
                if (!empty($user['admin']) && $user['admin'] === "oui") {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $captcha_error = "Captcha incorrect.";
        generateCaptcha();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Quizzeo</title>
    <link rel="stylesheet" href="./public/css/formulaire.css">
</head>
<body>
    <div class="form-container">
        <h2>Connexion</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>

            <div class="captcha-container">
                <div class="captcha-box"><?php echo $_SESSION['captcha']; ?></div>
                <input type="text" name="captcha" placeholder="Résolvez le captcha" required>
                <?php if (isset($captcha_error)) echo "<p class='error-message'>$captcha_error</p>"; ?>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>
        <p>Pas encore inscrit ? <br><a href="register.php">Créer un compte</a></p>
    </div>
</body>
</html>
