<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Userjoin
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class Userjoin {

    public $join_id;
    public $join_fName;
    public $join_email;
    public $join_timeStamp;
    public $join_checkNum;
    public $join_notifyCnt;
    public $join_langID;
    public $join_countryID;

    function exchangeArray($data) {
        $this->join_id = (isset($data['join_id'])) ? $data['join_id'] : NULL;
        $this->join_fName = (isset($data['join_fName'])) ? $data['join_fName'] : NULL;
        $this->join_email = (isset($data['join_email'])) ? $data['join_email'] : NULL;
        $this->join_timeStamp = (isset($data['join_timeStamp'])) ? $data['join_timeStamp'] : NULL;
        $this->join_checkNum = (isset($data['join_checkNum'])) ? $data['join_checkNum'] : NULL;
        $this->join_notifyCnt = (isset($data['join_notifyCnt'])) ? $data['join_notifyCnt'] : NULL;
        $this->join_countryID = (isset($data['join_countryID'])) ? $data['join_countryID'] : NULL;
        $this->join_langID = (isset($data['join_langID'])) ? $data['join_langID'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
