<?php

/**
 * Description of UserCertificationTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use Common\DbTable\AbstractTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;

class UserCertificationTable extends AbstractTable {

    protected $table = "userCertification";
    protected $primaryKey = 'uCert_id';

    public function fetchByUser($userId, $columns = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select($this->table);
        if($columns !==NULL){
            $select->columns($columns);
        }
        $select->join('CertificateTag', 'CertificateTag.cert_id=userCertification.uCert_TagID',array('cert_id','cert_text'));
        $select->where(array('uCert_userID' => $userId));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }
    
    public function insert(\Common\DbEntity\EntityInterface $entity) {
        if($this->getByCert($entity->uCert_TagID,$entity->uCert_userID)){
            return false;
        }
        return parent::insert($entity);
    }

        public function getByCert($cert_id,$userId){
        return $this->tableGateway->select(['uCert_TagID'=>$cert_id,'uCert_userID'=>$userId])->current();   
    }
    
}
