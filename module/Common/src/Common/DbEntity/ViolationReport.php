<?php

/**
 * Description of ViolationReport
 *
 * @author kimsreng
 */
namespace Common\DbEntity;

class ViolationReport {

    public $vp_id;
    public $vp_ideaId;
    public $vp_projID;
    public $vp_commentId;
    public $vp_prjwId;
    public $vp_userId;
    public $vp_timeStamp;
    public $vp_comments;

    public function exchangeArray($data) {
        $this->vp_id = (isset($data['vp_id'])) ? $data['vp_id'] : NULL;
        $this->vp_commentId = (isset($data['vp_commentId'])) ? $data['vp_commentId'] : NULL;
        $this->vp_comments = (isset($data['vp_comments'])) ? $data['vp_comments'] : NULL;
        $this->vp_ideaId = (isset($data['vp_ideaId'])) ? $data['vp_ideaId'] : NULL;
        $this->vp_prjwId = (isset($data['vp_prjwId'])) ? $data['vp_prjwId'] : NULL;
        $this->vp_timeStamp = (isset($data['vp_timeStamp'])) ? $data['vp_timeStamp'] : NULL;
        $this->vp_userId = (isset($data['vp_userId'])) ? $data['vp_userId'] : NULL;
        $this->vp_projID = (isset($data['vp_projID'])) ? $data['vp_projID'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
