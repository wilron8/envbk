<?php

/**
 * Description of SkillTag
 *
 * @author kimsreng
 */

namespace People\Model\DbEntity;

use Common\DbEntity\AbstractEntity;

class SkillTag extends AbstractEntity{

    public $stag_id;
    public $stag_text;
    public $stag_timeStamp;

    public function exchangeArray($data) {
        $this->stag_id = (isset($data['stag_id'])) ? $data['stag_id'] : NULL;
        $this->stag_text = (isset($data['stag_text'])) ? $data['stag_text'] : NULL;
        $this->stag_timeStamp = (isset($data['stag_timeStamp'])) ? $data['stag_timeStamp'] : NULL;
    }
    
    public function getArrayCopy(){
        return get_object_vars($this);
    }
    
    protected function outputScenario() {
        return[
            'json'=>['stag_id','stag_text']
        ];
    }

}
