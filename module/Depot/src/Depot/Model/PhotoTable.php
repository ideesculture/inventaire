<?php
// module/Depot/src/Depot/Model/PhotoTable.php:
namespace Depot\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class PhotoTable extends AbstractTableGateway
{
	protected $table ='inventaire_depot_photo';

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
		->join('inventaire_depot', 'inventaire_depot_photo.depot_id = inventaire_depot.id');
				
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
		->join('inventaire_depot', 'inventaire_depot_photo.depot_id = inventaire_depot.id');
				
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

   public function getPhotoFullInfosByDepotId($id)
   {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from($this->table)
		->join('inventaire_depot', 'inventaire_depot_photo.depot_id = inventaire_depot.id');

        $where = new  Where();
        $where->equalTo('depot_id', $id) ;
        $select->where($where);

        //you can check your query by echo-ing :
        // echo $select->getSqlString();
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }
		
	public function getPhotoByDepotId($depot_id)
	{
		$depot_id  = (int) $depot_id;

		$rowset = $this->select(array(
				'depot_id' => $depot_id,
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
				'depot_id' => $photo->depot_id,
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
				"depot_id",     //2
				"credits",     //3
				"file"
		);
	}
	
	public function getFieldsHumanName()
	{
		return array(
				"id" => "Identifiant", //1
				"depot_id" => "Identifiant de l'entrée d'depot correspondante",     //2
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
	
	public function deletePhotoByDepotId($depot_id)
	{
		$this->delete(array(
				'depot_id' => $depot_id,
		));
	}

	
	public function caDirectImportPhoto($ca_id, $depot_id, array $caDirectConfig)
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
		$return["depot_id"]=$depot_id;
		
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
		$photo->exchangeArray(array("id" => 0, "depot_id" => $depot_id,  "credits" => "", "file" => $file));
		// saving photo info into database
		$return["saved"] = $this->savePhoto($photo);
		return $return;
	}
	
}