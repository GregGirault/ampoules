<!-- equivalent de add -->

<?php
if ($_POST) {
    if (
        isset($_POST["date_changement"]) && isset($_POST["etage"])
        && isset($_POST["position"])
        && isset($_POST["prix_ampoule"])
    ) {
        require_once("connect.php");
        $date_changement = strip_tags($_POST["date_changement"]);
        $etage = strip_tags($_POST["etage"]);
        $position = strip_tags($_POST["position"]);
        $prix_ampoule = strip_tags($_POST["prix_ampoule"]);
        $sql = "INSERT INTO changement (date_changement, etage, position, prix_ampoule) VALUES (:date_changement, :etage, :position, :prix_ampoule)";
        $query = $db->prepare($sql);
        $query->bindValue(":date_changement", $date_changement, PDO::PARAM_STR);
        $query->bindValue(":etage", $etage, PDO::PARAM_STR);
        $query->bindValue(":position", $position, PDO::PARAM_STR);
        $query->bindValue(":prix_ampoule", $prix_ampoule, PDO::PARAM_STR);
        $query->execute();
        require_once("close.php");
        header("Location: historique.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ajout</title>
</head>

<body>
    <h1>Ajout</h1>
    <form action="" method="post">
        <label for="date_changement">Date de changement</label>
        <input type="date" name="date_changement" id="dates"> <br> <br>
        <label for="etage">Etage</label>
        <input type="text" name="etage" id="etage" required> <br> <br>
        <label for="position">Position</label>
        <select name="position" id="position" required>
            <option value="Nord">Nord</option>
            <option value="Sud">Sud</option>
            <option value="Est">Est</option>
            <option value="Ouest">Ouest</option>
        </select> <br> <br>
        <label for="prix">Prix de l'ampoule</label>
        <input type="text" name="prix_ampoule" required> <br> <br>
        <input type="submit" value="Enregistrer">
    </form> <br>
    <button>
        <a href="historique.php"><span>Retour</span></a>
    </button>
</body>

</html>