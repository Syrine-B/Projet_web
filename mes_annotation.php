<?php

// cette page permet aux annotateur de voir les sequence en cours d'annotation qui leur a ete attribuer

ini_set('memory_limit', '-1');
session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom'])){
?>

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
          <li><a href="http://localhost/recherche_genome.php">Recherche génome</a></li>
          <li><a href="http://localhost/blast_ncbi.php">BLAST</a></li>
          <li><a href="http://localhost/annotation.php">Toutes les annotations</a></li>
          <li><a href="http://localhost/visualisation.php">Visualiser génome</a></li>

          <?php
          if($_SESSION['role']!= 'lecteur'){ ?>
            <li><a href="#about">Mes annotations</a></li>
            <li><a href="http://localhost/forum.php">Forum</a></li>
          <?php
          } ?>

          <?php
          if($_SESSION['role']== 'validateur' || $_SESSION['role']== 'administrateur'){
          ?>
          <li><a href="http://localhost/valid_annot.php">Valider les annotations </a></li>
          <li><a href="http://localhost/affect_annot.php">Assigner séquences</a></li>
          <?php
          } ?>
          <ul id="navlist">
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
    <section id="container_resultats">
      <div id="position_box_recherche">
        <div class="text">
          <p align="center">
            Séléctionnez un identifiant pour commencer à annoter.<br>
            Vous pouvez commencer à annoter et finir plus tard, tout est enregistré !
          </p>
          <br>
          <div align="center"><b>Id des séquences à annoter</b><br></div>
            <?php
            if($_SESSION["role"]=='administrateur'){
              $rep = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE etat_validation = 'en_cours' AND ANNOTATION.id_annotation = SEQUENCE.id_annotation;");
              $rep->execute(array());
              $don = $rep->fetch();
              $req = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE etat_validation = 'en_cours' AND ANNOTATION.id_annotation = SEQUENCE.id_annotation;");
              $req->execute(array());
            }
            else{
              $id_user = $_SESSION['id'];
              $rep = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE id_utilisateur = ? AND etat_validation = 'en_cours' AND ANNOTATION.id_annotation = SEQUENCE.id_annotation;");
              $rep->execute(array($id_user));
              $don = $rep->fetch();
              $req = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE id_utilisateur = ? AND etat_validation = 'en_cours' AND ANNOTATION.id_annotation = SEQUENCE.id_annotation;");
              $req->execute(array($id_user));
            }
            if(empty($don)){
              echo ("Vous n'avez plus de séquences a annoter !");
            }
            else{
              $item=1;
              while($donnee = $req->fetch()){
            ?>
              <div class = "item" id = "<?php echo($item); ?>" <?php if($item > 9){echo("hidden");} ?> >
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" align="center">
                  <p align="center">
                    <a href="http://localhost/annotation_post.php?id_annotation=<?php echo($donnee["id_annotation"]); ?>"> <?php echo($donnee["id_seq"]); ?></a>
                    <input type = "hidden" name = "id_annotation" value = "<?php echo($donnee["id_annotation"]); ?>">
                  </p>
                </form>
                <hr class="short">
              </div>
              <?php
                $item++;
              } //fin while
              ?>

              <table width="100%">
                <td width="85%">
                    <p align="center" class="blast">
                      <a style="text-decoration:none" href="javascript:changePage('precedent')">page précédente </a>
                    </p>
                </td>
                <td width="15%">
                    <p align="center" class="blast">
                      <a style="text-decoration:none" href="javascript:changePage('suivant')">page suivante</a>
                    </p>
                </td>
              </table>

            <?php
            }//fin else?>
          </div>

        <script>

          var items = document.getElementsByClassName('item')
          var currentpage = 0

          function changePage(page){
            //Change de page
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

    <br><br>
  </body>

  <div class="footer_scroll">
    &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
  </div>
</html>

<?php
}//fin if
else {
  header("Location: login.php");
} ?>
