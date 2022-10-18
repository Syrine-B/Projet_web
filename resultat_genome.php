<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom'])){
   $array = [];
   if (!empty($_POST['id_genome'])){
      $requete ="SELECT * FROM GENOME WHERE GENOME.id_genome =  ? " ;
      $array[]=$_POST['id_genome'];
   }
   else{

      $requete = "SELECT * FROM GENOME AS G1, GENOME AS G2 WHERE G1.id_genome = G2.id_genome " ;

      if (!empty($_POST['sequence'])){
         $requete.= "AND G1.sequence LIKE ? ";
         $array[]='%'.$_POST['sequence'].'%';
      }

      if (!empty($_POST['taille_genom'])){
         $requete.= "AND G1.taille_genom = ?";
         $array[]=$_POST['taille_genome'];
      }

      if (!empty($_POST['organisme'])){
         $requete.= "AND G1.organisme LIKE ? ";
         $array[]='%'.$_POST['organisme'].'%';
      }

      if (!empty($_POST['souche_organisme'])){
         $requete.= "AND G1.souche_organisme = ? ";
         $array[]=$_POST['souche_organisme'];
      }

   }

   $req = $bdd ->prepare($requete);
   $req ->execute($array);
   $resultat = $req->fetchAll();

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
               <li><a href="http://localhost/accueil.php">Accueil</a></li>
               <li><a href="http://localhost/recherche_sequence.php">Recherche CDS</a></li>
               <li><a href="#about">Recherche génome</a></li>
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
                     echo('<li><a href="http://localhost/modif_role.php">Administrer</a></li>');
                     echo('<li><a href="http://localhost/parseur_form.php">Ajouter des données</a></li>');

               }
               ?>
            </ul>
         </form>
      </div>
   </head>
   
   <body>
      <?php
      if(empty($resultat)) {
         ?>
         <section id="container">
         <div id="position_box_recherche">
            <hr class="short"><br><br><br>
            <p align="center">
            <b>Pas de résultats</b>
            </p><br><br><br>
            <hr class="short">
            </div>
         </section>

         <?php
      }
      else {
         ?>
         <section id="container">
            <div id="position_box_recherche">
            <?php
            foreach ($resultat as $key => $genome){
               ?>
               <p align="center">
                  <a href ="http://localhost/affichage_resultat_genome.php?id_genome=<?php echo($genome['id_genome']);?>">
                  
                  <?php echo($genome['id_genome']);?>

                  <br><hr class="short">
                  </a>
               </p>

            <?php
            }
      }
      ?>
            </div>
         </section>
   </body>  
   
   <?php
   } //fin du si connecter
   else {
      header("Location: login.php");
   }
   ?>
</html>
