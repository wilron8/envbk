<?php

/**
 * Description of Smpt
 *
 * @author kimsreng
 */

namespace Common\Mail\Transport;

use Zend\Mail\Transport\Smtp as ZendSmtp;

class Smtp extends ZendSmtp {

    public function testConnection() {
       return $this->connect();
    }

}
