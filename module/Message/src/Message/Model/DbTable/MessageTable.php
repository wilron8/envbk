<?php

/**
 * Description of MessageTable
 *
 * @author kimsreng
 */

namespace Message\Model\DbTable;

use Message\Model\DbEntity\Message;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class MessageTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($paginated = false) {
        if ($paginated) {
            $select = new Select('message');
            $resultSetPrototype = new HydratingResultSet();
            $resultSetPrototype->setObjectPrototype(new Message());
            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchAllByUserId($userID) {
        $userID = (int)$userID;
        $driver = $this->tableGateway->getAdapter()->getDriver();
        $connection = $driver->getConnection();
        $result = $connection->execute( "CALL threadMsg({$userID})");
        $statement = $result->getResource();

        $resultSet = $statement->fetchAll();
        return $resultSet;
    }

    /**
     * Fetch all message created by a user
     * 
     * @param integer $userID
     * @return resultset
     */
    public function fetchSentMessages($userID) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $messages = $sql->select();
        $messages->from(array('m' => 'message'));
        $messages->join('user', 'm.msg_senderID=user.usr_id', array('usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $messages->join(array('mt' => 'messageTo'), 'm.msg_id=mt.msg2_messageID', array('*'));
        $messages->where(array('msg_senderID' => $userID));
        $messages->order('msg_timeStamp DESC');
        $statement = $sql->prepareStatementForSqlObject($messages);
        $result = $statement->execute();
        return $result;
    }

    /**
     * 
     * @param type $where
     * @return result set
     */
    public function select($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select();
        } else {
            return $this->tableGateway->select($where);
        }
    }

    public function find($keyword, $userID, $paginated = false) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $messages = $sql->select();
        $messages->from(array('m' => 'message'));
        $messages->join(array('mt' => 'messageTo'), 'm.msg_id=mt.msg2_messageID');
        $messages->where->like('msg_subject', '%' . $keyword . '%')
                ->or
                ->like('msg_body', '%' . $keyword . '%');
        $messages->where(array('mt.msg2_recepientID' => $userID));

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $messages, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($messages);
        $result = $statement->execute();
        return $result;
    }

    public function getById($msg_id) {
        $id = (int) $msg_id;
        $sql = new Sql($this->tableGateway->getAdapter());
        $messages = $sql->select();
        $messages->from(array('m' => 'message'));
        $messages->join(array('u' => 'user'), 'm.msg_senderID=u.usr_id');
        $messages->where(array('m.msg_id' => $id));
        $statement = $sql->prepareStatementForSqlObject($messages);
        $result = $statement->execute();
        $row = $result->current();
        return $row;
    }

    public function getByIdTb($msg_id) {
        return $this->tableGateway->select(array('msg_id' => (int) $msg_id))->current();
    }

    public function insert(Message $message) {
        $data = $message->getArrayCopy();
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(Message $message) {
        $id = (int) $message->msg_id;
        $this->tableGateway->update($message->getArrayCopy(), array('msg_id' => $id));
    }

    public function delete($msg_id) {
        $row = $this->select(array('msg_id'=>$msg_id))->current();
        if($row){
            $row->msg_isVisible2Sender=0;
            $row->msg_hideTime=date('Y-m-d H:i:s');
            $this->update($row);
        }
    }

    public function createMessage($data) {
        $msg = new Message();
        $msg->exchangeArray($data);
        $msg->msg_timeStamp = date('Y-m-d H:i:s');
        $id = $this->insert($msg);
        //save first message id as thread id
        if ($id && $msg->msg_threadID == NULL) {
            $msg->msg_threadID = $id;
            $msg->msg_id = $id;
            $this->update($msg);
        }
        return $id;
    }

    public function getThreadedMsg($threadId, $userId) {
        $userId = (int) $userId;
        $sql = new Sql($this->tableGateway->getAdapter());
        $messages = $sql->select();
        $messages->columns(array(new \Zend\Db\Sql\Expression('min(msg_id)'), '*'));
        $messages->from(array('m' => 'message'));
        $messages->join(array('mt' => 'messageTo'), 'm.msg_id=mt.msg2_messageID');
        $messages->join('user', 'm.msg_senderID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $messages->where(array('msg_threadID' => $threadId,'msg_isVisible'=>1));
        $messages->where("CASE WHEN msg_senderID={$userId} THEN msg_isVisible2Sender=1 ELSE TRUE END");
        $messages->where("CASE WHEN msg2_recepientID={$userId} THEN msg2_isVisible=1 ELSE TRUE END");
        $where = new \Zend\Db\Sql\Where();
        $where = $where->nest();
        $where->equalTo("msg_senderID", $userId)->or->equalTo("msg2_recepientID", $userId);
        $where->unnest();
        $messages->where($where);
        $messages->group('msg_id');
        $messages->order('msg_timeStamp DESC');
        $statement = $sql->prepareStatementForSqlObject($messages);
        $result = $statement->execute();
        //echo $messages->getSqlString(new \Zend\Db\Adapter\Platform\Mysql());
        return $result;
    }

}
