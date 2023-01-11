<head>
	<meta charset="utf-8">
	<title>LecRecDir</title>
	<link href="style.css" rel="stylesheet">
</head>
<h1>Explorator Files :</h1>

<?php

include 'connect.php';

//onn regarde si une table existe deja 
//si ce n'est pas le cas on en cree une 
$sql = "SELECT TABLE_NAME AS RESULT FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME = 'test'";

$query = $cnx->prepare($sql);
// // On exécute
$query->execute();
$result = $query->fetch();

$path= "docs";

if (!$result){
	// //si la table n'existe pas 

	$sql = "CREATE TABLE `test` (
		id int NOT NULL AUTO_INCREMENT,
		fichier varchar(255) NOT NULL,
		chemin varchar(255),
		dossier varchar(255),
		extension varchar(255),
		taille int,
		PRIMARY KEY (id)
	);";
	
	$query = $cnx->prepare($sql);
	// On exécute
	$query->execute();
	explorerFile($path);
}

//PARTIE CREATION DE LA TABLE GENERALE 
echo "<table>";
echo "<td>";
echo '<button >+</button><img style="width:20px" src="https://camo.githubusercontent.com/8fc860423cb8edf1e787428fba028d39626cbee558f079361a958fe83ce363d1/68747470733a2f2f776963672e6769746875622e696f2f656e74726965732d6170692f6c6f676f2d666f6c6465722e737667"/><a href=\'main.php?p=1&d='.$path.'\'>'.$path.'</a></br>';

// EXPLORATION DE DOSSIER 
echo "<ul>";
explorerDir($path);
echo "</ul>";

//fonction qui explore les dossier 
function explorerDir($path){
	//
	$folder = opendir($path);
	
	//
	while($entree = readdir($folder))
	{		

		//
		if($entree != "." && $entree != "..")
		{
			//
			if(is_dir($path."/".$entree))
			{

				
				echo '<button>+</button><img style="width:20px" src="https://camo.githubusercontent.com/8fc860423cb8edf1e787428fba028d39626cbee558f079361a958fe83ce363d1/68747470733a2f2f776963672e6769746875622e696f2f656e74726965732d6170692f6c6f676f2d666f6c6465722e737667"/><a href=\'main.php?p=1&d='.$entree.'\'>'.$entree.'</a></br>';
				//
				echo "<ul>";
				$sav_path = $path;
				//
				$path .= "/".$entree;
				//			
				explorerDir($path);
				//
				$path = $sav_path;
				echo "</ul>";
			
			}
		}
	}


	closedir($folder);
	

}

