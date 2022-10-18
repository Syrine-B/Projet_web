<?php

// cette page recupère l'id de la sequence choisi par l'utilisateur et affiche les informations présentent dans la BDD.
// elle permet le téléchargement des données selectionné

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
                  <li><a class="active" href="http://localhost/accueil.php">Accueil</a></li>
                  <li><a href="#about">Recherche CDS</a></li>
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
            if(!empty($_GET['id_seq'])) {

            $req = $bdd ->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation AND SEQUENCE.id_seq = ?");
            $req-> execute(array($_GET['id_seq']));
            $reponse = $req->fetch ();

            }
         ?>
         <section id="container_resultats">
            <form action="dl_annot.php" method="post">
               <div id="position_box_recherche">
                  <table class="tab_bottom" border="4" width="100%" bordercolor="#808080" frame="hsides" rules="rows">
                     <tr>
                        <td class="cell_ar">
                           <input type="checkbox" name="id_seq" value="<?php echo($reponse['id_seq']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b> Id de la sequence<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['id_seq']); ?><br><br>
                        </td>
                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="gene_nom" value="<?php echo($reponse['gene_nom']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Nom du gène<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['gene_nom']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="gene_symbole" value="<?php echo($reponse['gene_symbole']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Symbole du gène<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['gene_symbole']); ?><br><br>
                        </td>

                     </tr>

                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="descript_cds" value="<?php echo($reponse['descrip_cds']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Description du CDS<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['descrip_cds']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="gene_biotype" value="<?php echo($reponse['gene_biotype']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Biotype du gène<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['gene_biotype']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="transcript_biotype" value="<?php echo($reponse['trancript_biotype']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Biotype du transcrit<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['trancript_biotype']); ?><br><br>
                        </td>

                     </tr>

                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="taille_cds" value="<?php echo($reponse['taille_cds']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Taille de la séquence (nucl)<br><br></b>
                        </td>

                        <td class="cell_ar">
                           <?php echo($reponse['taille_cds']); ?><br><br>
                        </td>

                     </tr>
                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="seq_dna" value="<?php echo($reponse['seq_dna']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b> Séquence nucléotidique<br><br></b>
                        </td>

                        <td class="cell_ar" style="width:600px; word-wrap:break-word; display:inline-block;">
                           <?php echo(str_replace("\n",'',$reponse['seq_dna'])); ?><br><br>
                        </td>
                     </tr>

                     <tr>

                        <td class="cell_ar">
                           <input type="checkbox" name="seq_pep" value="<?php echo($reponse['seq_pep']); ?>">�<br><br>
                        </td>

                        <td class="cell_ar">
                           <b>Séquence peptidique<br><br></b>
                        </td>

                        <td class="cell_ar" style="width:600px; word-wrap:break-word; display:inline-block;">
                           <?php echo(str_replace("\n",'',$reponse['seq_pep'])); ?><br><br>
                        </td>

                     </tr>
                  </table>
                  <table class="largeur_tab">
                     <tr>
                        <td align="center">
                           <input style="width:330px" type="submit" name = "download" value="Télécharger les données séléctionnées">
                        </td>
                        <td align="center">
                           <p class="blast">
                              <a align="center" style="color:rgb(0, 0, 0);" href="http://localhost/blast_ncbi.php?id_seq=<?php echo($reponse['id_seq']); ?>">BLAST</a>
                           </p>
                        </td>
                        <td align="center">
                           <p class="blast">
                              <a align="center" style="color:rgb(0, 0, 0);" target="_blank" href="https://www.uniprot.org/uniprot/?query=<?php echo($reponse['id_seq']); ?>">Uniprot</a>
                           </p>
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

   <div class="footer">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>
</html>
