<?php
// module/Inventaire/src/Inventaire/Model/InventaireTable.php:
namespace Inventaire\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Inventaire\Form\InventaireForm;

class InventaireTable extends AbstractTableGateway
{
	protected $table ='inventaire_inventaire';

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
		->join('inventaire_photo', 'inventaire_inventaire.id = inventaire_id', array('credits','file'),'left');
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
		->join('inventaire_photo', 'inventaire_inventaire.id = inventaire_id', array('credits','file'),'left');
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
		->join('inventaire_photo', 'inventaire_inventaire.id = inventaire_photo.inventaire_id', array('credits','file'),'left');
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
		$sql = "SELECT year(date_inscription) AS year FROM inventaire_inventaire WHERE year(date_inscription) > 0 GROUP BY year(date_inscription) ORDER BY 1";
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
	
	private function caAuth(\RestClient $client, array $caWsConfig)
	{
		// Récupération de la config
		// Authentification
		$res = $client->post($caWsConfig["ca_service_url"]."/iteminfo/ItemInfo/rest",
				array("method"=>"auth", "username" => $caWsConfig["username"], "password" => $caWsConfig["password"])
		);
		// Traitement de la réponse
		$simplexml_response = new \SimpleXMLElement($client->response_object->body);
		// Retourne FALSE si la connexion n'est pas OK
		if ($simplexml_response->auth->status != "success") return false;
		return $client;
	}
	private function remplace_placeholder_par_valeur(&$item,$key,$valeur){
		if(stripos($item,"^valeur")){
			$item=str_ireplace("^valeur", $valeur, $item);
		}
	}
	public function caWsExport($id, array $caWsConfig)
	{

 		$inventaire = $this->getInventaire($id);
		
		$url = $caWsConfig["ca_service_url"];
		$mappings = $caWsConfig["inventaire"];
		
		$c = new \RestClient();
		
		// AUTHENTIFICATION
		if (! $this->caAuth($c, $caWsConfig) ) throw new \Exception("Impossible de se connecter aux webservices de CollectiveAccess, veuillez vérifier votre configuration");
				
		// INSERTION OBJET
		// le cas échéant, renseignement de ca_objects.idno
		if (isset($mappings["ca_objects.idno"])) $idno=$mappings["ca_objects.idno"]; else $idno="";
		$res = $c->post($url."/cataloguing/Cataloguing/rest",
				array(
						"method" => "add","type" => "ca_objects",
						"fieldInfo" => array('idno' => $idno,'status' => 0,'access' => 1,'type_id' => 21)
				));

		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		// Traitement des erreurs
		if ($simplexml_response->add->status != "success")
			throw new \Exception("<b>Impossible d'ajouter l'objet dans la base CA</b> : ".$simplexml_response->add->response->message);
				
		// récupération de la clé primaire de l'objet inséré
		$object_id = $simplexml_response->add->response * 1;
		
		// AJOUT DU TITRE ca_objects.preferred_labels
		$res = $c->post(
				$url."/cataloguing/Cataloguing/rest",
				array(
						"method" => "addLabel", "type" => "ca_objects",
						"item_id" => $object_id,
						"label_data_array" => array("name" => $inventaire->$mappings["ca_objects.preferred_labels"]["valeur"].date("H:i")),
						"localeID" => 2, // La locale est dépendante de la configuration, normalement pas nécessaire au MNHN
						"is_preferred" => 1
				));
		//print $c->response_object->body;die();
		
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		// Traitement des erreurs
		if ($simplexml_response->addLabel->status != "success")
			throw new \Exception("<b>Erreur lors de l'ajout du titre de l'objet dans la base CA</b> : ".$simplexml_response->addLabel->response->message);
		
		// TRAITEMENT DES AUTRES CHAMPS
		foreach($mappings as $field => $mapping) {
			// Le titre et l'idno sont ignorés car déjà traités
			if (in_array($field,array("ca_objects.idno", "ca_objects.preferred_labels"))) break 1;
			
			// Traitement des valeurs spécifiques (ajout suffixe, préfixe...)
			$valeur = $inventaire -> $mapping["valeur"];
			
			// Si pas de valeur à traiter, on passe
			if (!$valeur) break 1;
			
			$fields = explode(".",$field);
			$table = $fields[0];
			$fieldname = $fields[1];
			
			// Traitement des champs OK, à retirer une fois que tous les champs sont OK
			if (in_array($fieldname, array("prix","comments","observations","acquisitionDate","date_inventaire","acquisitionMethod"))) {
				if ($fieldname == "prix") $valeur = $inventaire->$mapping["valeur"]." EUR"; 		
				$res = $c->post(
						$url."/cataloguing/Cataloguing/rest",
						array(
								"method" => "addAttribute",
								"type" => "ca_objects",
								"item_id" => $object_id,
								"attribute_code_or_id" => $fieldname,
								"attribute_data_array" => array(
										$fieldname  => $valeur
								),
						)
				);
				$simplexml_response = new \SimpleXMLElement($c->response_object->body);
				// Traitement des erreurs
				if ($simplexml_response->addAttribute->status != "success")
					throw new \Exception("<b>Erreur lors de l'ajout de l'attribut ".$fieldname." dans la base CA</b> : ".$simplexml_response->addAttribute->response->message);
			} elseif (isset($mapping["container"])) {
				// Si le champ contient un conteneur, on remplace l'emplacement ^valeur par la valeur avant d'insérer dans la base
				$container=str_ireplace("^valeur", $valeur, $mapping["container"]);
				$res = $c->post(
						$url."/cataloguing/Cataloguing/rest",
						array(
								"method" => "addAttribute",
								"type" => "ca_objects",
								"item_id" => $object_id,
								"attribute_code_or_id" =>  $fieldname,
								"attribute_data_array" =>  $container
						)
				);
				$simplexml_response = new \SimpleXMLElement($c->response_object->body);
				if ($simplexml_response->addAttribute->status != "success") {
					throw new \Exception("<b>Erreur lors de l'ajout de l'attribut ".$fieldname." dans la base CA</b> : ".$simplexml_response->addAttribute->response->message);
				}
					
								
			}
		}
		return true;		
	}
	
