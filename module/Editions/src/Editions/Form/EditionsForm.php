<?php
// module/Editions/src/Editions/Form/CoverForm.php:
namespace Editions\Form;

use Zend\Form\Form;

class CoverForm extends Form
{
	public function __construct($name = null)
	{
		// we want to ignore the name passed
		parent::__construct('editions');
		$this->setAttribute('method', 'post');
		$this->add(array(
				'name' => 'id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));
		$this->add(array(
				'name' => 'title',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => 'Title',
				),
		));
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Go',
						'id' => 'submitbutton',
				),
		));
	}
}