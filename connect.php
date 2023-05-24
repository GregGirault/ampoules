<?php
try {
    $server_name = "localhost";
    $dbname = "changement_ampoules";
    $user_name = "root";
    $password = "";

    $db = new PDO("mysql:host=$server_name;dbname=$dbname;charset=utf8mb4", $user_name, $password);
} catch (PDOException $e) {
    echo "echec de connexion : " . $e->getMessage();
}
// içi on affiche le message d'erreur