<?php

// Ce code permet d'inserer une question / un sujet dans le forum.

	session_start(); // Starts the session
	$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

	if(isset($_SESSION['prenom']))
	{
?>



<?php
	// on teste si le formulaire a été soumis
	if (isset ($_POST['go']) && $_POST['go']=='Poster') {
		// on teste la déclaration de nos variables
		if (!isset($_POST['titre']) || !isset($_POST['message'])) {
		$erreur = 'Les variables nécessaires au script ne sont pas définies.';
		}
		else {
		// on teste si les variables ne sont pas vides
			if (empty($_POST['titre']) || empty($_POST['message'])) {
				$erreur = 'Au moins un des champs est vide.';
			}

			// si tout est bon, on peut commencer l'insertion dans la base
			else {
				// on se connecte à notre base
				$req = $bdd->prepare("INSERT INTO FORUM_SUJETS(id_utilisateur, sujet, question,date_derniere_reponse) VALUES (?,?,?,NOW())");
				$req -> execute(array($_SESSION["id"], $_POST['titre'], $_POST['message']));

				// on redirige vers la page d'accueil
				header('Location: forum.php');
			}
		}
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
                      <li><a href="#about">Forum</a></li>


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
				<br><br>
				<!-- on fait pointer le formulaire vers la page traitant les données -->
				<form action="insert_sujet_forum.php" method="post">
					<table>
						<tr><td>
							Titre :
						</td><td>
							<input type="text" name="titre" maxlength="50" size="50" value="<?php if (isset($_POST['titre'])) echo htmlentities(trim($_POST['titre'])); ?>">
						</td></tr>
						<tr><td>
							Message :
						</td><td>
							<textarea name="message" cols="50" rows="10"><?php if (isset($_POST['message'])) echo htmlentities(trim($_POST['message'])); ?></textarea>
						</td></tr>
						<tr><td><td align="right">
							<input type="submit" name="go" value="Poster">
						</td></tr>
					</table>
				</form>
				<?php
					// on affiche les erreurs éventuelles
					if (isset($erreur)) echo '<br /><br />',$erreur;
				?>
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
