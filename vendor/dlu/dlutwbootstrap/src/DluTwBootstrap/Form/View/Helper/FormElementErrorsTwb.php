<?php
namespace DluTwBootstrap\Form\View\Helper;

use \DluTwBootstrap\GenUtil;

use Zend\Form\View\Helper\FormElementErrors;
use \Zend\Form\ElementInterface;

/**
 * FormElementErrorsTwb
 * @package DluTwBootstrap
 * @copyright David Lukas (c) - http://www.zfdaily.com
 * @license http://www.zfdaily.com/code/license New BSD License
 * @link http://www.zfdaily.com
 * @link https://bitbucket.org/dlu/dlutwbootstrap
 */
class FormElementErrorsTwb extends FormElementErrors
{
    /**
     * General utils
     * @var GenUtil
     */
    protected $genUtil;

    /* **************************** METHODS ****************************** */

    /**
     * Constructor
     * @param \DluTwBootstrap\GenUtil $genUtil
     */
    public function __construct(GenUtil $genUtil) {
        $this->genUtil  = $genUtil;
    }

    /**
     * Render validation errors for the provided $element
     * @param  ElementInterface $element
     * @param array $attributes
     * @return string
     */
    public function render(ElementInterface $element, array $attributes = array()) {
    	//$attributes = $this->genUtil->addWordsToArrayItem('errors', $attributes, 'class');
    	$escapeHelper   = $this->getEscapeHtmlHelper();
    	//var_dump($element);die();
    	$messages= $element->getMessages();
    	if(sizeof($messages)) {
	    	$html  = '<span class="errors">';
	    	foreach($messages as $message) {
				    $html.= '<div class="alert"><span class="label label-warning">Erreur</span> '.$message.' <a href="#" class="close" data-dismiss="alert">×</a></div>';		
	    	}
	    	$html.="</span>";
	    	return $html;
    	} else {
    		return false;
    	}
    }

}