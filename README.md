# Lemauvaiscoin
This project is only made for educational purposes / The description, README and other details are in progress 

## Getting Started

### French
Pour lancer ce projet il vas vous être nécessaire d'avoir en possesion MAMP / XAMP / WAMP ou une autre plateforme de developpement Web PHP

une fois ceci fait, rendez-vous dans le chemin de vos fichiers pointé par le Serveur (géénralement .htdocs) une fois le chemin retrouvé, entrez le chemin dans votre terminal et effectuez la commande 

#### Cloner le projet
````
``git clone https://github.com/Kaox260/Lemauvaiscoin.git``
`````
Une fois la commande effectué vous etes censé avoir ce message affiché dans le terminal : 

`Cloning into 'Lemauvaiscoin'...
remote: Enumerating objects: 4, done.
remote: Counting objects: 100% (4/4), done.
remote: Compressing objects: 100% (4/4), done.
remote: Total 4 (delta 0), reused 0 (delta 0), pack-reused 0 (from 0)
Receiving objects: 100% (4/4), done.`

Si ce message est affiché, le projet est cloné 

#### Installer la Base de données

Pour installer la Base de données, il faut ouvrir dans votre ordinateur le fichier ``schema-bdd.sql``qui est situé dans le projet. 

Une fois le fichier ``schema-bdd.sql`` ouvert, copiez tout le contenu du fichier et rendez-vous dans PHP-My-Admin, qui est généralement a cette adresse mais _*il se peut que votre adresse soit différente, tout depends de votre serveur PHP*_ :

``http://localhost:8888/phpMyAdmin5/``

Uns fois sur votre page d'acceuil PHP my Admin rendez-vous à gauche de votre page et cliquez sur 

``"Nouvelle base de données"``

Une fois cliqué, cliquez maintenant au millieu de l'éran sur : 

``" Création d'une base de données Documentation"``

et renommez votre base de données : 

``leboncoin
``

sans espaces ni majuscules

cliquez sur "Créer"

Puis sur `SQL` en haut de votre écran

Et collez l'integralité du fichier ``schema-bdd.sql`` dans l'espace d'écriture puis cliquer sur : ``Exécuter``

Et voila, votre base de données est installé 


Il vous manque plus qu'à lancer votre serveur PHP et ouvrir votre chemin de projets généraux et choisir notre projet et vous connecter dessus

Généralement l'adresse est : 

``http://localhost:8888/``

Si la page de connexion s'affiche Felicitations !! Votre projet est lancé