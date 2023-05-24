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
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  //  utilise la méthode setAttribute de l'objet PDO pour définir un attribut de la connexion. L'attribut en question est PDO::ATTR_ERRMODE, qui contrôle la gestion des erreurs. En utilisant la constante PDO::ERRMODE_EXCEPTION comme valeur pour cet attribut, vous indiquez à PDO de lever des exceptions en cas d'erreur plutôt que de simplement afficher un avertissement ou une erreur.

    } catch (PDOException $e) {  // Les lignes suivantes sont placées dans un bloc try-catch. Un bloc try est utilisé pour entourer le code qui pourrait potentiellement générer une exception. Si une exception est levée à l'intérieur du bloc try, elle est capturée par le bloc catch correspondant.

        // Dans ce cas, le bloc catch capture les exceptions de type PDOException. Si une exception de ce type est levée lors de la tentative de connexion à la base de données, le code à l'intérieur du bloc catch est exécuté.

        // À l'intérieur du bloc catch, le message d'erreur est obtenu à partir de l'objet d'exception $e en utilisant la méthode getMessage(). Ce message est généralement une description détaillée de l'erreur survenue lors de la connexion à la base de données.

        // Si aucune exception n'est levée, cela signifie que la connexion à la base de données a réussi. Dans ce cas, le code suivant après le bloc catch peut être exécuté normalement.

        // En résumé, ces lignes de code établissent une connexion à une base de données MySQL en utilisant PDO, définissent le mode de gestion des erreurs pour qu'il lève des exceptions en cas de problème, et capturent toute exception PDOException qui pourrait être levée lors de la connexion pour afficher un message d'erreur approprié.
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }

    // On applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
    // pour éliminer toute attaque de type injection SQL et XSS
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if ($username !== "" && $password !== "") {
        $sql = "SELECT count(*) FROM utilisateur WHERE username = :username AND password = :password";  // cette requête permet de sélectionner le nombre d'enregistrements dans la table "utilisateur" qui correspondent à un certain nom d'utilisateur (username) et mot de passe (password).
        // Il est important de noter que cette ligne de code ne s'exécute pas immédiatement pour récupérer les résultats. Elle définit simplement la requête SQL qui sera utilisée plus tard pour interagir avec la base de données.

        $query = $db->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $count = $query->fetchColumn();  // cette ligne de code récupère la valeur d'une colonne spécifique dans le résultat de la requête SQL et l'assigne à la variable $count. Cette valeur peut être utilisée pour effectuer des opérations supplémentaires ou pour afficher des informations à l'utilisateur.
        // Lorsque fetchColumn() est appelé, il récupère la valeur de la première colonne dans la première ligne du résultat de la requête. Si la requête a été configurée pour retourner une seule colonne et une seule ligne, cette méthode récupérera donc la valeur de cette colonne.

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
