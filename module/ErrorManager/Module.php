<?php

/**
 * Description of Module
 *
 * @author kimsreng
 */

namespace ErrorManager;

use ErrorManager\Service\Email;
use ErrorManager\Service\LogManager;
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

    public function onBootstrap(MvcEvent $e) {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        //handle the dispatch error (exception) 
        $em->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleError'));
        //handle the view render error (exception) 
        $em->attach(\Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR, array($this, 'handleError'));
    }

    /**
     * 
     * @param \ErrorManager\MvcEvent $e
     */
    public function handleError(MvcEvent $e) {
        //get the exception
        $exception = $e->getParam('exception');
        $sm = $e->getApplication()->getServiceManager();
        //send error to admin emails
        if ($exception) {
            $sm->get('LogManager')->log($exception);
            $e->setController('ErrorManager\Controller\Error');
        }
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'ErrorMail' => function($sm) {
            $errorMail = new Email($sm->get('Mail'), $sm->get('ViewRenderer'), $sm->get('Config')['sysConfig']['adminEmail']);
            return $errorMail;
        },
                'SecondErrorMail' => function($sm) {
            $errorMail = new Email($sm->get('SecondMail'), $sm->get('ViewRenderer'), $sm->get('Config')['sysConfig']['adminEmail']);
            return $errorMail;
        },
                'LogManager' => function($sm) {
            $log = new LogManager($sm->get('Config')['logMethods'], $sm);
            return $log;
        }
            ),
        );
    }

}
