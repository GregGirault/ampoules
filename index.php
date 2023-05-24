<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <title>Connexion</title>
</head>

<body>
  <div class="background"></div>
  <div class="container">
    <h2>Connexion</h2>
    <form method="POST" action="verification.php">
      <div class="form-group">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required placeholder="Entrez le nom d'utilisateur">
      </div>
      <div class="form-group">
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required placeholder="Entrez le mot de passe">
        <i class="fas fa-eye"></i> <!-- Icône d'œil -->
      </div>
      <div class="form-group">
        <input type="submit" class="centered-button" value="Connexion">
        <?php
        if (isset($_GET['erreur'])) {
          $err = $_GET['erreur'];
          if ($err == 1 || $err == 2)
            echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
        }
        ?>
      </div>
    </form>
  </div>
  <script>
    const passwordInput = document.getElementById('password'); // crée une variable passwordInput qui fait référence à l'élément HTML avec l'identifiant "password". Il est supposé que cet élément est un champ de saisie de mot de passe (<input type="password">).
    const eyeIcon = document.querySelector('.fa-eye'); // crée une variable eyeIcon qui fait référence à la première occurrence d'un élément HTML ayant la classe CSS "fa-eye". Il est supposé que cet élément est une icône représentant un œil.

    eyeIcon.addEventListener('click', function() { // Cette ligne ajoute un écouteur d'événements de clic à l'élément eyeIcon. Lorsque cet élément est cliqué, la fonction anonyme fournie sera exécutée.
      if (passwordInput.type === 'password') { // vérifie si le type de champ de saisie de mot de passe est actuellement défini sur "password". Cela permet de déterminer si le texte saisi est masqué ou non.
        passwordInput.type = 'text'; // modifie le type du champ de saisie de mot de passe en le définissant sur "text". Cela a pour effet d'afficher le texte saisi.
        eyeIcon.classList.remove('fa-eye'); // supprime la classe CSS "fa-eye" de l'élément eyeIcon. Cela change l'apparence de l'icône pour qu'elle représente un œil barré.
        eyeIcon.classList.add('fa-eye-slash'); // ajoute la classe CSS "fa-eye-slash" à l'élément eyeIcon. Cela change l'apparence de l'icône pour qu'elle représente un œil barré.
      } else { // Si la condition de la ligne 4 n'est pas satisfaite, c'est-à-dire si le type de champ de saisie de mot de passe n'est pas "password", cette partie du code est exécutée.
        passwordInput.type = 'password'; // modifie le type du champ de saisie de mot de passe en le définissant à nouveau sur "password". Cela a pour effet de masquer le texte saisi.
        eyeIcon.classList.remove('fa-eye-slash'); // supprime la classe CSS "fa-eye-slash" de l'élément eyeIcon. Cela change l'apparence de l'icône pour qu'elle représente un œil normal.
        eyeIcon.classList.add('fa-eye'); //  ajoute la classe CSS "fa-eye" à l'élément eyeIcon. Cela change l'apparence de l'icône pour qu'elle représente un œil.

        // En résumé, lorsque l'icône d'œil est cliquée, le code vérifie le type du champ de saisie de mot de passe. S'il est actuellement défini sur "password", cela signifie que le texte est masqué. Le code change alors le type du champ de saisie en "text", modifie l'apparence de l'icône pour représenter un œil barré, et vice versa. Cela permet à l'utilisateur de masquer ou d'afficher le texte du mot de passe en cliquant sur l'icône.
      }
    });
  </script>
</body>

</html>