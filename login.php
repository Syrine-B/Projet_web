<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL);

    if(isset($_POST['connexion']))
    {
        if(isset($_POST['email']) && isset($_POST['password']))
        {
            $email = $_POST['email'];
            $password =$_POST['password'];
            if($email !== "" && $password !== "")
            {
            // connexion à la base de données
                $bdd =  new PDO('mysql:host=localhost;dbname=projetWeb;charset=utf8',"root","250297");
                //  Récupération de l'utilisateur et de son pass hashé
                $req = $bdd->prepare('SELECT * FROM UTILISATEUR WHERE email = ? AND inscription_valide = 2');
                $req->execute(array($email));
                $resultat = $req->fetch();

                // Comparaison du mot de passe envoyé via le formulaire avec la base
                $isPasswordCorrect = password_verify($_POST['password'], $resultat['mdp']);

                if (!$resultat)
                {
                    echo 'Mauvais identifiant ou mot de passe !';
                }
                else
                {
                    if ($isPasswordCorrect) {
                        session_start();
                        $chang_date = $bdd->prepare('UPDATE UTILISATEUR SET date_connex = NOW() WHERE id_utilisateur = ?');
                        $chang_date->execute(array($resultat['id_utilisateur']));

                        $_SESSION['id'] = $resultat['id_utilisateur'];
                        $_SESSION['prenom'] = $resultat['prenom'];
                        $_SESSION['nom'] = $resultat['nom'];
                        $_SESSION['role'] = $resultat['role'];
                        header("Location: accueil.php");
                        echo 'Vous êtes connecté !';
                    }
                    else {
                        echo 'Mauvais identifiant ou mot de passe !';
                    }
                }

            }
        }
    }
?>


<html>
    <head>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="login.css?t=<? echo time(); ?>" media="screen" type="text/css" />
    </head>
    <body>
        <div id="container">
            <!-- zone de connexion -->

            <form action="login.php" method="POST">
                <h1>Connexion</h1>

                <label><b>Adresse mail</b></label>
                <input type="text" placeholder="Entrer votre email" name="email" required>

                <label><b>Mot de passe</b></label>
                <input type="password" placeholder="Entrer le mot de passe" name="password" required>

                <input type="submit" name='connexion' value='Connexion' >

                <input type="button" name="inscription" onclick="self.location.href='inscription.php';" value='Pas de compte ? Inscrivez-vous !' >
            </form>
        </div>
    </body>
</html>
