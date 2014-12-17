<?php

/**
 * Description of Policy
 *
 * @author kimsreng
 */

namespace People\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Policy extends AbstractHelper {

    protected $policy;

    public function __invoke() {
        return $this->policy;
    }

    public function setPolicy($policy) {
        $this->policy = $policy;
    }

}