	public function caWsAvailableSets(array $caWsConfig)
	{
		
		$c = new \RestClient();
		
		// Authentification
		if (! $this->caAuth($c, $caWsConfig) ) throw new \Exception("Impossible de se connecter aux webservices de CollectiveAccess, veuillez vérifier votre configuration");
			
		$res = $c->post( $caWsConfig["ca_service_url"]."/iteminfo/ItemInfo/rest",
				array(
						"method" => "getSets"
				));
		
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		
		// Traitement des erreurs
		if ($simplexml_response->getSets->status != "success")
			throw new \Exception("<b>Impossible de récupérer la liste des ensembles (set) de la base CA</b> : ".$simplexml_response->getSets->response->message);
		
		// Création d'un tableau des sets disponibles
		$return = array();
		foreach($simplexml_response->getSets->children() as $set) {
			// Le statut fait partie des réponses, pas la peine de l'intégrer au tableau
			if(!isset($set->key_2->set_id)) break; 
			// Remplissage du tableau
			$return[] = array("set_id" => $set->key_2->set_id, "set_code" => $set->key_2->set_code, "name" => $set->key_2->name);
		}
		return $return;
	}
	
	public function caWsImportSet($set_id, array $caWsConfig)
	{
		$c = new \RestClient();
		
		$importableFields =  array("dimensions","inscription_c","othernumber","acquisitionMethod","ca_entities.preferred_labels","acquisitionDate");
		
		// AUTH
		if (! $this->caAuth($c, $caWsConfig) ) throw new \Exception("Impossible de se connecter aux webservices de CollectiveAccess, veuillez vérifier votre configuration");
		
		$res = $c->post( $caWsConfig["ca_service_url"]."/iteminfo/ItemInfo/rest",
				array(
						"method" => "getSetItems",
						"set_id" => $set_id,
						"options" => NULL
				));
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		// Traitement des erreurs
		if ($simplexml_response->getSetItems->status != "success")
			throw new \Exception("<b>Impossible de récupérer les enregistrements contenus dans l'ensemble $set_id</b> : ".$simplexml_response->getSetItems->response->message);
		
		// RECUPERATION DES ID DES OBJETS DU SET
		$result=array();
		foreach($simplexml_response->getSetItems->children() as $child) {
			if (isset($child->key_2->object_id)) {
				$object_id = (int) $child->key_2->object_id;
				//print "import de ".$object_id."<br/>\n";
				// IMPORT DE L'OBJET
				$result[$object_id] = $this->caWsImportItem( "ca_objects", $object_id, $c, $caWsConfig);
			}
		}
		return $result;
	}

