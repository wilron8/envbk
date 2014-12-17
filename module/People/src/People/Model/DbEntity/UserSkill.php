<?php

/**
 * Description of UserSkill
 *
 * @author kimsreng
 */

namespace People\Model\DbEntity;

class UserSkill {

    public $uSkll_id;
    public $uSkll_userID;
    public $uSkll_TagID;

    public function exchangeArray($data) {
        $this->uSkll_id = (isset($data['uSkll_id'])) ? $data['uSkll_id'] : NULL;
        $this->uSkll_userID = (isset($data['uSkll_userID'])) ? $data['uSkll_userID'] : NULL;
        $this->uSkll_TagID = (isset($data['uSkll_TagID'])) ? $data['uSkll_TagID'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
