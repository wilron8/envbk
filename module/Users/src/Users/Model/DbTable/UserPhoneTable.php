<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPhoneTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserPhone;
use Zend\Db\TableGateway\TableGateway;

class UserPhoneTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getById($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('uPhon_id' => $id));
        $row = $rowset->current();
        return $row;
    }

    public function getByUserId($userID) {
        return $this->tableGateway->select(array('uPhon_userID' => (int) $userID, 'uPhon_isSettingContact' => null));
    }

    public function getPrimByUserId($userID) {
        return $this->tableGateway->select(array('uPhon_userID' => (int) $userID, 'uPhon_isSettingContact' => 1))->current();
    }

    public function insert(UserPhone $userPhone) {
        $this->tableGateway->insert($userPhone->getArrayCopy());
    }

    public function update(UserPhone $userPhone) {
        $id = (int) $userPhone->uPhon_id;
        $this->tableGateway->update($userPhone->getArrayCopy(), array('uPhon_id' => $id));
    }

    public function delete($uPhon_id) {
        $this->tableGateway->delete(array('uPhon_id' => (int) $uPhon_id));
    }

}

?>
