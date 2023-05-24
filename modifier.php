<?php
if ($_POST) {
    if (isset($_POST["id"]) && isset($_POST["date_changement"]) && isset($_POST["etage"]) && isset($_POST["position"]) && isset($_POST["prix_ampoule"])) {
        require_once("connect.php");

        // nouvelles variable néttoyer
        $id = strip_tags($_POST["id"]);
        $date_changement = strip_tags($_POST["date_changement"]);
        $etage = strip_tags($_POST["etage"]);
        $position = strip_tags($_POST["position"]);
        $prix_ampoule = strip_tags($_POST["prix_ampoule"]);
        // nouvelles variable néttoyer

        $sql = "UPDATE changement SET date_changement=:date_changement, etage=:etage, position=:position, prix_ampoule=:prix_ampoule WHERE id = :id";
        $query = $db->prepare($sql);
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->bindValue(":date_changement", $date_changement);
        $query->bindValue(":etage", $etage);
        $query->bindValue(":position", $position);
        $query->bindValue(":prix_ampoule", $prix_ampoule);
        $query->execute();
        require_once("close.php");
        setlocale(LC_TIME, 'fr_FR.UTF-8'); // Définit la locale en français
        header("Location: historique.php");
    }
}

// sert a aller chercher les informations 
$date_changement = ""; // Initialisation avec une valeur par défaut
if (isset($_GET["id"]) && !empty($_GET['id'])) {
    require_once("connect.php");

    $id = strip_tags($_GET['id']);
    $sql = "SELECT * FROM changement WHERE id = :id";
    $query = $db->prepare($sql);
    $query->bindValue(":id", $id, PDO::PARAM_INT);
    $query->execute();
    $changement = $query->fetch();
    $date_changement = DateTime::createFromFormat('Y-m-d', $changement["date_changement"])->format('d/m/Y');
    // formate la date au format jj/mm/aaaa pour l'affichage dans le h1
    require_once("close.php");
} else {
    header("Location: historique.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>changement d'ampoule</title>
</head>

<body>
    <h1>Modification du changement d'ampoule en date du <?= $date_changement . " <br> au " . $changement["etage"] . " à la position " . $changement["position"] . " au prix de " . $changement["prix_ampoule"] . " TTC " ?></h1>
    <form method="post">
        <div>
            <label for="date_changement">Date du changement</label>
            <input type="date" name="date_changement" id="dates" value="<?= $changement['date_changement'] ?>">
            <label for="etage">Etage</label>
            <select name="etage" id="etage" required>
                <option value="1er Etage" <?php if ($changement['etage'] == '1er Etage') echo 'selected'; ?>>1er Etage</option>
                <option value="2eme Etage" <?php if ($changement['etage'] == '2eme Etage') echo 'selected'; ?>>2eme Etage</option>
                <option value="3eme Etage" <?php if ($changement['etage'] == '3eme Etage') echo 'selected'; ?>>3eme Etage</option>
                <option value="4eme Etage" <?php if ($changement['etage'] == '4eme Etage') echo 'selected'; ?>>4eme Etage</option>
                <option value="5eme Etage" <?php if ($changement['etage'] == '5eme Etage') echo 'selected'; ?>>5eme Etage</option>
                <option value="6eme Etage" <?php if ($changement['etage'] == '6eme Etage') echo 'selected'; ?>>6eme Etage</option>
                <option value="7eme Etage" <?php if ($changement['etage'] == '7eme Etage') echo 'selected'; ?>>7eme Etage</option>
                <option value="8eme Etage" <?php if ($changement['etage'] == '8eme Etage') echo 'selected'; ?>>8eme Etage</option>

            </select>
            <label for="position">Position</label>
            <select name="position" id="position" required>
                <option value="Nord" <?php if ($changement['position'] == 'Nord') echo 'selected'; ?>>Nord</option>
                <option value="Sud" <?php if ($changement['position'] == 'Sud') echo 'selected'; ?>>Sud</option>
                <option value="Est" <?php if ($changement['position'] == 'Est') echo 'selected'; ?>>Est</option>
                <option value="Ouest" <?php if ($changement['position'] == 'Ouest') echo 'selected'; ?>>Ouest</option>

            </select>
            <label for="prix_ampoule">Prix de l'ampoule TTC</label>
            <input type="text" name="prix_ampoule" required value="<?= $changement['prix_ampoule'] ?>">
            <input type="hidden" value="<?= $changement["id"] ?>" name="id">
            <div class="button-container">
                <input type="submit" class="boutton-enregistrer" value="Enregistrer"> <br> <br>
                <button class="boutton-retour">
                    <a href="historique.php"><span>Retour</span></a>
                </button>
            </div>

    </form> <br>

</body>

</html>