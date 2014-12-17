<?php

/**
 * Description of DocMetaUserTable
 *
 * @author kimsreng
 */

namespace DocumentManager\Model\DbTable;

use DocumentManager\Model\DbEntity\DocMetaUser;
use Zend\Db\TableGateway\TableGateway;

class DocMetaUserTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param \DocumentManager\Model\DocMetaUser $docMetaUser
     */
    public function insert(DocMetaUser $docMetaUser) {
        $this->tableGateway->insert($docMetaUser->getArrayCopy());
    }

    /**
     * 
     * @param \DocumentManager\Model\DocMetaUser $docMetaUser
     */
    public function update(DocMetaUser $docMetaUser) {
        $this->tableGateway->update($docMetaUser->getArrayCopy(), array('docMetaUsr_id' => $docMetaUser->docMetaUsr_id));
    }

    /**
     * 
     * @param integer $docMetaUsr_id
     */
    public function delete($docMetaUsr_id) {
        $this->tableGateway->delete(array('docMetaUsr_id' => $docMetaUsr_id));
    }

    /**
     * 
     * @param mixed $where
     * @return \DocumentManager\Model\DocMetaUser
     */
    public function fetchAll($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select();
        } else {
            return $this->tableGateway->select($where);
        }
    }

    /**
     * 
     * @param integer $docMetaUsr_id
     * @return \DocumentManager\Model\DocMetaUser
     */
    public function getById($docMetaUsr_id) {
        return $this->tableGateway->select(array('docMetaUsr_id' => $docMetaUsr_id))->current();
    }

}
