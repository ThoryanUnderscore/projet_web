<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barre de Navigation Personnalisée</title>
  <style>
    /* Style global de la page */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(45deg, blue, red, yellow);
      min-height: 100vh;
    }
    
    /* Conteneur principal de la navigation */
    nav {
      background-color: rgba(51, 51, 51, 0.8);
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 60px;
    }
    
    /* Sections de la barre */
    .nav-left, .nav-right {
      display: flex;
      align-items: center;
    }
    
    /* Logo (Accueil) */
    .logo img {
      height: 40px;
      margin-right: 20px;
    }
    
    /* Champ de recherche */
    .search {
      padding: 8px;
      border: none;
      border-radius: 4px;
      margin-right: 20px;
      width: 500px; 
    }
    
    /* Lien profil */
    .profile {
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 12px;
      background-color: #555;
      border-radius: 4px;
      transition: background-color 0.3s ease;
      margin-right: 10px;
    }
    
    .profile:hover {
      background-color: #777;
    }
    
    /* Bouton déconnexion */
    .logout {
      color: #fff;
      text-decoration: none;
      padding: 8px 12px;
      background-color: #555;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }
    
    .logout:hover {
      background-color: #777;
    }
    
    /* Adaptation pour les petits écrans */
    @media (max-width: 768px) {
      nav {
        flex-direction: column;
        height: auto;
        padding: 10px;
      }
      .nav-left {
        margin-bottom: 10px;
      }
      .search {
        width: 100%; /* Pleine largeur sur mobile */
        margin-right: 0;
      }
    }
  </style>
</head>
<body>
  <nav>
    <!-- Section gauche : logo et champ de recherche -->
    <div class="nav-left">
      <a href="/dashboard" class="logo">
        <img src="quizzeo_sansfond.png" alt="Logo">
      </a>
      <input type="text" placeholder="Recherchez un quiz" class="search">
    </div>
    
    <!-- Section droite : profil et déconnexion -->
    <div class="nav-right">
      <a href="/profile" class="profile">Mon profil</a>
      <a href="/logout" class="logout">Déconnexion</a>
    </div>
  </nav>
</body>
</html>

<?php
// Charger les utilisateurs et quiz depuis des fichiers texte
$users_file = 'users.txt';
$quizzes_file = 'quizzes.txt';
$users = file_exists($users_file) ? file($users_file, FILE_IGNORE_NEW_LINES) : [];
$quizzes = file_exists($quizzes_file) ? file($quizzes_file, FILE_IGNORE_NEW_LINES) : [];

// Supprimer un utilisateur
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['delete_user'];
    $users = array_filter($users, function ($user) use ($user_id) {
        return !str_starts_with($user, $user_id . '|');
    });
    file_put_contents($users_file, implode("\n", $users) . "\n");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Supprimer un quiz
if (isset($_POST['delete_quiz'])) {
    $quiz_id = $_POST['delete_quiz'];
    $quizzes = array_filter($quizzes, function ($quiz) use ($quiz_id) {
        return !str_starts_with($quiz, $quiz_id . '|');
    });
    file_put_contents($quizzes_file, implode("\n", $quizzes) . "\n");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="content">
        <section id="users">
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php 
                            $data = explode('|', $user);
                            if (count($data) < 5) continue;
                            list($id, $nom, $prenom, $email, $statut, $admin) = $data;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($id) ?></td>
                            <td><?= htmlspecialchars($nom) ?></td>
                            <td><?= htmlspecialchars($prenom) ?></td>
                            <td><?= htmlspecialchars($email) ?></td>
                            <td><?= htmlspecialchars($statut) ?></td>
                            <td><?= htmlspecialchars(string: $admin) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <button type="submit" name="delete_user" value="<?= htmlspecialchars($id) ?>">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <section id="quizzes">
            <h2>Gestion des Quiz</h2>
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <?php 
                            $data = explode('|', $quiz);
                            if (count($data) < 3) continue;
                            list($matiere, $statut, ) = $data;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($matiere) ?></td>
                            <td><?= htmlspecialchars($statut) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <button type="submit" name="delete_quiz" value="<?= htmlspecialchars($matiere) ?>">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
