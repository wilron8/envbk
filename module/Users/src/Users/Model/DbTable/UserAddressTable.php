<?php

/**
 * Description of UserAddressTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserAddress;
use Zend\Db\TableGateway\TableGateway;

class UserAddressTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getById($uAddr_id) {
        $rowset = $this->tableGateway->select(array('uAddr_id' => (int) $uAddr_id));
        $row = $rowset->current();
        return $row;
    }

    public function getByUserId($userID) {
        return $this->tableGateway->select(array('uAddr_descript'=>'(settingsPage)','uAddr_userID' => (int) $userID))->current();
    }
    
    public function getProfileAddresses($userId){
        return $this->tableGateway->select(array('uAddr_descript'=>'','uAddr_userID' => (int) $userId));
    }

    public function insert(UserAddress $userAddress) {
        $this->tableGateway->insert($userAddress->getArrayCopy());
    }

    public function update(UserAddress $userAddress) {
        $id = (int) $userAddress->uAddr_id;

        if ($this->getById($id)) {
            $this->tableGateway->update($userAddress->getArrayCopy(), array('uAddr_id' => $id));
        } else {
            throw new \Exception('Useraddress id does not exist');
        }
    }
    public function updateProfile(UserAddress $userAddress) {
        $id = (int) $userAddress->uAddr_id;
        $userAddress->uAddr_descript="(settingsPage)";
        if ($this->getById($id)) {
            $this->tableGateway->update($userAddress->getArrayCopy(), array('uAddr_id' => $id));
        } else {
            throw new \Exception('Useraddress id does not exist');
        }
    }
    

    public function delete($uAddr_id) {
        $this->tableGateway->delete(array('uAddr_id' => (int) $uAddr_id));
    }

}

?>
