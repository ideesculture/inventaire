<?php
// module/Inventaire/src/Inventaire/Model/InventaireTable.php:
namespace Inventaire\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;

class InventaireTable extends AbstractTableGateway
{
	protected $table ='inventaire';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Inventaire());
		
		$this->initialize();
	}

	public function fetchAll($year)
	{
		$resultSet = $this->select();
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		
		return $resultSet;
	}

	public function fetchAllPaginator($year)
	{
		$resultSet = $this->select();
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		
		$resultSet->buffer();
		$resultSet->next();
		
		return $resultSet;
	}

	public function fetchAllFullInfos($year)
	{
		
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('photo', 'inventaire.id = photo.inventaire_id', array('credits','file'),'left');
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		
		//you can check your query by echo-ing :
		//echo $select->getSqlString();
		$statement = $sql->prepareStatementForSqlObject($select);
	
		$resultSet = new ResultSet();
	
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
		
	public function fetchAllFullInfosPaginator($year)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('photo', 'inventaire.id = photo.inventaire_id', array('credits','file'),'left');
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		
		//you can check your query by echo-ing :
		//echo $select->getSqlString();
		$statement = $sql->prepareStatementForSqlObject($select);
	
		$resultSet = new ResultSet();
	
		$resultSet->initialize($statement->execute());
		$resultSet->buffer();
		$resultSet->next();
		
		return $resultSet;
	}
	
	public function fetchSearchResult($inventaireSearchArray)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('photo', 'inventaire.id = photo.inventaire_id', array('credits','file'),'left');
		$where = "";
		if(is_array($inventaireSearchArray) && count($inventaireSearchArray)>0) {
			foreach($inventaireSearchArray as $key => $value) {
				if ($key == "submit") break;
				/*if ($key == "year") {
					$where .= "YEAR(date_inscription) = ".$value;
				}*/
				if ($where) $where .= " AND ";
				if(strpos($key,"date_") === 0) {
					// Traitement filtre par dates limites mini et/ou maxi : le type date est récupéré par le début du nom de champ date_
					if(is_array($value)) {
						if($value["min"])
							$where .= $key." >= \"".$value["min"]."\"";
						if(($value["min"]) && ($value["max"])) {
							$where .= " AND ";
						} 
						if($value["max"])
							$where .= $key." <= \"".$value["max"]."\"";
					} else {
						throw new \Exception("Impossible de traiter les dates soumises : ".$key);
					}
				} else {
					// Traitement des autres critères en texte simple
					// Ajout des % en début
					$words=explode(" ",trim($value));
					$num = 1;
					foreach ($words as $word) {
						$where .= $key." LIKE \"%".$word."%\"";
						if ($num != count($words)) $where .= " AND ";
						$num++;
					}
				}
			}
			
		}
		// TODO : construire ici le filtre WHERE de la requête
		if ($where != "") {$select->where($where);}
		
		//you can check your query by echo-ing :
		//echo $select->getSqlString();die();
		$statement = $sql->prepareStatementForSqlObject($select);
	
		$resultSet = new ResultSet();
	
		$resultSet->initialize($statement->execute());
		$resultSet->buffer();
		$resultSet->next();
		
		return $resultSet;
	}
	
	public function getInventaireYearsAsOptions()
	{
		$sql = "SELECT year(date_inscription) AS year FROM inventaire WHERE year(date_inscription) > 0 GROUP BY year(date_inscription) ORDER BY 1";
    	$statement = $this->adapter->query($sql);
    	$res =  $statement->execute();
    	$rownumber=0;
    	// set the first option
    	$rows[$rownumber] = array (
    			'value' => '-',
    			'label' => '-',
    			'selected' => TRUE,
    			'disabled' => TRUE
    	);
    	// set other options from SQL request results
    	foreach ($res as $row) {
    		$rownumber++;
			$rows[$rownumber] = array (
    				'value' => $row['year'],
    				'label' => $row['year'],
    		);
    	}
    	return $rows;
	}
	
	public function getInventaire($id)
	{
		$id  = (int) $id;

		$rowset = $this->select(array(
				'id' => $id,
		));

		$row = $rowset->current();

		if (!$row) {
			throw new \Exception("Impossible de trouver la ligne $id");
		}

		return $row;
	}
	
	public function checkInventaireByNuminv($numinv)
	{

		$rowset = $this->select(array(
				'numinv' => $numinv,
		));

		$row = $rowset->current();

		if (!$row) {
			return false;
		}
		
		return true;
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
	
	public function saveInventaire(Inventaire $inventaire)
	{
		$data = array(
				'numinv' => $inventaire->numinv,
				'mode_acquisition' => $inventaire->mode_acquisition,//2
				'donateur' => $inventaire->donateur,//3
				'date_acquisition' => $inventaire->date_acquisition,//4
				'avis' => $inventaire->avis,//5
				'prix' => $inventaire->prix,//6
				'date_inscription' => $inventaire->date_inscription,//7
				'designation' => $inventaire->designation,//8
				'inscription' => $inventaire->inscription,//9
				'materiaux' => $inventaire->materiaux,//10
				'techniques' => $inventaire->techniques,//11
				'mesures' => $inventaire->mesures,//12
				'etat' => $inventaire->etat,//13
				'auteur'  => $inventaire->auteur,//14
				'epoque' => $inventaire->epoque,//15
				'usage' => $inventaire->usage,//16
				'provenance' => $inventaire->provenance,//17
				'observations' => $inventaire->observations,//18
				'validated'=> $inventaire->validated
		);

		$id = (int) $inventaire->id;

		if ($id == 0) {
			if (!$this->checkInventaireByNuminv($inventaire->numinv)) { 
				$this->insert($data);
			} else {
				throw new \Exception("Un autre enregistrement est déjà présent dans la base avec le même numéro d'inventaire.");
			}
		} elseif ($this->getInventaire($id)) {
			if(!$this->getInventaire($id)->validated) {
			// si l'enregistrement n'est pas validé, on a le droit de le modifier	*/
				$this->update(
						$data,
						array(
								'id' => $id,
						)
				);
			} else {
				throw new \Exception('Enregistrement déjà validé, impossible de le modifier.');
			}
		} else {
			throw new \Exception("Le formulaire n'existe pas.");
		}
		return true;
	}	
	
	public function getFieldsName()
	{
		return array(
				"numinv", //1
				"mode_acquisition",     //2
				"donateur",     //3
				"date_acquisition",     //4
				"avis",     //5
				"prix",     //6
				"date_inscription",     //7
				"designation",     //8
				"inscription",     //9
				"materiaux",     //10
				"techniques",     //11
				"mesures",     //12
				"etat",     //13
				"auteur",     //14
				"epoque",     //15
				"usage",     //16
				"provenance",     //17
				"observations"   //18
		);
	}
	
	public function getFulltextFieldsName()
	{
		return array(
				"designation",
				"auteur"			
				);
	}
	
	public function getFieldsHumanName()
	{
		return array(
				"numinv" => "Numéro d'inventaire", //1
				"mode_acquisition" => "Mode d'acquisition",     //2
				"donateur" => "Donateur",     //3
				"date_acquisition" => "Date d'acquisition",     //4
				"avis" => "Avis",     //5
				"prix" => "Prix",     //6
				"date_inscription" => "Date d'inscription",     //7
				"designation" => "Désignation",     //8
				"inscription" => "Inscription",     //9
				"materiaux" => "Matériaux",     //10
				"techniques" => "Techniques",     //11
				"mesures" => "Mesures",     //12
				"etat" => "Etat",     //13
				"auteur" => "Auteur",     //14
				"epoque" => "Epoque",     //15
				"usage" => "Usage",     //16
				"provenance" => "Provenance",     //17
				"observations" => "Observations"   //18
		);
	}
	
	
	public function validateInventaire($id)
	{
		$this->update(
			// set
			array("validated" => TRUE),
			// where
			"id = $id");
	}	
	
	public function deleteInventaire($id)
	{
		$this->delete(array(
				'id' => $id,
		));
	}
}