<?php
define("USER_FILE", "users.txt");

/**
 * Hache le mot de passe avec bcrypt
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Vérifie un utilisateur à la connexion
 */
function checkUser($email, $password) {
    if (!file_exists(USER_FILE)) return false;

    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES);
    
    foreach ($users as $user) {
        list($id, $nom, $prenom, $storedEmail, $hashedPassword, $type, $admin, $timestamp) = explode("|", $user);
        if ($email === $storedEmail && password_verify($password, $hashedPassword)) {
            return [
                "id" => $id,
                "nom" => $nom,
                "prenom" => $prenom,
                "email" => $storedEmail,
                "type" => $type,
                "admin" => $admin,
                "timestamp" => $timestamp
            ];
        }
    }

    return false;
}

/**
 * Inscrit un nouvel utilisateur
 */
function registerUser($nom, $prenom, $email, $password, $type) {
    if (!file_exists(USER_FILE)) {
        file_put_contents(USER_FILE, ""); // Créer le fichier s'il n'existe pas
    }

    $users = file(USER_FILE, FILE_IGNORE_NEW_LINES);
    foreach ($users as $user) {
        list(, , , $storedEmail) = explode("|", $user);
        if ($email === $storedEmail) {
            return false; // Email déjà utilisé
        }
    }

    $id = count($users) + 1; // Auto-incrémentation basique
    $hashedPassword = hashPassword($password);
    $admin = "non"; // Par défaut, pas admin
    $timestamp = time();

    $newUser = "$id|$nom|$prenom|$email|$hashedPassword|$type|$admin|$timestamp\n";
    file_put_contents(USER_FILE, $newUser, FILE_APPEND);

    return true;
}
?>
