<?php

/**
 * Description of UserSkillTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use People\Model\DbEntity\UserSkill;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class UserSkillTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($userSkillId) {
        return $this->tableGateway->select(array('uSkll_id' => $userSkillId))->current();
    }

    /**
     * Fetch skill list for a particular user
     * 
     * @param integer $userID
     * @return resultList
     */
    public function fetchByUser($userID, $columns = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('userSkill');
        if ($columns !== NULL) {
            $select->columns($columns);
        }
        $select->join('skillTag', 'skillTag.stag_id=userSkill.uSkll_TagID', array('stag_id', 'stag_text'));
        $select->where(array('uSkll_userID' => $userID));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function insert(UserSkill $userSkill) {
        if ($this->getBySkill($userSkill->uSkll_TagID, $userSkill->uSkll_userID)) {
            return false;
        }
        return $this->tableGateway->insert($userSkill->getArrayCopy());
    }

    public function delete($userSkillId) {
        $this->tableGateway->delete(array('uSkll_id' => $userSkillId));
    }

    public function getBySkill($skillId, $userId) {
        return $this->tableGateway->select(['uSkll_TagID' => $skillId, 'uSkll_userID' => $userId])->current();
    }

}
