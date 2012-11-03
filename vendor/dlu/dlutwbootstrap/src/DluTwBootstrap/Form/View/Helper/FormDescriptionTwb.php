<?php
namespace DluTwBootstrap\Form\View\Helper;

use \Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper as AbstractFormViewHelper;

/**
 * FormDescriptionTwb
 * @package DluTwBootstrap
 * @copyright David Lukas (c) - http://www.zfdaily.com
 * @license http://www.zfdaily.com/code/license New BSD License
 * @link http://www.zfdaily.com
 * @link https://bitbucket.org/dlu/dlutwbootstrap
 */
class FormDescriptionTwb extends AbstractFormViewHelper
{
    /**
     * Which element types support the description?
     * @var array
     */
    protected $supportedTypes   = array(
        'text',
        'password',
        'textarea',
        'checkbox',
        'radio',
        'select',
        'file',
    );

    /* **************************** METHODS ****************************** */

    /**
     * Render a description from the provided $element
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element) {
        $type           = $element->getAttribute('type');
        if(!in_array($type, $this->supportedTypes)) {
            return '';
        }
        $escapeHelper   = $this->getEscapeHtmlHelper();
        $html           = '';
        //Description
        if($element->getOption('description')) {
        	$html   = $element->getOption('description');
        	if(!$this->hasHtml($html)) {
				$html = "<p class='help-box'>".$html."</p>";        		
        	}
        }
        return $html;
    }

    private function hasHtml($str){
    	//we compare the length of the string with html tags and without html tags
    	if(strlen($str) != strlen(strip_tags($str)))
    		return true;
    	return false;
    }

    /**
     * Invoke helper as function
     * Proxies to {@link render()}.
     * @param  ElementInterface $element
     * @return string
     */
    public function __invoke(ElementInterface $element) {
        return $this->render($element);
    }
}