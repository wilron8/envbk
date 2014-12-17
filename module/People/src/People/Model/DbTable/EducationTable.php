<?php

/**
 * Description of EducationTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use People\Model\DbEntity\Education;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class EducationTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($edId){
        return $this->tableGateway->select(array('ed_id'=>(int)$edId))->current();   
    }

    /**
     * 
     * @param \Users\Model\Education $education
     * @param integer $userID
     */
    public function insert(Education $education, $userID) {
        //first create a cv
        $sql = new Sql($this->tableGateway->getAdapter());
        $insertCv = $sql->insert('CV');
        $insertCv->values(array('cv_userID' => $userID));
        $insertString = $sql->getSqlStringForSqlObject($insertCv);
        $this->tableGateway->getAdapter()->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        $cvID = $this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();
        $education->ed_cvID = $cvID;

        return $this->tableGateway->insert($education->getArrayCopy());
    }

    /**
     * Fetch education list for a user
     * 
     * @param integer $userId
     * @return resultSet
     */
    public function fetchByUser($userId) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('CV');
        $select->columns(array());
        $select->join('education', 'education.ed_cvID=CV.cv_id',array('ed_id','ed_name','ed_major','ed_toDate','ed_fromDate'));
        $select->where(array('cv_userID' => $userId));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultset = new ResultSet;
        $output = $resultset->initialize($result);
        return $output;
    }
    public function update(Education $education){
      return  $this->tableGateway->update($education->getArrayCopy(), array('ed_id'=>$education->ed_id));
    }
    
    public function delete($educationId){
        return $this->tableGateway->delete(array('ed_id'=>$educationId));
    }
}
