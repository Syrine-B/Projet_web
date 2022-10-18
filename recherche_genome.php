<?php

// cette page permet de rechercher un genome a l'aide d'attribut choisis

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
        <meta charset="utf-8">
        <?php require "header.php"; ?>
        <link type="text/css" rel="stylesheet" href="accueil.css?t=<? echo time(); ?>" /> <!--lien css-->

        <div class="header">
            <form action="res.php" method="post">

              <ul id="navlist">
                <li><a href="http://localhost/accueil.php">Accueil</a></li>
                <li><a href="http://localhost/recherche_sequence.php">Recherche CDS</a></li>
                <li><a href="#about">Recherche génome</a></li>
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
        <section id="container">
            <div id="position_box_recherche">
                <form action="resultat_genome.php" method="post">
                    <table class="tab_bottom" border="4" width="100%" bordercolor="#808080" frame="hsides" rules="rows">
                        <tr>
                            <td>
                                <span><b>Recherche par identifiant génome </b></span>
                                <br><br>
                            </td>
                            <td class="cell_gb">
                                <input type="text" name="id_genome">
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span><b>Recherche par séquence nucléotidique </b></span>
                                <br><br>
                            </td>
                            <td class="cell_gb">
                                <textarea type="text" name="sequence"></textarea>
                                <br><br>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <span><b>Recherche par organisme</b></span>
                                <br><br>
                            </td>
                            <td class="cell_gb">
                                <input type="text" placeholder="coli" name="organisme">
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span><b>Recherche par souche <b></span>
                                <br><br>
                            </td>
                            <td class="cell_gb">
                                <input type="text" name="souche_organisme">
                                <br><br>
                            </td>
                        </tr>

                            <td rowspan="2">

                            </td>
                        </tr>
                    </table>
                    <input type="submit" name ="submit" value="Recherche">
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
