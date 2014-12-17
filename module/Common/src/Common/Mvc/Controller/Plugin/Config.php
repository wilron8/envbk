<?php

/**
 * Controller helper to access system configuration
 *
 * @author kimsreng
 */

namespace Common\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Config extends AbstractPlugin {

    protected $config = array();

    public function setController(\Zend\Stdlib\DispatchableInterface $controller) {
        parent::setController($controller);
        $this->config = $this->getController()->getServiceLocator()->get('config')['sysConfig'];
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
