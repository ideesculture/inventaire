<?php
// module/Depot/src/Depot/Model/DepotTable.php:
namespace Depot\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Depot\Form\DepotForm;

define('__CA_DONT_DO_SEARCH_INDEXING__',1);

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
	
	private function preloadCaDirect($setup_path) {
		$path = getcwd();
		
		// AUTHENTIFICATION
		chdir($setup_path);
		$result = include($setup_path."/setup.php");
		chdir($path);
		if ($result) {
			require_once(__CA_LIB_DIR__.'/core/Db.php');
			return true;
		} else {
			return false;
		}
	}

	private function convertcurrency($from, $to, $amount)
	{
		$url = "http://currency-api.appspot.com/api/$from/$to.json?key=9fa0b032430252b51c673ba5076593943b83a18e&amount=$amount";
		//print $url;die();
		$result = file_get_contents($url);
		$result = json_decode($result);
	
		if ($result->success)
		{
			return $result->amount;
		} else {
			return false;
		}
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
	
	public function fetchAll()
	{
		$resultSet = $this->select();
		if ($year) {
			$resultSet = $this->select()->where("YEAR(date_inscription) = ".$year);
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

	public function fetchAllFullInfos($year = null)
	{
		
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('inventaire_depot_photo', 'inventaire_depot.id = depot_id', array('credits','file'),'left');
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		$select->order("numdepot_sort ASC");
		//you can check your query by echo-ing :
		//echo $select->getSqlString();
		$statement = $sql->prepareStatementForSqlObject($select);
	
		$resultSet = new ResultSet();
	
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
		
	public function fetchAllFullInfosPaginator($filtre=array())
		{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('inventaire_depot_photo', 'inventaire_depot.id = depot_id', array('credits','file'),'left');
		if (isset($filtre["brouillon"]) && ($filtre["brouillon"]==0)) {
			$select->where("validated = 1");
		} 
		if (isset($filtre["year"]) && $filtre["year"]>0) {
			$select->where("YEAR(date_inscription) = ".$filtre["year"]);
		}
		if (isset($filtre["numdepot"]) && $filtre["numdepot"]) {
			$select->where->like('numdepot_display', $filtre["numdepot"]."%");
		}
		if (isset($filtre["designation"])  && $filtre["designation"]) {
			$designation_words = explode(" ",$filtre["designation"]);
			foreach($designation_words as $designation_word) {
				$designation_word = "%".$designation_word."%";
				$select->where->like('designation_display', $designation_word);
			}
		}
		$select->order("numdepot_sort ASC");
		
		//Vérification de la requête en décommentant la ligne ci-dessous
		//echo $select->getSqlString();
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
		->join('inventaire_depot_photo', 'inventaire_depot.id = depot_id', array('credits','file'),'left');
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
	
	public function checkDepotByNumdepot($numdepot)
	{

		$rowset = $this->select(array(
				'numdepot' => $numdepot,
		));

		$row = $rowset->current();

		if (!$row) {
			return false;
		}
		
		return true;
	}
	
	public function checkDepotByCaId($ca_id)
	{

		$rowset = $this->select(array(
				'ca_id' => $ca_id,
		));

		$row = $rowset->current();

		if (!$row) {
			return false;
		}
		
		return $row;
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
	
	public function saveDepot(Depot $depot)
	{
		$data = array(
				'ca_id' => $depot->ca_id,
				'numdepot' => $depot->numdepot,
				'numdepot_sort' => $depot->numdepot_sort,
				'numdepot_display' => $depot->numdepot_display,
				'numinv' => $depot->numinv,
				'actedepot' => $depot->actedepot,//3
				'date_priseencharge' => $depot->date_priseencharge,//4
				'proprietaire' => $depot->proprietaire,//5
				'actefindepot' => $depot->actefindepot,//6
				'date_inscription' => $depot->date_inscription,//7
				'date_inscription_display' => $depot->date_inscription_display,//7
				'designation' => $depot->designation,//8
				'designation_display' => $depot->designation_display,//8
				'inscription' => $depot->inscription,//9
				'materiaux' => $depot->materiaux,//10
				'mesures' => $depot->mesures,//12
				'etat' => $depot->etat,//13
				'auteur'  => $depot->auteur,//14
				'auteur_display'  => $depot->auteur_display,//14
				'epoque' => $depot->epoque,//15
				'usage' => $depot->usage,//16
				'provenance' => $depot->provenance,//17
				'observations' => $depot->observations,//18
				'validated'=> $depot->validated
		);

		$id = (int) $depot->id;
		
		if ($id == 0) {
			if (($depot->ca_id) && ($conflit=$this->checkDepotByCaId($depot->ca_id))) {
				throw new \Exception("L'enregistrement ".$conflit->numdepot." ".$conflit->designation." est déjà lié à cet objet de CollectiveAccess : <small>id = ".$depot->ca_id."</small>");
			}
			if (!$this->checkDepotByNumdepot($depot->numdepot)) {
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

	/**
	 * checkCaAllowedType() : vérifie si le type de l'objet dans CollectiveAccess correspond à un dépôt
	 * 
	 * @param int $ca_id l'identifiant de l'objet dans CA
	 * @param array $caDirectConfig la configuration de connexion à l'installation de CA
	 * @throws \Exception si la connexion à CA n'est pas possible ou qu'aucune valeur n'est définie pour ca_id
	 * @return boolean vrai si le type est autorisé pour les dépôt (dep...), faux sinon
	 */
	public function checkCaAllowedType($ca_id,$caDirectConfig=array())
	{
		$authorized_types = array("dep","dep_art","dep_other","dep_costume","dep_ethno","dep_archeo","dep_nat","dep_techno");
		
		if(!$caDirectConfig) {
			throw new \Exception("Informations de connexions à CollectiveAccess manquantes");
		}
		if(!$ca_id) {
			throw new \Exception("checkCaAllowedType() : Aucun identifiant défini");
		}
		
		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
		
		include_once(__CA_MODELS_DIR__."/ca_objects.php");
		
		$t_object = new \ca_objects($ca_id);
		$t_object->setMode(ACCESS_READ);
		
		if(in_array($t_object->getTypeCode(), $authorized_types)) {
			return true;
		}
		return false;
	}

	public function getFieldsName()
	{
		return array(
			"numdepot", //1
			"numinv",//2
			"actedepot",//3
			"date_priseencharge",//4
			"proprietaire",//5
			"actefindepot",//6
			"date_inscription",//7
			"designation",//8
			"inscription",//9
			"materiaux",//10
			"mesures",//12
			"etat",//13
			"auteur",//14
			"epoque",//15
			"usage",//16
			"provenance",//17
			"observations" //18
		);
	}
	
	public function getMandatoryFieldsName()
	{
		return array(
			"numdepot", //1
			"numdepot_sort", 
			"numdepot_display",
			"numinv",//2
			"actedepot",//3
			"date_priseencharge",//4
			"proprietaire",//5
			"actefindepot",//6
			"date_inscription",//7
			"date_inscription_display",
			"designation",//8
			"designation_display",
			"inscription",//9
			"materiaux",//10
			"mesures",//12
			"etat" //13
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
			"numdepot" => "Numéro de dépôt", //1
			"numinv" => "Numéro d'inventaire",//2
			"actedepot" => "Acte de dépôt",//3
			"date_priseencharge" => "Date de prise en charge",//4
			"proprietaire" => "Propriétaire",//5
			"actefindepot" => "Acte de fin de dépôt",//6
			"date_inscription" => "Date d'inscription",//7
			"designation" => "Désignation",     //8
			"inscription" => "Inscription",     //9
			"materiaux" => "Matériaux/Techniques",     //10
			"mesures" => "Mesures",     //12
			"etat" => "Etat",     //13
			"auteur" => "Auteur",     //14
			"epoque" => "Epoque",     //15
			"usage" => "Usage",     //16
			"provenance" => "Provenance",     //17
			"observations" => "Observations"   //18
		);
	}
	
	
	public function validateDepot(Depot $depot, $caDirectConfig = array(), $options=array())
	{
		$id = (int) $depot->id;
		if(($depot->ca_id) && (isset($options["updateCaDate"])) && ($options["updateCaDate"] == true)) {
			if(!$this->preloadCaDirect($caDirectConfig["path"])) {
				throw new \Exception("Impossible d'accéder à CollectiveAccess.");
			}

			include_once(__CA_MODELS_DIR__."/ca_locales.php");
			include_once(__CA_MODELS_DIR__."/ca_objects.php");
			include_once(__CA_MODELS_DIR__."/ca_lists.php");
			include_once(__CA_MODELS_DIR__."/ca_attributes.php");
			
			$t_locale = new \ca_locales();
			$locale_id = $t_locale->loadLocaleByCode('fr_FR'); // Stockage explicite en français
			
			$t_object = new \ca_objects($depot->ca_id);
			$t_object->setMode(ACCESS_WRITE);
			$t_object->removeAttributes("date_depot");
			
			$t_object->addAttribute(array("date_depot" => date("Y-m-d")),"date_depot");
			$t_object->update();
			if ($t_object->numErrors()) throw new \Exception("Impossible de définir la nouvelle date d'ajout à l'depot.");
			unset($t_object);
		}
		$this->update(
			// set
			array("validated" => 1),
			// where
			"id = $id");
		$this->update(
			// set
			array("date_inscription" => date("Y/m/d")),
			// where
			"id = $id");
		$this->update(
			// set
			array("date_inscription_display" => date("Y/m/d")),
			// where
			"id = $id");
	}	
	
	public function unvalidateDepot(Depot $depot)
	{
		$id = (int) $depot->id;
		$this->update(
			// set
			array("validated" => 0),
			// where
			"id = $id");
	}	
	
	public function deleteDepot($id)
	{
		$this->delete(array(
				'id' => $id,
		));
	}
			
	public function caDirectImportObject($ca_id, array $caDirectConfig, $depot_id = 0)
	{
		$return = array();
	
		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
		$depot = new Depot();
		$depot->id = $depot_id;
		$depot->ca_id = $ca_id;
		$depot->validated = 0;
		$mappings = $caDirectConfig["depot"];
		
		include_once(__CA_MODELS_DIR__."/ca_locales.php");
		include_once(__CA_MODELS_DIR__."/ca_objects.php");
		include_once(__CA_MODELS_DIR__."/ca_lists.php");
		
		$t_locale = new \ca_locales();
		$locale_id = $t_locale->loadLocaleByCode('fr_FR'); // Stockage explicite en français
		
		$t_object = new \ca_objects($ca_id);
		$t_object->setMode(ACCESS_WRITE);
		$response_global = "";
		// ATTRIBUTS
		foreach ($mappings as $target => $fields) {
			$response_global = "";
			foreach($fields as $attribute) {
				$response = "";
				$field = $attribute["field"];
				$data = explode(".",$field);
				switch($data[0]) {
					case "ca_entities" :
						$entities = $t_object->getRelatedItems("ca_entities",array("restrictToRelationshipTypes"=>$attribute["relationshipTypes"]));
						foreach($entities as $entity) {
							$response = ($response ? $response.", " : "").$entity["displayname"];
						}
						break;
					case "ca_places" :
						$places = $t_object->getRelatedItems("ca_places",array("restrictToRelationshipTypes"=>$attribute["relationshipTypes"]));
						foreach($places as $place) {
							$response = ($response ? $response.", " : "").$place["name"];
						}
						break;
					case "ca_objects" :
					default:
						if ($field != "ca_objects.nonpreferred_labels") {
							// GESTION DES OPTIONS POUR LE get()
							$options = array("convertCodesToDisplayText"=>"true", "locale"=>$locale_id);
							if ($attribute["options"]) $options = array_merge($options,$attribute["options"]);
							// RECUPERATION DU CHAMP POUR L'AFFICHAGE
							
							$response = $t_object->get($field, $options);
							// POST-TRAITEMENT
							if (($attribute["post-treatment"]) && ($response)) {
								switch($attribute["post-treatment"]) {
									// Conversion monétaire
									case 'convertcurrencytoeuros' :
										if ($response) {
											preg_match('/([[:graph:]]*) ([[:graph:]]*)/i',$response, $matches);
											if ($matches[1] != "EUR") {
												$conversionresult = $this->convertcurrency($matches[1], "EUR", $matches[2]);
												if($conversionresult) {
													$response=$conversionresult;
												} else {
													throw new \Exception("Erreur dans la conversion de devise de ".$response." en euros ($response).");
												}
											} else {
												$response = $matches[2];
											}
											// Remplacement du point par la virgule
											$response = str_replace(".", ",",$response)." €";
										}
										break;
										// Conversion vers une date au format JJ/MM/AAAA
									case 'caDateToUnixTimestamp' :
										$response = date('Y/m/d',caDateToUnixTimestamp($response));
										break;
										// Post-traitement non reconnu
									default :
										throw new \Exception("Post-traitement non reconnu : ".$attribute["post-treatment"]);
								}
							}
						} else {
							// non preferred_labels
							$nonpreferred_labels = $t_object->get('ca_objects.nonpreferred_labels', array('returnAsArray' => true));
							if (sizeof($nonpreferred_labels)>0) { $nonpreferred_labels = reset($nonpreferred_labels); }
							//var_dump($attribute["otherLabelTypeId"]);
							//var_dump($nonpreferred_labels);
							//die();
							if (sizeof($nonpreferred_labels)>0) {
								foreach($nonpreferred_labels as $nonpreferred_label) {
									if ($nonpreferred_label["type_id"] == $attribute["otherLabelTypeId"]) {
										$response .= ($response ? ", ": "").$nonpreferred_label["name"];
									}
								}
							}
						}
						break;
				}
				$response_global .= ($response ? $attribute["prefixe"].$response.$attribute["suffixe"] : "");
			} 
			// DEFINITION DE L'ATTRIBUT
			$depot->$target = !$response_global ? "non renseigné" : $response_global;
		}
		if (!$depot->numdepot) {
			return new \Exception("Objet $ca_id sans numéro de depot");
		}
		$return["numdepot"]=$depot->numdepot;
		
		if (!$depot->designation) {
			return new \Exception("Objet $ca_id sans titre");
			}
		$return["designation"]=$depot->designation;
		
		$result = $this->saveDepot($depot);
		
		$depot_id = $this->checkDepotByCaId($ca_id)->id;
		if(!$depot_id) {
			return new \Exception("Problème de vérification depot_id.");
		}
		
		$return["id"]=$depot_id;
		
		$t_object->removeAttributes("depot_id");

		if ($t_object->numErrors()) throw new \Exception("Impossible de supprimer la date d'ajout à l'depot déjà renseignée.");
		//$t_object->addAttribute(array("depot_id" => $depot_id),"depot_id");
		$t_object->update();
		
		return $return;
	}


	/**
	 * caGetSetItems : retourne les identifiants dans CA des objets contenus dans un set
	 * 
	 * @param $ca_set_id : id du set dans CollectiveAccess
	 * @param array $caDirectConfig : informations de connexion locale à CA
	 * @return \Exception|array : exception si erreur, sinon liste des ca_id des objets contenus dans le set
	 */
	public function caGetSetItems($ca_set_id, array $caDirectConfig)
	{
		$return = array();
		
		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			return new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
		
		include_once(__CA_MODELS_DIR__."/ca_sets.php");
			
		$t_set = new \ca_sets($ca_set_id);
		
		$type = $t_set->getSetContentTypeName();
		
		if($type != "object") {
			return new \Exception("L'ensemble choisi contient d'autres enregistrements que des objets.");
		}
		
		
		if(sizeof($t_set->getItemRowIDs())) {
			foreach($t_set->getItemRowIDs() as $ca_id=>$void) {
				$ca_ids[]=$ca_id;
			}
			return $ca_ids;
		} else {
			return new \Exception("L'ensemble choisi ne contient aucun objet.");
		}
	}
	
	public function caDirectImportSet($ca_set_id, array $caDirectConfig)
	{
		$return = array();

		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}

		include_once(__CA_MODELS_DIR__."/ca_sets.php");
			
		$t_set = new \ca_sets($ca_set_id);
		
		$type = $t_set->getSetContentTypeName();
	
		if($type != "object") {
			return new \Exception("L'ensemble choisi contient d'autres enregistrements que des objets.");
		}

		$ca_ids = $t_set->getItemRowIDs();
		if(sizeof($ca_ids)) {
			foreach($ca_ids as $ca_id=>$void) {
				$return[$ca_id] = $this->caDirectImportObject($ca_id, $caDirectConfig);
			}
		} else {
			return new \Exception("L'ensemble choisi ne contient aucun objet.");
		}
		return $return;
	}
	
}