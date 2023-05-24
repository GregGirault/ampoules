<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Connexion à la base de données avec PDO
    $db_host = 'localhost';
    $db_name = 'changement_ampoules';
    $db_username = 'root';
    $db_password = '';

    try {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }

    // On applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
    // pour éliminer toute attaque de type injection SQL et XSS
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if ($username !== "" && $password !== "") {
        $sql = "SELECT count(*) FROM utilisateur WHERE username = :username AND password = :password";
        $query = $db->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $count = $query->fetchColumn();

        if ($count != 0) {
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
            header('Location: historique.php');
            exit();
        } else {
            header('Location: index.php?erreur=1'); // Utilisateur ou mot de passe incorrect
            exit();
        }
    } else {
        header('Location: index.php?erreur=2'); // Utilisateur ou mot de passe vide
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
