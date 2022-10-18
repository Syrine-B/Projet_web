<?php
session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom']))
{
   if(isset($_POST['enregistrer'])){
      $update = $bdd->prepare("UPDATE ANNOTATION SET  num_chr =?, gene_nom=?, gene_biotype=?, trancript_biotype=?, gene_symbole=?, descrip_cds=?, taille_cds=? WHERE id_annotation = ?;");

      $update->execute(array($_POST['num_chr'], $_POST['gene_nom'], $_POST['gene_biotype'], $_POST['trancript_biotype'], $_POST['gene_symbole'], $_POST['descrip_cds'], $_POST['taille_cds'],$_GET['id_annotation']));
   }

   if(isset($_POST['envoyer'])){
      $update = $bdd->prepare("UPDATE ANNOTATION SET  etat_validation=?,num_chr =?, gene_nom=?, gene_biotype=?, trancript_biotype=?, gene_symbole=?, descrip_cds=?, taille_cds=? WHERE id_annotation = ?;");

      $update->execute(array('terminer',$_POST['num_chr'], $_POST['gene_nom'], $_POST['gene_biotype'], $_POST['trancript_biotype'], $_POST['gene_symbole'], $_POST['descrip_cds'], $_POST['taille_cds'],$_GET['id_annotation']));

      header("Location: annotation.php");
      exit;
   }

   $id_user = $_SESSION['id'];

   $req = $bdd->prepare("SELECT * FROM ANNOTATION,SEQUENCE WHERE id_annotation = ? AND SEQUENCE.id_annotation = ANNOTATION.id_annotation AND id_utilisateur =?;");

   $req->execute(array($_GET['id_annotation'],$id_user));

   $donnee = $req->fetch()

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
               <li><a href="http://localhost/blast_ncbi.php">BLAST</a></li>
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

      <div class="text">

         <?php

            $id_user = $_SESSION['id'];

            $req = $bdd->prepare("SELECT * FROM ANNOTATION,SEQUENCE WHERE SEQUENCE.id_annotation = ANNOTATION.id_annotation AND etat_validation = 'en_cours';");

            $req->execute(array());

            $donnee = $req->fetch();

            //si la personne connecter est l'annotateur en chage de cette sequence ou l'administateur il peut la modifier sinon non

            if( ($_SESSION["role"] == 'annotateur' and $donnee["id_utilisateur"] == $id_user) || $_SESSION["role"] == 'administrateur'){

         ?>

         <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
            <section id="container">
               <div id="position_box_recherche">
                  <h1>Information sur la séquence</h1>
                     <table>
                        <tr>
                           <td>
                              <b>Identifiant de la séquence</b>
                           </td>

                           <td>
                              <?php echo($donnee["id_seq"]); ?>
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Identifiant du génome</b>
                           </td>

                           <td>
                              <?php echo($donnee["id_genome"]); ?>
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Séquence peptidique</b>
                           </td>

                           <td>
                              <?php echo($donnee["seq_pep"]); ?>
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Séquence nucléotidique</b>
                           </td>

                           <td>
                              <?php echo($donnee["seq_dna"]); ?>
                           </td>
                        </tr>


                     </table>
                     <br>
                     <hr>
                     <br>
                     <h1>Annotations</h1>

                     <table>

                        <tr>
                           <td>
                              <b>Numéro de chromosome</b>
                           </td>

                           <td>
                              <input type = "text" name = "num_chr" value = "<?php echo($donnee["num_chr"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Nom du gene</b>
                           </td>

                           <td>
                              <input type = "text" name = "gene_nom" value = "<?php echo($donnee["gene_nom"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Biotype du gene</b>
                           </td>

                           <td>
                              <input type = "text" name = "gene_biotype" value = "<?php echo($donnee["gene_biotype"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Biotype du transcrit</b>
                           </td>

                           <td>
                              <input type = "text" name = "trancript_biotype" value = "<?php echo($donnee["trancript_biotype"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Symbole du gene</b>
                           </td>

                           <td>
                              <input type = "text" name = "gene_symbole" value = "<?php echo($donnee["gene_symbole"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Description du CDS</b>
                           </td>

                           <td>
                              <input type = "text" name = "descrip_cds" value = "<?php echo($donnee["descrip_cds"]); ?>">
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Taille du CDS</b>
                           </td>

                           <td>
                              <input type = "text" name = "taille_cds" value = "<?php echo($donnee["taille_cds"]); ?>">
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <b>Début de la séquence nucléotidique</b>
                           </td>

                           <td>
                              <?php echo($donnee["debut_cds"]); ?>
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <b>Fin de la séquence nucléotidique</b>
                           </td>

                           <td>
                              <?php echo($donnee["fin_cds"]); ?>
                           </td>
                        </tr>
                     </table>


                     &nbsp; &nbsp; &nbsp; &nbsp; <input style="width:230px" type = "submit" name = "enregistrer" value = "Mettre à jour les annotations">

                     &nbsp; &nbsp; &nbsp; &nbsp; <input style="width:230px" type = "submit" name = "envoyer" value = "Soumettre les annotations">

                  </form>


                  <?php
                  }
                  else{
                  ?>

                  <section id="container">
                     <div id="position_box_recherche">
                        <body>

                           <h1>Information sur la séquence</h1>
                              <table>

                                 <tr>
                                    <td>
                                       <b>Identifiant de la séquence</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["id_seq"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Identifiant du génome</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["id_genome"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Séquence peptidique</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["seq_pep"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Séquence nucléotidique</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["seq_dna"]); ?>
                                    </td>
                                 </tr>
                              </table>

                              <br>
                              <hr>
                              <br>
                              <h1>Annotations</h1>
                              <table>

                                 <tr>
                                    <td>
                                       <b>Numéro de chromosome</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["num_chr"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Nom du gene</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["gene_nom"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Biotype du gene</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["gene_biotype"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Biotype du transcrit</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["trancript_biotype"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Symbole du gene</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["gene_symbole"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Description du CDS</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["descrip_cds"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Taille du CDS</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["taille_cds"]); ?>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <b>Début de la séquence nucléotidique</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["debut_cds"]); ?>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       <b>Fin de la séquence nucléotidique</b>
                                    </td>

                                    <td>
                                       <?php echo($donnee["fin_cds"]); ?>
                                    </td>
                                 </tr>
                              </table>

                           <?php
                           }
                           ?>

                        </body>
                     </div>
                  </section>
               </div>
            </div>
         </form>
      </body>
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