//fonction qui explore les fichier et les enregistre dans la table 
function explorerFile($path){
	//
	$folder = opendir($path);
	
	//
	while($entree = readdir($folder))
	{		

		//
		if($entree != "." && $entree != "..")
		{
			//
			if(is_dir($path."/".$entree))
			{

				$sav_path = $path;
				//
				$path .= "/".$entree;
				//			
				explorerFile($path);
				//
				$path = $sav_path;
			
			}
			else
			{
				//
				$path_source = $path."/".$entree;				
				
				// Requete sql
				$type = substr(strrchr($entree, "."), 1);
				$size = filesize($path_source)/1024;
				
				if (strpos($path, "/") == false)
					$dossier = $path;
				else
					$dossier = substr(strrchr($path, "/"), 1);

					
				$SQL = "INSERT INTO `test` (`fichier`, `chemin`, `dossier`, `extension`, `taille`) VALUES ('$entree','$path_source','$dossier','$type','$size')";
			
				//excuter sql
				$bdd="mysql:dbname=test;host=localhost";
				$user="root";
				$mdp="";
				
				//excuter sql
				$cnx = new PDO(
					$bdd,
					$user,
					$mdp,
					[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
				);
				$SQL = $cnx->prepare($SQL);
				$SQL->execute();
			}
		}
	}


	closedir($folder);
	

}

//CREATION DE LA DEUXIEME COLONNE OU IL Y AURA LA BARRE DE RECHERCHE LES FICHIER ET LA PAGINATION ET LE BOUTON POUR SUPPRIMER 
echo "</td>";

echo "<td>";
echo '<form action="" method="POST" enctype="multipart/form-data">
	<textarea name=\'doc\'"></textarea>
	<input type="submit" value="Parcourir"></input>
	</form>';

echo "<table>";
echo "<tr>";
echo "<td> id </td>";
echo "<td> fichier </td>";
echo "<td> path </td>";
echo "<td> extension </td>";
echo "<td> dossier </td>";
echo "<td> taille (KB) </td>";
echo "</tr>";


$dossier="docs";
// On détermine le nombre total d'articles
$sql = "SELECT COUNT(*) AS nb_articles FROM `test` WHERE dossier='$dossier' ";
// On prépare la requête
$query = $cnx->prepare($sql);
// On exécute
$query->execute();
// On récupère le nombre d'articles
$result = $query->fetch();
$nbArticles = (int) $result['nb_articles'];
$perPage = 4;
$nbPage = ceil($nbArticles/$perPage);

//parametre de l'url pouir la pagination
if(isset($_GET['p']) && $_GET['p']>0 && $_GET[ 'p']<=$nbPage) {
	$cPage = $_GET['p'];
}
	else{
	$cPage = 1;
}

//CAS OU ON RENSEIGNE LE DOSSIER 
if(isset($_POST['doc'])){
			$dossier = $_POST['doc'];

			$requete = "SELECT * FROM `test` WHERE dossier='$dossier' LIMIT " . ($cPage - 1) * $perPage . ",$perPage";
			$resultat = $cnx->prepare($requete);
			$resultat->execute();


			foreach ($resultat->fetchAll() as $colonne) {

				echo "<tr>";

				echo "<td>" . $colonne['id'] . "</td>";
				echo "<td>";

				if ($colonne['extension'] == "png" || $colonne['extension'] == "jpg") {
					echo '<img style="width:20px" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6yvGVEDQCVr0x6w7I2x5Gh_v7Sm8naD4pOJtEwViZYpOHqjHcIaLvPaH1pY0DZMJ1Iug&usqp=CAU"/>&nbsp;';
				} else {
					echo '<img style="width:20px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAV1BMVEW9w8f///+VpabCx8uksLKerK2vuby7wsWqtLebqavV2duXpqeQoaKuuruPoKG/xMnDy8zc4eGotbX19/e8xsbt7/Di5ebX2tzO09a1vcDY3d3l6emjsbGMaM+gAAAD5ElEQVR4nO3dbXOiMBiF4RAFolBSpbrF+v9/52Ldvky3kzyEEE6cc3/tTM01gEQMoopHT609gMWjMP8ozD8K84/Cb/Wn8+XPftFWFZ43qlb1oinztJqw39cqQVW3AFEkPNdJgKrSbXyiRPiaxncTLkAUCDepgDdhfKJfmA74LoxO9AqT7aIfwthEn/CQEPhPGJnoEfYpgR/CuESP8DUl8FMYlegWpt2EX8KYRLcwzVTmF2FEoluYFvhdGI/oFJ5WFEYjOoVJTxU/hbGITmHad9KfwkhEp/B5XWEcolO4WVkYhYgtjEEEF0YgogvnE+GFs4n4wrnEDIQziTkI5xGzEM4i5iGcQ8xEOIOYizCcmI0wmJiPMJSYkTCQmJMwjJiVMIiYlzCEmJkwgJibcDoxO+FkIpRwkAinEpGEViacSEQSKiUTTiNCCe1uASKUUBmhcAoRS1i38YlYQltKhXIillB4vphEBBOqbXQimrCWC4VENOGU/VRGhBNa8RlDSIQTRifiCZUVfcQQEwGFytbS2ZuEiChUVlW7aLMbSOGt4aqFSM/yd1jhuCGHymwllS95Cm9GZSWpTa5CcRRSiB+FFOK3kHDZexH/vzkxtdCWTdpKm1won/5HiUIKKaSQwiyF4gspUWqTC9VQpW1wjIXzUgrxo5BC/CikED8KKcSPQgrxo5BC/Ja5TrPdpW3La20UUkghhRRSmF6oKpO2yjEWztooxI9CCvGjkEL8KKQQPwopxI9CCvFb6HuLMm383oJCCimkkEIK1/jeQvaDDvEyjrEsNGsT/aBDvFxD4byUQvwopBA/CinEj0IK8aOQQvwoDBQ+/Cfgh7+K8fhXoiikkEIKKaRQIHz4lQqpJ23OaRtn3hTiRyGF+FFIIX4UUogfhRTiRyGF+FFIIX4UUogfhRTiR6Hjj7kULHQ+zg2oOljoerYLUkOw0LUOCSkTLCzXHrqwMlzo+ik/nKoZwp1zSSBIdjdD2ObwXjO0M4Q6hyNxHGWw8Kq1Qd9PrdH6Gix8arVG308HrdunYOHzKGywJzZ1Mwqfg4WHToMTb0DdHYKF/U2omwH1WLTDDai7PlhYHO+r5lBnb+Y+vKPT4BZeuvv/aCr37QArZG3V3AfXXWYIi881lo0ZFIxyHMhgms91mW6CR/ixEcfapjQm8aM5f8+Ysvl6CqpnE/qExTHtA1Wn17qPQr+wX1vgzflGKhAWb53/RVase/MBvMLigLyfts6TvVBYnHGJ7dk/fIGw6K+Ye2q39R2DUmFR7Hd4xm63F41dJhyN167F2VvbtrvKfHJhUZwuL8ey8b/64jXl8eVyEo/7LwStxYPzkQFSAAAAAElFTkSuQmCC"/>&nbsp;';
				}

				echo "<a href=\"" . $colonne['chemin'] . "\" target=_blank >" . $colonne['fichier'] . "</a></td>";
				echo "<td>" . $colonne['chemin'] . "</td>";
				echo "<td>" . $colonne['extension'] . "</td>";
				echo "<td>" . $colonne['dossier'] . "</td>";
				echo "<td>" . $colonne['taille'] . "</td>";

				echo "</tr>";

			}
			echo "</table>";

			echo "<table>";
			echo "<tr>";
			echo "<td>";
			echo '<form action="main.php?p=' . $nbPage . '&d=' . $dossier . '" method="POST" enctype="multipart/form-data">
			<input type="file" name="fichier" id="fileupload">
			<input type="submit" name="submit" value="Upload">
			<p>Seuls les formats .jpeg, .png sont acceptés, taille maximale de 8Mo !<br/></p>
			</form>
			<td class="row-delete">
			<form action="main.php?p=' . $nbPage . '&d=' . $dossier . '" method="post">
			<textarea name =\'id\'> ID pour supprimer !</textarea>
			<input type="submit" name="submit" value="Delete">
			</form>
			</tr>
			</table>
			</tr>';

			echo "</br>";

			// ---------PAGINATION 

			// Precedent 
			echo "<table>";
			echo "<tr>";
			echo "<td>";
			if ($cPage == 1) {
				echo "Précedent";
			} else {
				$prec = $cPage - 1;
				echo "<a href=\"main.php?p=$prec&d=$dossier\">Précedent</a>";
			}

			echo "</td>";


			// nombre de page 
			for ($i = 1; $i <= $nbPage; $i++) {
				if ($i == $cPage) {
					echo "<td>$i</td>";
				} else {
					echo "<td><a href=\"main.php?p=$i&d=$dossier\">$i</a></td>";
				}
			}


			echo "<td>";
			// Suivant 
			if ($cPage == $nbPage) {
				echo "Suivant";
			} else {
				$suiv = $cPage + 1;
				echo "<a href=\"main.php?p=$suiv&d=$dossier\">Suivant</a>";
			}

			echo "</td>";
			echo "</table>";
		
	}
	
//CAS OU ON CLIQUE SUR LE DOSSIER 
else if (isset($_GET['d'])) {


	$dossier = $_GET['d'];

	

	$requete = "SELECT * FROM `test` WHERE dossier='$dossier' LIMIT ".($cPage-1)*$perPage.",$perPage" ;
	$resultat = $cnx->prepare($requete);
	$resultat->execute();

	
	foreach($resultat->fetchAll() as $colonne ) {

		echo "<tr>";

		echo "<td>".$colonne['id']."</td>";
		echo "<td>";
		
		if ( $colonne['extension'] == "png" || $colonne['extension']== "jpg" ){
			echo '<img style="width:20px" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT6yvGVEDQCVr0x6w7I2x5Gh_v7Sm8naD4pOJtEwViZYpOHqjHcIaLvPaH1pY0DZMJ1Iug&usqp=CAU"/>&nbsp;';
		}

		else{
			echo '<img style="width:20px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAV1BMVEW9w8f///+VpabCx8uksLKerK2vuby7wsWqtLebqavV2duXpqeQoaKuuruPoKG/xMnDy8zc4eGotbX19/e8xsbt7/Di5ebX2tzO09a1vcDY3d3l6emjsbGMaM+gAAAD5ElEQVR4nO3dbXOiMBiF4RAFolBSpbrF+v9/52Ldvky3kzyEEE6cc3/tTM01gEQMoopHT609gMWjMP8ozD8K84/Cb/Wn8+XPftFWFZ43qlb1oinztJqw39cqQVW3AFEkPNdJgKrSbXyiRPiaxncTLkAUCDepgDdhfKJfmA74LoxO9AqT7aIfwthEn/CQEPhPGJnoEfYpgR/CuESP8DUl8FMYlegWpt2EX8KYRLcwzVTmF2FEoluYFvhdGI/oFJ5WFEYjOoVJTxU/hbGITmHad9KfwkhEp/B5XWEcolO4WVkYhYgtjEEEF0YgogvnE+GFs4n4wrnEDIQziTkI5xGzEM4i5iGcQ8xEOIOYizCcmI0wmJiPMJSYkTCQmJMwjJiVMIiYlzCEmJkwgJibcDoxO+FkIpRwkAinEpGEViacSEQSKiUTTiNCCe1uASKUUBmhcAoRS1i38YlYQltKhXIillB4vphEBBOqbXQimrCWC4VENOGU/VRGhBNa8RlDSIQTRifiCZUVfcQQEwGFytbS2ZuEiChUVlW7aLMbSOGt4aqFSM/yd1jhuCGHymwllS95Cm9GZSWpTa5CcRRSiB+FFOK3kHDZexH/vzkxtdCWTdpKm1won/5HiUIKKaSQwiyF4gspUWqTC9VQpW1wjIXzUgrxo5BC/CikED8KKcSPQgrxo5BC/Ja5TrPdpW3La20UUkghhRRSmF6oKpO2yjEWztooxI9CCvGjkEL8KKQQPwopxI9CCvFb6HuLMm383oJCCimkkEIK1/jeQvaDDvEyjrEsNGsT/aBDvFxD4byUQvwopBA/CinEj0IK8aOQQvwoDBQ+/Cfgh7+K8fhXoiikkEIKKaRQIHz4lQqpJ23OaRtn3hTiRyGF+FFIIX4UUogfhRTiRyGF+FFIIX4UUogfhRTiR6Hjj7kULHQ+zg2oOljoerYLUkOw0LUOCSkTLCzXHrqwMlzo+ik/nKoZwp1zSSBIdjdD2ObwXjO0M4Q6hyNxHGWw8Kq1Qd9PrdH6Gix8arVG308HrdunYOHzKGywJzZ1Mwqfg4WHToMTb0DdHYKF/U2omwH1WLTDDai7PlhYHO+r5lBnb+Y+vKPT4BZeuvv/aCr37QArZG3V3AfXXWYIi881lo0ZFIxyHMhgms91mW6CR/ixEcfapjQm8aM5f8+Ysvl6CqpnE/qExTHtA1Wn17qPQr+wX1vgzflGKhAWb53/RVase/MBvMLigLyfts6TvVBYnHGJ7dk/fIGw6K+Ye2q39R2DUmFR7Hd4xm63F41dJhyN167F2VvbtrvKfHJhUZwuL8ey8b/64jXl8eVyEo/7LwStxYPzkQFSAAAAAElFTkSuQmCC"/>&nbsp;';
		}
		
		echo "<a href=\"".$colonne['chemin']."\" target=_blank >".$colonne['fichier']."</a></td>";
		echo "<td>".$colonne['chemin']."</td>";
		echo "<td>".$colonne['extension']."</td>";
		echo "<td>".$colonne['dossier']."</td>";
		echo "<td>".$colonne['taille']."</td>";

		echo "</tr>";
		
	}
	echo "</table>";

	echo "<table>";
	echo "<tr>";
	echo "<td>";
	echo '<form action="main.php?p='.$nbPage.'&d='.$dossier.'" method="POST" enctype="multipart/form-data">
	<input type="file" name="fichier" id="fileupload">
	<input type="submit" name="submit" value="Upload">
	<p>Seuls les formats .jpeg, .png sont acceptés, taille maximale de 8Mo !<br/></p>
	</form>
	<td class="row-delete">
	<form action="main.php?p='.$nbPage.'&d='.$dossier.'" method="post">
	<textarea name =\'id\'> ID pour supprimer !</textarea>
	<input type="submit" name="submit" value="Delete">
	</form>
	</tr>
	</table>
	</tr>';

	echo "</br>";

	// ---------PAGINATION 

	// Precedent 
	echo "<table>";
	echo "<tr>";
	echo "<td>";
	if($cPage==1){
		echo "Précedent";
	}
	else{
		$prec = $cPage - 1; 
		echo "<a href=\"main.php?p=$prec&d=$dossier\">Précedent</a>";
	}

	echo "</td>";

	
	// nombre de page 
	for ($i=1; $i<=$nbPage ;$i++) {
		if ($i==$cPage) {
		echo "<td>$i</td>";
		}
		else{
		echo "<td><a href=\"main.php?p=$i&d=$dossier\">$i</a></td>";
		}
	}

	
	echo "<td>";
	// Suivant 
	if($cPage==$nbPage){
		echo "Suivant";
	}
	else{
		$suiv = $cPage + 1; 
		echo "<a href=\"main.php?p=$suiv&d=$dossier\">Suivant</a>";
	}

	echo "</td>";
	echo "</table>";
	}

//SUPPRESSION DE FICHIER 
if (isset($_POST['id'])) {
	$row_delete = $_POST['id'] ;
	$SQL = "DELETE FROM `test` WHERE id=$row_delete";
	//excuter sql
	$SQL = $cnx->prepare($SQL);
	$SQL->execute();
}
    
//TELEVERSEMENT 
if(isset($_FILES["fichier"]) && $_FILES["fichier"]["error"] == 0)
	 {
		 
		 $ok = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
		 echo $name = $_FILES["fichier"]["name"], "<br/>";
		 echo $type = $_FILES["fichier"]["type"], "<br/>";
		 echo $size = $_FILES["fichier"]["size"], "<br/>";

		 $path = __DIR__."/docs";
		$tmp = substr(strrchr($_FILES["fichier"]["tmp_name"], "/"), 1);
		echo $_FILES["fichier"]["tmp_name"], "<br/>";
		 echo $path = $path . "/" .$tmp, "<br/>";
		 
 
		 // Vérifie l'extension du fichier
		 $extension = pathinfo($name, PATHINFO_EXTENSION);
		 if(!array_key_exists($extension, $ok)) die("Erreur : format de fichier  non autorisé !");
 
		 // Vérifie la taille du fichier - 5Mo maximum
		 $sizemax = 8 * 1024 * 1024;
		 if($size > $sizemax) die("Erreur: La taille du fichier ne doit pas dépassée $sizemax !");
 
		 // Vérifie le type MIME du fichier
		 if(in_array($type, $ok)){
			
			 // Vérifie si le fichier existe avant de le télécharger.
			 if(file_exists("docs/". $_FILES["fichier"]["name"])){
				 echo "Ce ", $_FILES["fichier"]["name"] . " existe déjà !";
			 } 
			else{

				$uploaddir = "/".$dossier."/";
				$uploadfile = $uploaddir . basename($_FILES['fichier']['tmp_name']);

				echo $uploadfile, "</br>";
				move_uploaded_file($_FILES['fichier']['tmp_name'], $uploadfile);
					

						 // Requete sql
				 $SQL = "INSERT INTO `test` (`fichier`, `chemin`, `dossier`, `extension`, `taille`) VALUES ('$name','$dossier/$name','$dossier','$extension','$size')";
						 
				 //excuter sql
				 $SQL = $cnx->prepare($SQL);
				 $SQL->execute();
			} 
		 } 
			 else{
			 echo "Erreur: Problème de téléchargement du fichier !"; 
		   }
   } 


?>

</tr>
</table>

<script>
//script pour deplié et plié le menu des boutons dosssier
let btns = document.querySelectorAll('button');
let menu = document.querySelectorAll('ul');


for (let i in btns) {
	
	menu[i].style.visibility='hidden';
	menu[i].style.display='none';

	btns[i].addEventListener('click', () => {
		
		if(menu[i].style.visibility=='hidden' && menu[i].style.display=='none' ){
			btns[i].textContent = '-';
			menu[i].style.visibility='visible';
			menu[i].style.display='block';
		}
		else{
			btns[i].textContent = '+';
			menu[i].style.visibility='hidden';
			menu[i].style.display='none';
		}

	});
}

</script>


