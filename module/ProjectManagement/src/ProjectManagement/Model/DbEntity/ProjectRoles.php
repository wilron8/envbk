<?php

/**
 * Description of ProjectRoles
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbEntity;

use Common\DbEntity\EntityInterface;

class ProjectRoles implements EntityInterface{

    public $pRole_id = NULL;
    public $pRole_timeStamp = NULL;
    public $pRole_title = NULL;
    public $pRole_lang = NULL;
    public $pRole_isPM = 0;
    public $pRole_isSponsor = 0;
    public $pRole_isVisible = NULL;

    public function exchangeArray($data) {
        $this->pRole_id = (isset($data['pRole_id'])) ? $data['pRole_id'] : $this->pRole_id;
        $this->pRole_timeStamp = (isset($data['pRole_timeStamp'])) ? $data['pRole_timeStamp'] : $this->pRole_timeStamp;
        $this->pRole_title = (isset($data['pRole_title'])) ? $data['pRole_title'] : $this->pRole_title;
        $this->pRole_lang = (isset($data['pRole_lang'])) ? $data['pRole_lang'] : $this->pRole_lang;
        $this->pRole_isPM = (isset($data['pRole_isPM'])) ? $data['pRole_isPM'] : $this->pRole_isPM;
        $this->pRole_isSponsor = (isset($data['pRole_isSponsor'])) ? $data['pRole_isSponsor'] : $this->pRole_isSponsor;
        $this->pRole_isVisible = (isset($data['pRole_isVisible'])) ? $data['pRole_isVisible'] : $this->pRole_isVisible;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}