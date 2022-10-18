<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom'])){
   $array = [];

   if (!empty($_POST['symbole'])){
      $requete ="SELECT * FROM SEQUENCE, ANNOTATION WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation AND ANNOTATION.gene_symbole = ? " ;
      $array[]=$_POST['symbole'];
   }
   else{
      if (!empty($_POST['nom_gene'])){
         $requete ="SELECT * FROM SEQUENCE, ANNOTATION WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation AND ANNOTATION.gene_nom = ? " ;
         $array[]=$_POST['nom_gene'];
      }
      else{
         $requete = "SELECT * FROM SEQUENCE,ANNOTATION WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation " ;
      }

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

      if (!empty($_POST['ID_gen'])){
         $requete.= "AND SEQUENCE.id_genome = ? ";
         $array[]=$_POST['ID_gen'];
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
               <li><a href="http://localhost/visualisation.php">Genome browser</a></li>
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

   <?php
   if(empty($resultat)) {
      ?>
      <section>
         <br><br><br><br><br><br><br><br><hr class="short"><br><br><br>
         <p align="center">
            <b>Pas de résultats</b>
          </p><br><br><br><br>
          <hr class="short">
      </section>
      <?php
   }
   else {
      foreach ($resultat as $key => $genome){
         ?>
         <br><br>
         <p align="center">
            <a href ="http://localhost/affichage_resultat_sequence.php?id_seq=<?php echo($genome['ID_seq']);?>" >
            <?php echo($genome['id_genome']);?>
            <br><hr class="short">
            </a>
         </p>
         <?php
      }
   }
   ?>

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
