<?php
// module/Inventaire/src/Inventaire/Model/InventaireTable.php:
namespace Inventaire\Model;

class InventaireCAConnector
{
	//protected $table ='inventaire';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Inventaire());
		
		$this->initialize();
	}

	public function test($bool)
	{
		if ($bool) return true;
		return false;
	}
	
	public function add($array)
	{
		
	}

}