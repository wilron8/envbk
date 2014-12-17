<?php

/**
 * Description of DocFolderTable
 *
 * @author kimsreng
 */

namespace DocumentManager\Model\DbTable;

use DocumentManager\Model\DbEntity\DocFolder;
use Zend\Db\TableGateway\TableGateway;

class DocFolderTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param \DocumentManager\Model\DocFolder $docFolder
     */
    public function insert(DocFolder $docFolder) {
        $this->tableGateway->insert($docFolder->getArrayCopy());
    }

    /**
     * 
     * @param \DocumentManager\Model\DocFolder $docFolder
     */
    public function update(DocFolder $docFolder) {
        $this->tableGateway->update($docFolder->getArrayCopy(), array('docFolder_id' => $docFolder->docFolder_id));
    }

    /**
     * 
     * @param integer $docFolder_id
     */
    public function delete($docFolder_id) {
        $this->tableGateway->delete(array('docFolder_id' => $docFolder_id));
    }

    /**
     * 
     * @param mixed $where
     * @return \DocumentManager\Model\DocFolder
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
     * @param integer $docFolder_id
     * @return \DocumentManager\Model\DocFolder
     */
    public function getById($docFolder_id) {
        return $this->tableGateway->select(array('docFolder_id' => $docFolder_id))->current();
    }

}
