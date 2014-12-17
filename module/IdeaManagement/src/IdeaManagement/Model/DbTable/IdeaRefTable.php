<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IdeaRefTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use IdeaManagement\Model\DbEntity\IdeaRef;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class IdeaRefTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getByNewIdea($idea_id) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select(array('ir' => 'ideaReference'));
        $select->columns(array('iRef_id', 'iRef_srcIdea'));
        $select->join(array('i' => 'idea'), 'i.idea_id=ir.iRef_srcIdea', array('idea_id', 'idea_title', 'idea_img'));
        $select->where(array('ir.iRef_newIdea' => $idea_id));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }
    
    public function getById($iRef_id) {
        $rowset = $this->tableGateway->select(array('iRef_id' => (int) $iRef_id));
        return $rowset->current();
    }

    public function getBySrcIdea($idea_id) {
        $rowset = $this->tableGateway->select(array('iRef_srcIdea' => (int) $idea_id));
        return $rowset;
    }

    public function fetchAll($where = NULL) {
        if ($where == NULL) {
            $rowset = $this->tableGateway->select();
        } else {
            $rowset = $this->tableGateway->select($where);
        }
        return $rowset;
    }

    public function insert(IdeaRef $ideaRef) {
        $ideaRef->iRef_timeStamp = date('Y-m-d H:i:s');
        $this->tableGateway->insert($ideaRef->getArrayCopy());
    }

    public function update(IdeaRef $ideaRef) {
        $this->tableGateway->update($ideaRef->getArrayCopy(), array('iRef_id' => $ideaRef->iRef_id));
    }

    public function delete($iRef_id) {
        $this->tableGateway->delete(array('iRef_id' => (int) $iRef_id));
    }

    public function deleteByCon($where) {
        $this->tableGateway->delete($where);
    }

    /**
     * 
     * @param type $ideaId
     * @return array
     */
    public function fetchRefAsArray($ideaId) {
        $refs = $this->getByNewIdea($ideaId);
        $list = [];
        foreach ($refs as $ref) {
            $list[] = (int) $ref['iRef_srcIdea'];
        }
        return $list;
    }

}
