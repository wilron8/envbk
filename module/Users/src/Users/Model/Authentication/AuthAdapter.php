<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 *
 * @author kimsreng
 */
namespace Users\Model\Authentication;

use Zend\Authentication\Adapter\DbTable;
use Zend\Db\Sql;
use Zend\Db\Sql\Predicate\Operator as SqlOp;

class AuthAdapter extends DbTable {

    protected function authenticateCreateSelect() {
        // get select
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->tableName)
                ->columns(array('*'))
                ->where('usr_isSuspended=0')
                ->where(new SqlOp($this->identityColumn, '=', $this->identity));

        return $dbSelect;
    }

    protected function authenticateQuerySelect(Sql\Select $dbSelect) {
        $sql = new Sql\Sql($this->zendDb);
        $statement = $sql->prepareStatementForSqlObject($dbSelect);

        try {
            $result = $statement->execute();
            $resultIdentities = array();

            // create object ob Bcrypt class
            $bcrypt = new \Zend\Crypt\Password\Bcrypt();

            // iterate result, most cross platform way
            foreach ($result as $row) {
                if ($bcrypt->verify($this->credential, $row[$this->credentialColumn])) {
                    $row['zend_auth_credential_match'] = 1;
                    $resultIdentities[] = $row;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('The supplied parameters to DbTable failed to produce a valid sql statement. Please check table and column names for validity.', 0, $e);
        }

        return $resultIdentities;
    }

}

?>
