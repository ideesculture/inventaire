<?php 

// filename : module/Test/src/Test/Model/Photo.php
namespace Depot\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Photo implements InputFilterAwareInterface
{
	//identifiant photo
	public $id;
	//identifiant depot lié
	public $depot_id;
	//crédits photo
	public $credits;
	//fichier photo
	public $file;
	
	protected $inputFilter;

	public function exchangeArray($data)
	{
		$this->id  = (isset($data['id']))  ? $data['id']     : null;
		$this->depot_id  = (isset($data['id']))  ? $data['depot_id']     : null;
		$this->credits  = (isset($data['credits']))  ? $data['credits']     : null;
		$this->file  = (isset($data['file']))  ? $data['file']     : null;
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
					'name'     => 'depot_id',
					'required' => true,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
	
			$inputFilter->add(
					$factory->createInput(array(
							'name'     => 'credits',
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
					))
			);

			$inputFilter->add(
					$factory->createInput(array(
							'name'     => 'file',
							'required' => true,
					))
			);

			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}

?>