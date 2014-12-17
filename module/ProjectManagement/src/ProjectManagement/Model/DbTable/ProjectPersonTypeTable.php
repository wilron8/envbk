<?php

/**
 * Description of ProjectPersonTypeTable
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbTable;

use ProjectManagement\Model\DbEntity\ProjectPersonType;
use Zend\Db\TableGateway\TableGateway;

class ProjectPersonTypeTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    /**
     * 
     * @param type $where
     * @return result set
     */
    public function select($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select();
        } else {
            return $this->tableGateway->select($where);
        }
    }

    public function getById($pMem_id) {
        $rowset = $this->tableGateway->select(array('prjPType_id' => (int) $pMem_id));
        $row = $rowset->current();
        return $row;
    }

    public function insert(ProjectPersonType $projectPersonType) {
        $this->tableGateway->insert($projectPersonType->getArrayCopy());
    }

    public function update(ProjectPersonType $projectPersonType) {
        $this->tableGateway->update($projectPersonType->getArrayCopy(), array('prjPType_id' => $projectPersonType->prjPType_id));
    }

    public function delete($prjPType_id) {
        $this->tableGateway->delete(array('prjPType_id' => (int) $prjPType_id));
    }

}
