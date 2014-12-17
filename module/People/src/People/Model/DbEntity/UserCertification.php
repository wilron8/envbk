<?php

/**
 * Description of UserCertification
 *
 * @author kimsreng
 */

namespace People\Model\DbEntity;

class UserCertification implements \Common\DbEntity\EntityInterface{

    public $uCert_id;
    public $uCert_userID;
    public $uCert_TagID;

    public function exchangeArray($data) {
        $this->uCert_id = (isset($data['uCert_id'])) ? $data['uCert_id'] : NULL;
        $this->uCert_userID = (isset($data['uCert_userID'])) ? $data['uCert_userID'] : NULL;
        $this->uCert_TagID = (isset($data['uCert_TagID'])) ? $data['uCert_TagID'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
