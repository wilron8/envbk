<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserLangTable
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\UserLang;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class UserLangTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getById($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('uLang_id' => $id));
        $row = $rowset->current();
        return $row;
    }

    public function getByLang($langId, $userId) {
        $rowset = $this->tableGateway->select(array('uLang_lang' => (int) $langId, 'uLang_userID' => $userId));
        $row = $rowset->current();
        return $row;
    }

    public function getByUserId($userID, $columns) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('userLang');
        if ($columns !== NULL) {
            $select->columns($columns);
        }
        $select->join('geoLang', 'userLang.uLang_lang=geoLang.geoLang_id', array('geoLang_id', 'geoLang_name'));
        $select->where(array('uLang_userID' => $userID));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }

    public function insert(UserLang $user_lang) {
        if ($this->getByLang($user_lang->uLang_lang, $user_lang->uLang_userID)) {
            return false;
        }
        return $this->tableGateway->insert($user_lang->getArrayCopy());
    }

    public function save(UserLang $user_lang) {
        $id = (int) $user_lang->uLang_id;

        if ($this->getById($id)) {
            $this->tableGateway->update($user_lang->getArrayCopy(), array('uLang_id' => $id));
        } else {
            throw new \Exception('Userlang id does not exist');
        }
    }

    public function delete($uLang_id) {
        $this->tableGateway->delete(array('uLang_id' => (int) $uLang_id));
    }

}

?>
