<?php
// module/Depots/src/Depots/Controller/DepotController.php:
namespace Depot\Controller;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// définition du modèle et des formulaires et des validateurs pour les formulaires
use Depot\Model\Depot;
use Depot\Form\DepotForm; 

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;

class DepotController extends AbstractActionController
{
	protected $depotTable;
	protected $photoTable;
	
	private function listingPHPExcel()
	{
		$year = (int) $this->params()->fromRoute('annee', 1);
		
		// Inclusion PHPExcel
		require_once __DIR__.'/../../../../../vendor/os/php-excel/PHPExcel/PHPExcel.php';
		require_once __DIR__.'/../../../../../vendor/os/php-excel/PHPExcel/Writer/CSV.php';
		//use PHPExcel;
		// Création de l'objet PHPExcel
		$workbook = new \PHPExcel;
		
    	$sheet = $workbook->getActiveSheet();
		
    	// Getting column headers from model, adding an additional column for validation
    	$headers = $this->getDepotTable()->getFieldsHumanName();
    	$header_valid = array("validation" => "Validation");
    	$headers = array_merge($headers,$header_valid);
    	
    	$colonne = "A";
    	$ligne=1;
    	foreach($headers as $key => $header) {
    		
    		$sheet->setCellValue($colonne.$ligne,$header);
    		$colonne++;
    	}
    	
    	$depots = $this->getDepotTable()->fetchAll($year);
    	$ligne=2;
    	foreach($depots as $depot) {
    		$colonne = "A";
    		foreach($depot as $key => $field) {
    			$sheet->setCellValue($colonne.$ligne,$field);
    			$colonne++;
    		}
    		$ligne++;
    	}
    	return $workbook;
	}
	
	private function isLogged() {
		if ($this->zfcUserAuthentication()->hasIdentity()) return true; else return false;		
	}

	public function indexAction()
	{
		$page = (int) $this->params()->fromRoute('page', 1);
		$year = (int) $this->params()->fromRoute('annee', "");

		$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getDepotTable()->fetchAllFullInfosPaginator($year));
		$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				'depots' => $paginator, //$paginator,
				'yearsOptions' => $this->getDepotTable()->getDepotYearsAsOptions(),
				'fields' => $this->getDepotTable()->getFieldsName(),
				'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
				'page'=>$page
		));
		
		return $view;
	}

	public function listingAction()
	{
		return new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				'depots' => $this->getDepotTable()->fetchAll(),
				'page' => $this->params()->fromRoute('page'),
		));
	}
	
	public function listAction()
	{
		$adapter = new \Zend\Paginator\Adapter\ArrayAdapter($this->getDepotTable()->getArrayCopy());
		$paginator = new \Zend\Paginator\Paginator($adapter);
		$paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
		
		return new ViewModel(array(
				'depots' => $paginator,
		));
	}
		
	public function listingExportCSVAction()
	{
		$workbook = $this::listingPHPExcel();
		
		// Désactivation du layout
		$result = new ViewModel();
    	$result->setTerminal(true);
		$result->setVariables(array('workbook' => $workbook));
		
		return $result;
	}
	
	public function listingExportExcel2007Action()
	{
		$workbook = $this::listingPHPExcel();
		
		// Désactivation du layout
		$result = new ViewModel();
    	$result->setTerminal(true);
		$result->setVariables(array('workbook' => $workbook));
		
		return $result;
	}
	
	public function listingExportPdfAction()
	{
		$return = new ViewModel();
		$return->setVariable('yearsOptions', $this->getDepotTable()->getDepotYearsAsOptions());
		$return->setTemplate("depot/depot/listing-export-pdf-form.phtml");
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$year = (int) $request->getPost('year');
			
			if ($year != "-") {
				$pdf = new PdfModel();
				$pdf->setOption('filename', 'depot-contenu'); // Triggers PDF download, automatically appends ".pdf"
				$pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
				$pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"
				$pdf->setVariable('depots', $this->getDepotTable()->fetchAllFullInfos($year));
				$pdf->setVariable('year', $year);
				$pdf->setVariable('fields', $this->getDepotTable()->getFieldsName());
				$pdf->setVariable('fieldsname', $this->getDepotTable()->getFieldsHumanName());
				$pdf->setVariable('imagepath', __DIR__."/../../../../../public/");
				return $pdf;
			} else {
            	return $this->redirect()->toRoute('depot', array( 'action' => 'listingExportPdf'));
			}
			
		}		
		return $return;
	}
	
	public function listingExportPdf2Action()
	{
		//var_dump($depots);die();
		$pdf = new ViewModel();
		$pdf->setTerminal(true);
		$pdf->setVariable('depots', $this->getDepotTable()->fetchAllFullInfos());
		$pdf->setVariable('fields', $this->getDepotTable()->getFieldsName());
		$pdf->setVariable('fieldsname', $this->getDepotTable()->getFieldsHumanName());
		$pdf->setVariable('imagepath', "");
		return $pdf;
	}
	
	public function addActionBak()
    {
        $form = new DepotForm();
        $form->get('submit')->setValue('Ajouter');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $depot = new Depots();
            $form->setInputFilter($depot->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $depot->exchangeArray($form->getData());
                $this->getDepotTable()->saveDepots($depot);

                // Redirect to list of depots
                return $this->redirect()->toRoute('depot');
            }
        }
        return array('form' => $form);
    }
	
	public function addAction()
    {
        $form = new DepotForm();
        //$form->get('submit')->setValue('Ajouter');

        $request = $this->getRequest();
        if ($request->isPost()) {
        	$depot = new Depot();
            $form->setInputFilter($depot->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $depot->exchangeArray($form->getData());
                $this->getDepotTable()->saveDepot($depot);

                // Redirect to list of depots
                return $this->redirect()->toRoute('depot');
            }
        }
        return array('form' => $form, );
    }
	
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('depot', array(
                'action' => 'add'
            ));
        }
        $depot = $this->getDepotTable()->getDepot($id);
        
        // Reusing add.phmtl template
        $return = new ViewModel();
        
        $form  = new DepotForm();
        $form->bind($depot);
        $form->get('submitBtn')->setAttribute('value', 'Modifier');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($depot->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDepotTable()->saveDepots($form->getData());

                // Redirect to list of depots
                return $this->redirect()->toRoute('depot');
            }
        }
        $return->setVariable('form', $form);
        $return->setVariable('id', $id);
        $return->setTemplate("depot/depot/add.phtml");
        
        return $return;
    }
    
    public function viewAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('depot', array());
    	}
    	return new ViewModel(array(
    			'depot' => $this->getDepotTable()->getDepot($id),
    			'fields' => $this->getDepotTable()->getFieldsName(),
    			'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
    			 
    			//	'photo'  => $this->getPhotoTable()->getPhotoByDepotId($id),
    	));
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('depot');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getDepotTable()->deleteDepots($id);
            }

            // Redirect to list of depots
            return $this->redirect()->toRoute('depot');
        }

        return array(
            'id'    => $id,
            'depot' => $this->getDepotTable()->getDepot($id)
        );
    }
         
    public function validateAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('depot');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = $request->getPost('validate', 'No');

            if ($validate == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getDepotTable()->validateDepots($id);
            }

            // Redirect to list of depots
            return $this->redirect()->toRoute('depot');
        }

        return array(
            'id'    => $id,
            'depot' => $this->getDepotTable()->getDepot($id)
        );
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