<?php

/**
 * Description of Module
 *
 * @author kimsreng
 */

namespace LangManagement;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

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

    public function onBootstrap(MvcEvent $e) {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $this->setLang($e);
    }

    public function setLang(MvcEvent $e) {
        $sm = $e->getApplication()->getServiceManager();
        $session = new Container('default');
        if (!isset($session->language)) {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $session->language = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            } else {
                $session->language = "en_US";
            }
        }
        $sm->get('translator')->setLocale($session->language);
    }

    public function getServiceConfig() {
        return array(
        );
    }

}
