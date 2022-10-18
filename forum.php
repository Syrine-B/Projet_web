<?php
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

	<body class="corps">
		<section id="container">
    		<div id="position_box_recherche">
				<br>
				<!-- on place un lien permettant d'accéder à la page contenant le formulaire d'insertion d'un nouveau sujet -->
				<p style="padding-left:60px;">Vous pouvez créer un nouveau sujet ou consulter les discussions existantes</p>
				<a style="padding-left:60px;" href="insert_sujet_forum.php">Créer un sujet</a>

				<br><br>

				<?php

					// préparation de la requete
					$req = $bdd ->prepare("SELECT id, prenom, sujet, date_derniere_reponse FROM UTILISATEUR, FORUM_SUJETS WHERE UTILISATEUR.id_utilisateur = FORUM_SUJETS.id_utilisateur ORDER BY date_derniere_reponse DESC");
					$req -> execute(array());
					$requete = $req->fetch();

					$rep = $bdd ->prepare("SELECT id, prenom, sujet, date_derniere_reponse FROM UTILISATEUR, FORUM_SUJETS WHERE UTILISATEUR.id_utilisateur = FORUM_SUJETS.id_utilisateur ORDER BY date_derniere_reponse DESC");
					$rep -> execute(array());


					if (empty($requete)) {
							echo 'Aucun sujet';
					}
					else {
				?>
					<table width="99%" border="0"><tr>
					<td width=33%>
					<b>Auteur</b>
					</td><td width=33%>
					<b>Titre du sujet</b>
					</td><td width=33%>
					<b>Date de la dernière réponse</b>
					</td></tr>
					</table>
					<hr><br>
						
						
				<?php
					// on va scanner tous les tuples un par un
					while ($data = $reponse = $rep->fetch()) {
							// on décompose la date
							sscanf($data['date_derniere_reponse'], "%4s-%2s-%2s %2s:%2s:%2s", $annee, $mois, $jour, $heure, $minute, $seconde);
						?>
						<br>
						<table width="100%" border="0">
							<tr>
							<td width="33%">
						<?php
							// on affiche le nom de l'auteur de sujet
							echo htmlentities(trim($data['prenom']));
						?>
							</td>
							<td width="33%">
						<?php
						?>
						<?php
							// on affiche le titre du sujet, et sur ce sujet, on insère le lien qui nous permettra de lire les différentes réponses de ce sujet
							echo '<a href="lire_sujet_forum.php?id_sujet_a_lire=' , $data['id'] , '">' , htmlentities(trim($data['sujet'])) , '</a>';
						?>
						<?php
							echo '</td><td>';

							// on affiche la date de la dernière réponse de ce sujet
							echo $jour , '-' , $mois , '-' , $annee , ' ' , $heure , $minute;
						?>
						</td></tr></table><hr>
						<?php
							}
						?>

						<?php
							}
						?>

						<?php
						} //fin du si connecter
						else {
							header("Location: login.php");
						}
				?>

			</div>
		</div>
		</section>
	</body>

	<div class="footer">
        &copy; Syrine Benali & Thomas El Khilali & Hugues Herrmann
    </div>
</html>
