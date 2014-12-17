<?php

/**
 * Description of CertificateTag
 *
 * @author kimsreng
 */
namespace People\Model\DbEntity;

use Common\DbEntity\AbstractEntity;

class CertificateTag extends AbstractEntity{
    public $cert_id;
    public $cert_text;
    public $cert_timeStamp;
    
    public function exchangeArray($data){
        $this->cert_id = (isset($data['cert_id'])) ? $data['cert_id'] : NULL;
        $this->cert_text = (isset($data['cert_text'])) ? $data['cert_text'] : NULL;
        $this->cert_timeStamp = (isset($data['cert_timeStamp'])) ? $data['cert_timeStamp'] : NULL;
    }
    
    public function getArrayCopy(){
        return get_object_vars($this);
    }
    
    protected function outputScenario() {
        return[
            'json'=>['cert_id','cert_text']
        ];
    }
}
