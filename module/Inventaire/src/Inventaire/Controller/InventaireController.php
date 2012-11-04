<?php
// module/Inventaire/src/Inventaire/Controller/InventaireController.php:
namespace Inventaire\Controller;

// définition contrôleur, vue
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

// définition du modèle et des formulaires et des validateurs pour les formulaires
use Inventaire\Model\Inventaire;
use Inventaire\Form\InventaireForm; 

// définition de la classe pour la génération PDF
use DOMPDFModule\View\Model\PdfModel;

//require dirname(__FILE__).'/cawrapper/ItemService.php';
require dirname(__FILE__).'/restlib/rest_client.php';

class InventaireController extends AbstractActionController
{
	protected $inventaireTable;
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

		$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getInventaireTable()->fetchAllFullInfosPaginator($year));
		$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
		$view = new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
						),
				//'inventaires' => $this->getInventaireTable()->fetchAllFullInfos(), //$paginator,
				'inventaires' => $paginator, //$paginator,
				'yearsOptions' => $this->getInventaireTable()->getInventaireYearsAsOptions(),
				'fields' => $this->getInventaireTable()->getFieldsName(),
				'fieldsname' => $this->getInventaireTable()->getFieldsHumanName(),
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
				'inventaires' => $this->getInventaireTable()->fetchAll(),
				'page' => $this->params()->fromRoute('page'),
		));
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
    	return new ViewModel(array(
    			'inventaire' => $this->getInventaireTable()->getInventaire($id),
    			'photo'  => $this->getPhotoTable()->getPhotoByInventaireId($id),
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
                $this->getInventaireTable()->validateInventaire($id);
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
	public function testAction()
	{
		$config = $this->getServiceLocator()->get('Config');
		//var_dump($config["ca"]["ca_service_url"]);die();
		/*$restclient = new RestClient($config["ca"]["ca_service_url"]);
		$endpoint = "/iteminfo/ItemInfo/rest";
		$data = array(
				"method"=>"auth",
				"username" => "admin",
				"password" => "admin"
		);
		$response = $restclient->restPost($endpoint,$data);
		var_dump($response);*/
	
		$url = $config["ca"]["ca_service_url"];
		$c = new \RestClient();
			// AUTHENTIFICATION
		$res = $c->post($url."/iteminfo/ItemInfo/rest",
				array("method"=>"auth", "username" => $config["ca"]["username"], "password" => $config["ca"]["password"])
			);
		// INSERTION SPECIMEN BELETTE (ca_objects)
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
				array(
				"method" => "add",
						"type" => "ca_objects",
									"fieldInfo" => array(
											'idno' => "ObjBelette",
											'status' => 0,
													'access' => 1,
															'type_id' => 21
		)
		)
		);
		// Traitement de la valeur récupérée
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		$object_id = $simplexml_response->add->response * 1;
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
				array(
				"method" => "addLabel",
						"type" => "ca_objects",
								"item_id" => $object_id,
										"label_data_array" => array(
												"name" => "Mustella nivalis"
						),
						"localeID" => 2, // La locale est dépendante de la configuration, normalement pas nécessaire au MNHN
							"is_preferred" => 1
				)
		);
		// INSERTION CARTEL BELETTE (ca_occurrences)
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
				array(
						"method" => "add",
							"type" => "ca_occurrences",
							"fieldInfo" => array(
								'idno' => "OccBelette",
											'status' => 0,
											'access' => 1,
											'type_id' => 90
						)
		)
		);
		// Traitement de la valeur récupérée
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		$occ_id = $simplexml_response->add->response * 1;
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
				array(
						"method" => "addLabel",
							"type" => "ca_occurrences",
									"item_id" => $occ_id,
									"label_data_array" => array(
								"name" => "Cartel de la belette"
		),
						"localeID" => 2, // La locale est dépendante de la configuration, normalement pas nécessaire au MNHN
							"is_preferred" => 1
		)
		);
		// INSERTION LIEN SPECIMEN (ca_objects) CARTEL (ca_occurrences)
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
		array(
						"method" => "addRelationship",
							"type" => "ca_objects",
									"item_id" => $object_id,
											"related_type" => "ca_occurrences",
						"related_item_id" => $occ_id,
							"relationship_type_id" => 115, // relation "depicts"/"représente" dans les relations objet ⇔ occurrence
							"source_info" => ""
		)
		);
		// INSERTION EXPOSITION MAMMIFERES (ca_collections)
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
		array(
						"method" => "add",
				"type" => "ca_collections",
						"fieldInfo" => array(
						'idno' => "ExpoMamm",
								'status' => 0,
									'access' => 1,
											'type_id' => 96
		)
		)
		);
		// Traitement de la valeur récupérée
			$simplexml_response = new \SimpleXMLElement($c->response_object->body);
			$coll_id = $simplexml_response->add->response * 1;
			$res = $c->post(
					$url."/cataloguing/Cataloguing/rest",
					array(
							"method" => "addLabel",
						"type" => "ca_collections",
						"item_id" => $coll_id,
							"label_data_array" => array(
							"name" => "Les mammifères"
						),
							"localeID" => 2, // La locale est dépendante de la configuration, normalement pas nécessaire au MNHN
							"is_preferred" => 1
			)
		);
		// INSERTION LIEN CARTEL (ca_occurrences) EXPOSITION (ca_collections)
		$res = $c->post(
		$url."/cataloguing/Cataloguing/rest",
		array(
		"method" => "addRelationship",
						"type" => "ca_occurrences",
							"item_id" => $occ_id,
						"related_type" => "ca_collections",
									"related_item_id" => $coll_id,
						"relationship_type_id" => 172, // relation "a pour sujet"/"subject" dans les relations occurrence ⇔ collection
							"source_info" => ""
		)
		);
		print $c->response_object->body;
		die();
	
		return new ViewModel(array(
			'auth' => array(
					'logged' => $this::isLogged(),
					'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
			),
			'fields' => $this->getInventaireTable()->getFieldsName(),
			'fieldsname' => $this->getInventaireTable()->getFieldsHumanName()
		));
	}
	
}
?>