<?php
session_start();
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {  // isset($_SESSION["authenticated"]) : Cela vérifie si la clé "authenticated" existe dans la variable superglobale $_SESSION. $_SESSION est un tableau associatif en PHP utilisé pour stocker des données de session.

    // !isset($_SESSION["authenticated"]) : L'opérateur ! (négation) inverse le résultat de isset($_SESSION["authenticated"]). Ainsi, si la clé "authenticated" n'existe pas dans $_SESSION, cette expression sera évaluée à true.

    // $_SESSION["authenticated"] !== true : Cela vérifie si la valeur associée à la clé "authenticated" dans $_SESSION est différente de true. Plus précisément, !== est un opérateur de comparaison strict qui compare les valeurs et les types des deux opérandes. Si la valeur n'est pas strictement égale à true, cette expression sera évaluée à true.

    // En combinant les deux parties de la condition avec l'opérateur logique || (ou), nous obtenons :

    // (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) : Si l'une des deux conditions est vraie, c'est-à-dire si la clé "authenticated" n'existe pas dans $_SESSION ou si sa valeur n'est pas strictement égale à true, alors cette condition globale sera évaluée à true.
    // En résumé, cette condition vérifie si l'utilisateur n'est pas authentifié. Si la condition est vraie, cela signifie que l'utilisateur n'est pas authentifié et le code à l'intérieur du bloc conditionnel sera exécuté.

    // L'utilisateur n'est pas authentifié, rediriger vers la page de connexion
    header("Location: index.php");
    exit();
}
require_once("connect.php");

if ($_POST) {
    // Traitement de la soumission du formulaire d'ajout

    if (
        !empty($_POST["date_changement"]) && !empty($_POST["etage"])
        && !empty($_POST["position"]) && !empty($_POST["prix_ampoule"])
    ) {
        // Les champs sont valides, effectuer l'ajout dans la base de données
        $date_changement = strip_tags($_POST["date_changement"]);
        $etage = strip_tags($_POST["etage"]);
        $position = strip_tags($_POST["position"]);
        $prix_ampoule = strip_tags($_POST["prix_ampoule"]);

        $sql = "INSERT INTO changement (date_changement, etage, position, prix_ampoule) 
                VALUES (:date_changement, :etage, :position, :prix_ampoule)";
        $query = $db->prepare($sql);
        $query->bindValue(":date_changement", $date_changement, PDO::PARAM_STR);
        $query->bindValue(":etage", $etage, PDO::PARAM_STR);
        $query->bindValue(":position", $position, PDO::PARAM_STR);
        $query->bindValue(":prix_ampoule", $prix_ampoule, PDO::PARAM_STR);
        $query->execute();

        $_SESSION["toast_message"] = "Ajouté avec succès";  // $_SESSION["toast_message"] = "Ajouté avec succès"; : Cette ligne attribue la valeur "Ajouté avec succès" à la clé "toast_message" dans la variable de session $_SESSION. Cela signifie que le message "Ajouté avec succès" sera stocké dans la session et pourra être utilisé ultérieurement pour afficher un toast à l'utilisateur.
        $_SESSION["toast_type"] = "success";  // $_SESSION["toast_type"] = "success"; : Cette ligne attribue la valeur "success" à la clé "toast_type" dans la variable de session $_SESSION. Cela indique le type de toast à afficher. Dans cet exemple, le type de toast est défini sur "success", ce qui suggère que le toast sera une notification de succès.

        header("Location: historique.php");
        exit();
    }
}

// Pour pouvoir paginer, il nous faut connaître plusieurs informations :
// Le numéro de la page sur laquelle on se trouve
// Le nombre d'articles (de changement d'ampoule dans notre cas) au total
// Le nombre d'articles (de changement d'ampoule dans notre cas) souhaités par page
// Le nombre de pages au total

// On détermine sur quelle page on se trouve
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = (int) strip_tags($_GET['page']);
} else {
    $currentPage = 1;
}
// On détermine le nombre total d'articles
$sql = 'SELECT COUNT(*) AS nb_articles FROM `changement`;';

// On prépare la requête
$query = $db->prepare($sql);

// On exécute
$query->execute();

// On récupère le nombre d'articles
$results = $query->fetch();

$nbArticles = (int) $results['nb_articles'];

// On détermine le nombre d'articles (changement d'ampoule) par page
$parPage = 5; // Nombre d'articles (changement d'ampoule) à afficher par page

// Calcul du nombre total de pages
$pages = ceil($nbArticles / $parPage);

// Calcul de la valeur de "premier" pour la requête SQL
$premier = ($currentPage - 1) * $parPage;

