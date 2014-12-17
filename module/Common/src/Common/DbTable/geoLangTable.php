<?php

/**
 * Description of geoLangTable
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbTable;

use Users\Model\DbEntity\geoLang;
use Zend\Db\TableGateway\TableGateway;

class geoLangTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($geoLang_id) {
        $rowset = $this->tableGateway->select(array('geoLang_id' => (int) $geoLang_id));
        $row = $rowset->current();
        return $row;
    }
    
    /**
     * 
     * @param string $iso639
     * @return object
     */
    public function getByISO639($iso639){
        $rowset = $this->tableGateway->select(array('geoLang_ISO639' => $iso639));
        $row = $rowset->current();
        return $row;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(geoLang $geoLang) {
        $this->tableGateway->insert($geoLang->getArrayCopy());
    }

    public function update(geoLang $geoLang) {
        $this->tableGateway->update($geoLang->getArrayCopy(), array('geoLang_id' => $geoLang->geoLang_id));
    }

    public function delete($geoLang_id) {
        $this->tableGateway->delete(array('geoLang_id' => (int) $geoLang_id));
    }

    public function getSelectOptions() {
        $rows = $this->tableGateway->select(array('geoLang_isVisible' => 1));
        $options = array();
        
        foreach ($rows as $row) {
            $options[$row->geoLang_id] = $row->geoLang_name;
        }
        return $options;
    }
    
    public function getSelectLangSupport() {
        $rows = $this->tableGateway->select(array('geoLang_isSupported' => 1,'geoLang_isVisible' => 1));
        $options = array();
        
        foreach ($rows as $row) {
            $options[$row->geoLang_id] = $row->geoLang_name;
        }
        return $options;
    }


    public function getSelectJSON($start = 0, $limit = 100, $direction = "ASC") {
        $sql = "SELECT geoLang_id,";
		$sql .= " geoLang_name,";
		$sql .= " geoLang_roman,";
		$sql .= " geoLang_isRTL,";
		$sql .= " geoLang_ISO639,";
            $sql .= " geoLang_isVisible,";
            $sql .= " geoLang_isSupported";
		$sql .= " FROM geoLang ";
		$sql .= " WHERE geoLang_isVisible = 1 ";
		$sql .= " ORDER BY geoLang_roman $direction ";
        $sql .= " LIMIT $start,$limit ;";

        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }

    public function getSysLangJSON($start = 0, $limit = 50, $direction = "ASC") {
        $sql = "SELECT geoLang_id,";
		$sql .= " geoLang_name,";
		$sql .= " geoLang_roman,";
		$sql .= " geoLang_isRTL,";
		$sql .= " geoLang_ISO639";
		$sql .= " FROM geoLang ";
		$sql .= " WHERE geoLang_isVisible = 1 AND geoLang_isSupported = 1";
		$sql .= " ORDER BY geoLang_roman $direction ";
        $sql .= " LIMIT $start,$limit ;";

        $statement = $this->tableGateway->getAdapter()->query($sql);
        $dataSrc = $statement->execute();
        return $dataSrc;
    }
}

?>
