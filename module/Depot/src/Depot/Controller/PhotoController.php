<?php 

// filename : module/Test/src/Depot/Controller/PhotoController.php
namespace Depot\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Depot\Model\Photo;
use Depot\Model\PhotoTable;
use Depot\Form\PhotoForm;

use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\Extension;

class PhotoController extends AbstractActionController
{
	protected $photoTable;
	protected $depotTable;
	
	private function isLogged() {
		if ($this->zfcUserAuthentication()->hasIdentity()) return true; else return false;
	}
	private function preloadCaDirect($setup_path) {
		$path = getcwd();
		
		// AUTHENTIFICATION
		chdir($setup_path);
		$result = include($setup_path."/setup.php");
		chdir($path);
		if ($result) {
			require_once(__CA_LIB_DIR__.'/core/Db.php');
			return true;
		} else {
			return false;
		}
	}
	
	public function indexAction()
	{
		$page = (int) $this->params()->fromRoute('page', 1);
		
		$iteratorAdapter = new \Zend\Paginator\Adapter\Iterator($this->getPhotoTable()->fetchAllFullInfosPaginator());
		$paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(8);
		
		return new ViewModel(array(
				'auth' => array(
						'logged' => $this::isLogged(),
						'login' => ($this::isLogged() ? $this->zfcUserAuthentication()->getIdentity()->getEmail() : false)
				),
				'photos' => $paginator,
				'fields' => $this->getPhotoTable()->getFieldsName(),
				'fieldsname' => $this->getPhotoTable()->getFieldsHumanName()
		));
	}
	
	public function getPhotoFromCaAction()
	{
		$depot_id = (int) $this->params()->fromRoute('depot_id', 0);
		if (!$depot_id) {
			return $this->redirect()->toRoute('photo', array(
					'action' => 'index'
			));
		}
		if ($id=$this->getPhotoTable()->getPhotoByDepotId($depot_id))
			$this->getPhotoTable()->deletePhoto($id);
		$id=0;
		$ca_id = $this->getDepotTable()->getCaIdFromDepotId($depot_id);
		$config = $this->getServiceLocator()->get('Config');
        if(!$this->preloadCaDirect($config["ca_direct"]["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
				
		include_once(__CA_MODELS_DIR__."/ca_locales.php");
		include_once(__CA_MODELS_DIR__."/ca_objects.php");
		
		$t_locale = new \ca_locales();
		$locale_id = $t_locale->loadLocaleByCode('fr_FR'); // Stockage explicite en français
		$t_list = new \ca_lists();
		$object_type = $t_list->getItemIDFromList('object_types', 'art');
		
		$t_object = new \ca_objects($ca_id);
		$t_object->setMode(ACCESS_READ);
		
		$media = $t_object->getPrimaryRepresentation(array('large'));
		if (!copy(
				$media["paths"]["large"],
				($file = dirname(__DIR__).'/../../../../public/files/assets/'.basename($media["paths"]["large"]))
			)) {
			throw new \Exception("Impossible de recopier le fichier image dans public/files/assets.");
		}
		$photo = new Photo();
		$photo->exchangeArray(array("id" => 0, "depot_id" => $depot_id,  "credits" => "", "file" => $file));
		$this->getPhotoTable()->savePhoto($photo);
        return $this->redirect()->toRoute('photo', array(
        	'action' => 'added'
         ));					
	}
	
	public function addAction()
	{
		$depot_id = (int) $this->params()->fromRoute('depot_id', 0);
		if (!$depot_id) {
			return $this->redirect()->toRoute('photo', array(
					'action' => 'index'
			));
		}
		$id=0;
		$form = new PhotoForm();
		$binding = new \ArrayObject(array('id'=>$id, 'depot_id'=>$depot_id));
		$form->bind($binding);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$photo = new Photo();
			$form->setInputFilter($photo->getInputFilter());

			$nonFile = $request->getPost()->toArray();
			$File    = $this->params()->fromFiles('file');
			$data = array_merge(
					$nonFile,
					array('file'=> $File['name'])
			);
			//set data post and file ...
			$form->setData($data);
			 
			if ($form->isValid()) {
				// Validation du fichier uploadé : taille, dimensions en pixel et extension (png et jpg)
				$size = new Size(array('min'=>10000, 'max' =>2000000)); //minimum bytes filesize
				$extension = new Extension(array('extension'=>array('png','jpg','jpeg')));
				$imagesize = new ImageSize(array(
						'minwidth'=>150,
						'maxwidth'=>1500,
						'minheight'=>150,
						'maxheight'=>1500
						));
				
				$adapter = new \Zend\File\Transfer\Adapter\Http();
				$adapter->setValidators(array($size,$imagesize), $File['name']);
				if (!$adapter->isValid()){
					$dataError = $adapter->getMessages();
					$error = array();
					foreach($dataError as $key=>$row)
					{
						$error[] = $row;
					}
					$form->setMessages(array('file'=>$error ));
				} else {
					$adapter->setDestination(dirname(__DIR__).'/../../../../public/files/assets');
					if ($adapter->receive($File['name'])) {
						$photo->exchangeArray($form->getData());
						$this->getPhotoTable()->savePhoto($photo);
            			return $this->redirect()->toRoute('photo', array(
                			'action' => 'added'
            			));					
					} else {
						throw new \Exception("Impossible de recopier le fichier.");
					}
				}
			}
		}
		 
		return array('depot_id' => $depot_id, 'id'=> $id, 'form' => $form);
	}
	
	public function addedAction()
	{
		return array();
	}

	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
			return $this->redirect()->toRoute('photo');
		}
	
		$request = $this->getRequest();
		if ($request->isPost()) {
			$del = $request->getPost('del', 'No');
	
			if ($del == 'Yes') {
				$id = (int) $request->getPost('id');
				$this->getPhotoTable()->deletePhoto($id);
			}
	
			// Redirect to list of depots
			return $this->redirect()->toRoute('photo');
		}
	
		return array(
				'id'    => $id,
				'photo' => $this->getPhotoTable()->getPhoto($id)
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