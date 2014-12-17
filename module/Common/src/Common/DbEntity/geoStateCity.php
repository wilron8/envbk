<?php

/**
 * Description of geoStateCity
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbEntity;

use Common\DbEntity\EntityInterface;

class geoStateCity  implements EntityInterface{

    public $geoStateCity_id;
    public $geoStateCity_ISO3166;
    public $geoStateCity_ISO3166_2;
    public $geoStateCity_FIPS10_4;
    public $geoStateCity_cityState;
    public $geoStateCity_roman; 
    public $geoStateCity_demonym; 
    public $geoStateCity_lat; 
    public $geoStateCity_lng; 

    function exchangeArray($data) {
        $this->geoStateCity_id = (isset($data['geoStateCity_id'])) ? $data['geoStateCity_id'] : NULL;
        $this->geoStateCity_ISO3166 = (isset($data['geoStateCity_ISO3166'])) ? $data['geoStateCity_ISO3166'] : NULL;
        $this->geoStateCity_ISO3166_2 = (isset($data['geoStateCity_ISO3166_2'])) ? $data['geoStateCity_ISO3166_2'] : NULL;
        $this->geoStateCity_FIPS10_4 = (isset($data['geoStateCity_FIPS10_4'])) ? $data['geoStateCity_FIPS10_4'] : NULL;
        $this->geoStateCity_cityState = (isset($data['geoStateCity_cityState'])) ? $data['geoStateCity_cityState'] : NULL;
        $this->geoStateCity_roman = (isset($data['geoStateCity_roman'])) ? $data['geoStateCity_roman'] : NULL;
        $this->geoStateCity_demonym = (isset($data['geoStateCity_demonym'])) ? $data['geoStateCity_demonym'] : NULL;
        $this->geoStateCity_lat = (isset($data['geoStateCity_lat'])) ? $data['geoStateCity_lat'] : NULL;
        $this->geoStateCity_lng = (isset($data['geoStateCity_lng'])) ? $data['geoStateCity_lng'] : NULL;
     
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
