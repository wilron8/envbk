<?php

/**
 * Description of Policy
 *
 * @author kimsreng
 */

namespace IdeaManagement\Policy;

class Policy {

    protected $ideaOwner = NULL;
    protected $commentOwner = NULL;

    /**
     * @var \Users\Model\DbTable\UserTable 
     */
    protected $userTb;

    /**
     * @var \IdeaManagement\Model\DbTable\IdeaTable 
     */
    protected $ideaTb;

    public function __construct($userTable, $ideaTable) {
        $this->userTb = $userTable;
        $this->ideaTb = $ideaTable;
    }

    /**
     * @param \Users\Model\DbEntity\User $owner
     */
    public function setIdeaOwnder($owner) {
        $this->ideaOwner = $owner;
        return $this;
    }

    /**
     * @param \Users\Model\DbEntity\User $owner
     */
    public function setCommentOwner($owner) {
        $this->commentOwner = $owner;
        return $this;
    }

    public function canUpdate($idea, $userId) {
        if ($idea->idea_originator == $userId) {
            return true;
        }
        return false;
    }

    public function canRemove($idea, $userId) {
        if ($idea->idea_originator == $userId) {
            return true;
        }
        return false;
    }

    public function canReport($idea, $userId) {
        if ($idea->idea_originator != $userId) {
            return true;
        }
        return false;
    }

    public function canEvolve($idea, $userId) {
        if ($idea->idea_originator == $userId) {
            return true;
        }
        return false;
    }

    public function canViewEvolution($idea, $userId) {
        return true;
    }

    public function canComment($idea, $userId) {
        return true;
    }

    /**
     * Check if a user has permission to remove a comment
     * 
     * @param object $comment
     * @param integer $userId
     * @return boolean
     */
    public function canRemoveComment($comment, $userId) {
        if ($comment->iComm_userId != $userId) {
            return true;
        }
        if ($this->ideaOwner == NULL) {
            $idea = $this->ideaTb->getById($comment->iComm_ideaId);
            if ($idea->idea_originator == $userId) {
                return true;
            }
        }
        return ($this->ideaOwner->usr_id == $userId);
    }

    public function canReportComment($comment, $userId) {
        if ($comment->idea_originator != $userId) {
            return true;
        }
        return false;
    }

}
