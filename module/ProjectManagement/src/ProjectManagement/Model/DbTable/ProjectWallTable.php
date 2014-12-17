<?php

/**
 * Description of ProjectWallTable
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbTable;

use Common\DbTable\AbstractTable;
use Zend\Db\Sql\Sql;

class ProjectWallTable extends AbstractTable {

    protected $table = 'projectWall';
    protected $primaryKey = 'prjW_id';

    public function isOwner($userId, $prjWId) {
        $row = $this->tableGateway->select(array('prjW_userid' => $userId, $this->primaryKey => (int) $prjWId))->current();
        return $row;
    }

    public function getComments($projectId) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('projectWall');
        $select->join('user', 'user.usr_id=projectWall.prjW_userid');
        $select->where(array('prjW_projID' => (int) $projectId, 'prjW_isHidden' => 0));
        $select->order('prjW_timeStamp ASC');
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results;
    }

    public function delete($entityId) {
        $comment = $this->getById($entityId);
        if ($comment->prjW_readOnly == 1) {
            return false;
        }
        $comment->prjW_hideDate = date('Y-m-d H:i:s');
        $comment->prjW_isHidden = 1;
        $this->tableGateway->update($comment->getArrayCopy(), array('prjW_id' => $entityId));
        return true;
    }

}
