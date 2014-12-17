<?php

/**
 * Description of FollowPeople
 *
 * @author kimsreng
 */
namespace People\Model\DbEntity;

class FollowPeople {
    public $fp_id;
    public $fp_followerID;
    public $fp_followeeID;
    public $fp_timeStamp;
    
    public function exchangeArray($data){
        $this->fp_id = (isset($data['fp_id'])) ? $data['fp_id'] : NULL;
        $this->fp_followerID = (isset($data['fp_followerID'])) ? $data['fp_followerID'] : NULL;
        $this->fp_id = (isset($data['fp_followeeID'])) ? $data['fp_followeeID'] : NULL;
        $this->fp_id = (isset($data['fp_timeStamp'])) ? $data['fp_timeStamp'] : NULL;
    }
    public function getArrayCopy(){
        return get_object_vars($this);   
    }
}
