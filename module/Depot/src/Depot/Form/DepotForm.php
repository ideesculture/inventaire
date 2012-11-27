<?php
// module/Depot/src/Depot/Form/DepotForm.php:
namespace Depot\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class DepotForm extends Form
{
    public function __construct() {
        parent::__construct();
 
        $this->setName('depot');
        $this->setAttribute('method', 'post');
 
        //Caché
        $this->add(array(
                       'name'       => 'id',
                       'type'       => 'Zend\Form\Element\Hidden',
                       'attributes' => array(),
                   ));
 
        //Caché
        $this->add(array(
                       'name'       => 'idinv',
                       'type'       => 'Zend\Form\Element\Hidden',
                       'attributes' => array(),
                   ));
 
		//Rubrique 1
        $this->add(array(
            'name'          => 'fsOne',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
                'legend'        => 'Rubriques relatives au statut juridique des biens et aux conditions de son dépôt',
            ),
            'elements'      => array()
        	));

        
        $this->add(
        	array(
	        	'name'          => 'numinv',
				'type'          => 'Zend\Form\Element\Text',
	            'attributes'    => array(
	            'placeholder'        => 'numéro',
            ),
            'options'       => array(
	            'label'              => "Numéro d'inventaire du déposant",
	            'hint'               => '2008.8.5',
	            'description'        => "Le numéro, unique, attribué par le musée propriétaire, et marqué sur l'objet a valeur juridique.",
	        ))
         );
        
        $this->add(
        		array(
        				'name'          => 'numdep',
        				'type'          => 'Zend\Form\Element\Text',
        				'attributes'    => array(
        						'placeholder'        => 'numéro',
        				),
        				'options'       => array(
        						'label'              => "Numéro du dépôt",
        						'hint'               => 'D2012.3.8',
        						'description'        => "<p>Le rôle du numéro de dépôt est de distinguer, au sein du même musée, les biens reçus en dépôt des biens affectés aux collections permanentes. 
En aucun cas, le numéro de dépôt ne peut faire office de numéro d'inventaire. Seul le numéro d'inventaire attribué par l'institution propriétaire ayant consenti le dépôt a une valeur juridique.
L'arrêté du 25 mai 2004 indique que <i>\"lorsque le bien déposé est issu de la collection d'un musée de France, le numéro servant de référence à tous les actes de mouvement, restauration, prêt ou sortie temporaire du territoire national dudit bien est le numéro d'inventaire donné par le déposant\"</i>.</p>
<p>L'arrêté du 25 mai 2004 précise que le numéro de dépôt (donné par le dépositaire) est <i>\"attribué au bien déposé selon des règles identiques à celles utilisées pour l'enregistrement des collections permanentes.\"</i> Par conséquent, pour éviter toute confusion entre numéro d'inventaire et numéro de dépôt, ...\" le numéro de dépôt est précédé de la lettre \"D\".</p>
<p>Le numéro de dépôt devra donc être indiqué dans une rubrique spécifique.</p>",
        				))
        );

        $this->add(array(
			'name'          => 'date_ref_acte_depot',
            'type'          => 'Zend\Form\Element\Textarea',
            'attributes'    => array(
            	'placeholder'       => "date et références de l'acte unilatéral ou contractuel autorisant la mise en dépôt du bien.",
                ),
			'options'       => array(
            	'label'             => "Références de l'acte de dépôt",
                'hint'              => '1885 déposé ; Ref : arrêté du 25 novembre 1885',
                'description'       => "Date et références de l'acte unilatéral ou contractuel autorisant la mise en dépôt du bien.",
			)));

        $this->add(//Date
        		array(
        				'name'          => 'date_entree',
        				'type'          => 'Zend\Form\Element\Date',
        				'attributes'    => array(
        						'value'       => "",
        				),
        				'options'       => array(
        						'label'             => "Date d'entrée",
        						'hint'              => '',
        						'description'       => "Date de prise en charge du bien (date d'entrée matérielle).",
        				),
        		)
        );
                        
