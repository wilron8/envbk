<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPassForgotTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserPassForgot;
use Zend\Db\TableGateway\TableGateway;

class UserPassForgotTable {

    protected $tableGateway;
    protected $sm;
    public function __construct(TableGateway $tableGateway,$sm) {
        $this->tableGateway = $tableGateway;
        $this->sm=$sm;
    }
    public function fetchAll(){
        return $this->tableGateway->select();
    }

    public function getBySessId($sessId) {
        $rowset = $this->tableGateway->select(array('usrPassF_PHPSESSID' =>$sessId));
        $row = $rowset->current();
        return $row;
    }

    public function insert(UserPassForgot $userPassF) {
        $userPassF->usrPassF_dateTime=date('Y-m-d H:i:s');
        $userPassF->usrPassF_PHPSESSID=$this->sm->get('Zend\Session\SessionManager')->getId();
        $data = $userPassF->getArrayCopy();
        $this->tableGateway->insert($data);
        return $userPassF->usrPassF_PHPSESSID;
    }
    public function delete($usrPassF_id){
        $this->tableGateway->delete(array('usrPassF_id'=>(int)$usrPassF_id));
    }

}

?>
