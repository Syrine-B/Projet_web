<?php

// cette page permet de visualisé de manière graphique un genome. elle a été realisé a l'aide d'un template

ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom'])){
?>

<!DOCTYPE html>
<html>
    <head>

      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/regenerator-runtime@0.13.3/runtime.min.js"></script>
      <script type="text/javascript" src="https://unpkg.com/seqviz"></script>

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
              <li><a href="#about">Visualiser génome</a></li>
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
      <section style="width:1500px; margin:0 auto; margin-top:3.5%;">
        <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">
          <?php

          $genome = $_GET["id_genome"]; //renvoie la var car ce n'est pas une variable session


          $reqgenome = $bdd -> prepare("SELECT * FROM GENOME WHERE id_genome = ?;"); //récupère le genome dans la BD
          $reqgenome -> execute(array($genome)); // ? = genome
          $donneesgen = $reqgenome->fetch(); // toutes les colonnes de la première ligne


          $reqcds = $bdd -> prepare("SELECT SEQUENCE.id_seq, ANNOTATION.id_annotation, ANNOTATION.debut_cds, ANNOTATION.fin_cds, ANNOTATION.sens FROM ANNOTATION,SEQUENCE,GENOME  WHERE GENOME.id_genome = ? AND ANNOTATION.id_annotation = SEQUENCE.id_annotation AND GENOME.id_genome = SEQUENCE.id_genome ORDER BY debut_cds;");
          $reqcds -> execute(array($genome));

          $reqesp = $bdd -> prepare("SELECT GENOME.organisme FROM GENOME WHERE GENOME.id_genome = ? ;");
          $reqesp -> execute(array($genome));
          $donneesesp = $reqesp->fetch();

          $taille = $donneesgen["taille_genom"];
          $souche_name_genome = $donneesgen["souche_organisme"];
          $espece = $donneesesp["organisme"];
          ?>

          <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
            <input type="range" id="zoom" name ="zoom" oninput="updateTextInput(this.value)" value="<?php echo(!empty($_POST['zoom'])?$_POST['zoom']:2) ?>" min="1" max="100">
            <input type="submit" name ="zoomer" value="Zoomer">
          </form>

          <input type="text" id="textInput" value="<?php echo(!empty($_POST['zoom'])?$_POST['zoom']:2) ?>" min="1" max="100"" ></input>

          <div id="root" style="width:250px"></div>
            <script>
              window.seqviz
              .Viewer("root", {
              name: "L09136",
              seq: "<?php echo(str_replace("\n",'',$donneesgen["sequence"]));?>",
              style: { height: "90vh", width: "81vw" },
              viewer: "linear",
              annotations:[
                <?php
                while($cds = $reqcds->fetch()){
                  $debut_cds=$cds['debut_cds'];
                  $fin_cds = $cds['fin_cds'];
                  $id_sequence=$cds['id_seq'];
                  $sens = $cds['sens'];
                  ?>
                  {color:"#6B81FF", start:<?php echo($debut_cds)?>,end:<?php echo($fin_cds)?>,name:"<?php echo($id_sequence)?>",direction:<?php echo($sens)?>},
                <?php
                }
                ?>
              ],
              zoom:({linear:<?php echo(!empty($_POST['zoom'])?$_POST['zoom']:2) ?>}),

              })
              .render();
              function updateTextInput(val) {
              document.getElementById('zoom').value=val;
              }
            </script>
            <br><br>
        </div>
      </section>
      <br><br>
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
