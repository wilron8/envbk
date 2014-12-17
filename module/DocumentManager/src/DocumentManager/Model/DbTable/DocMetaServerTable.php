<?php

/**
 * Description of DocMetaServerTable
 *
 * @author kimsreng
 */

namespace DocumentManager\Model\DbTable;

use DocumentManager\Model\DbEntity\DocMetaServer;
use Zend\Db\TableGateway\TableGateway;

class DocMetaServerTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * 
     * @param \DocumentManager\Model\DocMetaServer $docMetaServer
     */
    public function insert(DocMetaServer $docMetaServer) {
        $this->tableGateway->insert($docMetaServer->getArrayCopy());
    }

    /**
     * 
     * @param \DocumentManager\Model\DocMetaServer $docMetaServer
     */
    public function update(DocMetaServer $docMetaServer) {
        $this->tableGateway->update($docMetaServer->getArrayCopy(), array('docMetaSvr_id' => $docMetaServer->docMetaSvr_id));
    }

    /**
     * 
     * @param integer $docMetaSvr_id
     */
    public function delete($docMetaSvr_id) {
        $this->tableGateway->delete(array('docMetaSvr_id' => $docMetaSvr_id));
    }

    /**
     * 
     * @param mixed $where
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
     * @param integer $docMetaSvr_id
     * @return \DocumentManager\Model\DocMetaServer
     */
    public function getById($docMetaSvr_id) {
        return $this->tableGateway->select(array('docMetaSvr_id' => $docMetaSvr_id))->current();
    }
    
    public function getServer(){
        //TODO: get the nearest server to the requesting client
        return $this->fetchAll()->current();
    }

}
