<?php

/**
 * Description of Education
 *
 * @author kimsreng
 */
namespace People\Model\DbEntity;

class Education {
    
    public $ed_id=NULL;
    public $ed_cvID=NULL;
    public $ed_name=NULL;
    public $ed_major=NULL;
    public $ed_descript=NULL;
    public $ed_fromDate=NULL;
    public $ed_toDate=NULL;
    
    public function exchangeArray($data) {
        $this->ed_id = (isset($data['ed_id'])) ? $data['ed_id'] : $this->ed_id ;
        $this->ed_cvID = (isset($data['ed_cvID'])) ? $data['ed_cvID'] : $this->ed_cvID;
        $this->ed_name = (isset($data['ed_name'])) ? $data['ed_name'] : $this->ed_name;
        $this->ed_major = (isset($data['ed_major'])) ? $data['ed_major'] : $this->ed_major;
        $this->ed_descript = (isset($data['ed_descript'])) ? $data['ed_descript'] : $this->ed_descript;
        $this->ed_fromDate = (isset($data['ed_fromDate'])) ? $data['ed_fromDate'] : $this->ed_fromDate;
        $this->ed_toDate = (isset($data['ed_toDate'])) ? $data['ed_toDate'] : $this->ed_toDate;
    }
    
    public function getArrayCopy(){
        return get_object_vars($this);
    }
}
