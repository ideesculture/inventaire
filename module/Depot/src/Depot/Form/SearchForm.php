<?php
// module/Depot/src/Depot/Form/DepotForm.php:
namespace Depot\Form;

use Zend\Form\Form;

class SearchForm extends Form
{
	public function __construct($name = null)
	{
		// we want to ignore the name passed
		parent::__construct('search');
		$this->setAttribute('method', 'post');
        $this->add(array(
				'name' => 'numinv',
				'attributes' => array(
						'type'  => 'text',
						'class'  => 'input-small',
						'placeholder' => 'Numéro',
				),
				'options' => array(
						'label' => "Numéro d'depot",
				),
		)); 
        $this->add(array(
				'name' => 'designation',
				'attributes' => array(
						'type'  => 'text',
						'class'  => 'input-xlarge',
						'placeholder' => 'Désignation',
				),
				'options' => array(
						'label' => 'Désignation',
				),
		)); 
		$this->add(array(
				'name' => 'auteur',
				'attributes' => array(
						'type'  => 'text',
						'class'  => 'input-xlarge',
						'placeholder' => 'Auteur',
				),
				'options' => array(
						'label' => 'Auteur',
				),
		)); 

		/*
		$this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'date_acquisition_min',
			'required' => false,
			'allow_empty' => true,
            'options' => array(
                'label' => "Date d'acquisition mini"
            )
        ));//4
        $this->add(array(
        		'type' => 'Zend\Form\Element\Date',
        		'name' => 'date_acquisition_max',
        		'options' => array(
        				'label' => "Date d'acquisition maxi"
        		)
        ));//4
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'date_inscription_min',
            'options' => array(
                'label' => "Date d'inscription mini"
            )
        ));//7
        $this->add(array(
            'type' => 'Zend\Form\Element\Date',
            'name' => 'date_inscription_max',
            'options' => array(
                'label' => "Date d'inscription maxi"
            )
        ));//7
        */
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Chercher',
						'id' => 'submitbutton',
						'class' => 'btn btn-success btn-small'
				),
		));
	}
}