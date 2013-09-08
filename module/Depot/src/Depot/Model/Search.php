<?php
// module/Depot/src/Depot/Model/Depot.php:
namespace Depot\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Search implements InputFilterAwareInterface
{
	public $numinv; //1
	public $mode_acquisition;//2
	public $donateur;//3
	public $date_acquisition_mini;//4
	public $date_acquisition_maxi;//4
	public $avis;//5
	public $prix;//6
	public $date_inscription_mini;//7
	public $date_inscription_maxi;//7
	public $designation;//8
	public $inscription;//9
	public $materiaux;//10
	public $techniques;//11
	public $mesures;//12
	public $etat;//13
	public $auteur;//14
	public $epoque;//15
	public $usage;//16
	public $provenance;//17
	public $observations;//18
	public $is_validated; // est-ce que l'enregistrement est validé (plus de modif) ? 0/1
	public $has_pic; // chemin vers la vignette sur le serveur
	
	protected $inputFilter;
	
	public function exchangeArray($data)
	{
		$this->numinv = (isset($data['numinv'])) ? $data['numinv'] : null;//1
		$this->mode_acquisition = (isset($data['mode_acquisition'])) ? $data['mode_acquisition'] : null;//2
		$this->donateur = (isset($data['donateur'])) ? $data['donateur'] : null;//3
		$this->date_acquisition = (isset($data['date_acquisition'])) ? $data['date_acquisition'] : null;//4
		$this->avis = (isset($data['avis'])) ? $data['avis'] : null;//5
		$this->prix = (isset($data['prix'])) ? $data['prix'] : null;//6
		$this->date_inscription = (isset($data['date_inscription'])) ? $data['date_inscription'] : null;//7
		$this->designation  = (isset($data['designation'])) ? $data['designation'] : null;//8
		$this->inscription = (isset($data['inscription'])) ? $data['inscription'] : null;//9
		$this->materiaux = (isset($data['materiaux'])) ? $data['materiaux'] : null;//10
		$this->techniques = (isset($data['techniques'])) ? $data['techniques'] : null;//11
		$this->mesures = (isset($data['mesures'])) ? $data['mesures'] : null;//12
		$this->etat = (isset($data['etat'])) ? $data['etat'] : null;//13
		$this->auteur = (isset($data['auteur'])) ? $data['auteur'] : null;//14 
		$this->epoque = (isset($data['epoque'])) ? $data['epoque'] : null;//15
		$this->usage = (isset($data['usage'])) ? $data['usage'] : null;//16
		$this->provenance = (isset($data['provenance'])) ? $data['provenance'] : null;//17
		$this->observations = (isset($data['observations'])) ? $data['observations'] : null;//18
		$this->is_validated = (isset($data['is_validated'])) ? $data['is_validated'] : false;//19
		$this->has_pic = (isset($data['has_pic'])) ? $data['has_pic'] : false;//19
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory     = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'numinv',
					'required' => false,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
			$inputFilter->add($factory->createInput(array(
					'name'     => 'designation',
					'required' => false,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
			$inputFilter->add($factory->createInput(array(
					'name'     => 'auteur',
					'required' => false,
					'filters'  => array(
							array('name' => 'StringTrim'),
							array('name' => 'StripTags'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 1,
											'max'      => 100,
									),
							),
					),
			)));
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
}