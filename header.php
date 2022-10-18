<!DOCTYPE html>
<header>

	<link rel="stylesheet" href="header.css?t=<? echo time(); ?>" media="screen" type="text/css" />

	<table id="bandeau" border="0">
        <tr>
            <td id="logo">
                <img src="Images/logo.png" alt="Logo" />
                <div id="titre_principal">
                    <h1>   Genoma   </h1>
                </div>


            </td>
			<td class=logout>
                <input style="border-radius: 10px;" type="button" name="moncompte" onclick="self.location.href='mon_compte.php';" value='Mon compte' >
                <br><br>
                <input style="border-radius: 10px;" type="button" name="sedeco" onclick="self.location.href='login.php';" value='Se déconnecter' >
                <br><br>
			    <?php // Tester si l'utilisateur est connecte
                    if($_SESSION['prenom'] !== ""){
                        $user = $_SESSION['prenom'];
                        // Afficher un message
                        echo "Bonjour $user, vous êtes connecté(e)";
                    }
			    ?>
			</td>
        </tr>
	</table>
</header>
