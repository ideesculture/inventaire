<?php
// module/Depot/src/Depot/Controller/DepotController.php:
namespace Depot\Controller;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendRest\Client\RestClient;
// définition du modèle et des formulaires et des validateurs pour les formulaires
use Depot\Model\Depot;
use Depot\Model\Search;
use Depot\Form\SearchForm;

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;

class SearchController extends AbstractActionController
{
	protected $depotTable;
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
				$depotSearchArray = array_merge($form->getData());
			} else {
				// Insertion de l'année dans le tableau des critères de recherche
			}
		}
		$depotSearchArray = array();
		//$depotSearchArray = array_merge($depotSearchArray, array("year" => $year));
		
    	// TRAITEMENT DES RESULTATS
    	//$depotSearchArray = array("fulltext" => "texte plein", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"), description => "poil à gratter");
    	//$depotSearchArray = array("designation" => "Wrecking Bal");
    	$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getDepotTable()->fetchSearchResult($depotSearchArray));
    	$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
    	$paginator->setCurrentPageNumber($page);
    	$paginator->setItemCountPerPage(10);
    	
    	return new ViewModel(array(
    			'auth' => array(
    					'logged' => $this::isLogged(),
    					'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
    			),
    			//'depots' => $this->getDepotTable()->fetchAllFullInfos(), //$paginator,
    			'depots' => $paginator, //$paginator,
    			'yearsOptions' => $this->getDepotTable()->getDepotYearsAsOptions(),
    			'fields' => $this->getDepotTable()->getFieldsName(),
    			'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
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
    		return $this->redirect()->toRoute('depot', array("action" => "view", "id" => $id));
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
    	//$depotSearchArray = array("fulltext" => "texte plein", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"), description => "poil à gratter");
    	$depotSearchArray = array("designation" => "Wrecking Bal", "date_inscription" => array("min" => "2010-01-01", "max" => "2012-12-31"), "date_acquisition" => array("min" => "1998-01-01"));
    	$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getDepotTable()->fetchSearchResult($depotSearchArray));
    	$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
    	$paginator->setCurrentPageNumber($page);
    	$paginator->setItemCountPerPage(10);
    	
    	return new ViewModel(array(
    			'auth' => array(
    					'logged' => $this::isLogged(),
    					'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
    			),
    			//'depots' => $this->getDepotTable()->fetchAllFullInfos(), //$paginator,
    			'depots' => $paginator, //$paginator,
    			'fields' => $this->getDepotTable()->getFieldsName(),
    			'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
    			'page'=>$page
    	));
    }
    
    public function getDepotTable()
	{
		if (!$this->depotTable) {
			$sm = $this->getServiceLocator();
			$this->depotTable = $sm->get('Depot\Model\DepotTable');
		}
		return $this->depotTable;
	}	

	public function getPhotoTable()
	{
		if (!$this->photoTable) {
			$sm = $this->getServiceLocator();
			$this->photoTable = $sm->get('Depot\Model\PhotoTable');
		}
		return $this->photoTable;
	}
	
}
?>