<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserEmailTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserEmail;
use Zend\Db\TableGateway\TableGateway;

class UserEmailTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($uEmail_id) {
        $rowset = $this->tableGateway->select(array('uEmail_id' => (int) $uEmail_id));
        $row = $rowset->current();
        return $row;
    }

    /**
     * 
     * @param string $uEmail_email
     * @return \Users\Model\DbEntity\UserEmail
     */
    public function getByEmail($uEmail_email) {
        $rowset = $this->tableGateway->select(array('uEmail_email' => $uEmail_email));
        $row = $rowset->current();
        return $row;
    }

    public function getByUserId($userId, $isPrivateOnly = true) {
        $condition = array('uEmail_userID' => (int) $userId, 'uEmail_isPrivateOnly' => $isPrivateOnly);
        $rowset = $this->tableGateway->select($condition);
        return $rowset;
    }

    /**
     * Get a row result by condition as array
     * 
     * @param array $condition
     * @return object
     */
    public function getOneByCondition(array $condition) {
        $rowset = $this->tableGateway->select($condition);
        return $rowset->current();
    }

    public function insert(UserEmail $userEmail) {
        $this->tableGateway->insert($userEmail->getArrayCopy());
    }

    public function update(UserEmail $userEmail) {
        $id = (int) $userEmail->uEmail_id;
        if ($this->getById($id)) {
            $this->tableGateway->update($userEmail->getArrayCopy(), array('uEmail_id' => $id));
        } else {
            throw new Exception("Cannot find email");
        }
    }

    public function delete($uEmail_id) {
        $this->tableGateway->delete(array('uEmail_id' => (int) $uEmail_id));
    }

}

?>
