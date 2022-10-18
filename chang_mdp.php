<?php

// cette page permet a l'utilisateur de changer son mot de passe

ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom']))
{

  $id_utilisateur = $_SESSION['id'];

  if(isset($_POST['connexion']))
  {
     if(isset($_POST['old_password']) && isset($_POST['password']))
     {
        $old_password = $_POST['old_password'];
        $password =$_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if(!empty($old_password) && !empty($password))
        {

           //  Récupération de l'utilisateur
           $req = $bdd->prepare('SELECT * FROM UTILISATEUR WHERE id_utilisateur = ? ');
           $req->execute(array($id_utilisateur));
           $resultat = $req->fetch();

           // Comparaison du mot de passe envoyé via le formulaire avec la base
           $isPasswordCorrect = password_verify($_POST['old_password'], $resultat['mdp']);

           if (!$resultat){
               echo 'Mauvais mot de passe !';
           }
           else
           {
             if($password == $confirm_password){

               if ($isPasswordCorrect) {

                  $chang_pass = $bdd->prepare('UPDATE UTILISATEUR SET mdp = ? WHERE id_utilisateur = ?');
                  $chang_pass->execute(array( password_hash($password, PASSWORD_DEFAULT) ,$id_utilisateur));

                   header("Location: mon_compte.php");
                   echo 'Mot de passe modifié avec succès';
               }
               else {
                   echo 'Mauvais mot de passe !';
               }

             }
             else{
               echo 'Vos mot de passe ne correspondent pas';
             }

           }

        }
     }
  }
  ?>


  <html>
    <head>
        <meta charset="utf-8">

        <link rel="stylesheet" href="login.css?t=<? echo time(); ?>" media="screen" type="text/css" />
    </head>
    <body>
      <div id="container">
        <form action="chang_mdp.php" method="POST">
          <h2 align=center>Changement de mot de passe</h2>

          <label><b>Ancien mot de passe</b></label>
          <input type="password" placeholder="Entrer le mot de passe" name="old_password" required>

          <label><b>Nouveau mot de passe</b></label>
          <input type="password" placeholder="Entrer le nouveau mot de passe" name="password" required>

          <label><b>Confirmez le nouveau mot de passe</b></label>
          <input type="password" placeholder="confirmer le mot de passe" name="confirm_password" required>

          <input type="submit" name='connexion' value='Appliquer les changements' >

        </form>
      </div>
    </body>
  </html>

<?php
}
else {
header("Location: login.php");
}
 ?>
