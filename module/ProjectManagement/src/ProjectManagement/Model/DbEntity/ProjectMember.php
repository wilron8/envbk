<?php

/**
 * Description of ProjectMember
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbEntity;

class ProjectMember {

    public $pMem_id = NULL;
    public $pMem_projectID = NULL;
    public $pMem_memberID = NULL;
    public $pMem_isPM = 0;
    public $pMem_isOwner = 0;
    public $pMem_isSponsor = 0;
    public $pMem_role = NULL;
    public $pMem_kickedDateTime = NULL;
    public $pMem_approvedState = 0;
    public $pMem_dateTime = NULL;
    public $pMem_rejectText=NULL;
    public $pMem_wallWrite = 1;
    public $pMem_docManagerAccess = 1;
    public $pMem_toolBoxAccess = 0;

    public function exchangeArray($data) {
        $this->pMem_id = (isset($data['pMem_id'])) ? $data['pMem_id'] : $this->pMem_id;
        $this->pMem_projectID = (isset($data['pMem_projectID'])) ? $data['pMem_projectID'] : $this->pMem_projectID;
        $this->pMem_memberID = (isset($data['pMem_memberID'])) ? $data['pMem_memberID'] : $this->pMem_memberID;
        $this->pMem_isPM = (isset($data['pMem_isPM'])) ? $data['pMem_isPM'] : $this->pMem_isPM;
        $this->pMem_isOwner = (isset($data['pMem_isOwner'])) ? $data['pMem_isOwner'] : $this->pMem_isOwner;
        $this->pMem_isSponsor = (isset($data['pMem_isSponsor'])) ? $data['pMem_isSponsor'] : $this->pMem_isSponsor;
        $this->pMem_role= (isset($data['pMem_role'])) ? $data['pMem_role'] : $this->pMem_role;
        $this->pMem_kickedDateTime= (isset($data['pMem_kickedDateTime'])) ? $data['pMem_kickedDateTime'] : $this->pMem_kickedDateTime;
        $this->pMem_approvedState = (isset($data['pMem_approvedState'])) ? $data['pMem_approvedState'] : $this->pMem_approvedState;
        $this->pMem_dateTime = (isset($data['pMem_dateTime'])) ? $data['pMem_dateTime'] : $this->pMem_dateTime;
        $this->pMem_rejectText = (isset($data['pMem_rejectText'])) ? $data['pMem_rejectText'] : $this->pMem_rejectText;
        $this->pMem_wallWrite = (isset($data['pMem_wallWrite'])) ? $data['pMem_wallWrite'] : $this->pMem_wallWrite;
        $this->pMem_docManagerAccess = (isset($data['pMem_docManagerAccess'])) ? $data['pMem_docManagerAccess'] : $this->pMem_docManagerAccess;
        $this->pMem_toolBoxAccess = (isset($data['pMem_toolBoxAccess'])) ? $data['pMem_toolBoxAccess'] : $this->pMem_toolBoxAccess;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
