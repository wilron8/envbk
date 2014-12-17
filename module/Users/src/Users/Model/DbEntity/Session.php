<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class Session {

    public $sess_id;
    public $sess_PHPSESSID;
    public $sess_userID;
    public $sess_timeStamp;
    public $sess_IP;
    public $sess_port;
    public $sess_DNS;
    public $sess_loginCnt;
    public $sess_browser;
    public $sess_browserVer;
    public $sess_OS;
    public $sess_OSver;
    public $sess_OSmake;
    public $sess_referrer;
    public $sess_logicalXDPI;
    public $sess_logicalYDPI;
    public $sess_isMobile;

    public function exchangeArray($data) {
        $this->sess_id = (isset($data['sess_id'])) ? $data['sess_id'] : NULL;
        $this->sess_PHPSESSID = (isset($data['sess_PHPSESSID'])) ? $data['sess_PHPSESSID'] : NULL;
        $this->sess_userID = (isset($data['sess_userID'])) ? $data['sess_userID'] : NULL;
        $this->sess_timeStamp = (isset($data['sess_timeStamp'])) ? $data['sess_timeStamp'] : NULL;
        $this->sess_port = (isset($data['sess_port'])) ? $data['sess_port'] : NULL;
        $this->sess_IP = (isset($data['sess_IP'])) ? $data['sess_IP'] : NULL;
        $this->sess_DNS = (isset($data['sess_DNS'])) ? $data['sess_DNS'] : NULL;
        $this->sess_loginCnt = (isset($data['sess_loginCnt'])) ? $data['sess_loginCnt'] : NULL;
        $this->sess_browser = (isset($data['sess_browser'])) ? $data['sess_browser'] : NULL;
        $this->sess_browserVer = (isset($data['sess_browserVer'])) ? $data['sess_browserVer'] : NULL;
        $this->sess_OS = (isset($data['sess_OS'])) ? $data['sess_OS'] : NULL;
        $this->sess_OSver = (isset($data['sess_OSver'])) ? $data['sess_OSver'] : NULL;
        $this->sess_OSmake = (isset($data['sess_OSmake'])) ? $data['sess_OSmake'] : NULL;
        $this->sess_referrer = (isset($data['sess_referrer'])) ? $data['sess_referrer'] : NULL;

        $this->sess_logicalXDPI = (isset($data['sess_logicalXDPI'])) ? $data['sess_logicalXDPI'] : NULL;
        $this->sess_logicalYDPI = (isset($data['sess_logicalYDPI'])) ? $data['sess_logicalYDPI'] : NULL;
        $this->sess_isMobile = (isset($data['sess_isMobile'])) ? $data['sess_isMobile'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
