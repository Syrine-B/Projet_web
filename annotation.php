<?php

// cette page permet de voir toutes les annotation en cours ou validé présentent dans la BDD
// l'affichage se fait sur plusieurs pages

ini_set('memory_limit', '-1');
session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

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
                <li><a href="http://localhost/recherche_genome.php">Recherche génome</a></li>
                <li><a href="active" href="http://localhost/blast_ncbi.php">BLAST</a></li>
                <li><a href="#about">Toutes les annotations</a></li>
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
    <section id="container_resultats">
      <div id="position_box_recherche">

        <div class="text">

          <?php

            $id_user = $_SESSION['id'];

            $req = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE  ANNOTATION.id_annotation = SEQUENCE.id_annotation;"); //verifie si il existe des sequences dans la base

            $rep = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE  ANNOTATION.id_annotation = SEQUENCE.id_annotation;"); // permet d'afficher les sequences validées

            $rap = $bdd->prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE  ANNOTATION.id_annotation = SEQUENCE.id_annotation;"); // permet d'afficher les séquences en cours ou terminer

            $req->execute(array());
            $rep->execute(array());
            $rap->execute(array());

            $test = $req->fetch();



            if(empty($test)) {
              echo ("il n'existe pas de séquence ");
            }
            else{
          ?>
          <h3 align="center"><b>Séquences dont l'annotation a été validée</b></h3>

          <?php
            $item=1;
            while($donnee = $rep->fetch()){

              if ($donnee['etat_validation']=='valider'){
          ?>

            <div class = "item" id = "<?php echo($item); ?>" <?php if($item > 9){echo("hidden");} ?> >
              <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">


                <p align="center">
                  <br>
                  <a href="http://localhost/annotation_post.php?id_annotation=<?php echo($donnee["id_annotation"]); ?>"> <?php echo($donnee["id_seq"]); ?>
                  </a>
                  <hr class="veryshort">
                </p>

                <input type = "hidden" name = "id_annotation" value = "<?php echo($donnee["id_annotation"]); ?>">
              </form>
            </div>
          <?php
            $item++;
            }
          }
          ?>
          <table width="100%">
            <td width="85%">
               <p align="center" class="blast">
                  <a style="text-decoration:none" href="javascript:changePage('precedent')">
                  page précédente
                  </a>
               </p>
            </td>
            <td width="15%">
               <p align="center" class="blast">
                  <a style="text-decoration:none" href="javascript:changePage('suivant')">
                  page suivante
                  </a>
               </p>
            </td>
          </table>
        </div>
      </div>
    </section>
    <br>
    <section id="container_resultats">
      <div id="position_box_recherche">


        <hr>
        <h3 align="center"><b>Séquences en cours d'annotation ou en attente de validation</b></h3>
        <?php
          $item=1;
            while($donnee = $rap->fetch()){

              if ($donnee['etat_validation']!='valider'){
        ?>
        <div class = "item_2" id = "<?php echo($item); ?>_2" <?php if($item > 9){echo("hidden");} ?> >
          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">


            <p align="center">

              <br>
              <a href="http://localhost/annotation_post.php?id_annotation=<?php echo($donnee["id_annotation"]); ?>"> <?php echo($donnee["id_seq"]); ?>
              </a>
              <hr class="veryshort">
            </p>

            <input type = "hidden" name = "id_annotation" value = "<?php echo($donnee["id_annotation"]); ?>">
          </form>
          </div>
          <?php
            $item++;
                }
              }
            }
          ?>
          <table width="100%">
            <td width="85%">
                <p align="center" class="blast">
                  <a style="text-decoration:none" href="javascript:changePage('precedent')">
                  page précédente
                  </a>
                </p>
            </td>
            <td width="15%">
                <p align="center" class="blast">
                  <a style="text-decoration:none" href="javascript:changePage('suivant')">
                  page suivante
                  </a>
                </p>
            </td>
          </table>
        </div>
        <script>
          //while 1
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

        <script>
          //while 2
          var items_2 = document.getElementsByClassName('item_2')

          var currentpage_2 = 0

          function changePageDeux(page){

            if(page == "suivant" && currentpage_2!=items_2.length){

              for(var i =0; i<items_2.length; i++) {
                items_2[i].setAttribute("hidden",true);
              };

              currentpage_2 += 1

              document.getElementById((currentpage_2*8) +1 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +2 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +3 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +4 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +5 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +6 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +7 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +8 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +9 + "_2").removeAttribute("hidden");

            }
            else if(page == "precedent" && currentpage_2!=0){

              for(var i =0; i<items_2.length; i++) {
                items_2[i].setAttribute("hidden",true);
              };
              currentpage_2 -= 1
              document.getElementById((currentpage_2*8) +1 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +2 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +3 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +4 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +5 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +6 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +7 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +8 + "_2").removeAttribute("hidden");
              document.getElementById((currentpage_2*8) +9 + "_2").removeAttribute("hidden");
            }
          }
        </script>
      </div>
    </section>
  </body>
    <div class="footer_scroll">
        &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
    </div>
</html>

<?php
}
else {

   header("Location: login.php");
}
?>