	public function caWsImportItem($type, $item_id, \RestClient $c, $caWsConfig)
	{
		$return = array();
		
		$inventaire_fields = array();
		$inventaire_fields["id"]=0;
		
		// TITRE
		$res = $c->post( $caWsConfig["ca_service_url"]."/iteminfo/ItemInfo/rest",
				array(
						"method" => "getLabelForDisplay",
						"type" => $type,
						"item_id" => $item_id,
						"options" => array("locale" => "fr_FR")
						//"attribute_code_or_id" => "dimensions"
  				));
		$simplexml_response = new \SimpleXMLElement($c->response_object->body);
		// Traitement des erreurs
		if ($simplexml_response->getLabelForDisplay->status != "success")
			$return["error"] = "<b>Impossible de récupérer le titre de l'objet $item_id ($type)</b> : ".$simplexml_response->getLabelForDisplay->response->message;
		// DEFINITION DU TITRE
		$inventaire_fields["designation"] = (string) $simplexml_response->getLabelForDisplay->response;
		
		// ATTRIBUTS
		foreach ($caWsConfig["attributesimported"] as $attribute => $target) {
			$data = explode(".",$attribute);

			if (($data[0] == "ca_objects") && ($data[1] != "preferred_labels")) {
				$res = $c->post( $caWsConfig["ca_service_url"]."/iteminfo/ItemInfo/rest",
						array(
								"method" => "getAttributesForDisplay",
								"type" => $type,
								"item_id" => $item_id,
								"attribute_code_or_id" => $data[1],
								"options" => array("locale" => "fr_FR")
						));
				$simplexml_response = new \SimpleXMLElement($c->response_object->body);

				// Traitement des erreurs
				if ($simplexml_response->getAttributesForDisplay->status != "success")
					$return["error"] = "<b>Impossible de récupérer l'attribut ".$data[1]." pour l'objet $item_id ($type)</b> : ".$simplexml_response->getAttributesForDisplay->response->message;
				
				$response = $simplexml_response->getAttributesForDisplay->response;
				if (stripos($data[1],"date") !== FALSE) {
					// TRAITEMENT SPECIFIQUE DATES
					if ($response) {
						$date = \DateTime::createFromFormat('F j Y', $response);
						if ($date) $response = $date->format('Y-m-d');
					}
				} 
				// DEFINITION DE L'ATTRIBUT
				// transtypage SimpleXMLElement vers string en encapsulant la variable entre guillemets http://www.php.net/manual/fr/language.types.type-juggling.php#language.types.typecasting
				$inventaire_fields[$target["field"]] = "$response";
			}	
		}
		if (!$inventaire_fields["numinv"]) {
			$return["error"] = "<b>Objet $object_id sans numéro d'inventaire</b>";
			return $return;
			}
		
		$return["numinv"] =  $inventaire_fields["numinv"];
		
		if (!$inventaire_fields["designation"]) {
			$return["error"] = "<b>Objet $object_id sans titre</b>";
			return $return;
		}
			
		if (!$this->checkInventaireByNuminv($inventaire_fields["numinv"])) {
			$this->insert($inventaire_fields);
		} else {
			$return["error"] = "<b>Un objet correspondant au numéro d'inventaire ".$inventaire_fields["numinv"]." est déjà présent dans l'inventaire.</b>";
			return $return;
		}
		return $return;
	}
	
}
