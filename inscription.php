<?php

	session_start();

	require_once('./src/option.php');

	if(isset($_SESSION['connect'])){

		header('location: ./index.php');
		exit();
	}

	if(!empty($_POST['email']) && $_POST['password'] && $_POST['password_two']){
		require_once('./src/connection.php');
		// Variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		$password_two = htmlspecialchars($_POST['password_two']);

		// Comparaison des mots de passe
		if($password != $password_two){
			header('location: ./inscription.php?error=1&message=Vos mots de passe ne sont pas identiques.');
			exit();
		}
		// Verification de l'adresse email
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){  // renvoie false si c'est un email donc ! pour vérifier que ce n'est pas un email
			header('location: ./inscription.php?error=1&message=Votre adresse email est invalide.');
			exit();
		}

		// Verification d'un éventuel doublon de l'adresse mail
		$req = $bdd->prepare('SELECT COUNT(*) AS emailNumber FROM user WHERE email=?');
		$req->execute([$email]);
		
		while($emailVerification = $req->fetch()){
			if($emailVerification['emailNumber'] != 0){
				header('location: ./inscription.php?error=1&message=Votre adresse email est déjà utilisée par un autre utilisateur.');
				exit();
			}
		}

		// Chiffrement du mot de passe
		$password = "aq1".sha1($password."135")."25"; // sha1 fonction pour hacher un password

		// Secret = identifiant unique généré pour l'utilisateur pour se connecter automatiquement
		$secret = sha1($email).time();
		$secret = sha1($secret).time();

		// Ajouter un utilisateur
		$req = $bdd->prepare('INSERT INTO user(email, password, secret) VALUES(?, ?, ?)');
		$req->execute([
			$email,
			$password,
			$secret		
		]);
		header('location: inscription.php?success=1');
		exit();
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="assets/favicon.png">
</head>
<body>

	<?php require_once('src/header.php'); ?>
	
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php if (isset($_GET['error']) && $_GET['message']) { 
				echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
			}else if (isset($_GET['success'])) {
				echo '<div class="alert success">Vous êtes désormais inscrit. <a href="index.php">Connectez-vous.</a>.</div>';
			}?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php require_once('src/footer.php'); ?>
</body>
</html>