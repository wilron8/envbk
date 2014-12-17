<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserjoinTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\Userjoin;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Where;

class UserjoinTable {

    //put your code here
    protected $tableGateway;
    protected $sm;

    public function __construct(TableGateway $tableGateway, $sm) {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getById($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('join_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getByCheckNum($checkNum) { //check the LAST entry for user's join key
        $rowset = $this->tableGateway->select(function (Select $select) use($checkNum) {
            $select->where(array('join_checkNum' => $checkNum));
            $select->order('join_timeStamp DESC');
        });
        $row = $rowset->current();
        return $row;
    }

    public function getByEmail($email) {
        $rowset = $this->tableGateway->select(array('join_email' => $email));
        $row = $rowset->current();
        return $row;
    }

    public function insert(Userjoin $userjoin) {
        $userjoin->join_checkNum=$this->sm->get('Zend\Session\SessionManager')->getId();
        $userjoin->join_timeStamp=date('Y-m-d H:i:s');
        $this->tableGateway->insert($userjoin->getArrayCopy());
    }

    public function update(Userjoin $userjoin) {
        $id = (int) $userjoin->join_id;
        if ($this->getById($id)) {
            $this->tableGateway->update($userjoin->getArrayCopy(), array('join_id' => $id));
        } else {
            throw new \Exception('Join request ID not found.');
        }
    }

    public function deleteUserjoin($id) {
        $this->tableGateway->delete(array('join_id' => $id));
    }
    
    public function deleteByEmail($email){
        $this->tableGateway->delete(array('join_email'=>$email));
    }

    /**
     * Delete expired signups
     */
    public function clearExpiredKeys($email) {
        $date = date('Y-m-d H:i:s', time() - 60 * 60 * 24);
        $this->tableGateway->delete(function(Delete $delete) use ($date, $email) {
            $delete->where(function(Where $where) use ($date, $email) {
                $where->lessThan('join_timeStamp', $date)
                        ->equalTo('join_email', $email);
            });
        });
    }

}

?>
