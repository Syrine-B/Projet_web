<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
 $bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom']) and ($_SESSION['role'] == 'validateur' || $_SESSION['role'] == 'administrateur')){
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
                  <li><a href="#about">Valider les annotations </a></li>

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
      <section style="width:1600px; margin:0 auto; margin-top:3.5%;">
         <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">


            <?php

            if (isset($_POST["valider"])){
               $updateannot = $bdd->prepare("UPDATE ANNOTATION SET  etat_validation = 'valider' WHERE id_annotation = ?;");
               $updateannot -> execute(array( $_POST["id_annotation"]));
               $valide = "Annotation validée avec succès.";
            }
            else if (isset($_POST["refuser"])){
               $updatembr = $bdd->prepare("UPDATE ANNOTATION SET  etat_validation = 'en_cours' WHERE id_annotation = ?;");
               $updatembr ->execute( array($_POST['id_annotation']));
               $valide = "Annotation refusée avec succès.";
            }

            $requserToValid = $bdd -> prepare("SELECT * FROM ANNOTATION WHERE etat_validation = 'terminer';");
            $requserToValid -> execute();

            ?>

            <section>
               <u>Liste des annotations à valider : </u><br><br>
               <?php
               $item=1;
               while ($datauser = $requserToValid->fetch()){
                  ?>
                  <div class = "item" id = "<?php echo($item); ?>" <?php if($item > 9){echo("hidden");} ?> >
                     <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                        &nbsp; &nbsp; &nbsp; &nbsp; Nom du gène :
                        <input class="affichage_annot" size="8" type="text" name="name" value="<?php echo($datauser["gene_nom"]); ?>"disabled>

                        &nbsp; &nbsp; &nbsp; &nbsp; Numéro de chromosome :
                        <input class="affichage_annot" size="1" type="text" name="chr" value="<?php echo($datauser["num_chr"]); ?>"disabled>

                        &nbsp; &nbsp; &nbsp; &nbsp; Biotype du gène :
                        <input class="affichage_annot" size="10" type="text" name="gene_biotype" value="<?php echo($datauser["gene_biotype"]); ?>"disabled>

                        &nbsp; &nbsp; &nbsp; &nbsp; Biotype du transcrit :
                        <input class="affichage_annot" size="10"  type = "text" name = "transcript_biotype" value = "<?php echo($datauser["trancript_biotype"]); ?>" disabled>
                        <br><br>
                        &nbsp; &nbsp; &nbsp; &nbsp; Symbole du gène :
                        <input class="affichage_annot" size="5"  type = "text" name = "gene_symbole" value = "<?php echo($datauser["gene_symbole"]); ?>" disabled>

                        &nbsp; &nbsp; &nbsp; &nbsp; Description du CDS :
                        <input class="affichage_annot" size="10"  type = "text" name = "descrip_cds" value = "<?php echo($datauser["descrip_cds"]); ?>" disabled>

                        &nbsp; &nbsp; &nbsp; &nbsp; Taille du CDS :
                        <input class="affichage_annot" size="1"  type = "text" name = "taille_cds" value = "<?php echo($datauser["taille_cds"]); ?>" disabled>

                        <input type = "hidden" name = "id_annotation" value = "<?php echo($datauser["id_annotation"]); ?>">
                        <br>
                        &nbsp; &nbsp; &nbsp; &nbsp; <input type = "submit" name = "valider" value = "valider les annotations">

                        &nbsp; &nbsp; &nbsp; &nbsp; <input class="bouton_refuser" type = "submit" name = "refuser" value = "refuser les annotations">

                     </form>
                  </div>
                  <br>
                  <hr>
                  <?php
                  $item++;
               }
            ?>
               <table width="100%">
                  <td width="85%">
                     <p align="center" class="blast">
                        <a style="text-decoration:none" href="javascript:changePage('precedent')">page précédente</a>
                     </p>
                  </td>
                  <td width="15%">
                     <p align="center" class="blast">
                        <a style="text-decoration:none" href="javascript:changePage('suivant')">page suivante</a>
                     </p>
                  </td>
               </table>
               <?php
               if(isset($valide)){
                  echo "<br> <br>";
                  echo '<font color = "green">'.$valide.'</font>';
               }
               ?>
            
         </section>

         <script>
            var items = document.getElementsByClassName('item')

            var currentpage = 0

            function changePage(page){

               if(page == "suivant" && currentpage!=items.length){

                  for(var i =0; i<items.length; i++) {
                     items[i].setAttribute("hidden",true);
                  };

                  currentpage += 1

                  document.getElementById((currentpage*8) +1).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +2).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +3).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +4).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +5).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +6).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +7).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +8).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +9).removeAttribute("hidden");

               }
               else if(page == "precedent" && currentpage!=0){

                  for(var i =0; i<items.length; i++) {
                     items[i].setAttribute("hidden",true);
                  };
                  currentpage -= 1
                  document.getElementById((currentpage*8) +1).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +2).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +3).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +4).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +5).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +6).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +7).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +8).removeAttribute("hidden");
                  document.getElementById((currentpage*8) +9).removeAttribute("hidden");
               }
            }
         </script>
      </div>
      </section>
   </body>
   
   <div class="footer">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>

 </html>

<?php
}

else {
 header("Location: login.php");
}
?>
