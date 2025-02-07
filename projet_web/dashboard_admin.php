<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php"); // Redirige vers la connexion si pas connecté
    exit();
}

$user = $_SESSION["user"]; // Récupère l'utilisateur connecté

$searchQuery = ""; // Initialisation de la variable pour éviter l'erreur

if (isset($_GET["search"])) {
    $searchQuery = trim($_GET["search"]); // Récupère la recherche et enlève les espaces inutiles
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des utilisateurs</title>
  <link rel="stylesheet" href="public/css/dashboard_admin.css">
  <link rel="stylesheet" href="public/css/navbar.css">
</head>
<body>
    <nav>
        <div class="nav-left">
            <a href="dashboard.php" class="logo">
                <img src="public/images/quizzeo_sansfond.png" alt="Logo">
            </a>
            <form method="GET" action="dashboard.php" class="search-form">
                <input type="text" name="search" placeholder="Recherchez un quiz" class="search" value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
        
        <div class="nav-right">
            <?php if ($user['type'] !== 'user'): ?>
                <a href="gestion_quiz.php" class="profile">Gestion des Quiz</a>
            <?php endif; ?>
            <a href="logout.php" class="logout">Déconnexion</a>
        </div>
    </nav>

  <div class="container">
    <h2>Gestion des Utilisateurs</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Email</th>
          <th>Statut</th>
          <th>Admin</th>
          <th>Statut du Compte</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $users_file = 'users.txt';
        $users = file_exists($users_file) ? file($users_file, FILE_IGNORE_NEW_LINES) : [];

        if (isset($_POST['toggle_user'])) {
            $user_id = $_POST['toggle_user'];
            foreach ($users as &$user) {
                $data = explode('|', $user);
                if ($data[0] == $user_id) {
                    $data[6] = $data[6] == "1" ? "0" : "1"; // Inversion du statut
                    $user = implode('|', $data);
                    break;
                }
            }
            file_put_contents($users_file, implode("\n", $users) . "\n");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        foreach ($users as $user):
            $data = explode('|', $user);
            if (count($data) < 7) continue;
            list($id, $nom, $prenom, $email, $statut, $admin, $actif) = $data;

            $statut = match ($statut) {
                'user' => 'Utilisateur',
                'school' => 'École',
                'enterprise' => 'Entreprise',
                default => 'Inconnu'
            };

            $admin = ($admin == 'yes') ? 'Oui' : 'Non';

            $statusText = $actif == "1" ? "🟢 Actif" : "🔴 Désactivé";
            $statusClass = $actif == "1" ? "status-active" : "status-inactive";
            
            $buttonText = $actif == "1" ? "Désactiver" : "Activer";
            $buttonClass = $actif == "1" ? "disable-btn" : "enable-btn";
        ?>
          <tr>
            <td><?= htmlspecialchars($id) ?></td>
            <td><?= htmlspecialchars($nom) ?></td>
            <td><?= htmlspecialchars($prenom) ?></td>
            <td><?= htmlspecialchars($email) ?></td>
            <td><?= htmlspecialchars($statut) ?></td>
            <td><?= htmlspecialchars($admin) ?></td>
            <td class="<?= $statusClass ?>"><?= $statusText ?></td>
            <td>
              <form method="post">
                <button type="submit" name="toggle_user" value="<?= htmlspecialchars($id) ?>" class="toggle-btn <?= $buttonClass ?>"><?= $buttonText ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
