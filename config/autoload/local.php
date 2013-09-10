<?php
/**
 * Fichier de configuration locale
 */

// config/autoload/local.php:
return array(

	/* LOCAL */
	// Paramétrage de la connexion à la base de données
    'db' => array(
        'username' => 'inventaire',
        'password' => 'inventaire',
        //'dsn'            => 'mysql:dbname=inventaire;host=localhost',
    	'dsn' =>  'mysql:dbname=inventaire'
    ),
	'ca_direct' => array(
		'path' => '/Sites/github/ideesculture_repo/musee',
		'url' => 'http://musee.site'
	),
    
    /* DISTANT 	
	// Paramétrage de la connexion à la base de données
    'db' => array(
        'username' => 'inventairemusee',
        'password' => '',
        //'dsn'            => 'mysql:dbname=inventaire;host=localhost',
    	'dsn' =>  'mysql:dbname=inventairemusee'
    ),
	'ca_direct' => array(
		'path' => '/path-to-ca-install',
		'url' => 'http://url.domain/path'
	),*/
		
	'ca_import_mapping' => array(
		// Correspondance des champs pour l'import dans les BIENS DEPOSES
		'depot' => array(
			"numdepot" => array(
				array(
					"field" => 'ca_objects.idno',
					"prefixe" => "Numéro de dépôt : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			"numdepot_sort" => array(
				array(
					"field" => 'ca_objects.idno_sort'
				)
			),
			"numdepot_display" => array(
				array(
					"field" => 'ca_objects.idno'
				)
			),
			"numinv" => array(
				array(
					"field" => 'ca_objects.otherNumber',
					"prefixe" => "Numéro d'inventaire dans les collections du déposant : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			"actedepot" => array(
				array(
					"field" => 'ca_objects.date_ref_acteDepot',
					"prefixe" => "Date et référence de l'acte de dépôt : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'date_priseencharge'=>array(  
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.date_acteAcquisition',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "Date de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.ref_acteAcquisition',
					"prefixe" => "Référence de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'date_affectation',
					"prefixe" => "Date d'affectation au musée : <b>",
					"post-treatment" => 'caDateToUnixTimestamp',
					"suffixe" => "</b><br/>"
				)
			),
			'proprietaire'=>array(
				array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "116", // donateur
					"prefixe" => "Donateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "117", // testateur
					"prefixe" => "Testateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "118", // vendeur
					"prefixe" => "Vendeur : <b>",
					"suffixe" => "</b><br/>"
				)
			), 
			"actefindepot" => array(
				array(
					"field" => 'ca_objects.date_ref_actefinDepot',
					"prefixe" => "Date et référence de l'acte de fin du dépôt : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'date_inscription'=>array( 
				// COLONNE 7
				array("field" => 'ca_objects.date_inventaire',
				"post-treatment" => 'caDateToUnixTimestamp')),
			'date_inscription_display'=>array( 
				array("field" => 'ca_objects.date_inventaire',
				"post-treatment" => 'caDateToUnixTimestamp')
				),
			'designation'=>array(
				// COLONNE 8
				array(
						"field" => 'ca_objects.domaine',
						"prefixe" => "Domaine (catégorie du bien) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels',
						"prefixe" => "Titre : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_decor',
						"prefixe" => "Représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_precisions',
						"prefixe" => "Précisions sur la représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "25589", // donateur
						"prefixe" => "Appellation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "25663", // donateur
						"prefixe" => "Dénomination : <b>",
						"suffixe" => "</b><br/>"
				)
			),				
			'designation_display'=>array( 
				//array("field" => 'ca_objects.preferred_labels')),//OK
				array(
						"prefixe" => "<small>",
						"field" => 'ca_objects.domaine',
						"suffixe" => "</small><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels'
				)
			),				
			'inscription'=>array( //A TESTER
				array(
						"field" => 'ca_objects.inscription_type',
						"prefixe" => "Type d'inscriptions ou de marque : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_txt_c.inscription_txt',
						"prefixe" => "Transcription de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_langue_c.inscription_langue',
						"prefixe" => "Langue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_alphabet_c.inscription_alphabet',
						"prefixe" => "Alphabet : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_emplacement_c.inscription_emplacement',
						"prefixe" => "Emplacement de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_info_c',
						"prefixe" => "Précisions : <b>",
						"suffixe" => "</b><br/>"
				)				
			),
			'materiaux'=>array( 
				// COLONNE 10
				array(
						"field" => 'ca_objects.materiaux_tech_c.materiaux',
						"prefixe" => "Matériaux : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'techniques'=>array( 
				// COLONNE 11
				array(
						"field" => 'ca_objects.materiaux_tech_c.techniques',
						"prefixe" => "Techniques : <b>",
						"suffixe" => "</b><br/>"
				)

			),
			'mesures'=>array(
				// COLONNE 12
				array(
						"field" => 'ca_objects.dimensions',
						"prefixe" => "Dimensions : <b>",
						"suffixe" => "</b><br/>"
				)	
			),
			'etat'=>array(
				// COLONNE 13
				array(
						"field" => 'ca_objects.constatEtat.constat_etat',
						"prefixe" => "Etat au moment de l'acquisition : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur'=>array(
				// COLONNE 14
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "115", // auteur
						"prefixe" => "Auteur : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "215", // auteur
						"prefixe" => "Exécutant : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "96", // auteur
						"prefixe" => "Collecteur : <b>",
						"suffixe" => "</b><br/>"
				),				
				array(
						"field" => 'ca_objects.ecole',
						"prefixe" => "Ecole : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur_display'=>array(
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "115" // auteur
				)
			),
			'epoque'=>array(
				// COLONNE 15
				array(
						"field" => 'ca_objects.datePeriod.datePeriod_datation',
						"prefixe" => "Période : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.epoque',
						"prefixe" => "Epoque / style / mouvement : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.dateMillesime',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Millésime : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.useDate',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date d'utilisation ou de découverte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'usage'=>array( 
				// COLONNE 16
				array(
						"field" => 'ca_objects.fonctions',
						"prefixe" => "Fonction d'usage : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.precisionFonction',
						"prefixe" => "Précisions : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'provenance'=>array( 
				// COLONNE 17
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "122", // auteur
						"prefixe" => "Lieux de création ou d'éxécution : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.geoHistorique',
						"prefixe" => "Géographie historique : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "133", // auteur
						"prefixe" => "Lieux d'utilisation ou de destination : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "132", // auteur
						"prefixe" => "Lieu de découverte, collecte, récolte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'observations'=>array(
				// COLONNE 18
				array(
						"field" => 'ca_objects.date_presence',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Première date attestée dans le musée si origine inconnue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.anciennes_appartenances',
						"prefixe" => "Utilisateur illustre, premier et dernier propriétaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.otherNumber',
						"prefixe" => "Ancien ou autre numéro d'inventaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.mention_radiation',
						"prefixe" => "Mentions apportées en cas de radiation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_vol',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date de vol ou de disparition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_bien_retrouve',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date à laquelle le bien a été retrouvé : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inv_ensemble_complexe_c',
						"prefixe" => "Sous-inventaire dans le cas d'un ensemble complexe : <b>",
						"suffixe" => "</b><br/>"
				)
			),
		), 
		// Correspondance des champs pour l'import dans les BIENS ACQUIS 
		'inventaire' => array(
			"numinv" => array(
				array(
					"field" => 'ca_objects.idno',
					"prefixe" => "Numéro de dépôt : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			"numinv_sort" => array(
				array(
					"field" => 'ca_objects.idno_sort'
				)
			),
			"numinv_display" => array(
				array(
					"field" => 'ca_objects.idno'
				)
			),
			"mode_acquisition" => array(
				array(
					"field" => 'ca_objects.AcquisitionMode',
					"prefixe" => "Mode d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'donateur'=>array(
				array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "116", // donateur
					"prefixe" => "Donateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "117", // testateur
					"prefixe" => "Testateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypeId" => "118", // vendeur
					"prefixe" => "Vendeur : <b>",
					"suffixe" => "</b><br/>"
				)
			), 
			'date_acquisition'=>array( //date_acteAcquisition 
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.date_acteAcquisition',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "Date de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.ref_acteAcquisition',
					"prefixe" => "Référence de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'date_affectation',
					"prefixe" => "Date d'affectation au musée : <b>",
					"post-treatment" => 'caDateToUnixTimestamp',
					"suffixe" => "</b><br/>"
				)
			),
			'avis'=>array( 
				array(
					"field" => 'ca_objects.avisScientifiques.instance',
					"prefixe" => "Instance : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.avis_sens',
					"prefixe" => "Sens de l'avis : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.date_avis',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "Date de l'avis : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.commentaire_avis',
					"prefixe" => "Commentaire sur l'avis : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'prix'=>array( 
				array(
					"field" => 'ca_objects.prix',
					"post-treatment" => 'convertcurrencytoeuros',
					"prefixe" => "Prix : <b>",
					"suffixe" => "</b><br/>"
				), //OK
				array(
					"field" => 'ca_objects.mentionConcours',
					"prefixe" => "Mention des concours publics : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'date_inscription'=>array( 
				// COLONNE 7
				array("field" => 'ca_objects.date_inventaire',
				"post-treatment" => 'caDateToUnixTimestamp')),
			'date_inscription_display'=>array( 
				array("field" => 'ca_objects.date_inventaire',
				"post-treatment" => 'caDateToUnixTimestamp')
				),
			'designation'=>array(
				// COLONNE 8
				array(
						"field" => 'ca_objects.domaine',
						"prefixe" => "Domaine (catégorie du bien) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels',
						"prefixe" => "Titre : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_decor',
						"prefixe" => "Représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_precisions',
						"prefixe" => "Précisions sur la représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "25589", // donateur
						"prefixe" => "Appellation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "25663", // donateur
						"prefixe" => "Dénomination : <b>",
						"suffixe" => "</b><br/>"
				)
			),				
			'designation_display'=>array( 
				//array("field" => 'ca_objects.preferred_labels')),//OK
				array(
						"prefixe" => "<small>",
						"field" => 'ca_objects.domaine',
						"suffixe" => "</small><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels'
				)
			),				
			'inscription'=>array( //A TESTER
				array(
						"field" => 'ca_objects.inscription_type',
						"prefixe" => "Type d'inscriptions ou de marque : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_txt_c.inscription_txt',
						"prefixe" => "Transcription de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_langue_c.inscription_langue',
						"prefixe" => "Langue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_alphabet_c.inscription_alphabet',
						"prefixe" => "Alphabet : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_info_c.inscription_emplacement_c.inscription_emplacement',
						"prefixe" => "Emplacement de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_info_c',
						"prefixe" => "Précisions : <b>",
						"suffixe" => "</b><br/>"
				)				
			),
			'materiaux'=>array( 
				// COLONNE 10
				array(
						"field" => 'ca_objects.materiaux_tech_c.materiaux',
						"prefixe" => "Matériaux : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'techniques'=>array( 
				// COLONNE 11
				array(
						"field" => 'ca_objects.materiaux_tech_c.techniques',
						"prefixe" => "Techniques : <b>",
						"suffixe" => "</b><br/>"
				)

			),
			'mesures'=>array(
				// COLONNE 12
				array(
						"field" => 'ca_objects.dimensions',
						"prefixe" => "Dimensions : <b>",
						"suffixe" => "</b><br/>"
				)	
			),
			'etat'=>array(
				// COLONNE 13
				array(
						"field" => 'ca_objects.constatEtat.constat_etat',
						"prefixe" => "Etat au moment de l'acquisition : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur'=>array(
				// COLONNE 14
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "115", // auteur
						"prefixe" => "Auteur : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "215", // auteur
						"prefixe" => "Exécutant : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "96", // auteur
						"prefixe" => "Collecteur : <b>",
						"suffixe" => "</b><br/>"
				),				
				array(
						"field" => 'ca_objects.ecole',
						"prefixe" => "Ecole : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur_display'=>array(
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypeId" => "115" // auteur
				)
			),
			'epoque'=>array(
				// COLONNE 15
				array(
						"field" => 'ca_objects.datePeriod.datePeriod_datation',
						"prefixe" => "Période : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.epoque',
						"prefixe" => "Epoque / style / mouvement : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.dateMillesime',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Millésime : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.useDate',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date d'utilisation ou de découverte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'usage'=>array( 
				// COLONNE 16
				array(
						"field" => 'ca_objects.fonctions',
						"prefixe" => "Fonction d'usage : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.precisionFonction',
						"prefixe" => "Précisions : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'provenance'=>array( 
				// COLONNE 17
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "122", // auteur
						"prefixe" => "Lieux de création ou d'éxécution : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.geoHistorique',
						"prefixe" => "Géographie historique : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "133", // auteur
						"prefixe" => "Lieux d'utilisation ou de destination : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypeId" => "132", // auteur
						"prefixe" => "Lieu de découverte, collecte, récolte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'observations'=>array(
				// COLONNE 18
				array(
						"field" => 'ca_objects.date_presence',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Première date attestée dans le musée si origine inconnue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.anciennes_appartenances',
						"prefixe" => "Utilisateur illustre, premier et dernier propriétaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.otherNumber',
						"prefixe" => "Ancien ou autre numéro d'inventaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.mention_radiation',
						"prefixe" => "Mentions apportées en cas de radiation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_vol',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date de vol ou de disparition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_bien_retrouve',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "Date à laquelle le bien a été retrouvé : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inv_ensemble_complexe_c',
						"prefixe" => "Sous-inventaire dans le cas d'un ensemble complexe : <b>",
						"suffixe" => "</b><br/>"
				)
			),
		)
	)
);