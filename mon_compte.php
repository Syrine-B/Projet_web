<?php

// cette page permet a l'utilisateur de voir les information qu'il a communiquer a son inscription et de changer son mot de passe si il le souhaite

session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom'])){
  $user = $bdd->prepare("SELECT * FROM UTILISATEUR WHERE id_utilisateur = ? ;");
  $user-> execute(array( $_SESSION["id"]));
  $utilisateur = $user->fetch();
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
               }//fin if
               ?>

               <?php
               if($_SESSION['role']== 'validateur' || $_SESSION['role']== 'administrateur'){
               ?>
                  <li><a href="http://localhost/valid_annot.php">Valider les annotations </a></li>
                  <li><a href="http://localhost/affect_annot.php">Assigner séquences</a></li>

               <?php
               }//fin if
               ?>

               <?php
               if($_SESSION['role'] == 'administrateur'){
                  echo('<li><a href="http://localhost/modif_role.php">Administrer</a></li>');
                  echo('<li><a href="http://localhost/parseur_form.php">Ajouter des données</a></li>');

               }//fin if
               ?>
            </ul>
         </form>
      </div>
   </head>

   <body>
      <section id="container">
         <div id="position_box_recherche">
            <h1 align=center>Informations personnelles :</h1>
            <br>
            <table align=center>
               <tr>
                  <td>
                     <b>Nom</b>
                  </td>
                  <td>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                     <?php echo($utilisateur["nom"]); ?>
                  </td>
               </tr>

               <tr>
                  <td>
                     <b>Prénom</b>
                  </td>

                  <td>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                     <?php echo($utilisateur["prenom"]); ?>
                  </td>
               </tr>

               <tr>
                  <td>
                     <b>Numéro de téléphone</b>
                  </td>

                  <td>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                     <?php echo($utilisateur["numtel"]); ?>
                  </td>
               </tr>

               <tr>
                  <td>
                     <b>Adresse mail</b>
                  </td>

                  <td>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                     <?php echo($utilisateur["email"]); ?>
                  </td>
               </tr>

               <tr>
                  <td>
                     <b>Role</b>
                  </td>

                  <td>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                     <?php echo($utilisateur["role"]); ?>
                  </td>
               </tr>
            </table>

            <br>

            <p align="center">
               <input type="submit" name="inscription" onclick="self.location.href='chang_mdp.php';" value='Changer de mot de passe'>
            </p>
         </div>
      </section>
   </body>



   <?php
   }
   else {
   header("Location: login.php");
   }
   ?>
   <div class="footer">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>
</html>
