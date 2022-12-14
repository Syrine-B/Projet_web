<?php

// cette page recupère l'id du genome choisi par l'utilisateur et affiche les informations présentent dans la BDD.
//Elle permet aussi le téléchagenment des information selectionner.

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();

// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom']))
{
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

   <body class="menu">
      <div class="text">
         <?php
            if(!empty($_GET['id_genome'])) { //si un id genome a bien été reçu
               $req = $bdd ->prepare("SELECT * FROM GENOME WHERE GENOME.id_genome = ?");
               $req-> execute(array($_GET['id_genome']));
               $reponse = $req->fetch ();
            }
         ?>

         <!-- Affichage des résultats de la requete -->

         <section id="container_resultats">
            <form action="dl_annot.php" method="post">
               <div id="position_box_recherche">
                  <table width="100%" class="largeur_tab">
                     <tr>
                        <td align="center">
                           <input style="width:330px" type="submit" name = "download" value="Télécharger les données séléctionnées">
                        </td>
                        <td align="center">
                           <p class="blast">
                              <a style="color:rgb(0, 0, 0);" href="http://localhost/visu_post_modif.php?id_genome=<?php echo($reponse['id_genome']); ?>">Visualisation</a>
                           </p>
                        </td>
                        <td align="center">
                           <p class="blast">
                              <a style="color:rgb(0, 0, 0);" href="http://localhost/blast_ncbi.php?id_genome=<?php echo($reponse['id_genome']); ?>">BLAST</a>
                           </p>
                        </td>
                     </tr>
                  </table>

                  <table class="tab_bottom" border="4" width="100%" bordercolor="#808080" frame="hsides" rules="rows">
                     <tr>
                        <td class="cell_ar">
                           <input type="checkbox" name="id_genome" value="<?php echo($reponse['id_genome']); ?>" > <br><br>
                        </td>

                        <td class="cell_ar">
                           <b> Id du génome<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['id_genome']); ?><br><br>
                        </td>

                     </tr>
                     <tr>
                        <td class="cell_ar">
                           <input type="checkbox" name="taille_genom" value="<?php echo($reponse['taille_genom']); ?>"> <br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Taille du génome<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['taille_genom']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="organisme" value="<?php echo($reponse['organisme']); ?>"> <br><br>
                        </td>

                        <td class="cell_ar">
                           <b> Nom de l'organisme<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['organisme']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="souche_organisme" value="<?php echo($reponse['souche_organisme']); ?>"> <br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Souche de l'organisme<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['souche_organisme']); ?><br><br>
                        </td>

                     </tr>
                     <tr>
                        <td class="cell_gb">
                           <input type="checkbox" name="sequence" value="<?php echo($reponse['sequence']); ?>"><br><br>
                        </td>

                        <td class="cell_gb">
                           <b>Séquence du génome<br><br></b>
                        </td>

                        <td class="cell_ar" style="width:600px; word-wrap:break-word; display:inline-block;">
                           <?php echo(str_replace("\n",'',$reponse['sequence'])); ?><br><br>
                        </td>

                     </tr>

                  </table>

               </div>
            </form>
         </section>

         <?php
         } //fin du si connecter
         else {
         header("Location: login.php");
         }
         ?>

      </div>

   </body>
   <div class="footer_scroll">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>
</html>
