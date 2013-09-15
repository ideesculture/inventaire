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

define('__CA_DONT_DO_SEARCH_INDEXING__',1);

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
		->join('inventaire_inventaire_photo', 'inventaire_inventaire.id = inventaire_id', array('credits','file'),'left');
		if ($year) {
			$select->where("YEAR(date_inscription) = ".$year);
		}
		$select->order("numinv_sort ASC");
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
		->join('inventaire_inventaire_photo', 'inventaire_inventaire.id = inventaire_id', array('credits','file'),'left');
		if (isset($filtre["brouillon"]) && ($filtre["brouillon"]==0)) {
			$select->where("validated = 1");
		} 
		if (isset($filtre["year"]) && $filtre["year"]>0) {
			$select->where("YEAR(date_inscription) = ".$filtre["year"]);
		}
		if (isset($filtre["numinv"]) && $filtre["numinv"]) {
			$select->where->like('numinv_display', $filtre["numinv"]."%");
		}
		if (isset($filtre["designation"])  && $filtre["designation"]) {
			$designation_words = explode(" ",$filtre["designation"]);
			foreach($designation_words as $designation_word) {
				$designation_word = "%".$designation_word."%";
				$select->where->like('designation_display', $designation_word);
			}
		}
		$select->order("numinv_sort ASC");
		
		//Vérification de la requête en décommentant la ligne ci-dessous
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
		->join('inventaire_inventaire_photo', 'inventaire_inventaire.id = inventaire_photo.inventaire_id', array('credits','file'),'left');
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
	
	public function checkInventaireByCaId($ca_id)
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
	
	public function saveInventaire(Inventaire $inventaire)
	{
		$data = array(
				'ca_id' => $inventaire->ca_id,
				'numinv' => $inventaire->numinv,
				'numinv_sort' => $inventaire->numinv_sort,
				'numinv_display' => $inventaire->numinv_display,
				'designation' => $inventaire->designation,//8
				'designation_display' => $inventaire->designation_display,//8
				'mode_acquisition' => $inventaire->mode_acquisition,//2
				'donateur' => $inventaire->donateur,//3
				'date_acquisition' => $inventaire->date_acquisition,//4
				'avis' => $inventaire->avis,//5
				'prix' => $inventaire->prix,//6
				'date_inscription' => "",
				'date_inscription_display' => "",//7
				'inscription' => $inventaire->inscription,//9
				'materiaux' => $inventaire->materiaux,//10
				'techniques' => $inventaire->techniques,//11
				'mesures' => $inventaire->mesures,//12
				'etat' => $inventaire->etat,//13
				'auteur'  => $inventaire->auteur,//14
				'auteur_display'  => $inventaire->auteur_display,//14
				'epoque' => $inventaire->epoque,//15
				'usage' => $inventaire->usage,//16
				'provenance' => $inventaire->provenance,//17
				'observations' => $inventaire->observations,//18
				'validated'=> $inventaire->validated
		);

		$id = (int) $inventaire->id;
		
		if ($id == 0) {
			if (($inventaire->ca_id) && ($conflit=$this->checkInventaireByCaId($inventaire->ca_id))) {
				throw new \Exception("L'enregistrement ".$conflit->numinv." ".$conflit->designation." est déjà lié à cet objet de CollectiveAccess : <small>id = ".$inventaire->ca_id."</small>");
			}
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
		$authorized_types = array("acq","acq_art","acq_other","acq_costume","acq_ethno","acq_archeo","acq_nat","acq_techno");
	
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
	
	public function getMandatoryFieldsName()
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
				"etat"     //13
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
	
	
	public function validateInventaire(Inventaire $inventaire, $caDirectConfig = array(), $options=array())
	{
		$id = (int) $inventaire->id;
		if(($inventaire->ca_id) && (isset($options["updateCaDate"])) && ($options["updateCaDate"] == true)) {
			if(!$this->preloadCaDirect($caDirectConfig["path"])) {
				throw new \Exception("Impossible d'accéder à CollectiveAccess.");
			}

			include_once(__CA_MODELS_DIR__."/ca_locales.php");
			include_once(__CA_MODELS_DIR__."/ca_objects.php");
			include_once(__CA_MODELS_DIR__."/ca_lists.php");
			include_once(__CA_MODELS_DIR__."/ca_attributes.php");
			
			$t_locale = new \ca_locales();
			$locale_id = $t_locale->loadLocaleByCode('fr_FR'); // Stockage explicite en français
			$t_list = new \ca_lists();
			$object_type = $t_list->getItemIDFromList('object_types', 'art');
			$t_attribute = new \ca_attributes();
			
			$t_object = new \ca_objects($inventaire->ca_id);
			$t_object->setMode(ACCESS_WRITE);
			$t_object->removeAttributes("date_inventaire");
			
			$t_object->addAttribute(array("date_inventaire" => date("Y-m-d")),"date_inventaire");
			$t_object->update();
			if ($t_object->numErrors()) throw new \Exception("Impossible de définir la nouvelle date d'ajout à l'inventaire.");
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
	
	public function unvalidateInventaire(Inventaire $inventaire)
	{
		$id = (int) $inventaire->id;
		$this->update(
			// set
			array("validated" => 0),
			// where
			"id = $id");
		$this->update(
			// set
			array("date_inscription" => null),
			// where
			"id = $id");
		$this->update(
			// set
			array("date_inscription_display" => null),
			// where
			"id = $id");
			
	}	
	
	public function deleteInventaire($id)
	{
		$this->delete(array(
				'id' => $id,
		));
	}
			
	public function caDirectImportObject($ca_id, array $caDirectConfig, $inventaire_id = 0)
	{
		$return = array();
	
		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
		
		$inventaire = new Inventaire();
		$inventaire->id = $inventaire_id;
		$inventaire->ca_id = $ca_id;
		$inventaire->validated = 0;
		$mappings = $caDirectConfig["inventaire"];
		
		include_once(__CA_MODELS_DIR__."/ca_locales.php");
		include_once(__CA_MODELS_DIR__."/ca_objects.php");
		include_once(__CA_MODELS_DIR__."/ca_lists.php");
		
		$t_locale = new \ca_locales();
		$locale_id = $t_locale->loadLocaleByCode('fr_FR'); // Stockage explicite en français
		$t_list = new \ca_lists();
		$object_type = $t_list->getItemIDFromList('object_types', 'art');
		
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
						break;
				}
				$response_global .= ($response ? $attribute["prefixe"].$response.$attribute["suffixe"] : "");
			} 
			// DEFINITION DE L'ATTRIBUT
			$inventaire->$target = !$response_global ? "non renseigné" : $response_global;
		}
		if (!$inventaire->numinv) {
			return new \Exception("Objet $ca_id sans numéro d'inventaire");
		}
		$return["numinv"]=$inventaire->numinv;
		
		if (!$inventaire->designation) {
			return new \Exception("Objet $ca_id sans titre");
			}
		$return["designation"]=$inventaire->designation;
		//var_dump($inventaire);die();
		$result = $this->saveInventaire($inventaire);
		
		$inventaire_id = $this->checkInventaireByCaId($ca_id)->id;
		if(!$inventaire_id) {
			return new \Exception("Problème de vérification inventaire_id.");
		}
		
		$return["id"]=$inventaire_id;
		
		$t_object->removeAttributes("inventaire_id");

		if ($t_object->numErrors()) throw new \Exception("Impossible de supprimer la date d'ajout à l'inventaire déjà renseignée.");
		//$t_object->addAttribute(array("inventaire_id" => $inventaire_id),"inventaire_id");
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