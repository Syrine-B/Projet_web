<?php
session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom'])) //verifie si la personne est connécté
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

              <!-- Les differents lien entre les pages ainsi que leur accès en fonction du role -->

              <ul id="navlist">
                  <li><a class="active" href="#about">Accueil</a></li>
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
                      echo('<li><a href="http://localhost/parseur_form.php">Ajouter des données</a></li>');

                  }
                  ?>
              </ul>
            </form>
        </div>
    </head>


    <body class="corps">
        <section id="container_accueil">
            <div id="position_box_accueil">
            <br>
              Bienvenue sur Genoma. <br><br>Ce site d'annotation a été crée dans le cadre de l'UE de programmation Web du M2 Biologie Computationnelle - Analyse, Modélisation et Ingénierie de l'Information Biologique et Médicale (AMI2B)
              de l'Université Paris-Saclay.<br><br>
              Il permet de réaliser l’annotation et l’analyse fonctionnelle de génomes bactériens (E.Coli).
              Il présente différentes fonctionnalités telle que la visualisation de génome, le blast de séquences, ou encore la visualisation de génomes.
              <br><br>
              Nous vous souhaitons une bonne visite et espérons que ce site saura satisfaire votre curiosité.

              <br><br>

              Pour plus d'informations sur notre Master: <a href="https://www.universite-paris-saclay.fr/formation/master/bio-informatique/m2-biologie-computationnelle-analyse-modelisation-et-ingenierie-de-linformation-biologique-et-medicale">Master AMI2B</a>

            </div>
        </section>
    </body>

    <?php
    }
    else {
      //si la personne n'est pas connecté elle est renvoyer sur la page login
    header("Location: login.php");
    }
    ?>

    <div class="footer">
        &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
    </div>

</html>
