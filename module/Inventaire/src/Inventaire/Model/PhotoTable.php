<?php
// module/Inventaire/src/Inventaire/Model/PhotoTable.php:
namespace Inventaire\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class PhotoTable extends AbstractTableGateway
{
	protected $table ='inventaire_inventaire_photo';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;

		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new Photo());

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
	
	public function fetchAll()
	{
		$resultSet = $this->select();
		return $resultSet;
	}
	
	public function fetchAllFullInfos()
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('inventaire_inventaire', 'inventaire_photo.inventaire_id = inventaire_inventaire.id');
				
		//you can check your query by echo-ing :
		//echo $select->getSqlString();
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$resultSet = new ResultSet();
		
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
	
	
	public function fetchAllFullInfosPaginator()
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table)
		->join('inventaire_inventaire', 'inventaire_photo.inventaire_id = inventaire_inventaire.id');
				
		//you can check your query by echo-ing :
		//echo $select->getSqlString();
		$statement = $sql->prepareStatementForSqlObject($select);
		
		$resultSet = new ResultSet();
		
		$resultSet->initialize($statement->execute());
		$resultSet->buffer();
		$resultSet->next();
		
		return $resultSet;
	}
	
	
	public function getPhoto($id)
	{
		$id  = (int) $id;

		$rowset = $this->select(array(
				'id' => $id,
		));

		$row = $rowset->current();

		if (!$row) {
			throw new \Exception("Could not find row $id");
		}

		return $row;
	}

   public function getPhotoFullInfosByInventaireId($id)
   {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from($this->table)
              ->join('inventaire_inventaire', 'inventaire_photo.inventaire_id = inventaire_inventaire.id');

        $where = new  Where();
        $where->equalTo('inventaire_id', $id) ;
        $select->where($where);

        //you can check your query by echo-ing :
        // echo $select->getSqlString();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
		
	public function getPhotoByInventaireId($inventaire_id)
	{
		$inventaire_id  = (int) $inventaire_id;

		$rowset = $this->select(array(
				'inventaire_id' => $inventaire_id,
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
	
	public function savePhoto(Photo $photo)
	{
		$data = array(
				'id' => $photo->id,
				'inventaire_id' => $photo->inventaire_id,
				'credits' => $photo->credits,
				'file' => $photo->file
		);

		$id = (int) $photo->id;

		if ($id == 0) {
			$this->insert($data);
		} elseif ($this->getPhoto($id)) {
				$this->update(
						$data,
						array(
								'id' => $id,
						)
				);
		} else {
			throw new \Exception("Le formulaire n'existe pas.");
		}
	}
	
	
	public function getFieldsName()
	{
		return array(
				"id", //1
				"inventaire_id",     //2
				"credits",     //3
				"file"
		);
	}
	
	public function getFieldsHumanName()
	{
		return array(
				"id" => "Identifiant", //1
				"inventaire_id" => "Identifiant de l'entrée d'inventaire correspondante",     //2
				"credits" => "Crédits photo",     //3
				"file" => "Fichier"
		);
	}
	
	public function deletePhoto($id)
	{
		$this->delete(array(
				'id' => $id,
		));
	}
	
	public function deletePhotoByInventaireId($inventaire_id)
	{
		$this->delete(array(
				'inventaire_id' => $inventaire_id,
		));
	}

	
	public function caDirectImportPhoto($ca_id, $inventaire_id, array $caDirectConfig)
	{
		$return = array();
		
		if(!$this->preloadCaDirect($caDirectConfig["path"])) {
			throw new \Exception("Impossible d'accéder à CollectiveAccess.");
		}
		
		include_once(__CA_MODELS_DIR__."/ca_locales.php");
		include_once(__CA_MODELS_DIR__."/ca_objects.php");
		
		$t_object = new \ca_objects($ca_id);
		$t_object->setMode(ACCESS_READ);

		$return["ca_id"]=$ca_id;
		$return["inventaire_id"]=$inventaire_id;
		
		// Fetching primary media info
		$media = $t_object->getPrimaryRepresentation(array('large'));
		if ($media) {
			// if we've a media, copy it
			if (!copy(
					$media["paths"]["large"],
					dirname(__DIR__).'/../../../../public/files/assets/'.basename($media["paths"]["large"])
			)) {
				// copy has crashed
				throw new \Exception("Impossible de recopier le fichier image dans public/files/assets.");
			}
		} else {
			// no media defined
			$return["error"]="Pas de représentation primaire";
			return $return;
		}

		$file = basename($media["paths"]["large"]);
		$return["file"] = $file;
		
		$photo = new Photo();
		$photo->exchangeArray(array("id" => 0, "inventaire_id" => $inventaire_id,  "credits" => "", "file" => $file));
		// saving photo info into database
		$return["saved"] = $this->savePhoto($photo);
		return $return;
	}
	
}