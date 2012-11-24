<?php
// module/Inventaire/src/Depot/Model/DepotTable.php:
namespace Depot\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;

class DepotTable extends AbstractTableGateway
{
	protected $table ='inventaire_depot';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Depot());
		
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
		$select->from($this->table);
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
		$select->from($this->table);
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		
		//you can check your query by echo-ing :
		$statement = $sql->prepareStatementForSqlObject($select);
	
		$resultSet = new ResultSet();
	
		$resultSet->initialize($statement->execute());
		$resultSet->buffer();
		$resultSet->next();
		
		return $resultSet;
	}
	
	public function fetchSearchResult($depotSearchArray)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('photo', 'inventaire_depot.id = inventaire_photo2.depot_id', array('credits','file'),'left');
		$where = "";
		if(is_array($depotSearchArray) && count($depotSearchArray)>0) {
			foreach($depotSearchArray as $key => $value) {
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
	
	public function getDepotYearsAsOptions()
	{
		$sql = "SELECT year(date_inscription) AS year FROM inventaire_depot WHERE year(date_inscription) > 0 GROUP BY year(date_inscription) ORDER BY 1";
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
	
	public function getDepot($id)
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
	
	public function checkDepotByNumdep($numdep)
	{

		$rowset = $this->select(array(
				'numdep' => $numdep,
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
	
	public function saveDepot(Depot $depot)
	{
		$data = array(
				'numinv' => $depot->numinv,//1
				'numdep'=> $depot->numdep,//2
				'date_ref_acte_depot'=> $depot->date_ref_acte_depot,//3
				'date_entree'=> $depot->date_entree,//4
				'proprietaire'=> $depot->proprietaire,//5
				'date_ref_acte_fin'=> $depot->date_ref_acte_fin,//6
				'date_inscription' => $depot->date_inscription,//7
				'designation' => $depot->designation,//8
				'inscription' => $depot->inscription,//9
				'materiaux' => $depot->materiaux,//10
				'techniques' => $depot->techniques,//11
				'mesures' => $depot->mesures,//12
				'etat' => $depot->etat,//13
				'auteur'  => $depot->auteur,//14
				'epoque' => $depot->epoque,//15
				'usage' => $depot->usage,//16
				'provenance' => $depot->provenance,//17
				'observations' => $depot->observations,//18
				'validated'=> $depot->validated
		);

		$id = (int) $depot->id;

		if ($id == 0) {
			if (!$this->checkDepotByNumDep($depot->numdep)) { 
				$this->insert($data);
			} else {
				throw new \Exception("Un autre enregistrement est déjà présent dans la base avec le même numéro de dépôt.");
			}
		} elseif ($this->getDepot($id)) {
			if(!$this->getDepot($id)->validated) {
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
				'numdep',//2
				'date_ref_acte_depot',//3
				'date_entree',//4
				'proprietaire',//5
				'date_ref_acte_fin',//6
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
				"numinv" => "Numéro d'inventaire du déposant", //1
				'numdep' => "Numéro de dépôt",//2
				'date_ref_acte_depot' => "Références du dépôt",//3
				'date_entree' => "Date d'entrée",//4
				'proprietaire' => "Propriétaire",//5
				'date_ref_acte_fin' => "Références de fin de dépôt",//6
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
	
	
	public function validateDepot($id)
	{
		$this->update(
			// set
			array("validated" => TRUE),
			// where
			"id = $id");
	}	
	
	public function deleteDepot($id)
	{
		$this->delete(array(
				'id' => $id,
		));
	}
}