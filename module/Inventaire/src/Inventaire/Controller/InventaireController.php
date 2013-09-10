<?php
// module/Inventaire/src/Inventaire/Controller/InventaireController.php:
namespace Inventaire\Controller;

use Zend\View\View;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// définition du modèle et des formulaires et des validateurs pour les formulaires
use Inventaire\Model\Inventaire;
use Inventaire\Form\InventaireForm; 

// définition du modèle pour le traitement des médias attachés
use Inventaire\Model\Photo;

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;
use Zend\View\Variables;

class InventaireController extends AbstractActionController
{
	protected $inventaireTable;
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
    	$headers = $this->getInventaireTable()->getFieldsHumanName();
    	$header_valid = array("validation" => "Validation");
    	$headers = array_merge($headers,$header_valid);
    	
    	$colonne = "A";
    	$ligne=1;
    	foreach($headers as $key => $header) {
    		
    		$sheet->setCellValue($colonne.$ligne,$header);
    		$colonne++;
    	}
    	
    	$inventaires = $this->getInventaireTable()->fetchAll($year);
    	$ligne=2;
    	foreach($inventaires as $inventaire) {
    		$colonne = "A";
    		foreach($inventaire as $key => $field) {
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
		
		$filtre=array();		
		if ($request->isPost()) {
			if((bool) $request->getPost('brouillon')) $filtre["brouillon"]=true; else $filtre["brouillon"]=false;
			$filtre["year"] = (int) $request->getPost('year');
			$filtre["designation"] = $request->getPost('filtre_designation');
			$filtre["numinv"] = $request->getPost('filtre_numinv');
		} else {
			if ($year) $filtre["year"] = $year;
			$filtre["brouillon"]= true;
		}
		
		$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator(
			$this->getInventaireTable()->fetchAllFullInfosPaginator($filtre)
		);
		$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
		
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				'inventaires' => $paginator, //$paginator,
				'filtre' => $filtre,
				'yearsOptions' => $this->getInventaireTable()->getInventaireYearsAsOptions(),
				'fields' => $this->getInventaireTable()->getFieldsName(),
				'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
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
				'inventaires' => $this->getInventaireTable()->fetchAll($year),
				'page' => $this->params()->fromRoute('page'),
		));
		//$view->setTerminal(true);
		return $view;
	}
	
	public function listAction()
	{
		$adapter = new \Zend\Paginator\Adapter\ArrayAdapter($this->getInventaireTable()->getArrayCopy());
		$paginator = new \Zend\Paginator\Paginator($adapter);
		$paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
		
		return new ViewModel(array(
				'inventaires' => $paginator,
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
		$return->setVariable('yearsOptions', $this->getInventaireTable()->getInventaireYearsAsOptions());
		$return->setTemplate("inventaire/inventaire/listing-export-pdf-form.phtml");
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$year = (int) $request->getPost('year');
			
			if ($year != "-") {
				$pdf = new PdfModel();
				$pdf->setOption('filename', 'inventaire-contenu'); // Triggers PDF download, automatically appends ".pdf"
				$pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
				$pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"
				$pdf->setVariable('inventaires', $this->getInventaireTable()->fetchAllFullInfos($year));
				$pdf->setVariable('year', $year);
				$pdf->setVariable('fields', $this->getInventaireTable()->getFieldsName());
				$pdf->setVariable('fieldsname', $this->getInventaireTable()->getFieldsHumanName());
				$pdf->setVariable('imagepath', __DIR__."/../../../../../public/");
				return $pdf;
			} else {
            	return $this->redirect()->toRoute('inventaire', array( 'action' => 'listingExportPdf'));
			}
			
		}		
		return $return;
	}
	
	public function listingExportPdf2Action()
	{
		//var_dump($inventaires);die();
		$pdf = new ViewModel();
		$pdf->setTerminal(true);
		$pdf->setVariable('inventaires', $this->getInventaireTable()->fetchAllFullInfos());
		$pdf->setVariable('fields', $this->getInventaireTable()->getFieldsName());
		$pdf->setVariable('fieldsname', $this->getInventaireTable()->getFieldsHumanName());
		$pdf->setVariable('imagepath', "");
		return $pdf;
	}
	
	public function addActionBak()
    {
        $form = new InventaireForm();
        $form->get('submit')->setValue('Ajouter');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $inventaire = new Inventaire();
            $form->setInputFilter($inventaire->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $inventaire->exchangeArray($form->getData());
                $this->getInventaireTable()->saveInventaire($inventaire);

                // Redirect to list of inventaires
                return $this->redirect()->toRoute('inventaire');
            }
        }
        return array('form' => $form);
    }
	
	public function addAction()
    {
        $form = new InventaireForm();
        //$form->get('submit')->setValue('Ajouter');

        $request = $this->getRequest();
        if ($request->isPost()) {
        	$inventaire = new Inventaire();
            $form->setInputFilter($inventaire->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $inventaire->exchangeArray($form->getData());
                $this->getInventaireTable()->saveInventaire($inventaire);

                // Redirect to list of inventaires
                return $this->redirect()->toRoute('inventaire');
            }
        }
        return array('form' => $form, );
    }
	
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('inventaire', array(
                'action' => 'add'
            ));
        }
        $inventaire = $this->getInventaireTable()->getInventaire($id);
        
        // Reusing add.phmtl template
        $return = new ViewModel();
        
        $form  = new InventaireForm();
        $form->bind($inventaire);
        $form->get('submitBtn')->setAttribute('value', 'Modifier');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($inventaire->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getInventaireTable()->saveInventaire($form->getData());

                // Redirect to list of inventaires
                return $this->redirect()->toRoute('inventaire');
            }
        }
        $return->setVariable('form', $form);
        $return->setVariable('id', $id);
        $return->setTemplate("inventaire/inventaire/add.phtml");
        
        return $return;
    }
    
    public function viewAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('inventaire', array());
    	}
    	$config = $this->getServiceLocator()->get('Config');
    	$config_ca_direct = $config["ca_direct"];
    	return new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
    			'inventaire' => $this->getInventaireTable()->getInventaire($id),
    			'photo'  => $this->getPhotoTable()->getPhotoByInventaireId($id),
    			'fields' => $this->getInventaireTable()->getFieldsName(),
    			'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
    			'mandatoryfieldsname' => $this->getInventaireTable()->getMandatoryFieldsName(),
    			'config_ca_direct' => $config_ca_direct
    	));
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('inventaire');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getInventaireTable()->deleteInventaire($id);
            }

            // Redirect to list of inventaires
            return $this->redirect()->toRoute('inventaire');
        }

        return array(
            'id'    => $id,
            'inventaire' => $this->getInventaireTable()->getInventaire($id)
        );
    }
         
    public function validateAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('inventaire');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $validate = $request->getPost('validate', 'No');

            if ($validate == 'Yes') {
                $id = (int) $request->getPost('id');
                
                $config = $this->getServiceLocator()->get('Config');
                $config_ca = $config["ca_direct"];
                                
				$inventaire = $this->getInventaireTable()->getInventaire($id);
                $this->getInventaireTable()->validateInventaire($inventaire, $config_ca, array("updateCaDate"=>true));
            }

            // Redirect to list of inventaires
            return $this->redirect()->toRoute('inventaire');
        }

        return array(
            'id'    => $id,
            'inventaire' => $this->getInventaireTable()->getInventaire($id)
        );
    }

    public function unvalidateAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('inventaire');
    	}
    
        $request = $this->getRequest();
        if ($request->isPost()) {
            $unvalidation_code = $request->getPost('unvalidation_code');
            
            $inventaire = $this->getInventaireTable()->getInventaire($id);
			
            if ($unvalidation_code === md5($inventaire->numinv_display)) {
    			$inventaire = $this->getInventaireTable()->getInventaire($id);
		 		$config = $this->getServiceLocator()->get('Config');
    			$this->getInventaireTable()->unvalidateInventaire($inventaire, array("updateCaDate" => true,"path"=> $config["ca_direct"]["path"]));
            }
            
            // Redirect to list of inventaires
            return $this->redirect()->toRoute('inventaire');
        }
    	
        return array(
        		'id'    => $id,
        		'inventaire' => $this->getInventaireTable()->getInventaire($id)
        );
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

	public function exportAction()
	{
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('inventaire', array(
                'action' => 'index'
            ));
        }
        
        // Traitement de la requête
        $request = $this->getRequest();
        if (!$request->isPost()) {
        	return array(
        			'id' => $id, 
        			'inventaire'=>$this->getInventaireTable()->getInventaire($id)
        			);
        }
        $insert = $request->getPost('insert', 'No');
        
		if ($insert == 'Yes') {
        	$id = (int) $request->getPost('id');
        	$config = $this->getServiceLocator()->get('Config');
        	$config_export = array_merge($config["ca_direct"],$config["ca_export_mapping"]);
        	$this->getInventaireTable()->caDirectExport($id,$config_export);
        	return array(
        			'id'    => $id,
        			'inventaire'=>$this->getInventaireTable()->getInventaire($id),
        			'inserted' => true
        			);
		}
        
        // Redirect to list of inventaires
        $this->redirect()->toRoute('inventaire', array(
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
			
		if ($this->getInventaireTable()->checkInventaireByCaId($ca_id)) {
			// si l'objet est déjà dans la base inventaire
			$id = $this->getInventaireTable()->checkInventaireByCaId($ca_id)->id;
			$inventaire = $this->getInventaireTable()->getInventaire($id);

			if(!$this->getInventaireTable()->checkCaAllowedType($ca_id,$config["ca_direct"])) {
				// si le type de l'objet ne fait pas partie des types d'objets autorisés
				throw new \Exception("Type non autorisé");
			}
			
			if ($inventaire->validated) {
				// si validé on ne touche à rien
				//var_dump($inventaire);die();
				return $this->redirect()->toRoute('inventaire', array('action' => "view", 'id'=> $id ) );
			} else {
				// sinon pas validé, on met à jour
				$result_import=$this->getInventaireTable()->caDirectImportObject($ca_id, $config_import, $id);
			}
		} else {
			//sinon pas présent, on importe
			$result_import=$this->getInventaireTable()->caDirectImportObject($ca_id, $config_import);
			
			if(isset($result_import["id"])) {
				$id = $result_import["id"];
				$result_photo_import=$this->getPhotoTable()->caDirectImportPhoto($ca_id, $result_import["id"], $config_import);
			} else {
				$result_photo_import="not imported";
				$return = new ViewModel();
				$return->setVariable('ca_id', $ca_id);
				$return->setVariable('results', array($ca_id => $result_import));
				$return->setTemplate("inventaire/inventaire/import-objet.phtml");
				return $return;
			}
		}
		
    	$config_ca_direct = $config["ca_direct"];
    	
    	$return = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
    			'inventaire' => $this->getInventaireTable()->getInventaire($id),
    			'photo'  => $this->getPhotoTable()->getPhotoByInventaireId($id),
    			'fields' => $this->getInventaireTable()->getFieldsName(),
    			'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
    			'mandatoryfieldsname' => $this->getInventaireTable()->getMandatoryFieldsName(),
    			'config_ca_direct' => $config_ca_direct
    	));
    	$return->setTemplate("inventaire/inventaire/view.phtml");
    	
    	return $return;
    	 
	}
	
	/**
	 * updateSetAction : importe ou met à jour le contenu d'un set de CA dans la base inventaire
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
			$return->setTemplate("inventaire/inventaire/update-set-confirm.phtml");
			return $return;
		}
		
		$result_photos_imports = array();
	
		$config = $this->getServiceLocator()->get('Config');
		$config_import = array_merge($config["ca_direct"],$config["ca_import_mapping"]);
	
		$ca_ids=$this->getInventaireTable()->caGetSetItems($ca_set_id, $config_import);
		foreach($ca_ids as $ca_id) {
			
			if ($this->getInventaireTable()->checkInventaireByCaId($ca_id)) {
				// si l'objet est déjà dans la base inventaire
				$id = $this->getInventaireTable()->checkInventaireByCaId($ca_id)->id;
				$inventaire = $this->getInventaireTable()->getInventaire($id);

				$result_imports[$ca_id]["id"]=$inventaire->id;
				$result_imports[$ca_id]["numinv_display"]=$inventaire->numinv_display;
				$result_imports[$ca_id]["designation_display"]=$inventaire->designation_display;
				
				if(!$this->getInventaireTable()->checkCaAllowedType($ca_id,$config["ca_direct"])) {
					// si le type de l'objet ne fait pas partie des types d'objets autorisés
					$result_imports[$ca_id]["error"]="l'objet n'est pas un bien acquis, objet ignoré";
				} elseif ($inventaire->validated) {
					// si validé on ne touche à rien
					$result_imports[$ca_id]["error"]="objet inscrit à l'inventaire, modification impossible";
				} else {
					// sinon pas validé, on met à jour
					$result_imports[$ca_id]=$this->getInventaireTable()->caDirectImportObject($ca_id, $config_import, $id);
					$result_photo_imports[$ca_id]["error"]="ignoré, objet déjà présent dans l'inventaire";
				}
			} else {
				//sinon pas présent, on importe
				$result_imports[$ca_id]=$this->getInventaireTable()->caDirectImportObject($ca_id, $config_import);
				
				if(isset($result_imports[$ca_id]["id"])) {
					$id = $result_imports[$ca_id]["id"];
					$result_photo_imports[$ca_id]=$this->getPhotoTable()->caDirectImportPhoto($ca_id, $result_imports[$ca_id]["id"], $config_import);
				} else {
					$result_photo_imports[$ca_id]["error"]="not imported";
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