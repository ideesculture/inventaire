<?php 

// filename : module/Test/src/Test/Form/PhotoForm.php
namespace Inventaire\Form;

use Zend\Form\Form;

class PhotoForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('Photo');
		$this->setAttribute('method', 'post');
		$this->setAttribute('enctype','multipart/form-data');

		$this->add(array(
				'name' => 'id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));
		
		$this->add(array(
				'name' => 'depot_id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));
		
		$this->add(array(
				'name' => 'credits',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => 'Crédits photo',
				),
		));


		$this->add(array(
				'name' => 'file',
				'attributes' => array(
						'type'  => 'file',
				),
				'options' => array(
						'label' => 'Fichier',
				),
		));


		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Attacher la photo',
						'class' => 'btn btn-success btn-large'
				),
		));
	}
}
?>