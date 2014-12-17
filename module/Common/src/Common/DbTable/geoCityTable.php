<?php

/**
 * Description of geoCityTable
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbTable;

use Common\DbEntity\geoCity;
use Zend\Db\TableGateway\TableGateway;

class geoCityTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param string $geoCity_id
     * @return object
     */
    public function getById($geoCity_id) {
        $rowset = $this->tableGateway->select(array('geoCity_id' => (int) $geoCity_id));
        $row = $rowset->current();
        return $row;
    }
    
    public function getByISO3166($iso3166){
        $rowset = $this->tableGateway->select(array('geoCity_ISO3166' => $iso3166));
        $row = $rowset->current();
        return $row;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(geoCity $geoCity) {
        $this->tableGateway->insert($geoCity->getArrayCopy());
    }

    public function update(geoCity $geoCity) {
        $this->tableGateway->update($geoCity->getArrayCopy(), array('geoCity_id' => $geoCity->geoCity_id));
    }

    public function delete($geoCity_id) {
        $this->tableGateway->delete(array('geoCity_id' => (int) $geoCity_id));
    }

    public function getSelectJSON($id, $prov = NULL, $start = 0, $limit = 7500, $direction = "ASC") {
        $sql = "SELECT geoCity_id,";
		$sql .= " geoCity_ISO3166,";
		$sql .= " geoCity_ISO3166_2,";
		$sql .= " geoCity_cityName,";
		$sql .= " geoCity_PostalCode,";
		$sql .= " geoCity_lat,";
		$sql .= " geoCity_lng,";
		$sql .= " geoCity_metroCode,";
		$sql .= " geoCity_areaCode ";
		$sql .= " FROM geoCity ";
		$sql .= " WHERE geoCity_cityName <> '' AND geoCity_ISO3166 = '$id' ";
		if ( $prov !== NULL ) {
			$sql .= " AND geoCity_ISO3166_2 = '$prov' ";
		}
        //$sql .= " ORDER BY geocity_cityName $direction ";
        $sql .= " GROUP BY geoCity_cityName $direction ";
        $sql .= " LIMIT $start,$limit ;";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

    public function getZipCodesJSON($country, $prov, $city = NULL, $start = 0, $limit = 100, $direction = "ASC") {
		//SELECT geoCity_id AS id, geoCity_areaCode AS `text` FROM envitz.geoCity WHERE geoCity_ISO3166 = '$country' AND geoCity_ISO3166_2 = '$prov' AND geoCity_cityName LIKE '$city' GROUP BY `text` ASC;
        $sql = "SELECT geoCity_id AS id,";
		$sql .= " geoCity_PostalCode AS `text` ";
		$sql .= " FROM geoCity ";
		$sql .= " WHERE geoCity_ISO3166 = '$country' ";
		$sql .= " AND geoCity_ISO3166_2 = '$prov' ";
		if ( $city !== NULL ) {
			$sql .= " AND geoCity_cityName LIKE '$city' ";
		}
        //$sql .= " ORDER BY `text` $direction";
        $sql .= " GROUP BY `text` $direction ";
        $sql .= " LIMIT $start,$limit ;";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

    public function getAreaCodesJSON($country, $prov, $city = NULL, $zip = NULL, $start = 0, $limit = 50, $direction = "ASC") {
		//SELECT geoCity_id AS id, geoCity_areaCode AS `text` FROM envitz.geoCity WHERE geoCity_ISO3166 = '$country' AND geoCity_ISO3166_2 = '$prov' AND geoCity_cityName LIKE '$city' GROUP BY `text` ASC;
        $sql = "SELECT geoCity_id AS id,";
		$sql .= " geoCity_areaCode AS `text` ";
		$sql .= " FROM geoCity ";
		$sql .= " WHERE geoCity_ISO3166 = '$country' ";
		$sql .= " AND geoCity_ISO3166_2 = '$prov' ";
		if ( $city !== NULL ) {
			$sql .= " AND geoCity_cityName LIKE '$city' ";
		}
		if ( $zip !== NULL ) {
			$sql .= " AND geoCity_PostalCode = '$zip' ";
		}
        //$sql .= " ORDER BY `text` $direction";
        $sql .= " GROUP BY `text` $direction ";
        $sql .= " LIMIT $start,$limit ;";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

}

?>
