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
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 20px;
    }

    .container {
      max-width: 1200px; /* Limiter la largeur du contenu */
      width: 100%;
      display: flex;
      flex-direction: column; /* Les sections seront empilées verticalement */
      align-items: center;
    }

    /* Barre de navigation */
    nav {
      background-color: rgba(51, 51, 51, 0.8);
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 60px;
      width: 80%; /* La barre de navigation prendra une bonne partie de la largeur */
      margin-bottom: 20px; /* Espacement entre la barre et les sections */
      border-radius: 10px;
    }

    .nav-left, .nav-right {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 40px;
      margin-right: 20px;
    }

    .search {
      padding: 8px;
      border: none;
      border-radius: 4px;
      margin-right: 20px;
      width: 300px; /* Largeur modérée pour la barre de recherche */
    }

    .profile, .logout {
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 12px;
      background-color: #555;
      border-radius: 4px;
      transition: background-color 0.3s ease;
      margin-right: 10px;
    }

    .profile:hover, .logout:hover {
      background-color: #777;
    }

    /* Contenu principal */
    .content {
      width: 80%; /* Largeur des sections sous la barre */
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    #users, #quizzes {
      background-color: rgba(255, 255, 255, 0.8);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: #f4f4f4;
    }

    form {
      display: inline;
    }

    /* Stylisation du bouton supprimer */
    button[type="submit"] {
      background-color: red;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: darkred;
    }

    /* Adaptation pour les petits écrans */
    @media (max-width: 768px) {
      .container {
        width: 90%; /* Réduire légèrement la largeur sur mobile */
      }

      nav {
        flex-direction: column;
        align-items: flex-start;
        height: auto;
        padding: 10px;
        width: 100%; /* Pleine largeur sur mobile */
      }

      .search {
        width: 100%;
        margin-right: 0;
      }

      #users, #quizzes {
        width: 100%; /* Pleine largeur sur mobile */
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Barre de navigation -->
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

    <!-- Contenu principal : Gestion des utilisateurs et des quiz -->
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
            <?php
            // Charger les utilisateurs depuis le fichier
            $users_file = 'users.txt';
            $users = file_exists($users_file) ? file($users_file, FILE_IGNORE_NEW_LINES) : [];

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

            foreach ($users as $user): 
                $data = explode('|', $user);
                if (count($data) < 5) continue;
                list($id, $nom, $prenom, $email, $statut, $admin) = $data;

                // Adaptation du statut et de l'admin
                $statut = match ($statut) {
                    'user' => 'Utilisateur',
                    'school' => 'École',
                    'enterprise' => 'Entreprise',
                    default => 'Inconnu'
                };

                $admin = ($admin == 'yes') ? 'Oui' : 'Non';
            ?>
              <tr>
                <td><?= htmlspecialchars($id) ?></td>
                <td><?= htmlspecialchars($nom) ?></td>
                <td><?= htmlspecialchars($prenom) ?></td>
                <td><?= htmlspecialchars($email) ?></td>
                <td><?= htmlspecialchars($statut) ?></td>
                <td><?= htmlspecialchars($admin) ?></td>
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
            <?php
            // Charger les quiz depuis le fichier
            $quizzes_file = 'quizzes.txt';
            $quizzes = file_exists($quizzes_file) ? file($quizzes_file, FILE_IGNORE_NEW_LINES) : [];

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

            foreach ($quizzes as $quiz): 
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
  </div>
</body>
</html>
