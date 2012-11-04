<?php
// module/Inventaire/src/Inventaire/Controller/InventaireController.php:
namespace Inventaire\Controller;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendRest\Client\RestClient;
// définition du modèle et des formulaires et des validateurs pour les formulaires
use Inventaire\Model\Inventaire;
use Inventaire\Model\Search;
use Inventaire\Form\SearchForm;

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;

class SearchController extends AbstractActionController
{
	protected $inventaireTable;
	protected $photoTable;
	
	private function isLogged() {
		if ($this->zfcUserAuthentication()->hasIdentity()) return true; else return false;		
	}

	public function indexAction()
	{
		$page = (int) $this->params()->fromRoute('page', 1);
		$year = (int) $this->params()->fromRoute('annee', "");

		$form = new SearchForm();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$search = new Search();
			$form->setInputFilter($search->getInputFilter());
			$form->setData($request->getPost());
			
				
			if ($form->isValid()) {
				// si le formulaire est valide
				$inventaireSearchArray = array_merge($form->getData());
			} else {
				// Insertion de l'année dans le tableau des critères de recherche
			}
		}
		$inventaireSearchArray = array();
		//$inventaireSearchArray = array_merge($inventaireSearchArray, array("year" => $year));
		
    	// TRAITEMENT DES RESULTATS
    	//$inventaireSearchArray = array("fulltext" => "texte plein", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"), description => "poil à gratter");
    	//$inventaireSearchArray = array("designation" => "Wrecking Bal");
    	$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getInventaireTable()->fetchSearchResult($inventaireSearchArray));
    	$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
    	$paginator->setCurrentPageNumber($page);
    	$paginator->setItemCountPerPage(10);
    	
    	return new ViewModel(array(
    			'auth' => array(
    					'logged' => $this::isLogged(),
    					'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
    			),
    			//'inventaires' => $this->getInventaireTable()->fetchAllFullInfos(), //$paginator,
    			'inventaires' => $paginator, //$paginator,
    			'yearsOptions' => $this->getInventaireTable()->getInventaireYearsAsOptions(),
    			'fields' => $this->getInventaireTable()->getFieldsName(),
    			'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
    			'page'=>$page,
    			'form'=>$form,
    			'year'=>$year
    	));
	}
	
    public function searchFormAction()
    {
    	// Si un identifiant est passé, on a pas besoin de chercher
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if ($id) {
    		return $this->redirect()->toRoute('inventaire', array("action" => "view", "id" => $id));
    	}

    	$search = new Search();
    	$form  = new SearchForm();
    	 
        $request = $this->getRequest();
        if ($request->isPost()) {
        	//print "ici <br />\n";
            $form->setInputFilter($search->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
				print "valide";die();
            }
        }
        return array(
            'form' => $form,
        );
    	    	 
    }
        
    public function searchAction()
    {    
    	$page = (int) $this->params()->fromRoute('page', 1);

    	$search = new Search();
    	$form  = new SearchForm();
    	 
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($search->getInputFilter());
    		$form->setData($request->getPost());
    		$postcontent = $request->getPost();
    		var_dump($postcontent);die();
    	}
    	
    	// TRAITEMENT DES RESULTATS
    	//$inventaireSearchArray = array("fulltext" => "texte plein", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"), description => "poil à gratter");
    	$inventaireSearchArray = array("designation" => "Wrecking Bal", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"));
    	$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getInventaireTable()->fetchSearchResult($inventaireSearchArray));
    	$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
    	$paginator->setCurrentPageNumber($page);
    	$paginator->setItemCountPerPage(10);
    	
    	return new ViewModel(array(
    			'auth' => array(
    					'logged' => $this::isLogged(),
    					'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
    			),
    			//'inventaires' => $this->getInventaireTable()->fetchAllFullInfos(), //$paginator,
    			'inventaires' => $paginator, //$paginator,
    			'fields' => $this->getInventaireTable()->getFieldsName(),
    			'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
    			'page'=>$page
    	));
    }
    
    public function getInventaireTable()
	{
		if (!$this->inventaireTable) {
			$sm = $this->getServiceLocator();
			$this->inventaireTable = $sm->get('Inventaire\Model\InventaireTable');
		}
		return $this->inventaireTable;
	}	

	public function getPhotoTable()
	{
		if (!$this->photoTable) {
			$sm = $this->getServiceLocator();
			$this->photoTable = $sm->get('Inventaire\Model\PhotoTable');
		}
		return $this->photoTable;
	}
	
}
?>