<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserLoginTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserLogin;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class UserLoginTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getById($id) {
        $rowset = $this->tableGateway->select(array('uLgin_id' => (int) $id));
        $row = $rowset->current();
        return $row;
    }

    public function getByUserId($uLogin_userID) {
        return $this->tableGateway->select(array('uLgin_userID' => (int) $uLogin_userID));
    }

    //start wilron8    
    public function showByUserDetails($uLogin_userID, $withIPCompare = 'yes', $limit = 1, $orderFields = 'uLgin_timeStamp', $orderType = 'DESC'){
        // don't forget to add "use Zend\Db\Sql\Select;" at the top
        $rowset = $this->tableGateway->select(function(Select $select) use ($uLogin_userID, $withIPCompare, $limit, $orderFields, $orderType) {
                    $select->where(array('uLgin_userID' => $uLogin_userID));
                    if($withIPCompare == 'yes'):
                        $select->where->notEqualTo('uLgin_ip', $this->getTheRealUserIP());  
                    endif;                  
                    $select->order("$orderFields $orderType");
                    if($limit > 0):
                        $select->limit($limit);
                    endif;
                });

        /*$datarow = array();
       
        foreach($rowset as $row):
            $datarow[] = $row;
        endforeach;
                   
        return $datarow;*/
        return $rowset;
    }

    public function removeByCustomFields(array $whereFields){
        $this->tableGateway->delete($whereFields);
    }
    
    public function showByCustomFields(array $whereFields){        
        $rowset = $this->tableGateway->select($whereFields);
        $row = $rowset->current();
        return $row;
    }

    public function getTheRealUserIP(){
        $ipaddress = '';
        $httpXArr = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        foreach($httpXArr as $httpServerType):
            if(getenv($httpServerType)):
                $ipaddress = getenv($httpServerType);
                break;
            endif;
        endforeach;

        return $ipaddress;
    }  

    //end wilron8

    /**
     *  Get last login of a user for the last 24hours
     * @param int $uLogin_userID
     * @return object 
     */
    public function getLastLogin($uLogin_userID) {
        $rowset = $this->tableGateway->select(function(Select $select) use ($uLogin_userID) {
                    $select->where(array('uLgin_userID' => $uLogin_userID))
                            ->where("uLgin_timeStamp > DATE_SUB(NOW(),INTERVAL 24 HOUR)");
                    $select->order('uLgin_timeStamp DESC');
                    $select->limit(1);
                });
        return $rowset->current();
    }

    public function insert(UserLogin $uLogin) {
        $this->tableGateway->insert($uLogin->getArrayCopy());
    }

    public function update(UserLogin $uLogin) {
        $this->tableGateway->update($uLogin->getArrayCopy(), array('uLogin_id' => $uLogin->uLgin_id));
    }

    public function delete($uLogin_id) {
        $this->tableGateway->delete(array('uLgin_id' => (int) $uLogin_id));
    }

}

?>
