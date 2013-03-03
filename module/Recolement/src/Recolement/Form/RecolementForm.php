<?php
namespace Recolement\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class RecolementForm extends Form
{
    public function __construct() {
        parent::__construct();
 
        $this->setName('recolement');
        $this->setAttribute('method', 'post');

		//Rubrique 1
        $this->add(array(
            'name'          => 'fsOne',
            'type'          => 'Zend\Form\Fieldset',
            'options'       => array(
                'legend'        => 'Field 1',
            ),
            'elements'      => array()
        	));

        
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