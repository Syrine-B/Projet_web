<?php
session_start(); // Starts the session
$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

if(isset($_SESSION['prenom']))
{
    if(!empty($_GET['id_genome'])){
      $req = $bdd ->prepare("SELECT * FROM GENOME WHERE GENOME.id_genome = ?");
      $req-> execute(array($_GET['id_genome']));
      $reponse = $req->fetch ();
      $sequence = $reponse['sequence'];

    } else {
      if(!empty($_GET['id_seq'])){
        $req = $bdd ->prepare("SELECT seq_dna FROM SEQUENCE WHERE SEQUENCE.id_seq = ?");
        $req-> execute(array($_GET['id_seq']));
        $reponse = $req->fetch ();
        $sequence = $reponse['seq_dna'];
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
                <li><a href="http://localhost/accueil.php">Accueil</a></li>
                <li><a href="http://localhost/recherche_sequence.php">Recherche CDS</a></li>
                <li><a href="http://localhost/recherche_genome.php">Recherche génome</a></li>
                <li><a href="active" href="#about">BLAST</a></li>
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
	<body class="is-preload">


		<!-- Heading -->

		<!-- Main -->
    <section id="main" class="wrapper" align="center">
      <div class="inner">
        <div class="content">
          <?php
          if(isset($_POST['ok-fasta']) || isset($_POST['ok-file'])){
              include("blast_ncbi_post.php");
          }
          else{
          ?>

          <section id="container">
            <div id="position_box_recherche">
              <h2>Recherche BLAST</h2>
              <br>
              <p class="paragraph">
                Il est possible de réaliser un BLAST sur les serveurs du NCBI ou en local.
                Vous pouvez choisir de renseigner un fichier ou directement la séquence au format fasta.
                Si vous renseignez une séquence protéique merci de bien chosir une base de donnée protéique sinon le BLAST échouera.
                Nous conseillons d'utiliser le blast local qui est beaucoup plus rapide.
              </p><br><br><br>

              <form method="post" action="blast_ncbi.php" enctype="multipart/form-data">
                <div class="row gtr-uniform">
                  <div>
                  <label for="banque"><b>Chosir la banque interrogée</b> </label>
                    <select name="banque" id="banque">
                      <option value="nt">Nucleotide collection (ADN)</option>
                      <option value="nr">Non-redundant (protéine)</option>
                      <option value="pdb">PDB protein database (protéine)</option>
                      <option value="refseq_rna">NCBI Transcript Reference Sequences (ADN)</option>
                      <option value="refseq_protein">NCBI Protein Reference Sequences (protéine)</option>
                      <option value="swissprot">Non-redundant UniProtKB/SwissProt sequences (protéine)</option>
                      <option value="localadn">Blast local (ADN)</option>
                      <option value="localprot">Blast local (protéine)</option>
                    </select>
                  </div>
                  <br><br>

                  <div>
                    <label for="sequence"><b>Entrez la séquence fasta au format fasta:</b></label><br>
                    <textarea name="sequence" id="sequence" placeholder="Séquence au format fasta" rows="6" maxlength=2000><?php if(!empty($sequence)){
                      echo("$sequence");
                    }
                    ?></textarea>
                  </div>

                  <div class="boutton1" align="center">
                      <input type="submit" name="ok-fasta" value="Blastez la séquence" class="primary">
                  </div>

                  <hr class="short">
                  <br><br>

                  <div>
                    <p align="center">
                      <label for="photo"><b>Choisir fichier au format .fa </b></label><br><br>
                      <input type="file" name="file" accept=".fa"/>
                    </p>
                  </div>

                  <div >
                    <p align="center">
                      <input type="submit" name="ok-file" value="Blastez le fichier fasta" class="primary">
                    </p>

                  </div>
                </div>
              </form>
            <?php } ?>
            </div>
          </div>
        </section>
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
