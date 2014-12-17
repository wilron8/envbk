<?php

/**
 * Description of Module
 *
 * @author kimsreng
 */

namespace MenuManager;

use Zend\Mvc\MvcEvent;

class Module {

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig() {
        return array(
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'laMenuWidget' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \MenuManager\View\Helper\MenuWidget();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        }
            )
        );
    }

}
