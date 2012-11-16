application Inventaire
======================
 
![image](http://inventaire.ideesculture.fr/images/inventaire_128x128.png)

L'inventaire des biens affectés aux collections d'un musée de France est un document unique, infalsifiable, titré, daté et paraphé par le professionnel responsable des collections, répertoriant tous les biens par ordre d'entrée dans les collections.

Cette application a été créée pour vous permettre de gérer votre inventaire et selon votre besoin d'importer ensuite vos données dans CollectiveAccess.

L'impression de votre inventaire est une obligation légale qui répond à des impératifs formels. Cette application vous permet d'y répondre avec style.

## Pré-requis
----------------------------
### Extensions PHP

* php-intl
* php-curl
* extension PECL http

## Installation
----------------------------

### PECL http
Si vous avez besoin de l'extension PECL http :

	apt-get install libcurl3-openssl-dev
	pecl install pecl_http

### Mettre à jour les bibliothèques requises à l'aide de composer
Ce projet utilise des bibliothèques libres (ZendFramework 2, ZfcUser, DOMpdf…). Pour des raisons de maintenance, ces bibliothèques ne sont pas incluses dans le code source du programme, il faut les télécharger lors de l'installation en ligne de commande :

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