<?php

/**
 * Description of IdeaRef
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbEntity;

class IdeaRef {

    public $iRef_id;
    public $iRef_newIdea;
    public $iRef_srcIdea;
    public $iRef_timeStamp;

    public function exchangeArray($data) {
        $this->iRef_id = (isset($data['iRef_id'])) ? $data['iRef_id'] : NULL;
        $this->iRef_newIdea = (isset($data['iRef_newIdea'])) ? $data['iRef_newIdea'] : NULL;
        $this->iRef_srcIdea = (isset($data['iRef_srcIdea'])) ? $data['iRef_srcIdea'] : NULL;
        $this->iRef_timeStamp = (isset($data['iRef_timeStamp'])) ? $data['iRef_timeStamp'] : NULL;
    }
    public function getArrayCopy(){
        return get_object_vars($this);
    }

}
