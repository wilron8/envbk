<?php

/**
 * Description of IdeaCommentTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use IdeaManagement\Model\DbEntity\IdeaComment;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class IdeaCommentTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getById($iComm_id) {
        $id = (int) $iComm_id;
        $rowset = $this->tableGateway->select(array('iComm_id' => $id));
        $row = $rowset->current();
        return $row;
    }

    public function addComment(IdeaComment $ideaComment) {
        $ideaComment->iComm_timeStamp = date('Y-m-d H:i:s');
        //$escaper = new \Zend\Escaper\Escaper("utf-8");
            
        //THIS MAKES DESIRED HTML TAGS ESCAPED, IT IS ALREADY FILTERD BY FORM
        ///$ideaComment->iComm_comment = $escaper->escapeHtml($ideaComment->iComm_comment); 
        //$ideaComment->iComm_comment = htmlspecialchars($ideaComment->iComm_comment, ENT_COMPAT, "UTF-8"); 
        $this->tableGateway->insert($ideaComment->getArrayCopy());
        $ideaComment->iComm_id = $this->tableGateway->getLastInsertValue();
        return true;
    }

    public function deleteComment($ideaCommentId) {
        $comment = $this->getById($ideaCommentId);
        if($comment->iComm_readOnly==1){
            return false;
        }
        $comment->iComm_hideDate= date('Y-m-d H:i:s');
        $comment->iComm_isHidden = 1;
        $this->tableGateway->update($comment->getArrayCopy(), array('iComm_id'=>$ideaCommentId));
    }

    /**
     * Get a list of comments with user for an idea
     * 
     * @param integer $ideaId
     * @return result set
     */
    public function getComments($ideaId, $limit = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('ideaComment');
        $select->join('user', 'user.usr_id=ideaComment.iComm_userId');
        $select->where(array('iComm_ideaId' => $ideaId, 'iComm_isHidden' => 0));
        $select->order('iComm_timeStamp ASC');
        if($limit!=NULL){
            $select->limit($limit);
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        return $results;
    }

    /**
     * Check if a user is the owner of a comment
     * 
     * @param type $userId
     * @param type $commentId
     * @return boolean
     */
    public function isCommentOwner($userId, $commentId) {
        $result = $this->tableGateway->select(array('iComm_userId' => $userId, 'iComm_id' => $commentId));
        $row = $result->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

}
