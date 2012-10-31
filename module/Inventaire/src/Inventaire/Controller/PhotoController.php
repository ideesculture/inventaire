<?php 

// filename : module/Test/src/Inventaire/Controller/PhotoController.php
namespace Inventaire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Inventaire\Model\Photo;
use Inventaire\Model\PhotoTable;
use Inventaire\Form\PhotoForm;

use Zend\Validator\File\Size;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\Extension;

class PhotoController extends AbstractActionController
{
	protected $photoTable;
	protected $inventaireTable;
	
	private function isLogged() {
		if ($this->zfcUserAuthentication()->hasIdentity()) return true; else return false;
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
	
	public function addAction()
	{
		$inventaire_id = (int) $this->params()->fromRoute('inventaire_id', 0);
		if (!$inventaire_id) {
			return $this->redirect()->toRoute('photo', array(
					'action' => 'index'
			));
		}
		$id=0;
		$form = new PhotoForm();
		$binding = new \ArrayObject(array('id'=>$id, 'inventaire_id'=>$inventaire_id));
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
				$size = new Size(array('min'=>20000, 'max' =>2000000)); //minimum bytes filesize
				$extension = new Extension(array('extension'=>'png,jpg,jpeg'));
				$imagesize = new ImageSize(array(
						'minwidth'=>150,
						'maxwidth'=>1500,
						'minheight'=>150,
						'maxheight'=>1500
						));
				
				$adapter = new \Zend\File\Transfer\Adapter\Http();
				$adapter->setValidators(array($size,$extension,$imagesize), $File['name']);
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
		 
		return array('inventaire_id' => $inventaire_id, 'id'=> $id, 'form' => $form);
	}
	
	public function addedAction()
	{
		return array();
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