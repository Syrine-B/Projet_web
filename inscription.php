<?php

// cette page permet a un utilisateur qui ne fait pas encore partis du site de demander a s'inscrire.
// une fois toute les informations renseignés il pourra se logger seulement si l'administrateur accepte sont inscription

ini_set('display_errors',1);
    error_reporting(E_ALL);

    // Connexion à la base de données
    $bdd = new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    if(isset($_POST['forminscription']))
    {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $numtel = $_POST['numtel'];
        $email = $_POST['email'];
        $password = $_POST['mdp'];
        $confirm_password = $_POST['confirm_mdp'];

        if(!empty($_POST['nom']) AND !empty($_POST['prenom']) AND !empty($_POST['numtel']) AND !empty($_POST['email']) AND !empty($_POST['mdp']) AND !empty($_POST['confirm_mdp']))
        {

            if ($password == $confirm_password) {
            $reqemail = $bdd->prepare("SELECT * FROM UTILISATEUR WHERE email = ?");
            $reqemail->execute(array($email));
            $emailexist = $reqemail->rowCount();
            if($emailexist == 0){
                $insertmbr = $bdd->prepare("INSERT INTO UTILISATEUR(email, nom, prenom, numtel,  mdp, date_connex, role) VALUES (?,?,?,?,?,NOW(), 'lecteur')");
                $insertmbr -> execute(array($_POST['email'],$_POST['nom'], $_POST['prenom'], $_POST['numtel'], password_hash($_POST['mdp'], PASSWORD_DEFAULT)));
                echo "compte crée";
            }
            else {
                $erreur = "Cet email a déjà été utilisé.";
            }
        }
        else
        {
                $erreur = "Vos mots de passe ne correspondent pas.";
            }

        }
        else
        {
            $erreur = "Tous les champs doivent être complétés.";
        }
    }

?>

<!DOCTYPE html>

<html>
    <head>
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="inscription.css?t=<? echo time(); ?>" /> <!--lien css-->
    </head>

    <body>
        <div id="container">
            <!-- zone d'inscription -->

            <form action="inscription.php" method="POST">
                <h1>Inscription</h1>

                <div>
                    <label for="name"><b>Nom :</b></label>
                    <input type="text" placeholder="Entrer votre nom" id="name" name="nom" required>
                </div>
                <div>
                    <label for="surname"><b>Prénom :</b></label>
                    <input type="text" placeholder="Entrer votre prénom" id="pname" name="prenom" required>
                </div>
                <div>
                    <label for="mail"><b>e-mail :</b></label>
                    <input type="email" placeholder="Entrer votre email" id="mail" name="email" required>
                </div>
                <div>
                    <label for="numtel"><b>numero de telephone :</b></label>
                    <input type="tel" placeholder="Entrer votre numéro de téléphone" id="numtel" name="numtel" required>
                </div>
                <div>
                    <label for="mdp"><b>Mot de passe :</b></label>
                    <input type="password" placeholder="Entrer le mot de passe" name="mdp" required>
                </div>
                <div>
                    <label for="confirm_mdp"><b>Comfirmer le mot de passe :</b></label>
                    <input type="password" placeholder="Entrer le mot de passe" name="confirm_mdp" required>
                </div>
                <div class="button">
                    <input type="submit" name ="forminscription" value="Inscription">
                </div>
            </form>
        </div>
    </body>
</html>
