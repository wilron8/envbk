<?php

/**
 * Description of FollowIdea
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbEntity;

class FollowIdea {

    public $fi_id;
    public $fi_userID;
    public $fi_ideaID;
    public $fi_timeStamp;
    public $fi_notifyLevel;

    public function exchangeArray($data) {
        $this->fi_id = (isset($data['fi_id'])) ? $data['fi_id'] : NULL;
        $this->fi_userID = (isset($data['fi_userID'])) ? $data['fi_userID'] : NULL;
        $this->fi_ideaID = (isset($data['fi_ideaID'])) ? $data['fi_ideaID'] : NULL;
        $this->fi_timeStamp = (isset($data['fi_timeStamp'])) ? $data['fi_timeStamp'] : NULL;
        $this->fi_notifyLevel = (isset($data['fi_notifyLevel'])) ? $data['fi_notifyLevel'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
