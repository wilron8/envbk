<?php

/**
 * Description of ExperienceTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use People\Model\DbEntity\Experience;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class ExperienceTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($edId) {
        return $this->tableGateway->select(array('xp_id' => (int) $edId))->current();
    }

    public function insert(Experience $experience, $userID) {
        //first create a cv
        $sql = new Sql($this->tableGateway->getAdapter());
        $insertCv = $sql->insert('CV');
        $insertCv->values(array('cv_userID' => $userID));
        $insertString = $sql->getSqlStringForSqlObject($insertCv);
        $this->tableGateway->getAdapter()->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        $cvID = $this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();
        $experience->xp_cvID = $cvID;

        $this->tableGateway->insert($experience->getArrayCopy());
    }

    /**
     * Fetch education list for a user
     * 
     * @param integer $userId
     * @return resultSet
     */
    public function fetchByUser($userId, $condition = null) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('CV');
        $select->columns(array());
        $select->join('experience', 'experience.xp_cvID=CV.cv_id', array('xp_id', 'xp_name', 'xp_jobTitle', 'xp_fromDate', 'xp_toDate'));
        $select->where(array('cv_userID' => $userId));
        if ($condition !== null && is_array($condition)) {
            $select->where($condition);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function update(Experience $education) {
        $this->tableGateway->update($education->getArrayCopy(), array('xp_id' => $education->xp_id));
    }

    public function delete($experienceId) {
       return $this->tableGateway->delete(array('xp_id' => $experienceId));
    }

}
