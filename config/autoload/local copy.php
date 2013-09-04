<?php
/**
 * Fichier de configuration locale
 */

// config/autoload/local.php:
return array(
	// Paramétrage de la connexion à la base de données
    'db' => array(
        'username' => 'inventairemusee',
        'password' => 'Y69sT4Ozscea',
        //'dsn'            => 'mysql:dbname=inventaire;host=localhost',
    	'dsn' =>  'mysql:dbname=inventairemusee'
    ),
	'ca_direct' => array(
		'path' => '/home/musee/collectiveaccess/providence',
		'url' => 'http://musee.idcultu.re/gestion'
	),
	// Paramétrage des accès aux webservices de CollectiveAccess
	'ca' => array(
    	'ca_service_url' => "http://musee.idcultu.re/gestion/service.php",
        'username' => 'webservices',
        'password' => 'webservices',
    	'object_status' => 0,
    	'object_access' => 1,
    	'object_type_id' => 21),
	'ca_export_mapping' => array(
		'inventaire' => array(
			// Exemple de paramétrage simple de correspondance pour l'export (champ de l'inventaire vers attribut simple)
			// 'ca_objects.codeattributsimple' => array("valeur" => "nomchampinventaire")
			'ca_objects.idno'=> 'numinv',
			'ca_objects.acquisitionMethod'=> array(
				"valeur" => 'mode_acquisition'),//OK
			'ca_entities.preferred_labels'=>array( 
				"valeur" => 'donateur'),
			'ca_objects.acquisitionDate'=>array( 
				"valeur" => 'date_acquisition'),//OK
			'ca_objects.comments'=>array( 
				"valeur" => 'avis'),//OK
			'ca_objects.prix'=>array( 
				"valeur" => 'prix'), //OK
			'ca_objects.date_inventaire'=>array( 
				"valeur" => 'date_inscription'), //OK
			'ca_objects.preferred_labels'=>array( 
				"valeur" => 'designation'),//OK
			'ca_objects.inscription_c'=>array( //A TESTER
				"valeur" => 'inscription',
				"container" => 	array(
					"inscription_presence" => "inscription1",
					"inscription_info" => "^valeur")), 
			'ca_objects.materiaux'=>array( 
				"valeur" => 'materiaux'),
			'ca_objects.techniqueFabrication'=>array( 
				"valeur" => 'techniques'), 
			'ca_objects.dimensions'=>array( //A TESTER
				"valeur" => 'mesures',
				"container" => array(
					"dimensions_width" => "10 cm",
					"dimensions_height" => "11 cm",
					"dimensions_depth" => "12 cm",
					"circumference" => "13 cm",
					"dimensions_type" => "i2")),
			'ca_objects.constatEtat.remarques_etatActuel'=>array( 
				"valeur" => 'etat'), 
			'ca_entities.preferred_labels'=>array( 
				"valeur" => 'auteur'),
			'ca_objects.style'=>array( 
				"valeur" => 'epoque'),
			'ca_objects.fonctions'=>array( 
				"valeur" => 'usage'), 
			'ca_places.preferred_labels'=>array( 
				"valeur" => 'provenance') ,
			'ca_objects.observations'=>array( 
				"valeur" => 'observations') //OK
			),
		'depot' => array(),
	),
	'ca_import_mapping' => array(
		'inventaire' => array(
			"numinv" => array(
				array("field" => 'ca_objects.idno')),
			"mode_acquisition" => array(
				array("fields" => array(
					"1" => 'ca_objects.AcquisitionMode')
				)),
			'donateur'=>array(
				array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "116", // donateur
					"prefixe" => "Donateur : <br/>",
					"suffixe" => "<br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "117", // testateur
					"prefixe" => "Testateur : <br/>",
					"suffixe" => "<br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "118", // vendeur
					"prefixe" => "Vendeur : <br/>",
					"suffixe" => "<br/>"
				)
			), //ca_entities.preferred_labels.forename%delimiter=;_%restrictToRelationshipTypes=donateur|testateur|vendeur")), //"donateur","testateur","vendeur"
			'date_acquisition'=>array( //date_acteAcquisition 
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.date_acteAcquisition',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "Date de l'acte d'acquisition : <br/>",
					"suffixe" => "<br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.ref_acteAcquisition',
					"prefixe" => "Référence de l'acte d'acquisition : <br/>",
					"suffixe" => "<br/>"
				),
				array(
					"field" => 'date_affectation',
					"prefixe" => "Date d'affectation au musée : <br/>",
					"post-treatment" => 'caDateToUnixTimestamp',
					"suffixe" => "<br/>"
				)
			),
			'avis'=>array( 
				array(
					"field" => 'ca_objects.avisScientifiques.instance',
					"prefixe" => "Instance : <br/>",
					"suffixe" => "<br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.avis_sens',
					"prefixe" => "Sens de l'avis : <br/>",
					"suffixe" => "<br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.date_avis',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "Date de l'avis : <br/>",
					"suffixe" => "<br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.commentaire_avis',
					"prefixe" => "Commentaire sur l'avis : <br/>",
					"suffixe" => "<br/>"
				)
			),// A REVOIR
			'prix'=>array( 
				array(
					"field" => 'ca_objects.prix',
					"post-treatment" => 'convertcurrencytoeuros',
					"prefixe" => "Prix : <br/>",
					"suffixe" => "<br/>"
				), //OK
				array(
					"field" => 'ca_objects.mentionConcours',
					"prefixe" => "Mention des concours publics :<br/>",
					"suffixe" => "<br/>"
				)
			),
			'date_inscription'=>array( 
				array("field" => 'ca_objects.date_inventaire',
				"post-treatment" => 'caDateToUnixTimestamp')),
			'designation'=>array( 
				//array("field" => 'ca_objects.preferred_labels')),//OK
				array(
						"field" => 'ca_objects.domaine',
						"prefixe" => "Domaine (catégorie du bien) :<br/>",
						"suffixe" => "<br/>"
				),
				/*array(
						"field" => 'ca_objects.mentionConcours',
						"prefixe" => "Dénomination :<br/>",
						"suffixe" => "<br/>"
				),
				array(
						"field" => 'ca_objects.mentionConcours',
						"prefixe" => "Appellation :<br/>",
						"suffixe" => "<br/>"
				),*/
				array(
						"field" => 'ca_objects.preferred_labels',
						"prefixe" => "Titre :<br/>",
						"suffixe" => "<br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_decor',
						"prefixe" => "Représentation (décor porté) :<br/>",
						"suffixe" => "<br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_precisions',
						"prefixe" => "Précisions sur la représentation (décor porté) :<br/>",
						"suffixe" => "<br/>"
				)
			),				
			'inscription'=>array( //A TESTER
				array("field" => 'ca_objects.inscription_c')), 
			'materiaux'=>array( 
				array("field" => 'ca_objects.materiaux_tech_c',
				"options" => array("template" => "^materiaux"))
			),
			'techniques'=>array( 
				array("field" => 'ca_objects.materiaux_tech_c',
				"options" => array("template" => "^techniques"))
			),
			'mesures'=>array( //A TESTER
				array("field" => 'ca_objects.dimensions')),
			'etat'=>array( 
				array("field" => 'ca_objects.constatEtat')), 
			'auteur'=>array(
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "115", // auteur
						"prefixe" => "Auteur : <br/>",
						"suffixe" => "<br/>"
				)
			),
			'epoque'=>array(
				array("field" => 'ca_objects.useDate')),
			'usage'=>array( 
				array("field" => 'ca_objects.fonctions')), 
			'provenance'=>array( 
				array("field" => 'ca_places.preferred_labels')) ,
			'observations'=>array( 
				array("field" => 'ca_objects.observations'))
		),
		'depot' => array()
	)
);