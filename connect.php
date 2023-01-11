<?php
	//Exemple de syntaxe de connexion à la base de données pour PHP et MySQL.
	
	//Se connecter à la base de données

	$bdd="mysql:dbname=test;host=localhost";
	$user="root";
	$mdp="";
	
	try {
        $cnx = new PDO(
            $bdd,
            $user,
            $mdp,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
        );
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }

?>