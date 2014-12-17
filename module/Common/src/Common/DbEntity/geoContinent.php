<?php

/**
 * Description of geoContinent
 *
 * @author Rich@RichieBartlett.com
 */

namespace Common\DbEntity;

use Common\DbEntity\EntityInterface;

class geoContinent  implements EntityInterface{

    public $geocontinent_id;
    public $geocontinent_region;
    public $geocontinent_parent;
    public $geocontinent_visible;

    function exchangeArray($data) {
        $this->geocontinent_id = (isset($data['geocontinent_id'])) ? $data['geocontinent_id'] : NULL;
        $this->geocontinent_region = (isset($data['geocontinent_region'])) ? $data['geocontinent_region'] : NULL;
        $this->geocontinent_parent = (isset($data['geocontinent_parent'])) ? $data['geocontinent_parent'] : NULL;
        $this->geocontinent_visible = (isset($data['geocontinent_visible'])) ? $data['geocontinent_visible'] : NULL;
     
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}

?>