         $this->add(array(
			'name'          => 'proprietaire',
            'type'          => 'Zend\Form\Element\Textarea',
            'attributes'    => array(
            	'placeholder'       => "nom de la personne morale ou physique propriétaire du bien déposé",
                ),
            'options'       => array(
            	'label'             => "Propriétaire",
                'hint'              => "Ville de Bordeaux",
                'description'       => "Nom de la personne morale ou physique propriétaire du bien déposé.",
             )));

         $this->add(array(
         		'name'          => 'date_ref_acte_fin',
         		'type'          => 'Zend\Form\Element\Textarea',
         		'attributes'    => array(
         				'placeholder'       => "date et références de l'acte unilatéral ou contractuel décidant de mettre fin au dépôt",
         		),
         		'options'       => array(
         				'label'             => "Références de l'acte de fin de dépôt",
         				'hint'              => '',
         				'description'       => "Date et références de l'acte unilatéral ou contractuel décidant de mettre fin au dépôt.",
         		)));
         
          
         $this->add(//Date
        		array(
        				'name'          => 'date_inscription',
        				'type'          => 'Zend\Form\Element\Date',
        				'attributes'    => array(
        						'value'       => "",
        				),
        				'options'       => array(
        						'label'             => "Date d'inscription",
        						'hint'              => '',
        						'description'       => "Date d'inscription au registre des biens reçus en dépôt par le musée.",
        				),
        		)
        );
         
