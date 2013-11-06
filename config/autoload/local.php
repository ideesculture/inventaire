<?php
/**
 * Fichier de configuration locale
 */

// config/autoload/local.php:
return array(

	/* LOCAL 
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
	),*/
    
    /* DISTANT 	*/
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
					"prefixe" => "numéro de dépôt : <b>",
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
					"field" => 'ca_objects.otherNumber.objectNo',
					"prefixe" => "numéro d'inventaire dans les collections du déposant : <b>",
					"suffixe" => "</b><br/>",
					"options" => array("template" => '^objectNo')
				)
			),
			"actedepot" => array(
				array(
					"field" => 'ca_objects.date_ref_acteDepot.date_acteDepot',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "date de l'acte de dépôt : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_acteDepot.ref_acteDepot',
					"prefixe" => "référence de l'acte de dépôt : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'date_priseencharge'=>array(  
				array(
					"field" => 'ca_objects.date_priseencharge_bien',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "date de prise en charge du bien : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'proprietaire'=>array(
				array(
					"field" => 'ca_entities.displayname',
					"relationshipTypes" => "origine_proprietaire", // propriétaire
					"prefixe" => "propriétaire : <b>",
					"suffixe" => "</b><br/>"
				)
			), 
			"actefindepot" => array(
				array(
					"field" => 'ca_objects.date_ref_actefinDepot.date_actefinDepot',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "date de l'acte de fin de dépôt : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_actefinDepot.ref_actefinDepot',
					"prefixe" => "référence de l'acte de fin de dépôt : <b>",
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
						"prefixe" => "domaine (catégorie du bien) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels',
						"prefixe" => "titre : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_decor',
						"prefixe" => "représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_precisions',
						"prefixe" => "précisions sur la représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "53", 
						"prefixe" => "appellation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "54", 
						"prefixe" => "dénomination : <b>",
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
						"field" => 'ca_objects.inscription_c.inscription_type',
						"prefixe" => "type d'inscriptions ou de marque : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_txt',
						"prefixe" => "transcription de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_langue',
						"prefixe" => "langue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_alphabet',
						"prefixe" => "alphabet : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_emplacement',
						"prefixe" => "emplacement de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_info_c',
						"prefixe" => "précisions : <b>",
						"suffixe" => "</b><br/>"
				)				
			),
			'materiaux'=>array( 
				// COLONNE 10
				array(
						"field" => 'ca_objects.materiaux_tech_c',
						"prefixe" => "matériaux/techniques : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'mesures'=>array(
				// COLONNE 12
				array(
						"field" => 'ca_objects.dimensions',
						"prefixe" => "dimensions : <b>",
						"suffixe" => "</b><br/>"
				)	
			),
			'etat'=>array(
				// COLONNE 13
				array(
						"field" => 'ca_objects.constatEtat.constat_etat',
						"prefixe" => "état au moment de l'acquisition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.constatEtat.constat_date',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date du constat d'état : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur'=>array(
				// COLONNE 14
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "creation_auteur", // auteur
						"prefixe" => "auteur : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "executant", 
						"prefixe" => "exécutant : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "origine_collector", 
						"prefixe" => "collecteur : <b>",
						"suffixe" => "</b><br/>"
				),				
				array(
						"field" => 'ca_objects.ecole',
						"prefixe" => "école : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur_display'=>array(
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "creation_auteur"
				)
			),
			'epoque'=>array(
				// COLONNE 15
				array(
						"field" => 'ca_objects.datePeriod.datePeriod_datation',
						"prefixe" => "période : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.epoque',
						"prefixe" => "époque / style / mouvement : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.dateMillesime',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "millésime : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.useDate',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date d'utilisation ou de découverte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'usage'=>array( 
				// COLONNE 16
				array(
						"field" => 'ca_objects.fonctions',
						"prefixe" => "fonction d'usage : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.precisionFonction',
						"prefixe" => "précisions : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'provenance'=>array( 
				// COLONNE 17
				array(
						"field" => 'ca_places.name',
						"relationshipTypes" => "created",
						"prefixe" => "lieux de création ou d'exécution : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.geoHistorique',
						"prefixe" => "géographie historique : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.name',
						"relationshipTypes" => "utilisation",
						"prefixe" => "lieux d'utilisation ou de destination : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.name',
						"relationshipTypes" => "decouverte",
						"prefixe" => "lieu de découverte, collecte, récolte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'observations'=>array(
				// COLONNE 18
				array(
						"field" => 'ca_objects.date_presence',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "première date attestée dans le musée si origine inconnue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.anciennes_appartenances',
						"prefixe" => "utilisateur illustre, premier et dernier propriétaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.mention_radiation',
						"prefixe" => "mentions apportées en cas de radiation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_vol',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date de vol ou de disparition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_bien_retrouve',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date à laquelle le bien a été retrouvé : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inv_ensemble_complexe_c',
						"prefixe" => "sous-inventaire dans le cas d'un ensemble complexe : <b>",
						"suffixe" => "</b><br/>"
				)
			),
		), 
		// Correspondance des champs pour l'import dans les BIENS ACQUIS 
		'inventaire' => array(
			"numinv" => array(
				array(
					"field" => 'ca_objects.idno',
					"prefixe" => "numéro d'inventaire : <b>",
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
					"prefixe" => "mode d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'donateur'=>array(
				array(
					"field" => 'ca_entities.displayname',
					"relationshipTypes" => "origine_donateur", // donateur
					"prefixe" => "donateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypes" => "origine_testateur", // testateur
					"prefixe" => "testateur : <b>",
					"suffixe" => "</b><br/>"
				), array(
					"field" => 'ca_entities.displayname',
					"relationshipTypes" => "origine_vendeur", // vendeur
					"prefixe" => "vendeur : <b>",
					"suffixe" => "</b><br/>"
				)
			), 
			'date_acquisition'=>array( //date_acteAcquisition 
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.date_acteAcquisition',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "date de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.date_ref_acteAcquisition.ref_acteAcquisition',
					"prefixe" => "référence de l'acte d'acquisition : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'date_affectation',
					"prefixe" => "date d'affectation au musée : <b>",
					"post-treatment" => 'caDateToUnixTimestamp',
					"suffixe" => "</b><br/>"
				)
			),
			'avis'=>array( 
				array(
					"field" => 'ca_objects.avisScientifiques.instance',
					"prefixe" => "instance : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.avis_sens',
					"prefixe" => "sens de l'avis : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.date_avis',
					"post-treatment" => 'caDateToUnixTimestamp',
					"prefixe" => "date de l'avis : <b>",
					"suffixe" => "</b><br/>"
				),
				array(
					"field" => 'ca_objects.avisScientifiques.commentaire_avis',
					"prefixe" => "commentaire sur l'avis : <b>",
					"suffixe" => "</b><br/>"
				)
			),
			'prix'=>array( 
				array(
					"field" => 'ca_objects.prix',
					"prefixe" => "prix : <b>",
					"suffixe" => " EUR</b><br/>"
				), //OK
				array(
					"field" => 'ca_objects.mentionConcours',
					"prefixe" => "mention des concours publics : <b>",
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
						"prefixe" => "domaine (catégorie du bien) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.preferred_labels',
						"prefixe" => "titre : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_decor',
						"prefixe" => "représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.element_decoratif_precisions',
						"prefixe" => "précisions sur la représentation (décor porté) : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "53", 
						"prefixe" => "appellation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.nonpreferred_labels',
						"otherLabelTypeId" => "54", 
						"prefixe" => "dénomination : <b>",
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
			'inscription'=>array(
				array(
						"field" => 'ca_objects.inscription_c.inscription_type',
						"prefixe" => "type d'inscriptions ou de marque : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_txt',
						"prefixe" => "transcription de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_langue',
						"prefixe" => "Langue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_alphabet',
						"prefixe" => "alphabet : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_c.inscription_emplacement',
						"prefixe" => "emplacement de l'inscription : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inscription_info_c',
						"prefixe" => "précisions : <b>",
						"suffixe" => "</b><br/>"
				)				
			),
			'materiaux'=>array( 
				// COLONNE 10
				array(
						"field" => 'ca_objects.materiaux_tech_c',
						"prefixe" => "matériaux/techniques : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'mesures'=>array(
				// COLONNE 12
				array(
						"field" => 'ca_objects.dimensions',
						"prefixe" => "dimensions : <b>",
						"suffixe" => "</b><br/>"
				)	
			),
			'etat'=>array(
				// COLONNE 13
				array(
						"field" => 'ca_objects.constatEtat.constat_etat',
						"prefixe" => "etat au moment de l'acquisition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.constatEtat.constat_date',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date du constat d'état : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur'=>array(
				// COLONNE 14
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "creation_auteur", // auteur
						"prefixe" => "auteur : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "executant", 
						"prefixe" => "exécutant : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "origine_collector",
						"prefixe" => "collecteur : <b>",
						"suffixe" => "</b><br/>"
				),				
				array(
						"field" => 'ca_objects.ecole',
						"prefixe" => "ecole : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'auteur_display'=>array(
				array(
						"field" => 'ca_entities.preferred_labels',
						"relationshipTypes" => "creation_auteur" // auteur
				)
			),
			'epoque'=>array(
				// COLONNE 15
				array(
						"field" => 'ca_objects.datePeriod.datePeriod_datation',
						"prefixe" => "période : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.epoque',
						"prefixe" => "époque / style / mouvement : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.dateMillesime',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "millésime : <b>",
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
						"prefixe" => "fonction d'usage : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.precisionFonction',
						"prefixe" => "précisions : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'provenance'=>array( 
				// COLONNE 17
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypes" => "created",
						"prefixe" => "lieux de création ou d'éxécution : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.geoHistorique',
						"prefixe" => "géographie historique : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypes" => "utilisation",
						"prefixe" => "lieux d'utilisation ou de destination : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_places.preferred_labels',
						"relationshipTypes" => "decouverte",
						"prefixe" => "lieu de découverte, collecte, récolte : <b>",
						"suffixe" => "</b><br/>"
				)
			),
			'observations'=>array(
				// COLONNE 18
				array(
						"field" => 'ca_objects.date_presence',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "première date attestée dans le musée si origine inconnue : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.anciennes_appartenances',
						"prefixe" => "utilisateur illustre, premier et dernier propriétaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.otherNumber',
						"prefixe" => "ancien ou autre numéro d'inventaire : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.mention_radiation',
						"prefixe" => "mentions apportées en cas de radiation : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_vol',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date de vol ou de disparition : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.date_bien_retrouve',
						"post-treatment" => 'caDateToUnixTimestamp',
						"prefixe" => "date à laquelle le bien a été retrouvé : <b>",
						"suffixe" => "</b><br/>"
				),
				array(
						"field" => 'ca_objects.inv_ensemble_complexe_c',
						"prefixe" => "sous-inventaire dans le cas d'un ensemble complexe : <b>",
						"suffixe" => "</b><br/>"
				)
			),
		)
	)
);