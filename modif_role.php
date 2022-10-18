<?php

// cette page permet a l'administarteur de gerer les autre utilisateurs, de changer leur role ou d'accepter/refuser les inscriptions

ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom']) and $_SESSION['id'] == 1 ){
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <?php require "header.php"; ?>
      <link type="text/css" rel="stylesheet" href="accueil.css?t=<? echo time(); ?>" /> <!--lien css-->

      <div class="header">
         <form action="res.php" method="post">
            <ul id="navlist">
               <li><a class="active" href="http://localhost/accueil.php">Accueil</a></li>
               <li><a href="http://localhost/recherche_sequence.php">Recherche CDS</a></li>
               <li><a href="http://localhost/recherche_genome.php">Recherche génome</a></li>
               <li><a href="http://localhost/blast_ncbi.php">BLAST</a></li>
               <li><a href="http://localhost/annotation.php">Toutes les annotations</a></li>
               <li><a href="http://localhost/visualisation.php">Visualiser génome</a></li>
               <?php
               if($_SESSION['role']!= 'lecteur'){
                  ?>
                  <li><a href="http://localhost/mes_annotation.php">Mes annotations</a></li>
                  <li><a href="http://localhost/forum.php">Forum</a></li>


               <?php
               }
               ?>

               <?php
               if($_SESSION['role']== 'validateur' || $_SESSION['role']== 'administrateur'){
               ?>
                  <li><a href="http://localhost/valid_annot.php">Valider les annotations </a></li>
                  <li><a href="http://localhost/affect_annot.php">Assigner séquences</a></li>
               <?php
               }
               ?>
               <?php
               if($_SESSION['role'] == 'administrateur'){
                  echo('<li><a href="#about">Administrer</a></li>');
                  echo('<li><a href="http://localhost/parseur_form.php">Ajouter des données</a></li>');
               }
               ?>
            </ul>
         </form>
      </div>
   </head>

   <body>
      <?php
      if (isset($_POST["valider"])){
         $updatembr = $bdd->prepare("UPDATE UTILISATEUR SET  inscription_valide = '2' WHERE id_utilisateur = ?;");
         $updatembr -> execute(array( $_POST["id"]));
         $valide = "Inscription validée avec succès.";

         $email = $bdd->prepare("SELECT email FROM UTILISATEUR WHERE id_utilisateur = ? ;");
         $email-> execute(array( $_POST["id"]));
         $destinataire = $email->fetch();

         // Contenu du mail
         $message = "Votre inscription au site GENOMA a été validée";

         // Envoi du mail
         mail($destinataire['email'], 'inscription validée', $message);

      }
      else if (isset($_POST["refuser"])){
         $email = $bdd->prepare("SELECT email FROM UTILISATEUR WHERE id_utilisateur = ? ;");
         $email-> execute(array( $_POST["id"]));
         $destinataire = $email->fetch();

         $updatembr = $bdd->prepare("UPDATE UTILISATEUR SET  inscription_valide = '1' WHERE id_utilisateur = ?;");
         $updatembr -> execute(array( $_POST["id"]));
         $valide = "Inscription refusée avec succès.";

      }
      else if(isset($_POST["envoyer"])){
      $email = $bdd->prepare("SELECT email FROM UTILISATEUR WHERE id_utilisateur = ? ;");
      $email-> execute(array( $_POST["id"]));
      $destinataire = $email->fetch();

      mail($destinataire['email'], 'Demande d information', $_POST["message"]);

      $valide = "Mail envoyé avec succès.";
      }

      else if(isset($_POST["id"])){
         $updatembr = $bdd->prepare("UPDATE UTILISATEUR SET  role = ? WHERE id_utilisateur = ?;");
         $updatembr -> execute(array( $_POST["statut"], $_POST["id"]));
         $valide = "Base de données modifiée avec succès.";
         $email = $bdd->prepare("SELECT email FROM UTILISATEUR WHERE id_utilisateur = ? ;");
         $email-> execute(array( $_POST["id"]));
         $destinataire = $email->fetch();

      // Contenu du mail
      $message = "L'administrateur vous a attribué un nouveau role : ".$_POST["statut"];

      // Envoi du mail
      mail($destinataire['email'], 'changement role', $message);

      }



      $requser = $bdd -> prepare("SELECT * FROM UTILISATEUR WHERE id_utilisateur != '1'AND inscription_valide = 2;");
      $requser -> execute();

      $requserrefused = $bdd -> prepare("SELECT * FROM UTILISATEUR WHERE id_utilisateur != '1' AND inscription_valide = 1;");
      $requserrefused-> execute();

      $requserToValid = $bdd -> prepare("SELECT * FROM UTILISATEUR WHERE id_utilisateur != '1' AND inscription_valide = 0;");
      $requserToValid -> execute();
      ?>

      <section style="width:1500px; margin:0 auto; margin-top:3.5%;">
         <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">
            <h3 align="center"><b>Liste des utilisateurs récemment inscrits</b></h3>

            <?php
            while ($datauser = $requserToValid->fetch()){
            ?>
               <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                  Nom :
                  <input type="text" name="name" value="<?php echo($datauser["nom"]); ?>"disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; Prénom :
                  <input type="text" name="firstname" value="<?php echo($datauser["prenom"]); ?>"disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; Role :
                  <input type="text" name="statut" value="<?php echo($datauser["role"]); ?>"disabled>

                  <input type = "hidden" name = "id" value = "<?php echo($datauser["id_utilisateur"]); ?>">

                  &nbsp; &nbsp; &nbsp; &nbsp; Dernière Connexion :
                  <input type = "text" name = "date" value = "<?php echo($datauser["date_connex"]); ?>" disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; <input type = "submit" name = "valider" value = "valider inscription">

                  &nbsp; &nbsp; &nbsp; &nbsp; <input type = "submit" name = "refuser" value = "refuser inscription">

               </form>
            <?php
            }
            ?>

               <?php

            if(isset($valide)){
               echo "<br> <br>";
               echo '<font color = "green">'.$valide.'</font>';
            }
            ?>
            <br>
         </div>
      </section>
      <br>

      <section style="width:1500px; margin:0 auto; margin-top:3.5%;">
         <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">
            <h3 align="center"><b>Modifier les droits d'un utilisateur</b></h3>
            <?php
            while ($datauser = $requser->fetch()){
            ?>

            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">

               Nom :
               <input type="text" name="name" value="<?php echo($datauser["nom"]); ?>"disabled>

               &nbsp; &nbsp; &nbsp; &nbsp; Prénom :
               <input type="text" name="firstname" value="<?php echo($datauser["prenom"]); ?>"disabled>

               &nbsp; &nbsp; &nbsp; &nbsp; Role :
               <select name="statut" >

                  <option value = 'annotateur'
                     <?php
                     if($datauser['role']=='annotateur')
                     {
                        echo ("selected");
                     }
                     ?> >
                     annotateur
                  </option>

                  <option value = 'validateur'
                     <?php
                     if($datauser['role']=='validateur')
                     {
                        echo ("selected");
                     }
                     ?> >
                     validateur
                  </option>

                  <option value = 'lecteur'
                     <?php
                     if($datauser['role']=='lecteur'){
                        echo ("selected");
                     }
                     ?> >
                     lecteur
                  </option>
               </select>

               <input type = "hidden" name = "id" value = "<?php echo($datauser["id_utilisateur"]); ?>">

               &nbsp; &nbsp; &nbsp; &nbsp; Dernière Connexion :
               <input type = "text" name = "date" value = "<?php echo($datauser["date_connex"]); ?>" disabled>

               &nbsp; &nbsp; &nbsp; &nbsp; <input type = "submit" value = "Appliquer les changements">
            </form>
            <?php
            }
            ?>
         </div>
      </section>
      <br>

      <section style="width:1500px; margin:0 auto; margin-top:3.5%;">
         <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">
            <h3 align="center"><b>Liste des utilisateurs refusé </b></h3>

            <?php
               while ($datauser = $requserrefused->fetch()){
            ?>

               <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">

                  Nom :
                  <input type="text" name="name" value="<?php echo($datauser["nom"]); ?>"disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; Prénom :
                  <input type="text" name="firstname" value="<?php echo($datauser["prenom"]); ?>"disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; Role :
                  <input type="text" name="statut" value="<?php echo($datauser["role"]); ?>"disabled>

                  <input type = "hidden" name = "id" value = "<?php echo($datauser["id_utilisateur"]); ?>">

                  &nbsp; &nbsp; &nbsp; &nbsp; Dernière Connexion :
                  <input type = "text" name = "date" value = "<?php echo($datauser["date_connex"]); ?>" disabled>

                  &nbsp; &nbsp; &nbsp; &nbsp; <input style="width:150px;" type = "submit" name = "valider" value = "valider inscription">
                  <br>
                  corps du mail:
                  <input type="text" name="message">

                  <input style="width:150px;" type = "submit" name = "envoyer" value = "Envoyer le mail">
                  <hr>

               </form>
            <?php
            }//fin while
            if(isset($valide)){
               echo "<br> <br>";
               echo '<font color = "green">'.$valide.'</font>';
            }

            ?>
            <br>
         </div>
      </section>
      <br>
   </body>

   <div class="footer_scroll">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>
</html>

<?php
}
else {
   header("Location: login.php");
}
?>
