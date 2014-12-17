<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserLogin
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserLogin {

    public $uLgin_id;
    public $uLgin_userID;
    public $uLgin_timeStamp;
    public $uLgin_attempt;
    public $uLgin_fail;
    public $uLgin_ip;
    public $uLgin_mobile;

    public function exchangeArray($data) {
        $this->uLgin_id = (isset($data['uLgin_id'])) ? $data['uLgin_id'] : NULL;
        $this->uLgin_userID = (isset($data['uLgin_userID'])) ? $data['uLgin_userID'] : NULL;
        $this->uLgin_timeStamp = (isset($data['uLgin_timeStamp'])) ? $data['uLgin_timeStamp'] : NULL;
        $this->uLgin_attempt = (isset($data['uLgin_attempt'])) ? $data['uLgin_attempt'] : NULL;
        $this->uLgin_fail = (isset($data['uLgin_fail'])) ? $data['uLgin_fail'] : NULL;
        $this->uLgin_ip = (isset($data['uLgin_ip'])) ? $data['uLgin_ip'] : NULL;
        $this->uLgin_mobile = (isset($data['uLgin_mobile'])) ? $data['uLgin_mobile'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
