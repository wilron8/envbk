<?php

/**
 * Description of DocFileTable
 *
 * @author kimsreng
 */

namespace DocumentManager\Model\DbTable;

use DocumentManager\Model\DbEntity\DocFile;
use Zend\Db\TableGateway\TableGateway;

class DocFileTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param \DocumentManager\Model\DocFile $docFile
     */
    public function insert(DocFile $docFile) {
        $this->tableGateway->insert($docFile->getArrayCopy());
    }

    /**
     * 
     * @param \DocumentManager\Model\DocFile $docFile
     */
    public function update(DocFile $docFile) {
        $this->tableGateway->update($docFile->getArrayCopy(), array('docFile_id' => $docFile->docFile_id));
    }

    /**
     * 
     * @param integer $docFile_id
     */
    public function delete($docFile_id) {
        $this->tableGateway->delete(array('docFile_id' => $docFile_id));
    }

    /**
     * 
     * @param integer $where
     * @return type
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
     * @param integer $docFile_id
     * @return \DocumentManager\Model\DocFile
     */
    public function getById($docFile_id) {
        return $this->tableGateway->select(array('docFile_id' => $docFile_id))->current();
    }

}
