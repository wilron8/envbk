<?php

/**
 * Description of Experience
 *
 * @author kimsreng
 */

namespace People\Model\DbEntity;

class Experience {

    public $xp_id=NULL;
    public $xp_cvID=NULL;
    public $xp_name=NULL;
    public $xp_jobTitle=NULL;
    public $xp_descript=NULL;
    public $xp_fromDate=NULL;
    public $xp_toDate=NULL;

    public function exchangeArray($data) {
        $this->xp_id = (isset($data['xp_id'])) ? $data['xp_id'] : $this->xp_id;
        $this->xp_cvID = (isset($data['xp_cvID'])) ? $data['xp_cvID'] : $this->xp_cvID;
        $this->xp_name = (isset($data['xp_name'])) ? $data['xp_name'] : $this->xp_name;
        $this->xp_jobTitle = (isset($data['xp_jobTitle'])) ? $data['xp_jobTitle'] : $this->xp_jobTitle;
        $this->xp_descript = (isset($data['xp_descript'])) ? $data['xp_descript'] : $this->xp_descript;
        $this->xp_fromDate = (isset($data['xp_fromDate'])) ? $data['xp_fromDate'] : $this->xp_fromDate;
        $this->xp_toDate = (isset($data['xp_toDate'])) ? $data['xp_toDate'] : $this->xp_toDate;
    }
    
    public function getArrayCopy(){
        return get_object_vars($this);
    }

}
