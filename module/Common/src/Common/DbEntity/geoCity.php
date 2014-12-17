<?php

/**
 * Description of geoCity
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbEntity;

use Common\DbEntity\EntityInterface;

class geoCity implements EntityInterface{

    public $geoCity_id;
    public $geoCity_ISO3166;
    public $geoCity_ISO3166_2;
    public $geoCity_cityName;
	//TODO: add geoCity_cityRoman
    public $geoCity_PostalCode;
    public $geoCity_lat; 
    public $geoCity_lng; 
    public $geoCity_metroCode; 
    public $geoCity_areaCode; 

    function exchangeArray($data) {
        $this->geoCity_id = (isset($data['geoCity_id'])) ? $data['geoCity_id'] : NULL;
        $this->geoCity_ISO3166 = (isset($data['geoCity_ISO3166'])) ? $data['geoCity_ISO3166'] : NULL;
        $this->geoCity_ISO3166_2 = (isset($data['geoCity_ISO3166_2'])) ? $data['geoCity_ISO3166_2'] : NULL;
        $this->geoCity_cityName = (isset($data['geoCity_cityName'])) ? $data['geoCity_cityName'] : NULL;
        $this->geoCity_PostalCode = (isset($data['geoCity_PostalCode'])) ? $data['geoCity_PostalCode'] : NULL;
        $this->geoCity_lat = (isset($data['geoCity_lat'])) ? $data['geoCity_lat'] : NULL;
        $this->geoCity_lng = (isset($data['geoCity_lng'])) ? $data['geoCity_lng'] : NULL;
        $this->geoCity_metroCode = (isset($data['geoCity_metroCode'])) ? $data['geoCity_metroCode'] : NULL;
        $this->geoCity_areaCode = (isset($data['geoCity_areaCode'])) ? $data['geoCity_areaCode'] : NULL;
     
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
