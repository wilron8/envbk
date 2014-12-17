<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPhone
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserPhone {

    //put your code here
    public $uPhon_id=NULL;
    public $uPhon_userid=NULL;
    public $uPhon_type=NULL;
    public $uPhon_countryCode=NULL;
     public $uPhon_areaCode=NULL;
    public $uPhon_number=NULL;
    public $uPhon_isPrimary=NULL;
    public $uPhon_isSettingContact=NULL;

    public function exchangeArray($data) {
        $this->uPhon_id = (isset($data['uPhon_id'])) ? $data['uPhon_id'] : $this->uPhon_id;
        $this->uPhon_userid = (isset($data['uPhon_userid'])) ? $data['uPhon_userid'] : $this->uPhon_userid;
        $this->uPhon_type = (isset($data['uPhon_type'])) ? $data['uPhon_type'] : $this->uPhon_type;
        $this->uPhon_countryCode = (isset($data['uPhon_countryCode'])) ? $data['uPhon_countryCode'] : $this->uPhon_countryCode;
        $this->uPhon_areaCode = (isset($data['uPhon_areaCode'])) ? $data['uPhon_areaCode'] : $this->uPhon_areaCode;
        $this->uPhon_number = (isset($data['uPhon_number'])) ? $data['uPhon_number'] : $this->uPhon_number;
        $this->uPhon_isPrimary = (isset($data['uPhon_isPrimary'])) ? $data['uPhon_isPrimary'] : $this->uPhon_isPrimary;
        $this->uPhon_isSettingContact = (isset($data['uPhon_isSettingContact'])) ? $data['uPhon_isSettingContact'] : $this->uPhon_isSettingContact;
    }
    public function getArrayCopy(){
        return get_object_vars($this);
    }

}

?>
