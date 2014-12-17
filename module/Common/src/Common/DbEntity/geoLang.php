<?php

/**
 * Description of geoLang
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbEntity;

use Common\DbEntity\EntityInterface;

class geoLang  implements EntityInterface{

    public $geoLang_id;
    public $geoLang_name;
    public $geoLang_roman;
    public $geoLang_isRTL;
    public $geoLang_ISO639;
    public $geoLang_isVisible;
    public $geoLang_isSupported;

    function exchangeArray($data) {
        $this->geoLang_id = (isset($data['geoLang_id'])) ? $data['geoLang_id'] : NULL;
        $this->geoLang_name = (isset($data['geoLang_name'])) ? $data['geoLang_name'] : NULL;
        $this->geoLang_roman = (isset($data['geoLang_roman'])) ? $data['geoLang_roman'] : NULL;
        $this->geoLang_isRTL = (isset($data['geoLang_isRTL'])) ? $data['geoLang_isRTL'] : NULL;
        $this->geoLang_ISO639 = (isset($data['geoLang_ISO639'])) ? $data['geoLang_ISO639'] : NULL;
        $this->geoLang_isVisible = (isset($data['geoLang_isVisible'])) ? $data['geoLang_isVisible'] : NULL;
        $this->geoLang_isSupported = (isset($data['geoLang_isSupported'])) ? $data['geoLang_isSupported'] : NULL;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
