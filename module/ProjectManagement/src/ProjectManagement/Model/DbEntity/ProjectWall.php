<?php

/**
 * Description of ProjectWall
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbEntity;

class ProjectWall implements \Common\DbEntity\EntityInterface{

    public $prjW_id = NULL;
    public $prjW_projID = NULL;
    public $prjW_userid = NULL;
    public $prjW_timeStamp = NULL;
    public $prjW_comment = NULL;
    public $prjW_readOnly = 0;
    public $prjW_hideDate = NULL;
    public $prjW_isHidden = 0;
    public $prjW_isSpam = 0;
    public $prjW_violationCnt = 0;

    public function exchangeArray($data) {
        $this->prjW_id = (isset($data['prjW_id'])) ? $data['prjW_id'] : $this->prjW_id;
        $this->prjW_projID = (isset($data['prjW_projID'])) ? $data['prjW_projID'] : $this->prjW_projID;
        $this->prjW_userid = (isset($data['prjW_userid'])) ? $data['prjW_userid'] : $this->prjW_userid;
        $this->prjW_timeStamp = (isset($data['prjW_timeStamp'])) ? $data['prjW_timeStamp'] : $this->prjW_timeStamp;
        $this->prjW_comment = (isset($data['prjW_comment'])) ? $data['prjW_comment'] : $this->prjW_comment;
        $this->prjW_readOnly = (isset($data['prjW_readOnly'])) ? $data['prjW_readOnly'] : $this->prjW_readOnly;
        $this->prjW_hideDate = (isset($data['prjW_hideDate'])) ? $data['prjW_hideDate'] : $this->prjW_hideDate;
        $this->prjW_isHidden = (isset($data['prjW_isHidden'])) ? $data['prjW_isHidden'] : $this->prjW_isHidden;
        $this->prjW_isSpam = (isset($data['prjW_isSpam'])) ? $data['prjW_isSpam'] : $this->prjW_isSpam;
        $this->prjW_violationCnt = (isset($data['prjW_violationCnt'])) ? $data['prjW_violationCnt'] : $this->prjW_violationCnt;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
