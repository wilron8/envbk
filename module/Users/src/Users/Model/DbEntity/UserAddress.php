<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserAddress
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserAddress {

    public $uAddr_id=NULL;
    public $uAddr_userID=NULL;
    public $uAddr_descript='';
    public $uAddr_timeStamp=NULL;
    public $uAddr_address1=NULL;
    public $uAddr_address2=NULL;
    public $uAddr_city=NULL;
    public $uAddr_state=NULL;
    public $uAddr_ZIP=NULL;
    public $uAddr_country=NULL;
    public $uAddr_TZ=NULL;
    public $uAddr_TZwDST=NULL;
    public function exchangeArray($data) {
         $this->uAddr_id = (isset($data['uAddr_id'])) ? $data['uAddr_id'] :  $this->uAddr_id;
         $this->uAddr_userID = (isset($data['uAddr_userID'])) ? $data['uAddr_userID'] :  $this->uAddr_userID;
         $this->uAddr_descript = (isset($data['uAddr_descript'])) ? $data['uAddr_descript'] :  $this->uAddr_descript;
         $this->uAddr_timeStamp = (isset($data['uAddr_timeStamp'])) ? $data['uAddr_timeStamp'] : $this->uAddr_timeStamp;
         $this->uAddr_address1 = (isset($data['uAddr_address1'])) ? $data['uAddr_address1'] :  $this->uAddr_address1;
         $this->uAddr_address2 = (isset($data['uAddr_address2'])) ? $data['uAddr_address2'] : $this->uAddr_address2;
         $this->uAddr_city = (isset($data['uAddr_city'])) ? $data['uAddr_city'] : $this->uAddr_city;
         $this->uAddr_state = (isset($data['uAddr_state'])) ? $data['uAddr_state'] : $this->uAddr_state;
         $this->uAddr_ZIP = (isset($data['uAddr_ZIP'])) ? $data['uAddr_ZIP'] : $this->uAddr_ZIP;
         $this->uAddr_country = (isset($data['uAddr_country'])) ? $data['uAddr_country'] : $this->uAddr_country;
         $this->uAddr_TZ = (isset($data['uAddr_TZ'])) ? $data['uAddr_TZ'] : $this->uAddr_TZ;
         $this->uAddr_TZwDST = (isset($data['uAddr_TZwDST'])) ? $data['uAddr_TZwDST'] : $this->uAddr_TZwDST;
    }
    public function getArrayCopy(){
        return get_object_vars($this);
    }

}

?>
