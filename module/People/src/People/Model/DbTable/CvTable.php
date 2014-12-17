<?php

/**
 * Description of CvTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use Zend\Db\Sql\Sql;

class CvTable {
    
    /**
     *
     * @var Zend\Db\Adapter\Adapter 
     */
    protected $adapter;
    
    public function __construct($adapter) {
        $this->adapter=$adapter;
    }

    public function getUserByCv($cvId){
        $sql = new Sql($this->adapter);
        $select = $sql->select('CV');
        $select->where(array('cv_id'=>$cvId));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row= $result->current();
        if($row){
            return $row['cv_userID'];
        }
        return false;
    }
}
