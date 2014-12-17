<?php

/**
 * Description of MessageToTable
 *
 * @author kimsreng
 */
namespace Message\Model\DbTable;

use Message\Model\DbEntity\MessageTo;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class MessageToTable {
     protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    public function insert(MessageTo $msgTo){
        $this->tableGateway->insert($msgTo->getArrayCopy());
    }
    public function select($where=NULL){
        if($where==NULL){
            return $this->tableGateway->select();
        }else{
            return $this->tableGateway->select($where);
        }
    }
    public function getRecepients($msgId){
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('messageTo');
        $select->join('user', 'user.usr_id=messageTo.msg2_recepientID');
        $select->where(array('msg2_messageID'=>$msgId));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results;
    }
    public function delete($msg2ID){
       $row = $this->select(array('msg2_id'=>$msg2ID))->current();
       if($row){
           $row->msg2_isVisible=0;
           $row->msg2_hideTime=date('Y-m-d H:i:s');
           $this->update($row);
       }
    }
    
    public function update(MessageTo $msgTo){
        $this->tableGateway->update($msgTo->getArrayCopy(), array('msg2_id'=>$msgTo->msg2_id));
    }
    
    public function getRecipientAsArray($msgId){
        $list = $this->getRecepients($msgId);
        $arr=[];
        foreach ($list as $item){
            $arr[]=(int)$item['msg2_recepientID'];
        }
        return $arr;
    }
}
