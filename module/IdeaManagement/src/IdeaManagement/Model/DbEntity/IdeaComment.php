<?php

/**
 * Description of IdeaComment
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbEntity;

class IdeaComment {

    public $iComm_id = NULL;
    public $iComm_ideaId = NULL;
    public $iComm_userId = NULL;
    public $iComm_timeStamp = NULL;
    public $iComm_comment = NULL;
    public $iComm_readOnly = 0;
    public $iComm_hideDate = NULL;
    public $iComm_isHidden = 0;
    public $iComm_isSpam = 0;
    public $iComm_violationCnt = 0;

    public function exchangeArray($data) {
        $this->iComm_comment = (isset($data['iComm_comment'])) ? $data['iComm_comment'] : $this->iComm_comment;
        $this->iComm_userId = (isset($data['iComm_userId'])) ? $data['iComm_userId'] : $this->iComm_userId;
        $this->iComm_id = (isset($data['iComm_id'])) ? $data['iComm_id'] : $this->iComm_id;
        $this->iComm_ideaId = (isset($data['iComm_ideaId'])) ? $data['iComm_ideaId'] : $this->iComm_ideaId;
        $this->iComm_timeStamp = (isset($data['iComm_timeStamp'])) ? $data['iComm_timeStamp'] : $this->iComm_timeStamp;
        $this->iComm_readOnly = (isset($data['iComm_readOnly'])) ? $data['iComm_readOnly'] : $this->iComm_readOnly;
        $this->iComm_hideDate = (isset($data['iComm_hideDate'])) ? $data['iComm_hideDate'] : $this->iComm_hideDate;
        $this->iComm_isHidden = (isset($data['iComm_isHidden'])) ? $data['iComm_isHidden'] : $this->iComm_isHidden;
        $this->iComm_isSpam = (isset($data['iComm_isSpam'])) ? $data['iComm_isSpam'] : $this->iComm_isSpam;
        $this->iComm_violationCnt = (isset($data['iComm_violationCnt'])) ? $data['iComm_violationCnt'] : $this->iComm_violationCnt;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
