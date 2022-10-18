<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_SESSION['prenom'])){
    $req = $bdd->prepare("SELECT * FROM GENOME ;");
    $req->execute(array());
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

    <body class="menu">
        <section id="container">
            <div id="position_box_recherche">

                <h3 align="center"><b>Cliquez sur le génome que vous souhaitez visualiser</b></h3>

                <?php
                while($donnee = $req->fetch()){
                    ?>

                    <div class="petitblock_wrapper">
                        <p align="center">
                            <br>
                            <a href="http://localhost/visu_post_modif.php?id_genome=<?php echo($donnee["id_genome"]); ?>"> <?php echo($donnee["organisme"]." ".$donnee["souche_organisme"]); ?>
                            </a>
                            <hr class="short">
                        </p>
                    </div>

                    <?php
                }
                ?>

                <br>
                <br>
            </div>
        </section>
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
