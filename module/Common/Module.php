<?php

/**
 * Description of Module
 *
 * @author kimsreng
 */

namespace Common;


use Common\View\Helper\Config as ViewConfig;
use Common\View\Helper\Util;

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
        return include __DIR__ . '/config/service.config.php';
    }
    public function getControllerPluginConfig() {
        return array(
            'invokables' => array(
                'config' => 'Common\Mvc\Controller\Plugin\Config'
            ),
        );
    }

    public function getViewHelperConfig() {
        return [
            'factories' => [
                'config' => function($sm) {
                    $config = new ViewConfig();
                    $config->setUtil($sm->getServiceLocator()->get('Config')['sysConfig']);
                    return $config;
                },
                'util' => function($sm) {
                    $util = new Util();
                    $util->setUtil($sm->getServiceLocator()->get('Util'));
                    return $util;
                }
            ],
            'invokables' => [
                'listErrors' => 'Common\View\Helper\ListErrors',
                'listSuccess' => 'Common\View\Helper\ListSuccess',
                'filterErrors' => 'Common\View\Helper\DisplayFilterErrors',
            ]
        ];
    }

}
