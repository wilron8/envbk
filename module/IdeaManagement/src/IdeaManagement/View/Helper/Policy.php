<?php

/**
 * Description of Policy
 *
 * @author kimsreng
 */

namespace IdeaManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Policy extends AbstractHelper {

    /**
     * @var \IdeaManagement\Policy\Policy 
     */
    protected $policy;

    public function __invoke() {
        return $this->policy;
    }

    public function setPolicy($policy) {
        $this->policy = $policy;
    }

}
