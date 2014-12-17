<?php

/**
 * Description of Config
 *
 * @author kimsreng
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Config extends AbstractHelper {

    protected $config;

    public function __invoke() {
        return $this;
    }

    public function setConfig(Array $config) {
        $this->config = $config;
    }

    public function get($key) {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
        return false;
    }

    public function getConfig() {
        return $this->config;
    }

}
