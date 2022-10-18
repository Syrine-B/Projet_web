<?php

// ici nous avons la page principale qui  permet ensuite de lancer les parseurs en arrière plan  een fonction du type de données présent dans les fichiers

session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom']) && $_SESSION['role'] == 'administrateur'){

  if(isset($_POST['genome'])){
    include("parseur_genome.php");
  }
  if(isset($_POST['peptide'])){
    include("parseur_peptide.php");
  }
  if(isset($_POST['sequence'])){
    include("parseur_cds.php");
  }

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
              echo('<li><a href="http://localhost/modif_role.php">Administrer</a></li>');
              echo('<li><a href="#about">Ajouter des données</a></li>');

          }
          ?>
        </ul>
      </form>
    </div>
  </head>

  <br><br>

  <body class="corps">
    <section id="container">
      <div id="position_box_recherche">
        <form action="parseur_form.php" method="post" enctype="multipart/form-data">
          <div>
            <label for="photo">
            <b><p align="center">Choisir fichier au format .fa </p></b>
            </label><br>

            <p align="center">
              <input type="file" name="file" accept=".fa"/>
            </p>
          </div>

          <div>
            <p align="center">
              <input type="submit" style="width:330px" name="genome" value="Mon fichier contient un génome" class="primary">
            </p>
          </div>

          <div>
            <p align="center">
              <input type="submit" style="width:330px" name="peptide" value="Mon fichier contient une séquence peptidique" class="primary">
            </p>
          </div>

          <div >
            <p align="center">
              <input type="submit" style="width:370px" name="sequence" value="Mon fichier contient une séquence nucléotidique" class="primary">
            </p>
          </div>
        </form>
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