$sql = 'SELECT * FROM `changement` ORDER BY `date_changement` ASC LIMIT :premier, :parpage;';  // La clause LIMIT est utilisée pour limiter le nombre de résultats retournés. :premier et :parpage sont des paramètres qui seront définis plus tard.
$query = $db->prepare($sql);
$query->bindValue(':premier', $premier, PDO::PARAM_INT);  // Cette ligne associe la valeur de la variable $premier au paramètre :premier dans la requête SQL. La méthode bindValue() permet de lier une valeur à un paramètre dans une requête préparée. PDO::PARAM_INT indique que le paramètre est un entier.
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);  // Cette ligne fait la même chose que la ligne précédente, mais elle associe la valeur de la variable $parPage au paramètre :parpage.
$query->execute();
$articles = $query->fetchAll(PDO::FETCH_ASSOC);  // Cette ligne récupère tous les résultats de la requête sous la forme d'un tableau associatif. Chaque ligne de la table "changement" sera représentée par un tableau associatif contenant les noms de colonnes comme clés et les valeurs correspondantes.

require_once("close.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <title>Historique des changements d'ampoules</title>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <?php if (isset($_SESSION["toast_message"]) && isset($_SESSION["toast_type"])) : ?>
        <!-- Vérifier si un message de toast est défini dans la session -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Toastify({
                    text: "<?php echo $_SESSION["toast_message"]; ?>",
                    duration: 3000,
                    destination: "https://github.com/apvarun/toastify-js",
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "center",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(45deg, #555, #333)",
                        borderRadius: "10px",
                        textAlign: "center",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        border:"1px solid white",
                        opacity: "0.95"

                    },
                    onClick: function() {}
                }).showToast();
            });
        </script>
       
    <?php
        // Supprimer les variables de la session après avoir affiché le toast
        unset($_SESSION["toast_message"]);
        unset($_SESSION["toast_type"]);
    endif;
    ?>

</head>

