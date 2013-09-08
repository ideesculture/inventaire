<?php
// module/Depot/src/Depot/Controller/DepotController.php:
namespace Depot\Controller;

use Zend\View\View;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// définition du modèle et des formulaires et des validateurs pour les formulaires
use Depot\Model\Depot;
use Depot\Form\DepotForm; 

// définition du modèle pour le traitement des médias attachés
use Depot\Model\Photo;

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;
use Zend\View\Variables;

class DepotController extends AbstractActionController
{
	protected $depotTable;
	protected $photoTable;
	
	private function listingPHPExcel()
	{
		$year = (int) $this->params()->fromRoute('annee');
		
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
		$config = $this->getServiceLocator()->get('Config');
		$request = $this->getRequest();
		if ($request->isPost()) {
			$brouillon= (bool) $request->getPost('brouillon');
		} else {
			$brouillon = (bool) $this->params()->fromRoute('brouillon', 1);	
		}
		
		
		$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator(
			$this->getDepotTable()->fetchAllFullInfosPaginator(
				array("year"=>$year,"validated"=>!$brouillon)
			)
		);
		$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				//'depots' => $this->getDepotTable()->fetchAllFullInfos(), //$paginator,
				'depots' => $paginator, //$paginator,
				'yearsOptions' => $this->getDepotTable()->getDepotYearsAsOptions(),
				'brouillon' => $brouillon,
				'fields' => $this->getDepotTable()->getFieldsName(),
				'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
				'page'=>$page,
				'config_ca_direct'=>$config["ca_direct"]
		));
		return $view;
	}

	public function listingAction()
	{
		$year = (int) $this->params()->fromRoute('annee');
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				// TODO : corriger, reprendre depuis la route ou un post
				'depots' => $this->getDepotTable()->fetchAll($year),
				'page' => $this->params()->fromRoute('page'),
		));
		//$view->setTerminal(true);
		return $view;
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

	public function listingExportExcel5Action()
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
                $this->getDepotTable()->saveDepot($form->getData());

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
    	$config = $this->getServiceLocator()->get('Config');
    	$config_ca_direct = $config["ca_direct"];
    	return new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
    			'depot' => $this->getDepotTable()->getDepot($id),
    			'photo'  => $this->getPhotoTable()->getPhotoByDepotId($id),
    			'fields' => $this->getDepotTable()->getFieldsName(),
    			'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
    			'mandatoryfieldsname' => $this->getDepotTable()->getMandatoryFieldsName(),
    			'config_ca_direct' => $config_ca_direct
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
                $this->getDepotTable()->deleteDepot($id);
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
                
                $config = $this->getServiceLocator()->get('Config');
                $config_ca = $config["ca_direct"];
                                
				$depot = $this->getDepotTable()->getDepot($id);
                $this->getDepotTable()->validateDepot($depot, $config_ca, array("updateCaDate"=>true));
            }

            // Redirect to list of depots
            return $this->redirect()->toRoute('depot');
        }

        return array(
            'id'    => $id,
            'depot' => $this->getDepotTable()->getDepot($id)
        );
    }

    public function unvalidateAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('depot');
    	}
    
		$depot = $this->getDepotTable()->getDepot($id);
		 $config = $this->getServiceLocator()->get('Config');
    	$this->getDepotTable()->unvalidateDepot($depot, array("updateCaDate" => true,"path"=> $config["ca_direct"]["path"]));
    	
    	// Redirect to list of depots
		return $this->redirect()->toRoute('depot');
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

	public function exportAction()
	{
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('depot', array(
                'action' => 'index'
            ));
        }
        
        // Traitement de la requête
        $request = $this->getRequest();
        if (!$request->isPost()) {
        	return array(
        			'id' => $id, 
        			'depot'=>$this->getDepotTable()->getDepot($id)
        			);
        }
        $insert = $request->getPost('insert', 'No');
        
		if ($insert == 'Yes') {
        	$id = (int) $request->getPost('id');
        	$config = $this->getServiceLocator()->get('Config');
        	$config_export = array_merge($config["ca_direct"],$config["ca_export_mapping"]);
        	$this->getDepotTable()->caDirectExport($id,$config_export);
        	return array(
        			'id'    => $id,
        			'depot'=>$this->getDepotTable()->getDepot($id),
        			'inserted' => true
        			);
		}
        
        // Redirect to list of depots
        $this->redirect()->toRoute('depot', array(
                'action' => 'index'
            ));
	}


	/**
	 * afficherObjetAction : La méthode afficherObjetAction réalise un import ou une mise à jour et redirige vers l'affichage de l'objet
	 *  
	 * @return multitype:|\Zend\View\Model\ViewModel
	 */
	public function afficherObjetAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$ca_id = $request->getPost('ca_id', '0');
		} else {
			$ca_id = (int) $this->params()->fromRoute('id', 0);
		}
		
		if (!$ca_id) {
			return array();
		}

		$config = $this->getServiceLocator()->get('Config');
		$config_import = array_merge($config["ca_direct"],$config["ca_import_mapping"]);
			
		if ($this->getDepotTable()->checkDepotByCaId($ca_id)) {
			// si l'objet est déjà dans la base depot
			$id = $this->getDepotTable()->checkDepotByCaId($ca_id)->id;
			$depot = $this->getDepotTable()->getDepot($id);
			
			if(!$this->getDepotTable()->checkCaAllowedType($ca_id,$config["ca_direct"])) {
				// si le type de l'objet ne fait pas partie des types d'objets autorisés
				throw new \Exception("Type non autorisé");
			}

			if ($depot->validated) {
				// si validé on ne touche à rien
				//var_dump($depot);die();
				return $this->redirect()->toRoute('depot', array('action' => "view", 'id'=> $id ) );
			} else {
				// sinon pas validé, on met à jour
				$result_import=$this->getDepotTable()->caDirectImportObject($ca_id, $config_import, $id);
			}
		} else {
			//sinon pas présent, on importe
			$result_import=$this->getDepotTable()->caDirectImportObject($ca_id, $config_import);
			
			if(isset($result_import["id"])) {
				$id = $result_import["id"];
				$depot = $this->getDepotTable()->getDepot($id);
				$result_photo_import=$this->getPhotoTable()->caDirectImportPhoto($ca_id, $result_import["id"], $config_import);
			} else {
				$result_photo_import="not imported";
				$return = new ViewModel();
				$return->setVariable('ca_id', $ca_id);
				$return->setVariable('results', array($ca_id => $result_import));
				$return->setTemplate("depot/depot/import-objet.phtml");
				return $return;
			}
		}
		
    	$config_ca_direct = $config["ca_direct"];
    	
    	$return = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
    			'depot' => $depot,
    			'photo'  => $this->getPhotoTable()->getPhotoByDepotId($id),
    			'fields' => $this->getDepotTable()->getFieldsName(),
    			'fieldsname' => $this->getDepotTable()->getFieldsHumanName(),
    			'mandatoryfieldsname' => $this->getDepotTable()->getMandatoryFieldsName(),
    			'config_ca_direct' => $config_ca_direct
    	));
    	$return->setTemplate("depot/depot/view.phtml");
    	
    	return $return;
    	 
	}
	
	/**
	 * updateSetAction : importe ou met à jour le contenu d'un set de CA dans la base depot
	 *
	 * @return multitype:|\Zend\View\Model\ViewModel
	 */
	public function updateSetAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$ca_set_id = $request->getPost('set_id', '0');
			$confirm = $request->getPost('confirm', 'No');
		} else {
			$ca_set_id = (int) $this->params()->fromRoute('id', 0);
		}
	
		if (!$ca_set_id) {
			return array();
		}
		
		if ($confirm == 'No') {
			$return = new ViewModel();
			$return->setVariable('set_id', $ca_set_id);
			$return->setTemplate("depot/depot/update-set-confirm.phtml");
			return $return;
		}
		
		$result_photos_imports = array();
	
		$config = $this->getServiceLocator()->get('Config');
		$config_import = array_merge($config["ca_direct"],$config["ca_import_mapping"]);
	
		$ca_ids=$this->getDepotTable()->caGetSetItems($ca_set_id, $config_import);
		foreach($ca_ids as $ca_id) {
			
			if ($this->getDepotTable()->checkDepotByCaId($ca_id)) {
				// si l'objet est déjà dans la base depot
				$id = $this->getDepotTable()->checkDepotByCaId($ca_id)->id;
				$depot = $this->getDepotTable()->getDepot($id);

				$result_imports[$ca_id]["id"]=$depot->id;
				$result_imports[$ca_id]["numinv_display"]=$depot->numinv_display;
				$result_imports[$ca_id]["designation_display"]=$depot->designation_display;
				
				if(!$this->getDepotTable()->checkCaAllowedType($ca_id,$config["ca_direct"])) {
					// si le type de l'objet ne fait pas partie des types d'objets autorisés
					$result_imports[$ca_id]["error"]="l'objet n'est pas un bien déposé, objet ignoré";
				} elseif ($depot->validated) {
					// si validé on ne touche à rien
					$result_imports[$ca_id]["error"]="objet inscrit à l'depot, modification impossible";
				} else {
					// sinon pas validé, on met à jour
					$result_imports[$ca_id]=$this->getDepotTable()->caDirectImportObject($ca_id, $config_import, $id);
					$result_photo_imports[$ca_id]["error"]="ignoré, objet déjà présent dans l'depot";
				}
			} else {
				//sinon pas présent, on importe
				$result_imports[$ca_id]=$this->getDepotTable()->caDirectImportObject($ca_id, $config_import);
				
				if(isset($result_imports[$ca_id]["id"])) {
					$id = $result_imports[$ca_id]["id"];
					$result_photo_imports[$ca_id]=$this->getPhotoTable()->caDirectImportPhoto($ca_id, $result_imports[$ca_id]["id"], $config_import);
				} else {
					$result_photo_imports[$ca_id]["error"]="problème d'import";
				}
			}
		}
		$return = new ViewModel();
		$return->setVariable('ca_set_id', $ca_set_id);
		$return->setVariable('result_imports', $result_imports);
		$return->setVariable('result_photos_imports', $result_photos_imports);
	
		return $return;
	}
	
	
}
?>