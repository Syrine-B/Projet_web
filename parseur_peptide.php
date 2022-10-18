<?php

// cette page est un parseur, il permet a l'administrateur d'ajouter une ou plusieurs sequence peptidique a partir de fichier fasta

$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

/*Si on a réussi à ouvrir le fichier*/
if(!empty($_FILES['file']['tmp_name'])){
  $tmp_file = $_FILES['file']['tmp_name']; //nom temporaire
  $name_file = $_FILES['file']['name']; //nom renseigné par l'utilisateur
  $allowed = array("fa" => "fa"); //formats acceptés
  $content_dir = 'upload/'; // dossier où sera déplacé le fichier

  $fichier = $content_dir . $name_file;

  // Vérifie l'extension du fichier
  $ext = pathinfo($name_file, PATHINFO_EXTENSION);

  if(!array_key_exists($ext, $allowed)){
    ?>
    <div class="col-12">
      <h4><b><center>Veuillez sélectionner un fichier au format fasta</center></b></h4>
   </div>
   <?php
  }
  elseif(!is_uploaded_file($tmp_file)){
      $photo_valide=false;
      ?>
      <div class="col-12">
        <h4><b><center>erreur chargement fichier fasta</center></b></h4>
     </div>
     <?php
  }
  // on copie le fichier dans le dossier de destination
  elseif(!move_uploaded_file($tmp_file, $content_dir . $name_file)){
      ?>
      <div class="col-12">
        <h4><b><center>erreur chargement fichier fasta</center></b></h4>
     </div>
     <?php
  }
}

$fichier = fopen($fichier, "r");

  /*Si on a réussi à ouvrir le fichier*/
  if ($fichier){

    $id_genome="";
    $sequence="";

    while (!feof($fichier)){
      /*On lit la ligne courante*/
      $ligne = fgets($fichier);

      if(preg_match("#^>#", $ligne)){

        if(!empty($sequence)){

          $req_fin = $bdd->prepare("UPDATE SEQUENCE SET seq_pep = ? WHERE id_seq = ?");
          $req_fin->execute(array($sequence,$id_sequence));

        }

        $sequence = "";
        $tete = preg_split("/[:]+/", $ligne);

        $id_seq = str_replace(">","", $tete[0]); // besoin de split
        $id_sequence = str_replace(" pep chromosome","", $id_seq);
        $id_sequence = str_replace(" pep plasmid","", $id_sequence);
        $id_genome = $tete[1];
        $debut_cds = $tete[3];
        $fin_cds = $tete[4];

        if(!empty($tete[5])){
          $sens = str_replace(" gene","",$tete[5]);
        }
        else{
          $sens = null;
        }

        if (preg_match('/gene:(.*?) /', $ligne, $match) == 1) {
          $gene_nom = $match[1];
        } else {
          $gene_nom = null;
        }
        if (preg_match('/gene_biotype:(.*?) /', $ligne, $match) == 1) {
          $gene_biotype = $match[1];
        } else {
          $gene_biotype = null;
        }

        if (preg_match('/transcript_biotype:(.*?) /', $ligne, $match) == 1) {
          $transcript = $match[1];
        } else {
          $transcript= null;
        }

        if (preg_match('/gene_symbol:(.*?) /', $ligne, $match) == 1) {
          $gene_symbole = $match[1];
        } else {
          $gene_symbole = null;
        }
        if (preg_match('/description:(.*?)$/', $ligne, $match) == 1) {
          $description = $match[1];
        } else {
          $description = null;
        }

        $taille = intval($fin_cds) - intval($debut_cds);


        $test = $bdd->prepare("SELECT * FROM SEQUENCE WHERE id_seq = ? ");
        $test->execute(array($id_sequence));
        $existe = $test->fetch();



        if(empty($existe)){ //si la sequence n'existe pas
          $req_seq = $bdd->prepare("INSERT INTO SEQUENCE (id_seq, id_genome) VALUES (?,?)");
          $verif =  $req_seq->execute(array($id_sequence,$id_genome));


        }
        else{
          $req_seq = $bdd->prepare("UPDATE SEQUENCE SET id_genome = ?  WHERE id_seq = ?");
          $req_seq->execute(array($id_genome, $id_sequence));
        }


        $test_annot = $bdd->prepare("SELECT * FROM ANNOTATION WHERE id_seq = ? ");
        $test_annot->execute(array($id_sequence));
        $existe_annot = $test_annot->fetch();

        if(empty($existe_annot)){ //si la sequence n'existe pas
          $req_annot = $bdd->prepare("INSERT INTO ANNOTATION(gene_nom, gene_biotype, trancript_biotype, gene_symbole, descrip_cds, taille_cds, debut_cds, fin_cds, sens) VALUES (?,?,?,?,?,?,?,?,?)");
          $req_annot->execute(array($gene_nom, $gene_biotype, $transcript, $gene_symbole, $description, $taille, $debut_cds, $fin_cds, $sens));

          $majseq= $bdd->prepare ("UPDATE SEQUENCE SET id_annotation = ? WHERE id_seq = ?");
          $majseq->execute(array($bdd->lastInsertId(), $id_sequence));

        }
        else{
          $req_annot = $bdd->prepare("UPDATE ANNOTATION SET gene_nom = ?, gene_biotype =?, trancript_biotype =?, gene_symbole =?, descrip_cds=?, taille_cds=?, debut_cds=?, fin_cds=?, sens=?  WHERE id_annotation = (SELECT id_annotation FROM SEQUENCE WHERE id_seq = ?) ");
          $req_annot->execute(array($gene_nom, $gene_biotype, $transcript, $gene_symbole, $description, $taille, $debut_cds, $fin_cds, $sens,$id_sequence));

        }


      }
      else{

        $sequence.= $ligne;

      }
    }

    if(!empty($sequence)){

      $req_fin = $bdd->prepare("UPDATE SEQUENCE SET seq_pep = ? WHERE id_seq = ?");
      $req_fin->execute(array($sequence,$id_sequence));
    }
  }
?>