<body>
    <h1 class="titre-principal">Historique des changements d'ampoules</h1>
    <table>
        <thead>
            <th>Date du changement</th>
            <th>Etage</th>
            <th>Position</th>
            <th>Prix de l'ampoule TTC</th>
            <th>Modifier / Supprimer</th>
        </thead>
        <tbody>
            <?php
            setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
            foreach ($articles as $changement) {
            ?>
                <tr>
                    <td class="animated-cell"><?= (new DateTime($changement['date_changement']))->format('d/m/Y') ?></td>
                    <td class="animated-cell"><?= $changement['etage'] ?></td>
                    <td class="animated-cell"><?= $changement['position'] ?></td>
                    <td class="animated-cell"><?= $changement['prix_ampoule'] ?></td>
                    <td>
                        <a class="modify-link btn-modif" href="modifier.php?id=<?= $changement['id'] ?>" onclick="modif(event)">Modifier</a>

                        <!-- onclick="modif(event)" : C'est un attribut d'événement qui spécifie le code JavaScript à exécuter lorsque le lien est cliqué. Dans ce cas, il appelle la fonction JavaScript "modif" en passant l'événement de clic (event) en tant que paramètre. -->
                        <a class="delete-link btn-suppr" href="supprimer.php?id=<?= $changement['id'] ?>" onclick="supprimer(event)">Supprimer</a>
                        <!-- onclick="supprimer(event)" : C'est un attribut d'événement qui spécifie le code JavaScript à exécuter lorsque le lien est cliqué. Dans ce cas, il appelle la fonction JavaScript "supprimer" en passant l'événement de clic (event) en tant que paramètre. -->
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination">
            <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
            <li class="pagination-item <?php if ($currentPage == 1) echo 'disabled'; ?>">
                <a href="./historique.php?page=<?php echo $currentPage - 1; ?>" class="pagination-link">&laquo; Précédente</a>
            </li> <!-- <?php if ($currentPage == 1) echo 'disabled'; ?> est une balise PHP intégrée dans le code HTML. Elle vérifie si la variable $currentPage est égale à 1. Si c'est le cas, la classe CSS "disabled" est ajoutée à l'élément de liste. Cela permet de désactiver le lien lorsqu'on se trouve sur la première page de la pagination. -->

            <!-- Cette ligne crée un lien (<a>) qui servira de lien vers la page précédente de la pagination. La partie href="./historique.php?page=<?php echo $currentPage - 1; ?>" définit l'URL du lien en ajoutant un paramètre page dont la valeur est $currentPage - 1. Cela permet de générer l'URL avec le numéro de page précédent. La classe CSS "pagination-link" est également appliquée au lien.
            Le texte "« Précédente" est le contenu du lien, affichant une flèche vers la gauche et le mot "Précédente". -->

            <?php for ($page = 1; $page <= $pages; $page++) { ?>
                <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                <li class="pagination-item <?php if ($currentPage == $page) echo 'active'; ?>">
                    <a href="./historique.php?page=<?php echo $page; ?>" class="pagination-link"><?php echo $page; ?></a>
                </li>
            <?php } ?>
            <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
            <li class="pagination-item <?php if ($currentPage == $pages) echo 'disabled'; ?>">
                <a href="./historique.php?page=<?php echo $currentPage + 1; ?>" class="pagination-link">Suivante &raquo;</a>
            </li>
        </ul>
    </nav>
    <br><br>

    <h1 class="titre2">Ajouter un changement d'ampoule</h1>
    <form action="" method="post">
        <label for="date_changement">Date de changement</label>
        <input type="date" name="date_changement" id="dates" required> <br> <br>
        <label for="etage">Etage</label>
        <select name="etage" id="etage" required>
            <option value="1er Etage">1er Etage</option>
            <option value="2eme Etage">2eme Etage</option>
            <option value="3eme Etage">3eme Etage</option>
            <option value="4eme Etage">4eme Etage</option>
            <option value="5eme Etage">5eme Etage</option>
            <option value="6eme Etage">6eme Etage</option>
            <option value="7eme Etage">7eme Etage</option>
            <option value="8eme Etage">8eme Etage</option>
        </select> <br> <br>
        <label for="position">Position</label>
        <select name="position" id="position" required>
            <option value="Nord">Nord</option>
            <option value="Sud">Sud</option>
            <option value="Est">Est</option>
            <option value="Ouest">Ouest</option>
        </select> <br> <br>
        <label for="prix">Prix de l'ampoule TTC</label>
        <input type="text" name="prix_ampoule" id="prix_ampoule" list="ampoule_prix" required placeholder="4€12 / 4$12 / 4£12" pattern="[0-9]+(€|\$|£)[0-9]{1,2}"> <br> <br>
        <!-- Explication du pattern [0-9]+(€|\$|£)[0-9]{1,2} :
        [0-9]+ : Correspond à un ou plusieurs chiffres de 0 à 9.
        (€|\$|£) : Correspond à l'alternative entre les symboles €, $ et £. L'opérateur | signifie "ou".
        [0-9]{1,2} : Correspond à un nombre de 1 à 2 chiffres de 0 à 9.
        Cela signifie que le champ de saisie acceptera des valeurs telles que 4€12, 25$9, 100£50, etc., où le premier nombre peut avoir plusieurs chiffres, suivi d'un des symboles €, $ ou £, et d'un nombre de 1 à 2 chiffres. -->

        <datalist id="ampoule_prix">
            <option>4€12</option>
            <option>3$55</option>
            <option>5£49</option>
        </datalist>

        <input class="boutton" type="submit" value="Enregistrer"> &emsp;
        <a class="deconnexion-link" href="deconnexion.php">Déconnexion</a>
    </form> <br>

    <script>
        function modif(event) {
            event.preventDefault();

            const confirmationBox = document.createElement('div');
            confirmationBox.className = 'confirmation';

            const message = document.createElement('p');
            message.textContent = 'Êtes-vous sûr de vouloir modifier cet élément ?';

            const confirmButton = document.createElement('button');
            confirmButton.textContent = 'Oui, modifier';
            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Annuler';
            cancelButton.className = 'cancel';

            confirmButton.addEventListener('click', function() {
                // Action à effectuer si l'utilisateur confirme la modification
                window.location.href = event.target.href;
                confirmationBox.remove();
            });

            cancelButton.addEventListener('click', function() {
                confirmationBox.remove();
            });

            confirmationBox.appendChild(message);
            confirmationBox.appendChild(confirmButton);
            confirmationBox.appendChild(cancelButton);
            document.body.prepend(confirmationBox);
        }

        function supprimer(event) {
            event.preventDefault();

            const confirmationBox = document.createElement('div');
            confirmationBox.className = 'confirmation';

            const message = document.createElement('p');
            message.textContent = 'Êtes-vous sûr de vouloir supprimer cet élément ?';

            const confirmButton = document.createElement('button');
            confirmButton.textContent = 'Oui, supprimer';
            const cancelButton = document.createElement('button');
            cancelButton.textContent = 'Annuler';
            cancelButton.className = 'cancel';

            confirmButton.addEventListener('click', function() {
                // Action à effectuer si l'utilisateur confirme la suppression
                window.location.href = event.target.href;
                confirmationBox.remove();
            });

            cancelButton.addEventListener('click', function() {
                confirmationBox.remove();
            });

            confirmationBox.appendChild(message);
            confirmationBox.appendChild(confirmButton);
            confirmationBox.appendChild(cancelButton);
            document.body.prepend(confirmationBox);
        }
    </script>

    <?php
    require_once("close.php");
    ?>

</body>

</html>
