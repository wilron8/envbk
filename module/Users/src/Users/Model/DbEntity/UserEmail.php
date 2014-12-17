<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserEmail
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserEmail {

    public $uEmail_id;
    public $uEmail_timeStamp;
    public $uEmail_email;
    public $uEmail_userID;
    public $uEmail_emailType;
    public $uEmail_isMobile;
    public $uEmail_isVerified;
    public $uEmail_isPrivateOnly;
    public function exchangeArray($data) {
        $this->uEmail_id = (isset($data['uEmail_id'])) ? $data['uEmail_id'] : NULL;
        $this->uEmail_email = (isset($data['uEmail_email'])) ? $data['uEmail_email'] : NULL;
        $this->uEmail_timeStamp = (isset($data['uEmail_timeStamp'])) ? $data['uEmail_timeStamp'] : NULL;
        $this->uEmail_userID = (isset($data['uEmail_userID'])) ? $data['uEmail_userID'] : NULL;
        $this->uEmail_emailType = (isset($data['uEmail_emailType'])) ? $data['uEmail_emailType'] : NULL;
        $this->uEmail_isMobile = (isset($data['uEmail_isMobile'])) ? $data['uEmail_isMobile'] : NULL;
        $this->uEmail_isVerified = (isset($data['uEmail_isVerified'])) ? $data['uEmail_isVerified'] : NULL;
        $this->uEmail_isPrivateOnly = (isset($data['uEmail_isPrivateOnly'])) ? $data['uEmail_isPrivateOnly'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
