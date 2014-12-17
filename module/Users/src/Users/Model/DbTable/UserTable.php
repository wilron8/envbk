<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserTable
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\User;
use Zend\Db\TableGateway\TableGateway;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceManager;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;


class UserTable {

    protected $tableGateway;
    protected $sm;
    protected $adapter;

    public function __construct(TableGateway $tableGateway, ServiceManager $sm) {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
        $this->adapter = $this->sm->get('Zend\Db\Adapter\Adapter');
    }

    public function fetchAll($where = NULL, $columns = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('user');

        if ($where !== NULL) {
            $select->where($where);
        }
        if ($columns !== NULL) {
            $select->columns($columns);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function getById($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('usr_id' => $id));
        $row = $rowset->current();
        return $row;
    }

    public function getByEmail($email) {
        $rowset = $this->tableGateway->select(array('usr_email' => $email));
        $row = $rowset->current();
        return $row;
    }

    public function getBySecretAnswer($answer, $id) {
        $rowset = $this->tableGateway->select(array('usr_id' => (int) $id, 'usr_secretA' => $answer));
        $row = $rowset->current();
        return $row;
    }

    /*
     * This function will return the ISO 639 lang chode (2 - 4 char long)
     *
     * userID: INT
     */

    public function getUserISO639($userID) {
        //SELECT geoLang_ISO639 FROM envitz.user, geoLang where user.usr_lang=geoLang.geoLang_id AND user.usr_id=$userID
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('user');
        $select->columns(array('geoLang_ISO639'));
        $select->join('geoLang', 'user.usr_lang=geoLang.geoLang_id', array('geoLang_ISO639'));
        $select->where(array('usr_id' => (int) $userID));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function insert(User $user) {
        $user->usr_password = $this->encryptPassword($user->usr_password);
        $data = $user->getArrayCopy();
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(User $user) {
        $id = (int) $user->usr_id;
        $user->usr_ideaCnt = max($user->usr_ideaCnt, 0);
        $user->usr_followerCnt = max($user->usr_followerCnt, 0);
        $user->usr_followingCnt = max($user->usr_followingCnt, 0);
        if ($this->getById($id)) {
            $this->tableGateway->update($user->getArrayCopy(), array('usr_id' => $id));
        } else {
            throw new \Exception('Invalid User ID');
        }
    }

    public function delete($userID) {
        $this->tableGateway->delete(array('usr_id' => (int) $userID));
    }

    public function getProjectMemSelectOptions() {
        $rows = $this->tableGateway->select();
        $options = array();
        foreach ($rows as $row) {
            $options[$row->usr_id] = $row->usr_fName . ' ' . $row->usr_lName;
        }
        return $options;
    }

    /**
     * Suspend a user
     * 
     * @param User $user
     */
    public function suspendUser($user) {
        $user->usr_isSuspended = 1;
        $user->usr_suspendDate = date('Y-m-d H:i:s');
        $user->usr_suspendDuration = \People\Policy\Policy::SUSPEND_DURATION;
        return $this->update($user);
    }

    /**
     * Use bcrypt to encrypt password
     * @param string $password
     * @return string encrypted string
     */
    public function encryptPassword($password) {
        $bcrypt = new Bcrypt();
        $encrypted = $bcrypt->create($password);
        return $encrypted;
    }
   
}

?>
