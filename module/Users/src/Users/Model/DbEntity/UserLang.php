<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserLang
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class UserLang {

    public $uLang_id;
    public $uLang_userID;
    public $uLang_lang;
    public $uLang_primary;

    function exchangeArray($data) {
        $this->uLang_id = (isset($data['uLang_id'])) ? $data['uLang_id'] : NULL;
        $this->uLang_userID = (isset($data['uLang_userID'])) ? $data['uLang_userID'] : NULL;
        $this->uLang_lang = (isset($data['uLang_lang'])) ? $data['uLang_lang'] : NULL;
        $this->uLang_primary = (isset($data['uLang_primary'])) ? $data['uLang_primary'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
