<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPassForgot
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserPassForgot {

    public $usrPassF_id;
    public $usrPassF_usrID;
    public $usrPassF_dateTime;
    public $usrPassF_PHPSESSID;

    public function exchangeArray($data) {
        $this->usrPassF_id = (isset($data['usrPassF_id'])) ? $data['usrPassF_id'] : NULL;
        $this->usrPassF_usrID = (isset($data['usrPassF_usrID'])) ? $data['usrPassF_usrID'] : NULL;
        $this->usrPassF_dateTime = (isset($data['usrPassF_dateTime'])) ? $data['usrPassF_dateTime'] : NULL;
        $this->usrPassF_PHPSESSID = (isset($data['usrPassF_PHPSESSID'])) ? $data['usrPassF_PHPSESSID'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
