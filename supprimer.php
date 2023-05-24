<!-- equivalent delete -->

<?php
session_start();
require_once("connect.php");

if ($_GET && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Vérifier si l'id existe dans la base de données
    $sql = "SELECT * FROM `changement` WHERE `id` = :id";
    $query = $db->prepare($sql);
    $query->bindValue(":id", $id, PDO::PARAM_INT);
    $query->execute();
    $changement = $query->fetch(PDO::FETCH_ASSOC);

    if ($changement) {
        // Supprimer l'enregistrement de la base de données
        $sql = "DELETE FROM `changement` WHERE `id` = :id";
        $query = $db->prepare($sql);
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();

        $_SESSION["toast_message"] = "Supprimé avec succès";
        $_SESSION["toast_type"] = "success";
    }
}

// Redirection après la suppression
header("Location: historique.php?suppression=success");
exit();
?>