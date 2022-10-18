<?php
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
		<section>
			<br>
			<p>Entrez les bornes de la région à visualiser. La taille du génome de <?php echo "<i>".$espece."</i>"?> souche <?php echo $souche_name_genome ?> est de <?php echo $taille ?> paires de nucléotides.</p>
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
				<input type = "number" name = "debut_ut" value = "<?php if(isset($_GET['debut_ut'])) {echo($_GET['debut_ut']);} ?>">
				<input type = "number" name = "fin_ut" value = "<?php if(isset($_GET['fin_ut'])) {echo($_GET['fin_ut']);} ?>">
				<input type = "hidden" name = "id_genome" value = "<?php echo($genome); ?>">
				<?php
				if(isset($_GET["pc"])){
					?> <input type = "hidden" name = "pc" value = "<?php echo($pc); ?>"> <?php
				}
				?>
				<input type = "submit" value = "Afficher la région">
			</form>
			<br>

			<?php
				if(isset($_GET['debut_ut']) AND isset($_GET['fin_ut'])){
					?> <div class="scrollmenu" style="width:1500px; word-wrap:break-word; display:inline-block; padding-left:90px;"> <?php
					if ($_GET['debut_ut']<$_GET['fin_ut'] AND $_GET['fin_ut']<=$taille){
						$debut_ut = $_GET['debut_ut'];
						$fin_ut   = $_GET['fin_ut'];
						$partiel = substr($donneesgen["sequence"], $debut_ut-1, $fin_ut-$debut_ut+1);
						echo $partiel;

						while ($donneescds = $reqcds->fetch()){
							if($donneescds["debut_cds"] >= $debut_ut AND $donneescds["fin_cds"] <= $fin_ut){
								echo "<br>";
								$partiel = substr($donneesgen["sequence"], $debut_ut-1, $donneescds["debut_cds"]-$debut_ut);
								echo '<span style="color:red">'.$partiel.'</span>';
								if($donneescds["sens"] == "1"){
									$partiel = substr($donneesgen["sequence"], $donneescds["debut_cds"]-1, $donneescds["fin_cds"]-$donneescds["debut_cds"]+1);
									echo '<a class = "textnondecobleu" href="annotation_post.php?id_annotation='.$donneescds['id_annotation'].'">'.$partiel.'</a> ';
								}
								else if($donneescds["sens"] == "-1"){
									$partiel = substr($donneesgen["sequence"], $donneescds["debut_cds"]-1, $donneescds["fin_cds"]-$donneescds["debut_cds"]+1);
									echo '<a class = "textnondecovert" href="annotation_post.php?id_annotation='.$donneescds['id_annotation'].'">'.$partiel.'</a> '; //on va plutot renvoyer vers affichage annotation

								}
								else{
									$partiel = substr($donneesgen["sequence"], $donneescds["debut_cds"]-1, $donneescds["fin_cds"]-$donneescds["debut_cds"]+1);
									echo '<a class = "textnondeconoir" href="annotation_post.php?id_annotation='.$donneescds['id_annotation'].'">'.$partiel.'</a> ';
								}
							}
						}
					}
					else{
						echo '<font color = "red"> Erreur, veuillez entrer un début et une fin de séquence valide. </font>';
					}
					?> </div><br><br> <?php
				}


			if(isset($_GET['debut_ut']) AND isset($_GET['fin_ut'])){
				echo "<br> <i><b> Légende : </i></b> <br>

				<svg width='40' height='10'>
				<rect width='30' height='10' style='fill:rgb(0,0,255);stroke-width:3;stroke:rgb(0,0,255)' />
				</svg>: brin direct (5' -> 3')<br>

				<svg width='40' height='10'>
				<rect width='30' height='10' style='fill:rgb(0,255,0);stroke-width:3;stroke:rgb(0,255,0)' />
				</svg>: brin indirect (3' -> 5') <br>

				<svg width='40' height='10'>
				<rect width='30' height='10' style='fill:rgb(150,150,150);stroke-width:3;stroke:rgb(150,150,150)' />
				</svg>: sens non renseigné";
			}
			?>
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
