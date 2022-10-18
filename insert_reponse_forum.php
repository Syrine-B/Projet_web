<?php

// Ce code permet d'inserer une réponse a une question dans le forum.

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

	<?php
		// on teste si le formulaire a été soumis
		if (isset ($_POST['go']) && $_POST['go']=='Poster') {
			// on teste le contenu de la variable $auteur
			if (!isset($_POST['message']) || !isset($_GET['numero_du_sujet'])) {
				$erreur = 'Les variables nécessaires au script ne sont pas définies.';
			}
			else {
				if (empty($_POST['message']) || empty($_GET['numero_du_sujet'])) {
					$erreur = 'Au moins un des champs est vide.';
				}
				// si tout est bon, on peut commencer l'insertion dans la base
				else {

					$date = date("Y-m-d H:i:s");

					// préparation de la requête d'insertion (table forum_reponses)
					$sql = $bdd->prepare("INSERT INTO FORUM_REPONSES(id_utilisateur,message,date_reponse,sujet) VALUES(?,?,?,?)");
					$sql ->execute(array($_SESSION['id'],$_POST['message'],$date,$_GET['numero_du_sujet']));

					// préparation de la requête de modification de la date de la dernière réponse postée (dans la table forum_sujets)

					$sql = $bdd->prepare("UPDATE FORUM_SUJETS SET date_derniere_reponse= ?");
					$sql->execute(array($date));

					// on redirige vers la page de lecture du sujet en cours
					header('Location: lire_sujet_forum.php?id_sujet_a_lire='.$_GET['numero_du_sujet']);
				}
			}
		}
	?>


	<br><br>
	<body>
		<section id="container">
    		<div id="position_box_recherche">
				<!-- on fait pointer le formulaire vers la page traitant les données -->
				<form action="insert_reponse_forum.php?numero_du_sujet=<?php echo $_GET['numero_du_sujet']; ?>" method="post">
					<table>
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
