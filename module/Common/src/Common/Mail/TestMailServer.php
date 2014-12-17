<?php

/**
 * Description of TestMailServer
 *
 * @author kimsreng
 */

namespace Common\Mail;

use Common\Mail\MailServer;

class TestMailServer extends MailServer {

    public function __construct() {
        parent::__construct(null, null);
    }

    public function test($mailConfig) {
        return $this->testServer($mailConfig);
    }

}
