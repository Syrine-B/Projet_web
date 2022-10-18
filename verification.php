<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

if(isset($_POST['email']) && isset($_POST['password'])){
   $email = $_POST['email'];
   $password =password_hash($_POST['password'], PASSWORD_DEFAULT);
   if($email !== "" && $password !== ""){
      // connexion à la base de données
      $db =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");

      $req = $bdd->prepare('SELECT * FROM UTILISATEUR WHERE email = ? AND mdp = ?');
      $req->execute(array($_GET['email'], $_GET['password']));
         //$reponse = $req->fetch();
      $count = $req->rowCount();
      if($count!=0){ // nom d'utilisateur et mot de passe correctes
         $_SESSION['email'] = $email;
         header('Location: accueil.php');
      }
      else{
         header('Location: login.php?erreur=1'); // utilisateur ou mot de passe incorrect
      }
      $reponse->closeCursor(); // Termine le traitement de la requête
   }
   else{
       header('Location: login.php?erreur=2'); // utilisateur ou mot de passe vide
   }
}
else{
   header('Location: login.php');
}
?>
