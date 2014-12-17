<?php

/**
 * Base class to abstract crud operations for data table
 *
 * @author kimsreng
 */

namespace Common\DbTable;

use Common\DbEntity\EntityInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractTable {

    protected $table = NULL;
    protected $primaryKey = 'id';
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Select one row
     * 
     * @param mixed $where
     * @return entity object
     */
    public function fetchOne($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select()->current();
        } else {
            return $this->tableGateway->select($where)->current();
        }
    }

    /**
     * Select a set of rows as object result
     * 
     * @param mixed $where
     * @return a set of object result
     */
    public function fetchAll($where = NULL, $columns = NULL, $order = NULL) {

        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select($this->table);

        if ($where !== NULL) {
            $select->where($where);
        }

        if ($columns !== NULL) {
            $select->columns($columns);
        }

        if ($order !== NULL) {
            $select->order($order);
        }

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }

    /**
     * Get a row by some specific columns
     * 
     * @param string|array $columns
     * @param mixed $where
     * @return array
     */
    public function fetchOneByCol($columns = array("*"), $where = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select($this->table);
        $select->columns($columns);
        if ($where !== NULL) {
            $select->where($where);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    /**
     * Get a collection of records with specified columns
     * 
     * @param string|array $columns
     * @param mixed $where
     * @return array
     */
    public function fetchAllByCol($columns = "*", $where = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select($this->table);
        $select->columns($columns);
        if ($where !== NULL) {
            $select->where($where);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    public function getById($entityId) {
        return $this->tableGateway->select(array($this->primaryKey => (int) $entityId))->current();
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function insert(EntityInterface $entity) {
        $this->tableGateway->insert($entity->getArrayCopy());
        return $this->tableGateway->lastInsertValue;
    }

    /**
     * Update a data row by the object instance
     * 
     * @param object $entity
     * @return boolean
     */
    public function update(EntityInterface $entity) {
        $primaryKey = $this->primaryKey;
        return $this->tableGateway->update($entity->getArrayCopy(), array($primaryKey => $entity->{$primaryKey}));
    }

    /**
     * Delete a data row by id
     * 
     * @param integer $entityId
     * @return boolean
     */
    public function delete($entityId) {
        return $this->tableGateway->delete(array($this->primaryKey => (int) $entityId));
    }

}
