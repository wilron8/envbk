<?php

namespace People;

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

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'followUserButton' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \People\View\Helper\FollowUser();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
                'usrHelper' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \People\View\Helper\Helper();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
                'usrPolicy' => function($sm){
                $serviceLocator = $sm->getServiceLocator();
                $policy = new View\Helper\Policy();
                $policy->setPolicy($serviceLocator->get('UserPolicy'));
                return $policy;
                }
        ));
    }

}
