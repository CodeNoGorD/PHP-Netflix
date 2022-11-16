<?php
    if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])){

        // Connexion BDD
        require_once('connection.php');

        // Securiser les variables
        $secret = htmlspecialchars($_COOKIE['auth']);

        // Vérification de l'existence du secret
        $req = $bdd->prepare('SELECT COUNT(*) AS secretNumber FROM user WHERE secret = ?');
        $req->execute([$secret]);

        while($user = $req->fetch()){

            if($user['secretNumber'] == 1){

        // Lire ce qui concerne l'utilisateur
                $informations = $bdd->prepare('SELECT * FROM user WHERE secret = ?');
                $informations->execute([$secret]);

                while($userInformations = $informations->fetch()){
                    $_SESSION['connect'] = 1;
				    $_SESSION['email'] = $userInformations['email'];

                }
            }
        }
    }
