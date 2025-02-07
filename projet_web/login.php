<?php
session_start();
require 'config.php';
// Générer un captcha aléatoire (calcul ou suite de caractères)
function generateCaptcha() {
    $captcha_type = rand(0, 1); // 0 pour calcul, 1 pour suite de caractères
    if ($captcha_type == 0) {
        // Générer un calcul aléatoire
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $_SESSION['captcha'] = $num1 . ' + ' . $num2;
        $_SESSION['captcha_answer'] = $num1 + $num2;
    } else {
        // Générer une suite aléatoire de lettres et chiffres
        $_SESSION['captcha'] = strtoupper(substr(md5(rand()), 0, 6));
        $_SESSION['captcha_answer'] = $_SESSION['captcha']; // Réponse correcte est la suite générée
    }
}
// Initialiser le captcha lors de chaque chargement de la page
if (!isset($_SESSION['captcha'])) {
    generateCaptcha();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $captcha_input = trim($_POST["captcha"]);
    // Vérification du captcha
    if ($captcha_input == $_SESSION['captcha_answer']) {
        $user = checkUser($email, $password);
        if ($user) {
            $_SESSION["user"] = $user;
            $message = "Connexion réussie ! Vous allez être redirigé.";
            header("refresh:3;url=dashboard.php"); // Redirection après 3 secondes
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $captcha_error = "Réponse au captcha incorrecte.";
        generateCaptcha(); // Générer un nouveau captcha en cas d'erreur
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
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            margin-top: 200px; 
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            background: linear-gradient(to right, #cf20c9,rgb(214, 94, 64) , #79df69);
        }
        h2 {
            color: #333;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .captcha-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 250px;
            margin: auto;
        }
        .captcha-box-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .captcha-box { 
            display: inline-block; 
            padding: 8px 15px; 
            background: #ff6b6b; 
            font-size: 18px; 
            font-weight: bold; 
            letter-spacing: 2px;
            color: white;
            border-radius: 5px;
            margin-right: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        button {
            margin-top: 10px;
            padding: 10px 15px;
            font-size: 16px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        input {
            padding: 8px;
            font-size: 16px;
            width: 80%;
            border: 2px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }
        #message {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Connexion</h2>
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>

            <div class="captcha-container">
                <div class="captcha-box-container">
                    <div class="captcha-box"><?php echo $_SESSION['captcha']; ?></div>
                </div>
                <input type="text" name="captcha" placeholder="Résolvez le captcha" required>
                <?php if (isset($captcha_error)) echo "<p class='error-message'>$captcha_error</p>"; ?>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>
        <p>Pas encore inscrit ? <br><a href="register.php">Créer un compte</a></p>
    </div>
    <script>
        <?php if (isset($message)): ?>
            setTimeout(function() {
                window.location.href = "dashboard.php";
            }, 3000); 
        <?php elseif (isset($error)): ?>
            setTimeout(function() {
                location.reload(); 
            }, 3000); 
        <?php endif; ?>
    </script>
</body>
</html>