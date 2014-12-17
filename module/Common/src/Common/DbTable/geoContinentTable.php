<?php

/**
 * Description of geoContinentTable
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbTable;

use Common\DbEntity\geoContinent;
use Zend\Db\TableGateway\TableGateway;

class geoContinentTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param string $geoContinent_id
     * @return object
     */
    public function getById($geoContinent_id) {
        $rowset = $this->tableGateway->select(array('geoContinent_id' => (int) $geoContinent_id));
        $row = $rowset->current();
        return $row;
    }
    
    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(geoContinent $geoContinent) {
        $this->tableGateway->insert($geoContinent->getArrayCopy());
    }

    public function update(geoContinent $geoContinent) {
        $this->tableGateway->update($geoContinent->getArrayCopy(), array('geoContinent_id' => $geoContinent->geoContinent_id));
    }

    public function delete($geoContinent_id) {
        $this->tableGateway->delete(array('geoContinent_id' => (int) $geoContinent_id));
    }

    public function getSelectOptions() {
        $rows = $this->tableGateway->select(array('geoContinent_isVisible' => 1));
        $options = array();
        
        foreach ($rows as $row) {
            $options[$row->geoContinent_id] = $row->geoContinent_name;
        }
        return $options;
    }

}

?>
