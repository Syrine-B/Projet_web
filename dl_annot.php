<?php

// permet le telechargnement des informations recupéré depuis les page proposans cette fonction

   ini_set('display_errors',1);
   error_reporting(E_ALL);
   session_start();
   // Connexion à la base de données
   $bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
   $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

   if(isset($_SESSION['prenom']))
   {
      $resultat = "";
      foreach ($_POST as $nom => $value) {

         if($nom != "download"){

            $resultat.= $value.";";

         }
      }

      $resultat = substr_replace($resultat,"",-1);

      header('Content-Type: text/plain');
      header('Content-Disposition: attachment; filename="output.txt"');
      echo($resultat);
   }
   else {
   header("Location: login.php");
   }
?>
