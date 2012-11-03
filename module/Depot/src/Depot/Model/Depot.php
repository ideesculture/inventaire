<?php
// module/Depot/src/Depot/Model/Depot.php:
namespace Depot\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Depot implements InputFilterAwareInterface
{
	public $id;

	public $numinv; //1
	public $numdep;//2
	public $date_ref_acte_depot;//3
	public $date_entree;//4
	public $proprietaire;//5
	public $date_ref_acte_fin;//6
	public $date_inscription;//7
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
	
	public $validated; // est-ce que l'enregistrement est validé (plus de modif) ? 0/1
	public $pic; // chemin vers la vignette sur le serveur
	
	
	protected $inputFilter;
	protected $searchInputFilter;
	
	public function exchangeArray($data)
	{
		$this->id     = (isset($data['id'])) ? $data['id'] : null;
		$this->numinv = (isset($data['numinv'])) ? $data['numinv'] : null;//1
		$this->numdep = (isset($data['numdep'])) ? $data['numdep'] : null;//2
		$this->date_ref_acte_depot = (isset($data['date_ref_acte_depot'])) ? $data['date_ref_acte_depot'] : null;//3
		$this->date_entree = (isset($data['date_entree'])) ? $data['date_entree'] : null;//4
		$this->proprietaire = (isset($data['proprietaire'])) ? $data['proprietaire'] : null;//5
		$this->date_ref_acte_fin = (isset($data['date_ref_acte_fin'])) ? $data['date_ref_acte_fin'] : null;//6
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
		$this->validated = (isset($data['validated'])) ? $data['validated'] : false;//19
		$this->pic = (isset($data['pic'])) ? $data['pic'] : null;//19
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
					'name'     => 'id',
					'required' => true,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'numinv',
					'required' => true,
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
					'name'     => 'numdep',
					'required' => true,
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
					'required' => true,
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
					'name'     => 'date_inscription',
					'required' => false,
					'filters'  => array(),
					'validators' => array(
							array(
									'name'    => 'Date'
							),
					),
			)));

			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}

	public function getSearchInputFilter()
	{
		if (!$this->searchInputFilter) {
			$searchInputFilter = new InputFilter();
			$factory     = new InputFactory();
			
			$searchInputFilter->add($factory->createInput(array(
					'name'     => 'designation',
					'required' => true,
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
			$this->searchInputFilter = $searchInputFilter;
		}
		return $this->searchInputFilter;
		
	}
}