        //Fieldset Two
        $this->add(array(
            'name'          => 'fsTwo',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
               'legend'        => 'Rubriques portant description des biens',
            ),
            'elements'      => array())
        	);
        $this->add(//Textarea
        		array(
        				'name'          => 'designation',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "désignation du bien",
        				),
        				'options'       => array(
        						'label'             => "Désignation",
        						'hint'              => "Plat aux armes de François-Maurice Gontieri, archevêque d'Avignon",
        						'description'       => "Catégorie du bien (tableau, meuble, véhicule automobile, spécimen d’histoire naturelle, etc.),
suivie de son nom, sujet, titre ou décor.",
        				),
        		)
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'inscriptions',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "marques et inscriptions portées sur le bien",
        				),
        				'options'       => array(
        						'label'             => "Inscriptions",
        						'hint'              => "Inscription concernant la représentation - Sur la face A, deux inscriptions désignent, l'une la déesse (au-dessus de son bras gauche : Ahénaiai), l'autre le géant (dans son dos : Enkelados) ",
        						'description'       => "Ce champ est destiné à permettre l'interrogation des types d'inscriptions, marques, numéros, signatures en précisant si nécessaire l'alphabet ou la langue utilisés.
Mentionnez ici la présence d'annotations et d'inscriptions à l'exception des titres.
Pour mémoire : une annotation est faite de la main de l'artiste tandis qu'une inscription est d'une autre main.",
        				),
        		)
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'materiaux',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "matières ou matériaux",
        				),
        				'options'       => array(
        						'label'             => "Matériaux",
        						'hint'              => "bronze (fonte à la cire perdue)",
        						'description'       => "Ce champ sert à énumérer les différents matériaux constitutifs d'un objet et les techniques employées pour sa réalisation.",
        				),
        		)
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'techniques',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "techniques de réalisation, préparation, fabrication",
        				),
        				'options'       => array(
        						'label'             => "Techniques",
        						'hint'              => "Burin (gravure)",
        						'description'       => "Techniques de préparation (squelette, taxidermie, exemplaire séché, plastination, liquide
conservateur,...) lorsqu’il s’agit de collections d’histoire naturelle ; techniques de fabrication (artisanale,
manufacturée, industrielle, série, prototype,...) pour les collections scientifiques et techniques.",
        				),
        		)
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'mesures',
        				'type'          => 'Zend\Form\Element\Text',
        				'attributes'    => array(
        						'placeholder'       => "mesures (avec précision des unités de mesure)",
        				),
        				'options'       => array(
        						'label'             => "Mesures",
        						'hint'              => "H. 18.5, l. 6.8 (avec socle) ; H. 13.2, l. 4.5, P. 5.6 (sans socle)",
        						'description'       => "Les informations de ce champ permettent de préciser les mesures et dimensions des objets. Une mesure se compose du type de mesure, d'une valeur numérique et, éventuellement, de l'unité de mesure. Le type de mesure est fonction de l'objet : la hauteur, la largeur, l'épaisseur, la profondeur, le poids, l'échelle (cartes, maquettes), la pointure (chaussures, gants), la taille (vêtements), le titre (matériaux précieux), le tirant d'eau (bateaux), les chevaux-vapeur..."
        				),
        		)
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'etat',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "indications particulières sur l’état du bien au moment du dépôt, telle la mention d’un manque",
        				),
        				'options'       => array(
        						'label'             => "Etat",
        						'hint'              => "bon état ; restauré : 1998. IRRAP",
        						'description'       => "Ce champ sert à qualifier de façon sommaire l'état de conservation de l'objet en relevant les dégradations visibles de sa structure ou de son décor. Ces dernières peuvent être de plusieurs types : intégrité, déformation, traces d'humidité, traces d'infestation,  fort empoussièrement... Afin que cette information soit fiable, il convient de la dater (année / mois / jour). En effet, l'état de l'objet peut varier entre son entrée matérielle et le récolement des collections.",
        				),
        		)
        );
        //Fieldset Three
        $this->add(array(
        		'name'          => 'fsThree',
        		'type'          => 'Zend\Form\Fieldset',
        		'options'       => array(
        				'legend'        => 'Rubriques complémentaires',
        		),
        		'elements'      => array())
        );
        $this->add(//Textarea
        		array(
        				'name'          => 'auteur',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "auteur, collecteur, fabricant, commanditaire",
        				),
        				'options'       => array(
        						'label'             => "Auteur",
        						'hint'              => "HORDUBOIS Nicolas (peintre)",
        						'description'       => "Auteur ; collecteur, fabricant, commanditaire, propriétaire lorsqu’il s’agit de collections scientifiques et
techniques.",
        				),
        		)
        );
        
        $this->add(//Textarea
        		array(
        				'name'          => 'epoque',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "date, époque",
        				),
        				'options'       => array(
        						'label'             => "Epoque",
        						'hint'              => "2e millénaire av JC (fin) ; 1er millénaire av JC (début)",
        						'description'       => "Epoque, datation ou date de récolte (voire d'utilisation ou de découverte).",
        				),
        		)
        );
        
                $this->add(//Textarea
        		array(
        				'name'          => 'usage',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "fonction d’usage",
        				),
        				'options'       => array(
        						'label'             => "Usage",
        						'hint'              => "viticulture (utilisation primaire) ; décoration (utilisation détournée)",
        						'description'       => "Indiquez ici le (ou les termes) qui précisent le type de fonction associée à l'objet étudié (quand celle-ci est connue).",
        				),
        		)
        );
        
		$this->add(array(
        				'name'          => 'provenance',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "provenance géographique",
        				),
        				'options'       => array(
        						'label'             => "Provenance",
        						'hint'              => "Cameroun (lieu d'utilisation)",
        						'description'       => "Indiquez ici, en texte libre, les précisions et informations supplémentaires sur la provenance du bien.")
				));
        
		$this->add(array(
        				'name'          => 'observations',
        				'type'          => 'Zend\Form\Element\Textarea',
        				'attributes'    => array(
        						'placeholder'       => "observations",
        				),
        				'options'       => array(
        						'label'             => "Observations",
        						'hint'              => "Plat aux armes de François-Maurice Gontieri, archevêque d'Avignon",
        				),
        		)
        );
        
		//Submit button
        $this->add(array(
                       'name'       => 'submitBtn',
                       'type'       => 'Zend\Form\Element\Submit',
                       'attributes' => array(
                           'value'      => 'Enregistrer',
                       ),
                       'options'    => array(
                           'primary'    => true,
                       ),
                   ));
 
        //Reset button
        $this->add(array(
                       'name'       => 'resetBtn',
                       'attributes' => array(
                           'type'       => 'reset',
                           'value'      => 'Reset',
                       ),
                   ));
 
    }
}   