<?php
// module/Inventaire/src/Inventaire/Model/Inventaire.php:
namespace Inventaire\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Inventaire implements InputFilterAwareInterface
{
	public $id;
	public $ca_id;
	
/* a) Rubriques relatives au statut juridique des biens et aux conditions de leur acquisition
 * 
	N° colonne	Rubrique
	1	N° inventaire
	2	Mode d'acquisition
	3	Nom du donateur, testateur ou vendeur
	4	Date de l'acte d'acquisition et d'affectation au musée
	5	Avis des instances scientifiques
	6	Prix d'achat - subvention publique
	7	Date d'inscription au registre d'inventaire
 */
	public $numinv; //1
	public $mode_acquisition;//2
	public $donateur;//3
	public $date_acquisition;//4
	public $avis;//5
	public $prix;//6
	public $date_inscription;//7
	
/* b) Rubriques portant description des biens
 * 
	N° colonne	Rubrique
	8	Désignation du bien
	9	Marques et inscriptions
	10	Matières ou matériaux
	11	Techniques de réalisation, préparation, fabrication
	12	Mesures
	13	Indications particulières sur l'état du bien au moment de l'acquisition
 */	
	public $designation;//8
	public $inscription;//9
	public $materiaux;//10
	public $techniques;//11
	public $mesures;//12
	public $etat;//13
	
/* c) Rubriques complémentaires
 * 
	N° colonne	Rubrique
	14	Auteur, collecteur, fabricant, commanditaire...
	15	Epoque, datation ou date de récolte (voire d'utilisation ou de découverte)
	16	Fonction d'usage
	17	Provenance géographique
	18	Observations
 */			
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
		$this->ca_id     = (isset($data['ca_id'])) ? $data['ca_id'] : null;
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
					'required' => false,
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
					'name'     => 'date_validation',
					'required' => false,
					'filters'  => array(),
					'validators' => array(
							array(
									'name'    => 'Date'
							),
					),
			)));
				
			$inputFilter->add($factory->createInput(array(
					'name'     => 'prix',
					'required' => false,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'Float'
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