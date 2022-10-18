<?php

// cette page est un parseur, il permet a l'administrateur d'ajouter un ou plusieurs genomes a partir de fichier fasta

$bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

/*Si on a réussi à ouvrir le fichier*/
if(!empty($_FILES['file']['tmp_name'])){

  $tmp_file = $_FILES['file']['tmp_name']; //nom temporaire
  $name_file = $_FILES['file']['name']; //nom renseigné par l'utilisateur
  $allowed = array("fa" => "fa"); //formats acceptés
  $content_dir = 'upload/'; // dossier où sera déplacé le fichier

  $fichier = $content_dir . $name_file;

  $nom = preg_split("/[_]+/", $name_file);

  $organisme = $nom[0]." ".$nom[1];

  $souche = str_replace(".fa","",$nom[2]);

  // Vérifie l'extension du fichier
  $ext = pathinfo($name_file, PATHINFO_EXTENSION);

  if(!array_key_exists($ext, $allowed)){
  ?>
    <div class="col-12">
      <h4><b><center>Veuillez sélectionner un fichier au format fasta</center></b></h4>
    </div>
    <?php
  }//fin if

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

if ($fichier){

  $id_genome="";
  $sequence="";

  while (!feof($fichier)){
    /*On lit la ligne courante*/
    $ligne = fgets($fichier);

    if(preg_match("#^>#", $ligne)){

      if(!empty($sequence)){

        $req_fin = $bdd->prepare("UPDATE GENOME SET sequence = ? WHERE id_genome = ?");
        $req_fin->execute(array($sequence,$id_genome));

      }

      $sequence = "";
      $tete = preg_split("/[:]+/", $ligne);



      $id_genome = $tete[2];
      $taille = intval($tete[5]) +1;

      $test = $bdd->prepare("SELECT * FROM GENOME WHERE id_genome = ? ");
      $test->execute(array($id_genome));
      $existe = $test->fetch();


      if(empty($existe)){
        $req_debut = $bdd->prepare("INSERT INTO GENOME (id_genome, taille_genom, organisme, souche_organisme) VALUES (?,?,?,?)");
        $req_debut->execute(array($id_genome,$taille, $organisme, $souche));
      }
      else{
        $req_fin = $bdd->prepare("UPDATE GENOME SET taille_genom = ?, organisme = ?, souche_organisme =? WHERE id_genome = ?");
        $req_fin->execute(array($taille, $organisme, $souche ,$id_genome));

      }
    }
    else{

      $sequence.= $ligne;

    }
  }

  if(!empty($sequence)){

    $req_fin = $bdd->prepare("UPDATE GENOME SET sequence = ? WHERE id_genome = ?");
    $req_fin->execute(array($sequence,$id_genome));
  }
}
?>
