<?php
session_start();
$host = "localhost";
$dbname = "mon_site";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
        $action = $_POST["action"];

        if ($action === "inscription") {
            $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
            $nom = htmlspecialchars(trim($_POST["nom"]));
            $prenom = htmlspecialchars(trim($_POST["prenom"]));
            $mdp = trim($_POST["mdp"]);

            if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
                $_SESSION["error"] = "Tous les champs sont obligatoires.";
                header("Location: inscription.php");
                exit();
            }

            // Vérifie si l'email est valide
            if (!$email) {
                $_SESSION["error"] = "Adresse email invalide.";
                header("Location: inscription.php");
                exit();
            }

            // Vérifier si l'utilisateur existe déjà
            $checkUser = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $checkUser->execute([$email]);

            if ($checkUser->rowCount() > 0) {
                $_SESSION["error"] = "Cet email est déjà utilisé.";
                header("Location: inscription.php");
                exit();
            }

            // Insérer l'utilisateur
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mdp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $mdp]);

            $_SESSION["success"] = "Inscription réussie ! Connectez-vous.";
            header("Location: inscription.php");
            exit();
        }

        if ($action === "connexion") {
            $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
            $mdp = $_POST["mdp"];

            if (empty($email) || empty($mdp)) {
                $_SESSION["error"] = "Veuillez remplir tous les champs.";
                header("Location: inscription.php");
                exit();
            }

            if (!$email) {
                $_SESSION["error"] = "Adresse email invalide.";
                header("Location: inscription.php");
                exit();
            }

            // Vérifier si l'utilisateur existe
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($mdp, $user["mdp"])) {
                $_SESSION["error"] = "Identifiants incorrects.";
                header("Location: inscription.php");
                exit();
            }

            // Stocker l'utilisateur en session
            $_SESSION["user"] = [
                "nom" => $user["nom"],
                "prenom" => $user["prenom"],
                "email" => $user["email"]
            ];
            header("Location: dashboard.php");
            exit();
        }
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
