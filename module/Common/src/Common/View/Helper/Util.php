<?php

/**
 * Description of Util
 *
 * @author kimsreng
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Util extends AbstractHelper {

    protected $util;

    public function __invoke() {
        return $this->util;
    }

    public function setUtil($util) {
        $this->util = $util;
    }

}
