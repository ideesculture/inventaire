<?php
// module/Inventaire/src/Inventaire/Form/InventaireForm.php:
namespace Inventaire\Form;

use Zend\Form\Form;

class InventaireForm extends Form
{
	public function __construct($name = null)
	{
		// we want to ignore the name passed
		parent::__construct('inventaire');
		$this->setAttribute('method', 'post');
		$this->add(array(
				'name' => 'id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));
		$this->add(array(
				'name' => 'numinv',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => "Numéro d'inventaire",
				),
		)); //1
		$this->add(array(
				'name' => 'mode_acquisition',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => "Mode d'acquisition",
				),
		)); //2
		$this->add(array(
				'name' => 'donateur',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Donateur',
				),
		)); //3
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'date_acquisition',
            'options' => array(
                'label' => "Date d'acquisition"
            )
        ));//4
		/*$this->add(array(
				'name' => 'date_acquisition',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => "Date d'acquisition",
				),
		)); //4*/
		$this->add(array(
				'name' => 'avis',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Avis',
				),
		)); //5
		$this->add(array(
				'name' => 'prix',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => 'Prix',
				),
		)); //6
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'date_inscription',
            'options' => array(
                'label' => "Date d'inscription"
            )
        ));//7
		$this->add(array(
				'name' => 'designation',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Désignation',
				),
		)); //8
		$this->add(array(
				'name' => 'inscription',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Inscription',
				),
		)); //9
		$this->add(array(
				'name' => 'materiaux',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Matériaux',
				),
		)); //10
		$this->add(array(
				'name' => 'techniques',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Techniques',
				),
		)); //11
		$this->add(array(
				'name' => 'mesures',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => 'Mesures',
				),
		)); //12
		$this->add(array(
				'name' => 'etat',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Etat',
				),
		)); //13	
		$this->add(array(
				'name' => 'auteur',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Auteur',
				),
		)); //14
		$this->add(array(
				'name' => 'epoque',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Epoque',
				),
		)); //15
		$this->add(array(
				'name' => 'usage',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Usage',
				),
		)); //16
		$this->add(array(
				'name' => 'provenance',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Provenance',
				),
		)); //17
		$this->add(array(
				'name' => 'observations',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => 'Observations',
				),
		)); //18
		
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Valider',
						'id' => 'submitbutton',
						'class' => 'btn btn-success btn-large'
				),
		));
	}
}