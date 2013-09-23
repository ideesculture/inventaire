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
	public $ca_id;
	
/* a) Rubriques relatives au statut juridique des biens et aux conditions de son dépôt
 * 
	N° 	Rubrique
	1 	Numéro de dépôt attribué au bien déposé
	2 	Numéro d'inventaire du bien dans les collections du déposant
	3 	Date et références de l'acte unilatéral ou contractuel autorisant la mise en dépôt du bien
	4 	Date de prise en charge du bien (date d'entrée matérielle)
	5 	Nom de la personne morale ou physique propriétaire du bien déposé
	6 	Date et références de l'acte unilatéral ou contractuel décidant de mettre fin au dépôt
	7 	Date d'inscription au registre des biens reçus en dépôt par le musée
 */
	public $numdepot; //1
	public $numdepot_sort; 
	public $numdepot_display;
	public $numinv;//2
	public $actedepot;//3
	public $date_priseencharge;//4
	public $proprietaire;//5
	public $actefindepot;//6
	public $date_inscription;//7
	public $date_inscription_display;
	
/* b) Rubriques portant description des biens
 * 
	N°	Rubrique
	8	Désignation du bien
	9	Marques et inscriptions
	10	Matières ou matériaux
	11	Techniques de réalisation, préparation, fabrication
	12	Mesures
	13	Indications particulières sur l'état du bien au moment de l'acquisition
 */	
	public $designation;//8
	public $designation_display;
	public $inscription;//9
	public $materiaux;//10 et 11 (matériaux et techniques)
	public $mesures;//12
	public $etat;//13
	
/* c) Rubriques complémentaires
 * 
	N°	Rubrique
	14	Auteur, collecteur, fabricant, commanditaire...
	15	Epoque, datation ou date de récolte (voire d'utilisation ou de découverte)
	16	Fonction d'usage
	17	Provenance géographique
	18	Observations
 */			
	public $auteur;//14
	public $auteur_display;
	public $epoque;//15
	public $usage;//16
	public $provenance;//17
	public $observations;//18
	
	public $validated; // est-ce que l'enregistrement est validé (plus de modif) ? 0/1	
	
	protected $inputFilter;
	protected $searchInputFilter;
	
	public function exchangeArray($data)
	{
		$this->id     = (isset($data['id'])) ? $data['id'] : null;
		$this->ca_id     = (isset($data['ca_id'])) ? $data['ca_id'] : null;
		$this->numdepot = (isset($data['numdepot'])) ? $data['numdepot'] : null;//1
		$this->numdepot_sort = (isset($data['numdepot_sort'])) ? $data['numdepot_sort'] : null;
		$this->numdepot_display = (isset($data['numdepot_display'])) ? $data['numdepot_display'] : null;
		$this->numinv = (isset($data['numinv'])) ? $data['numinv'] : null;//1
		$this->actedepot = (isset($data['actedepot'])) ? $data['actedepot'] : null;//2
		$this->date_priseencharge= (isset($data['date_priseencharge'])) ? $data['date_priseencharge'] : null;//3
		$this->proprietaire = (isset($data['proprietaire'])) ? $data['proprietaire'] : null;//4
		$this->actefindepot = (isset($data['actefindepot'])) ? $data['actefindepot'] : null;//5
		$this->date_inscription = (isset($data['date_inscription'])) ? $data['date_inscription'] : null;//7
		$this->date_inscription_display = (isset($data['date_inscription_display'])) ? $data['date_inscription_display'] : null;
		$this->designation  = (isset($data['designation'])) ? $data['designation'] : null;//8
		$this->designation_display  = (isset($data['designation_display'])) ? $data['designation_display'] : null;//8
		$this->inscription = (isset($data['inscription'])) ? $data['inscription'] : null;//9
		$this->materiaux = (isset($data['materiaux'])) ? $data['materiaux'] : null;//10
		$this->mesures = (isset($data['mesures'])) ? $data['mesures'] : null;//12
		$this->etat = (isset($data['etat'])) ? $data['etat'] : null;//13
		$this->auteur = (isset($data['auteur'])) ? $data['auteur'] : null;//14 
		$this->auteur_display = (isset($data['auteur_display'])) ? $data['auteur_display'] : null;//14 
		$this->epoque = (isset($data['epoque'])) ? $data['epoque'] : null;//15
		$this->usage = (isset($data['usage'])) ? $data['usage'] : null;//16
		$this->provenance = (isset($data['provenance'])) ? $data['provenance'] : null;//17
		$this->observations = (isset($data['observations'])) ? $data['observations'] : null;//18
		$this->validated = (isset($data['validated'])) ? $data['validated'] : false;//19
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
					'required' => false,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'numdepot',
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

			$inputFilter->add($factory->createInput(array(
					'name'     => 'date_priseencharge',
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