<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom'])){
   $array = [];

   if (!empty($_POST['ID_seq'])){
      $requete ="SELECT * FROM SEQUENCE, ANNOTATION WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation  AND SEQUENCE.id_seq = ? " ;
      $array[]=$_POST['id_seq'];
   }
   else{
      $requete = "SELECT * FROM SEQUENCE, ANNOTATION WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation " ;

      if (!empty($_POST['seqn'])){
         $requete.= "AND SEQUENCE.seq_dna LIKE ? ";
         $array[]='%'.$_POST['seqn'].'%';
      }

      if (!empty($_POST['seqp'])){
         $requete.= "AND SEQUENCE.seq_pep LIKE ? ";
         $array[]='%'.$_POST['seqp'].'%';
      }

      if (!empty($_POST['ID_seq'])){
         $requete.= "AND SEQUENCE.id_seq = ? ";
         $array[]=$_POST['ID_seq'];
      }

      if (!empty($_POST['bio_gene'])){
         $requete.= "AND ANNOTATION.gene_biotype LIKE ? ";
         $array[]='%'.$_POST['gene_biotype'].'%';
      }

      if (!empty($_POST['bio_transcrit'])){
         $requete.= "AND ANNOTATION.transcrit_biotype LIKE ? ";
         $array[]='%'.$_POST['transcrit_biotype'].'%';
      }

      if (!empty($_POST['descri_cds'])){
         $requete.= "AND ANNOTATION.descrip_cds LIKE ? ";
         $array[]='%'.$_POST['descrip_cds'].'%';
      }

      if (!empty($_POST['nom_gene'])){
         $requete .= "AND ANNOTATION.gene_nom = ? " ;
         $array[]=$_POST['nom_gene'];
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

   <body>
      <?php
      if(empty($resultat)) {
         ?>
         <section id="container">
            <br><br><br><br><br>
            <div id="position_box_recherche">
               <hr class="short"><br><br><br>
               <p align="center">
                  <b>Pas de résultats</b>
               </p>
               <br><br><br><br>
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
               $item=1;
               foreach ($resultat as $key => $sequence){
               ?>
                  <div class = "item" id = "<?php echo($item); ?>" <?php if($item > 9){echo("hidden");} ?> >
                     <p align="center">
                        <a href ="http://localhost/affichage_resultat_sequence.php?id_seq=<?php echo($sequence['id_seq']);?>">
                        <?php echo($sequence['id_seq']);?></a>
                        <br><hr class="short"> 
                     </p>
                  </div>

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

            </div>
         </section>
         <?php
      }
      ?>
            
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

   </body>
   
   <?php
   } //fin du si connecter
   else {
      header("Location: login.php");
   }
   ?>

   <div class="footer">
      &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
   </div>
</html>
