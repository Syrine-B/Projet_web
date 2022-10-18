<?php

// cette page permet l'attribution des annotation aux différents annotateurs. Cette dernière n'es e=accessible qu'aux validateurs et a l'administrateur

ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom']) and ($_SESSION['role'] == 'validateur' || $_SESSION['role'] == 'administrateur') ) //verifie si la personne est connécté et si son role lui donne les droit d'accès a la page
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

                    <li><a href="#about">Assigner séquences</a></li>

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

  <section style="width:1700px; margin:0 auto; margin-top:3.5%;">
    <div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">

      <?php

        // nous recupérons apres selection par le validateur l'id de l'annotateur et nous l'ajoutons a la table annotation en face de la sequence correspondante

        if(isset($_POST["id"]) and isset($_POST["annot"])) {
          $createannot = $bdd->prepare("INSERT INTO ANNOTATION(id_utilisateur) VALUES (?)");
          $createannot -> execute(array( $_POST["annot"]));

          $id_annot = $bdd -> lastInsertId();

          $updatesequence = $bdd->prepare("UPDATE SEQUENCE SET  id_annotation = ? WHERE id_seq = ?;");
          $updatesequence -> execute(array( $id_annot, $_POST["id"]));
          $valide = "Annotation attribuée avec succès.";

          $email = $bdd->prepare("SELECT email FROM UTILISATEUR WHERE id_utilisateur = ? ;");
          $email-> execute(array( $_POST["annot"]));
          $destinataire = $email->fetch();

          // Le message
          $message = "Une nouvelle séquence vous a été attribuée pour l'annotation, son identifiant est".$id_annot;

          // Envoi du mail
          $test = mail($destinataire['email'], 'Nouvelle attribution', $message);

        }

        $requser = $bdd -> prepare("SELECT * FROM SEQUENCE WHERE id_annotation IS NULL ;"); //selection de tout les elements de la table sequence dont l'id_annotation est nul, soit qui n'ont pas d'annotation
        $requser -> execute();

        $reqann = $bdd -> prepare("SELECT * FROM ANNOTATION, SEQUENCE WHERE ANNOTATION.id_annotation = SEQUENCE.id_annotation AND ANNOTATION.id_utilisateur IS NULL ;"); //selection de tout les elements de la table sequence et annotation dont l'id_utilisateur est nul, soit qui n'ont pas d'annotateur
        $requser -> execute();
        $reqann-> execute();


        $reqnewannot = $bdd -> prepare("SELECT * FROM UTILISATEUR WHERE role='annotateur';"); // recupération de tout les utilisateur qui sont des annotateurs
        $reqnewannot -> execute();

      ?>

      <section>
        <br><b>
        Liste des séquences attribuer </b><br> <br>
        <?php
          $item=1;

          // premier while les sequences qui n'ont pas d'annotation
            while ($datauser = $requser->fetch()) { //on utilisa fetch et non fetchAll pour pouvoir aficher les reponse a la requete une par une
          ?>
                  <div class = "item" id = "<?php echo($item); ?>" <?php if($item > 9){echo("hidden");} ?> >
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">

                      &nbsp; &nbsp;  Identifiant de la séquence :
                      <input class="affichage_annot" size="5" type="text" name="id_seq" value="<?php echo($datauser["id_seq"]); ?>"disabled>

                      &nbsp; &nbsp;  Séquence peptidique :
                      <input  class="affichage_annot" size="8"type="text" name="seq_pep" value="<?php echo($datauser["seq_pep"]); ?>"disabled>

                      &nbsp; &nbsp; Séquence nucléotidique :
                      <input  class="affichage_annot" size="8" type="text" name="seq_dna" value="<?php echo($datauser["seq_dna"]); ?>"disabled>

                      &nbsp; &nbsp;  Annotateur :
                      <select name="annot" >

                        <?php
                        while ($dataannot = $reqnewannot->fetch()) {

                        ?>

                        <option value = "<?php echo($dataannot['id_utilisateur']);?>"> <!-- bouton deroulant qui affiche les annotateurs  -->
                          <?php
                            echo ($dataannot['nom']);
                            echo("   ");
                            echo ($dataannot['prenom']);
                          ?>
                        </option>
                        <?php
                          }
                        ?>
                      </select>

                      <input type = "hidden" name = "id" value = "<?php echo($datauser["id_seq"]); ?>">

                      &nbsp; &nbsp; &nbsp; &nbsp; <input style="width:290px;" type = "submit" value = "Attribuer à l'annotateur sélectionné">
                    </form>
                  </div>

                <?php
              $item++;
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

      <?php
        //second while les annotation qui n'ont pas d'annotateur

        $item=1;
        while ($datauser = $reqann ->fetch()) {
      ?>
          <div class = "item_2" id = "<?php echo($item); ?>_2" <?php if($item > 9){echo("hidden");} ?> >
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">

              &nbsp; &nbsp;  Identifiant de la séquence :
              <input class="affichage_annot" size="5" type="text" name="id_seq" value="<?php echo($datauser["id_seq"]); ?>"disabled>
                  &nbsp; &nbsp;  Séquence peptidique :
              <input  class="affichage_annot" size="8"type="text" name="seq_pep" value="<?php echo($datauser["seq_pep"]); ?>"disabled>
                  &nbsp; &nbsp; Séquence nucléotidique :
              <input  class="affichage_annot" size="8" type="text" name="seq_dna" value="<?php echo($datauser["seq_dna"]); ?>"disabled>
                  &nbsp; &nbsp;  Annotateur :
              <select name="annot" >
                  <?php
                  $reqnewannot = $bdd -> prepare("SELECT * FROM UTILISATEUR WHERE role='annotateur';");
                  $reqnewannot -> execute();
                  while ($dataannot = $reqnewannot->fetch()){?>
                    <option value = "<?php echo($dataannot['id_utilisateur']);?>"> <!-- bouton deroulant qui affiche les annotateurs  -->
                      <?php
                        echo ($dataannot['nom']);
                        echo("   ");
                        echo ($dataannot['prenom']);
                      ?>
                    </option>
                  <?php
                  }
                  ?>
              </select>

              <input type = "hidden" name = "id" value = "<?php echo($datauser["id_seq"]); ?>">

                &nbsp; &nbsp; &nbsp; &nbsp; <input style="width:290px;" type = "submit" value = "Attribuer à l'annotateur sélectionné">
            </form>
          </div>
          <?php
            $item++;
          }

          ?>

        <a href="javascript:changePageDeux('precedent')">
          page précédente
        </a>

        <a href="javascript:changePageDeux('suivant')">
          page suivante
        </a>

      <?php

        if(isset($valide)) {
          echo "<br> <br>";
          echo '<font color = "green">'.$valide.'</font>';
        }

      ?>
      <br>
      </section>

      <!-- Definition des fonctions en java script, permet l'affichage des resultats sur plusieur page -->
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

            //Affiche 9 element par page

            document.getElementById((currentpage*8) +1).removeAttribute("hidden"); //element qui s'affiche sur la page (sequence)
            document.getElementById((currentpage*8) +2).removeAttribute("hidden");
            document.getElementById((currentpage*8) +3).removeAttribute("hidden");
            document.getElementById((currentpage*8) +4).removeAttribute("hidden");
            document.getElementById((currentpage*8) +5).removeAttribute("hidden");
            document.getElementById((currentpage*8) +6).removeAttribute("hidden");
            document.getElementById((currentpage*8) +7).removeAttribute("hidden");
            document.getElementById((currentpage*8) +8).removeAttribute("hidden");
            document.getElementById((currentpage*8) +9).removeAttribute("hidden");

          }
          else if(page == "precedent" && currentpage!=0)
          {

            for(var i =0; i<items.length; i++)
            {
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

          if(page == "suivant" && currentpage_2!=items_2.length)
          {

            for(var i =0; i<items_2.length; i++)
            {
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
          else if(page == "precedent" && currentpage_2!=0)
          {

            for(var i =0; i<items_2.length; i++)
            {
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
