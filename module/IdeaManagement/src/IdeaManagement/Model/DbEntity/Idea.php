<?php

/**
 * Description of Idea
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbEntity;

class Idea {

    public $idea_id = NULL;
    public $idea_title = NULL;
    public $idea_img = NULL;
    public $idea_descript = NULL;
    public $idea_isVisible = NULL;
    public $idea_legalAccept = NULL;
    public $idea_categoryID = NULL;
    public $idea_timeStamp = NULL;
    public $idea_lastModified = NULL;
    public $idea_lastAccess = NULL;
    public $idea_lastPost = NULL;
    public $idea_originator = NULL;
    public $idea_nodeRight = NULL;
    public $idea_nodeLeft = NULL;
    public $idea_nodeDepth = NULL;
    public $idea_nodeParent = NULL;
    public $idea_hitCnt = 0;
    public $idea_coolCnt = 0;
    public $idea_followCnt = 0;
    public $idea_violationCnt = 0;
    public $idea_attachment = NULL;

    public function exchangeArray($data) {
        $this->idea_id = (isset($data['idea_id'])) ? $data['idea_id'] : $this->idea_id;
        $this->idea_title = (isset($data['idea_title'])) ? $data['idea_title'] : $this->idea_title;
        $this->idea_img = (isset($data['idea_img'])) ? $data['idea_img'] : $this->idea_img;
        $this->idea_descript = (isset($data['idea_descript'])) ? $data['idea_descript'] : $this->idea_descript;
        $this->idea_isVisible = (isset($data['idea_isVisible'])) ? $data['idea_isVisible'] : $this->idea_isVisible;
        $this->idea_legalAccept = (isset($data['idea_legalAccept'])) ? $data['idea_legalAccept'] : $this->idea_legalAccept;
        $this->idea_categoryID = (isset($data['idea_categoryID'])) ? $data['idea_categoryID'] : $this->idea_categoryID;
        $this->idea_timeStamp = (isset($data['idea_timeStamp'])) ? $data['idea_timeStamp'] : $this->idea_timeStamp;
        $this->idea_lastModified = (isset($data['idea_lastModified'])) ? $data['idea_lastModified'] : $this->idea_lastModified;
        $this->idea_originator = (isset($data['idea_originator'])) ? $data['idea_originator'] : $this->idea_originator;
        $this->idea_lastAccess = (isset($data['idea_lastAccess'])) ? $data['idea_lastAccess'] : $this->idea_lastAccess;
        $this->idea_lastPost = (isset($data['idea_lastPost'])) ? $data['idea_lastPost'] : $this->idea_lastPost;
        $this->idea_nodeRight = (isset($data['idea_nodeRight'])) ? $data['idea_nodeRight'] : $this->idea_nodeRight;
        $this->idea_nodeLeft = (isset($data['idea_nodeLeft'])) ? $data['idea_nodeLeft'] : $this->idea_nodeLeft;
        $this->idea_nodeDepth = (isset($data['idea_nodeDepth'])) ? $data['idea_nodeDepth'] : $this->idea_nodeDepth;
        $this->idea_nodeParent = (isset($data['idea_nodeParent'])) ? $data['idea_nodeParent'] : $this->idea_nodeParent;
        $this->idea_hitCnt = (isset($data['idea_hitCnt'])) ? $data['idea_hitCnt'] : $this->idea_hitCnt;
        $this->idea_coolCnt = (isset($data['idea_coolCnt'])) ? $data['idea_coolCnt'] : $this->idea_coolCnt;
        $this->idea_followCnt = (isset($data['idea_followCnt'])) ? $data['idea_followCnt'] : $this->idea_followCnt;
        $this->idea_violationCnt = (isset($data['idea_violationCnt'])) ? $data['idea_violationCnt'] : $this->idea_violationCnt;
        $this->idea_attachment = (isset($data['idea_attachment'])) ? $data['idea_attachment'] : $this->idea_attachment;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
