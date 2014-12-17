<?php

/**
 * Description of FollowIdeaTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use IdeaManagement\Model\DbEntity\FollowIdea;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;

class FollowIdeaTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($fi_id) {
        $id = (int) $fi_id;
        $rowset = $this->tableGateway->select(array('fi_id' => $id));
        $row = $rowset->current();
        return $row;
    }

    /**
     * Check if a user has followed an idea
     * 
     * @param integer $userId
     * @param integer $ideaId
     * @return boolean
     */
    public function isUserFollowIdea($userId, $ideaId) {
        $rowset = $this->tableGateway->select(array('fi_ideaID' => (int) $ideaId, 'fi_userID' => (int) $userId));
        $row = $rowset->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function fetchByFollower($userID) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from(array('i' => 'idea'));
        $select->join('user', 'i.idea_originator=user.usr_id', array('usr_id','usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $select->join('followIdea', 'i.idea_id=followIdea.fi_ideaID');
        $select->join('category', 'i.idea_categoryID=category.cat_id', array('cat_text'), Select::JOIN_LEFT);
        $select->where(array('i.idea_isVisible' => 1, 'fi_userID' => (int) $userID));
        $select->order('idea_lastAccess DESC');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function insert(FollowIdea $followIdea) {
        $data = $followIdea->getArrayCopy();
        return $this->tableGateway->insert($data);
    }

    public function update(FollowIdea $followIdea) {
        $id = (int) $followIdea->fi_id;
        if ($this->getById($id)) {
          return  $this->tableGateway->update($followIdea->getArrayCopy(), array('fi_id' => $id));
        } else {
            throw new \Exception('Idea does not exist');
        }
    }

    public function delete($fi_id) {
       return $this->tableGateway->delete(array('fi_id' => (int) $fi_id));
    }

    /**
     * 
     * @param integer $userId
     * @param integer $ideaId
     */
    public function followIdea($userId, $ideaId) {
        $follow = new FollowIdea();
        $follow->fi_ideaID = $ideaId;
        $follow->fi_userID = $userId;
        $follow->fi_timeStamp = date('Y-m-d H:i:s');
        return $this->insert($follow);
    }

    /**
     * 
     * @param integer $userId
     * @param integer $ideaId
     */
    public function unfollowIdea($userId, $ideaId) {
        return $this->tableGateway->delete(array('fi_userID' => $userId, 'fi_ideaID' => $ideaId));
    }
    
    public function allFollower($ideaId) {
        $rowset = $this->tableGateway->select(array('fi_ideaID' => $ideaId));
        return $rowset;
    }

}
