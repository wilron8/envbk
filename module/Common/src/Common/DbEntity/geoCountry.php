<?php

/**
 * Description of geoCountry
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbEntity;

use Common\DbEntity\AbstractEntity;

class geoCountry extends AbstractEntity {

    public $geoCountry_id = NULL;
    public $geoCountry_name = NULL;
    public $geoCountry_demonym = NULL;
    public $geoCountry_roman = NULL;
    public $geoCountry_ISO3166 = NULL;
    public $geoCountry_ISO3166_2 = NULL;
    public $geoCountry_lang = NULL;
    public $geoCountry_currencyName = NULL;
    public $geoCountry_currencyAbbrev = NULL;
    public $geoCountry_currencySign = NULL;
    public $geoCountry_currencyPatternPos = NULL;
    public $geoCountry_currencyPatternNeg = NULL;
    public $geoCountry_tz = NULL;
    public $geoCountry_dst = 0;
    public $geoCountry_localize_decimalSign = '.';
    public $geoCountry_localize_decimalPlaces = 0;
    public $geoCountry_localize_numGroupSign = ',';
    public $geoCountry_localize_numGroupPattern = NULL;
    public $geoCountry_localize_date = 'Y/m/d';
    public $geoCountry_localize_dateLong = NULL;
    public $geoCountry_localize_time = 'H:i:s';
    public $geoCountry_continent = 0;
    public $geoCountry_internetTLD = NULL;
    public $geoCountry_latitude = NULL;
    public $geoCountry_lngitude = NULL;
    public $geoCountry_isVisible = 0;
    public $geoCountry_flagImg = NULL;
    public $geoCountry_callingCode = NULL;

    function exchangeArray($data) {
        $this->geoCountry_id = (isset($data['geoCountry_id'])) ? $data['geoCountry_id'] : $this->geoCountry_id;
        $this->geoCountry_name = (isset($data['geoCountry_name'])) ? $data['geoCountry_name'] : $this->geoCountry_name;
        $this->geoCountry_demonym = (isset($data['geoCountry_demonym'])) ? $data['geoCountry_demonym'] :  $this->geoCountry_demonym;
        $this->geoCountry_roman = (isset($data['geoCountry_roman'])) ? $data['geoCountry_roman'] :  $this->geoCountry_roman;
        $this->geoCountry_ISO3166 = (isset($data['geoCountry_ISO3166'])) ? $data['geoCountry_ISO3166'] : $this->geoCountry_ISO3166;
        $this->geoCountry_ISO3166_2 = (isset($data['geoCountry_ISO3166_2'])) ? $data['geoCountry_ISO3166_2'] : $this->geoCountry_ISO3166_2;
        $this->geoCountry_lang = (isset($data['geoCountry_lang'])) ? $data['geoCountry_lang'] : $this->geoCountry_lang;
        $this->geoCountry_currencyName = (isset($data['geoCountry_currencyName'])) ? $data['geoCountry_currencyName'] : $this->geoCountry_currencyName;
        $this->geoCountry_currencyAbbrev = (isset($data['geoCountry_currencyAbbrev'])) ? $data['geoCountry_currencyAbbrev'] : $this->geoCountry_currencyAbbrev;
        $this->geoCountry_currencySign = (isset($data['geoCountry_currencySign'])) ? $data['geoCountry_currencySign'] : $this->geoCountry_currencySign;
        $this->geoCountry_currencyPatternPos = (isset($data['geoCountry_currencyPatternPos'])) ? $data['geoCountry_currencyPatternPos'] : $this->geoCountry_currencyPatternPos;
        $this->geoCountry_currencyPatternNeg = (isset($data['geoCountry_currencyPatternNeg'])) ? $data['geoCountry_currencyPatternNeg'] : $this->geoCountry_currencyPatternNeg;
        $this->geoCountry_tz = (isset($data['geoCountry_tz'])) ? $data['geoCountry_tz'] : $this->geoCountry_tz;
        $this->geoCountry_dst = (isset($data['geoCountry_dst'])) ? $data['geoCountry_dst'] : $this->geoCountry_dst;
        $this->geoCountry_localize_decimalSign = (isset($data['geoCountry_localize_decimalSign'])) ? $data['geoCountry_localize_decimalSign'] : $this->geoCountry_localize_decimalSign;
        $this->geoCountry_localize_decimalPlaces = (isset($data['geoCountry_localize_decimalPlaces'])) ? $data['geoCountry_localize_decimalPlaces'] : $this->geoCountry_localize_decimalPlaces;
        $this->geoCountry_localize_numGroupSign = (isset($data['geoCountry_localize_numGroupSign'])) ? $data['geoCountry_localize_numGroupSign'] : $this->geoCountry_localize_numGroupSign;
        $this->geoCountry_localize_numGroupPattern = (isset($data['geoCountry_localize_numGroupPattern'])) ? $data['geoCountry_localize_numGroupPattern'] : $this->geoCountry_localize_numGroupPattern;
        $this->geoCountry_localize_date = (isset($data['geoCountry_localize_date'])) ? $data['geoCountry_localize_date'] : $this->geoCountry_localize_date;
        $this->geoCountry_localize_dateLong = (isset($data['geoCountry_localize_dateLong'])) ? $data['geoCountry_localize_dateLong'] : $this->geoCountry_localize_dateLong;
        $this->geoCountry_localize_time = (isset($data['geoCountry_localize_time'])) ? $data['geoCountry_localize_time'] : $this->geoCountry_localize_time;
        $this->geoCountry_continent = (isset($data['geoCountry_continent'])) ? $data['geoCountry_continent'] : $this->geoCountry_continent;
        $this->geoCountry_internetTLD = (isset($data['geoCountry_internetTLD'])) ? $data['geoCountry_internetTLD'] : $this->geoCountry_internetTLD;
        $this->geoCountry_latitude = (isset($data['geoCountry_latitude'])) ? $data['geoCountry_latitude'] : $this->geoCountry_latitude;
        $this->geoCountry_lngitude = (isset($data['geoCountry_lngitude'])) ? $data['geoCountry_lngitude'] : $this->geoCountry_lngitude;
        $this->geoCountry_isVisible = (isset($data['geoCountry_isVisible'])) ? $data['geoCountry_isVisible'] : $this->geoCountry_isVisible;
        $this->geoCountry_flagImg = (isset($data['geoCountry_flagImg'])) ? $data['geoCountry_flagImg'] : $this->geoCountry_flagImg;
        $this->geoCountry_callingCode = (isset($data['geoCountry_callingCode'])) ? $data['geoCountry_callingCode'] : $this->geoCountry_callingCode;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    protected function outputScenario() {
        return[
            'country' => ['geoCountry_id', 'geoCountry_roman']
        ];
    }

}

?>
