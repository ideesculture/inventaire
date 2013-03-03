<?php
namespace Recolement\Controller;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// définition du modèle et des formulaires et des validateurs pour les formulaires
use Recolement\Model\Recolement;
use Recolement\Form\RecolementForm; 

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;

class RecolementController extends AbstractActionController
{
	protected $recolementTable;
	
	private function isLogged() {
		if ($this->zfcUserAuthentication()->hasIdentity()) return true; else return false;		
	}

	public function indexAction()
	{
		$recolement_object = new Recolement();
		
		$config = $this->getServiceLocator()->get('Config');
		$config_ws = array_merge($config["ca"]);
		$recolements = $recolement_object->caWsListeCampagnes($config_ws);
		//var_dump($recolements);die();
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false),
						
						),
				'recolements' => $recolements
		));
		
		return $view;
	}

	public function pvAction()
	{
		$id = $this->params()->fromRoute('id', "");
		
		require_once __DIR__.'/../../../../../lib/phpword/phpword/src/PHPWord.php';
		
		$recolement_object = new Recolement();
		
		$config = $this->getServiceLocator()->get('Config');
		$campagne = $recolement_object->caWsInfoCampagne($config["ca"], "2");
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false),
						),
				'campagne' => $campagne
				));

		var_dump($campagne->related);die();
		$view->setTerminal(true);
		return $view;
		//return new ViewModel();
	}
}
?>