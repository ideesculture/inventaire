application Inventaire
======================
 
![image](http://www.ideesculture.com/idculture/inventaire2_256x256.png)

L'inventaire des biens affectés aux collections d'un musée de France est un document unique, infalsifiable, titré, daté et paraphé par le professionnel responsable des collections, répertoriant tous les biens par ordre d'entrée dans les collections.

Cette application a été créée pour vous permettre de gérer votre inventaire et selon votre besoin d'importer ensuite vos données dans CollectiveAccess.

L'impression de votre inventaire est une obligation légale qui répond à des impératifs formels. Cette application vous permet d'y répondre avec style.

## Pré-requis
----------------------------
### Extensions PHP

* php-intl
* php-curl
* extension PECL http
* DluwTwBootstrap pour ZendFramework

## Installation
----------------------------

### Prérequis Apache
Apache doit avoir l'extension *mod_rewrite* installée et configurée. 
Sous Ubuntu server, ceci se fait à l'aide de :

	a2enmod rewrite
	service apache2 restart


Les fichiers .htaccess doivent être autorisés. Vous pouvez réaliser cela en modifiant le paramètre :

	AllowOverride None
	
en :

	AllowOverride FileInfo

dans votre fichier *httpd.conf* ou équivalent.

A titre d'exemple, vous trouverez dans le répertoire *install* un exemple de fichier de configuration pour un vhost d'Apache. Le répertoire *DocumentRoot* doit pointer vers *inventaire/public*

### PECL http
L'extension PECL http est requise pour activer les webservices vers CollectiveAccess :

	apt-get install libcurl3-openssl-dev
	pecl install pecl_http

### Mettre à jour les bibliothèques requises à l'aide de composer
Ce projet utilise des bibliothèques libres (ZendFramework 2, DOMpdf…). Pour des raisons de maintenance, certaines de ces bibliothèques ne sont pas incluses dans le code source du programme (uniquement les versions personnalisées le sont), il faut les télécharger lors de l'installation en ligne de commande :

    cd inventaire
    php composer.phar update


### Paramétrer la configuration de l'application
#### En ligne de commandes

    cd inventaire
    cd config/autoload
    cp local.php.exemple local.php
    nano local.php

#### Avec un éditeur :

* recopier le fichier **config/autoload/local.php.exemple** dans **config/autoload/local.php**

* ouvrir le fichier **config/autoload/local.php**

### Charger la base de données avec la structure de départ

#### Dans phpMyAdmin ou en ligne de commandes, charger la base

Utiliser le fichier 

	install/database_structure.sql
	
### Se connecter

* login **test**

* mot de passe **testtest**

## Erreurs
--------------------------
* **Problème de connexion à la base MySQL**

		Connect Error: could not find driver
	
	Vous n'avez pas configuré **inventaire/config/local.php**. Recopiez **inventaire/config/local.php.exemple** dans **inventaire/config/local.php** et modifiez le contenu du fichier pour configurer l'accès à la base de données MySQL.