<?php

/**
 * Allow access to project policy in view
 *
 * @author kimsreng
 */

namespace ProjectManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ProjectManagement\Policy\Policy as MgrPolicy;

class Policy extends AbstractHelper {

    protected $policy;

    public function __invoke() {
        return $this->policy;
    }

    /**
     * Get policy from service manager
     */
    public function setPolicy(MgrPolicy $policy) {
        $this->policy = $policy;
    }

}
