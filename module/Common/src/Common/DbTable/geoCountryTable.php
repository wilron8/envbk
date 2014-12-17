<?php

/**
 * Description of CountryTable
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbTable;

use Common\DbEntity\geoCountry;
use Zend\Db\TableGateway\TableGateway;

class geoCountryTable {

    //put your code here
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($geoCountryId) {
        $rowset = $this->tableGateway->select(array('geoCountry_id' => (int) $geoCountryId));
        $row = $rowset->current();
        return $row;
    }

    /**
     * 
     * @param type $ISO3166
     * @return object
     */
    public function getByISO3166($ISO3166) {
        $rowset = $this->tableGateway->select(array('geoCountry_ISO3166' =>$ISO3166));
        $row = $rowset->current();
        return $row;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(geoCountry $geoCountry) {
        $this->tableGateway->insert($geoCountry->getArrayCopy());
        return $this->tableGateway->lastInsertValue;
    }

    public function update(geoCountry $geoCountry) {
        $this->tableGateway->update($geoCountry->getArrayCopy(), array('geoCountry_id' => $geoCountry->geoCountry_id));
    }

    public function delete($geoCountryId) {
        $this->tableGateway->delete(array('geoCountry_id' => (int) $geoCountryId));
    }

    public function getSelectJSON($start = 0, $limit = 300, $direction = "ASC") {
        $sql = "SELECT geoCountry_id,";
		$sql .= " geoCountry_name,";
		$sql .= " geoCountry_roman,";
		$sql .= " geoCountry_flagImg,";
		$sql .= " geoCountry_callingCode,";
		$sql .= " geoContinent_region,";
		$sql .= " geocontinent_id,";
		$sql .= " geoCountry_ISO3166 ";
		$sql .= " FROM envitz.geoCountry, envitz.geoContinent ";
		$sql .= " WHERE geoCountry_continent=geoContinent_id AND geoCountry_isVisible=1 ";
		$sql .= " ORDER BY geoContinent_region, geoCountry_name $direction;";
		
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

    public function getSelectOptions() {
        $sql = "SELECT geoCountry_id,";
		$sql .= " geoCountry_name,";
		$sql .= " geoCountry_roman,";
		$sql .= " geoContinent_region,";
		$sql .= " geocontinent_id ";
		$sql .= " FROM envitz.geoCountry, envitz.geoContinent ";
		$sql .= " WHERE geoCountry_continent=geoContinent_id AND geoCountry_isVisible=1 ";
		$sql .= " ORDER BY geoContinent_region, geoCountry_name ASC;";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $rows = $statement->execute();
        $old_group = "";
        $options = array();
        $temp_group = array();
        $i = 1;
        foreach ($rows as $row) {
            //start first group
            if ($old_group == "") {
                $old_group = $row['geocontinent_id'];
                $temp_group['label'] = $row['geoContinent_region'];
                $temp_group['options'] = array();
            }

            if ($row['geocontinent_id'] == $old_group) {
                $temp_group['options'][$row['geoCountry_id']] = $row['geoCountry_name'];
                $old_group = $row['geocontinent_id'];
            } else {
                // add group to options
                $options[] = $temp_group;
                // reset group
                $temp_group['label'] = $row['geoContinent_region'];
                $temp_group['options'] = array();
                $temp_group['options'][$row['geoCountry_id']] = $row['geoCountry_name'];
                $old_group = $row['geocontinent_id'];
            }
            // add group to options if it is last item
            if (count($rows) == $i) {
                $options[] = $temp_group;
            }
            $i++;
        }
        return $options;
    }

}
