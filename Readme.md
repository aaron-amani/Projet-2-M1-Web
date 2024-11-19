# LecRecDir

`LecRecDir` est une application PHP permettant d'explorer les fichiers et dossiers d'un répertoire, d'enregistrer des informations sur ces fichiers dans une base de données MySQL, et de gérer le téléchargement et la suppression de fichiers. Il est également possible de naviguer entre différents dossiers et de visualiser des fichiers (images, etc.) dans l'interface.

## Prérequis

Avant d'utiliser cette application, assurez-vous que votre environnement de développement réunit les prérequis suivants :

- Serveur Web avec PHP (version 7.4 ou supérieure) et MySQL ou MariaDB
- Une base de données MySQL
- Accès à un dossier (par exemple, `docs`) contenant les fichiers à explorer et à télécharger

## Installation

1. **Clonez ou téléchargez le projet**
   Téléchargez ou clonez le dépôt dans un répertoire accessible par votre serveur web.

   ```bash
   git clone https://votre-repository.git
   ```

2. **Base de données**
   Créez une base de données MySQL pour stocker les informations des fichiers. Le script SQL suivant va créer la table nécessaire :

   ```sql
   CREATE DATABASE lec_rec_dir;
   USE lec_rec_dir;

   CREATE TABLE `test` (
       id INT NOT NULL AUTO_INCREMENT,
       fichier VARCHAR(255) NOT NULL,
       chemin VARCHAR(255),
       dossier VARCHAR(255),
       extension VARCHAR(255),
       taille INT,
       PRIMARY KEY (id)
   );
   ```

3. **Configuration de la connexion à la base de données**
   Dans le fichier `connect.php`, modifiez les informations de connexion à la base de données MySQL selon votre environnement.

   Exemple :
   ```php
   $bdd = "mysql:dbname=lec_rec_dir;host=localhost";
   $user = "root";
   $mdp = "votre_mot_de_passe";
   ```

4. **Dossier de fichiers**
   Assurez-vous que le dossier `docs` existe à la racine du projet et contient les fichiers à explorer.

   Vous pouvez également utiliser un autre dossier en modifiant la variable `$path` dans le code PHP.

5. **Permissions**
   Vérifiez que le serveur Web a les permissions nécessaires pour lire, écrire et supprimer des fichiers dans le répertoire `docs`.

## Fonctionnalités

### 1. **Exploration des fichiers et dossiers**
   Le script permet de naviguer à travers les dossiers et fichiers d'un répertoire. Il affiche une interface sous forme de table, indiquant les informations suivantes pour chaque fichier :

   - ID
   - Nom du fichier
   - Chemin d'accès
   - Extension
   - Dossier
   - Taille (en Ko)

### 2. **Téléchargement de fichiers**
   Vous pouvez télécharger de nouveaux fichiers dans le dossier. Seuls les fichiers avec les extensions `.jpeg`, `.jpg` et `.png` sont autorisés, et leur taille ne doit pas dépasser 8 Mo.

### 3. **Suppression de fichiers**
   Il est possible de supprimer un fichier en spécifiant son ID dans un formulaire de suppression.

### 4. **Pagination**
   L'application permet de paginer les résultats afin de naviguer facilement entre les fichiers lorsque le nombre de fichiers est trop grand.

### 5. **Affichage des miniatures d'images**
   Si un fichier est une image (extension `.jpg`, `.jpeg`, `.png`), une miniature de l'image est affichée à côté de son nom.

## Utilisation

1. **Explorer un dossier :**
   En accédant à la page principale, vous verrez la liste des dossiers présents dans le répertoire `docs`. Vous pouvez cliquer sur un dossier pour afficher son contenu.

2. **Télécharger un fichier :**
   - Allez dans un dossier spécifique.
   - Utilisez le formulaire de téléchargement pour ajouter un fichier.
   - Seuls les fichiers `.jpeg`, `.jpg`, et `.png` sont acceptés, avec une taille maximale de 8 Mo.

3. **Supprimer un fichier :**
   - Allez dans le dossier contenant le fichier que vous souhaitez supprimer.
   - Utilisez le formulaire de suppression en entrant l'ID du fichier à supprimer.

4. **Navigation entre les pages :**
   - Vous pouvez utiliser les liens de pagination pour naviguer à travers les fichiers d'un dossier.

## Technologies utilisées

- PHP pour la gestion côté serveur
- MySQL pour la gestion de la base de données
- HTML/CSS pour l'interface utilisateur

## Sécurité

Il est recommandé de sécuriser l'accès à l'application en limitant l'accès via un mot de passe ou une autre forme d'authentification. Actuellement, aucune protection d'accès n'est implémentée.

## Auteurs

- Aaron Amani

