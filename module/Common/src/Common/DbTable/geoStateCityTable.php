<?php

/**
 * Description of geoStateCityTable
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbTable;

use Users\Model\DbEntity\geoStateCity;
use Zend\Db\TableGateway\TableGateway;

class geoStateCityTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param string $geoStateCity_id
     * @return object
     */
    public function getById($geoStateCity_id) {
        $rowset = $this->tableGateway->select(array('geoStateCity_id' => (int) $geoStateCity_id));
        $row = $rowset->current();
        return $row;
    }
    
    public function getByISO3166($iso3166){
        $rowset = $this->tableGateway->select(array('geoStateCity_ISO3166' => $iso3166));
        $row = $rowset->current();
        return $row;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(geoStateCity $geoStateCity) {
        $this->tableGateway->insert($geoStateCity->getArrayCopy());
    }

    public function update(geoStateCity $geoStateCity) {
        $this->tableGateway->update($geoStateCity->getArrayCopy(), array('geoStateCity_id' => $geoStateCity->geoStateCity_id));
    }

    public function delete($geoStateCity_id) {
        $this->tableGateway->delete(array('geoStateCity_id' => (int) $geoStateCity_id));
    }


    public function getSelectJSON($id, $start = 0, $limit = 300, $direction = "ASC") {
        $sql = "SELECT geoStateCity_id,";
		$sql .= " geoStateCity_ISO3166,";
		$sql .= " geoStateCity_ISO3166_2,";
		$sql .= " geoStateCity_FIPS10_4,";
		$sql .= " geoStateCity_cityState,";
		$sql .= " geoStateCity_roman,";
		$sql .= " geoStateCity_demonym,";
		$sql .= " geoStateCity_lat,";
		$sql .= " geoStateCity_lng ";
		$sql .= " FROM geoStateCity ";
		$sql .= " WHERE geoStateCity_ISO3166 = '$id' ";
		switch ($id) {
			case 'US':
			case 'CA':
				$sql .= "  AND geoStateCity_ISO3166_2 <> '' ";
				break;
		}
		$sql .= " ORDER BY geoStateCity_roman $direction ";
        $sql .= " LIMIT $start,$limit ;";

        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

}

?>
