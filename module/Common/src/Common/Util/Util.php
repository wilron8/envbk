<?php

/**
 * Description of Util
 *
 * @author kimsreng
 */

namespace Common\Util;

use Common\Util\StringHelper;
use Common\Util\ArrayHelper;
use Zend\View\HelperPluginManager;

class Util {

    protected $stringHelper = NULL;
    protected $arrayHelper = NULL;

    /**
     * @var HelperPluginManager 
     */
    protected $viewHelperMgr = NULL;

    public function setViewHelperManager($mgr) {
        $this->viewHelperMgr = $mgr;
    }

    public function helpString() {
        if ($this->stringHelper == NULL) {
            $this->stringHelper = new StringHelper();
        }
        return $this->stringHelper;
    }

    public function helpArray() {
        if ($this->arrayHelper == NULL) {
            $this->arrayHelper = new ArrayHelper();
        }
        return $this->arrayHelper;
    }

    /**
     * Get a view helper
     * 
     * @param string $helper view helper name
     * @return ViewHelper
     * @throws Exception
     */
    public function viewHelper($helper) {
        if ($this->viewHelperMgr->has($helper)) {
            return $this->viewHelperMgr->get($helper);
        }
        throw new Exception("$helper is not a view helper");
    }

}
