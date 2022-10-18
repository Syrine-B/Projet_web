<?php

// Ce code permet de lire les réponses a une question du  forum.

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

	<body>
		<section style="width:1500px; margin:0 auto; margin-top:3.5%;">
  			<div style="width:100%; padding: 30px; border: 1px solid #f1f1f1; border-radius: 10px; background: #fff; box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);">


				<?php
					if (!isset($_GET['id_sujet_a_lire'])) {
						echo 'Sujet non défini.';
					}
					else {
				?>

				<?php

					// on prépare notre requête
					$sql = $bdd->prepare("SELECT prenom, nom, message, date_reponse FROM FORUM_REPONSES, UTILISATEUR WHERE UTILISATEUR.id_utilisateur = FORUM_REPONSES.id_utilisateur AND  FORUM_REPONSES.sujet= ? ORDER BY date_reponse ASC");
					$sql ->execute(array($_GET['id_sujet_a_lire']));


					$sujet = $bdd->prepare("SELECT prenom, nom, sujet, question FROM FORUM_SUJETS, UTILISATEUR WHERE UTILISATEUR.id_utilisateur = FORUM_SUJETS.id_utilisateur AND FORUM_SUJETS.id= ? ");
					$sujet ->execute(array($_GET['id_sujet_a_lire']));
					$suj = $sujet ->fetch();

					?>

					<br>
					<section class="forum">
						<div class="position_forum">
							<table border="1" width=900px bordercolor="#808080" frame="hsides">
								<td width="20%">
									<p style="padding-left:10px;">
										<?php echo($suj['prenom']); ?> <?php echo($suj['nom']); ?><br>
									</p>
								</td>
								<td width=80%>
									<p style="padding-left:30px;">
										<?php echo($suj['question']); ?><br><br>
									</p>
								</td>
							</table>
						</div>
					</section>
					<br>

					<?php
					// on va scanner tous les tuples un par un
						while ($data = $sql->fetch()){
					?>


						<section class="forum">
							<div class="position_forum">
								<table border="1" width=900px bordercolor="#808080" frame="hsides">
									<td width="20%">
										<p style="padding-left:10px;">
											<?php echo($data['prenom']); ?> <?php echo($data['nom']); ?><br>
											<?php echo($data['date_reponse']); ?><br><br>
										</p>
									</td>
									<td width=80%>
										<p style="padding-left:30px;">
											<?php echo($data['message']); ?><br><br>
										</p>
									</td>
								</table>
							</div>
						</section>
						<br>
						<?php
						}
				?>
				<!-- on ferme notre table html -->
				</table>
				<br/><br/>
				<!-- on insère un lien qui nous permettra de rajouter des réponses à ce sujet -->
				<p align="center"><a href="./insert_reponse_forum.php?numero_du_sujet=<?php echo $_GET['id_sujet_a_lire']; ?>">Répondre</a></p>
				<?php
					}
				?>
				<br/>
				<!-- on insère un lien qui nous permettra de retourner à l'accueil du forum -->
				<p align="center"><a href="./forum.php">Retour à l'accueil</a></p>
					<br><br>
			</div>
		</section>
	</body>
</html>

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
