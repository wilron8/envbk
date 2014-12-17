<?php

namespace Message;

use Message\Model\DbEntity\Message;
use Message\Model\DbTable\MessageTable;
use Message\Model\DbEntity\MessageTo;
use Message\Model\DbTable\MessageToTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
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
            'factories' => array(
                'MessageTable' => function($sm) {
                    $tableGateway = $sm->get('MessageTableGateway');
                    $table = new MessageTable($tableGateway);
                    return $table;
                },
                'MessageTableGateway' => function($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new HydratingResultSet();
                $resultSetPrototype->setObjectPrototype(new Message());
                return new TableGateway('message', $dbAdapter, null, $resultSetPrototype);
                 },
                'MessageToTable' => function($sm) {
                    $tableGateway = $sm->get('MessageToTableGateway');
                    $table = new MessageToTable($tableGateway);
                    return $table;
                },
                'MessageToTableGateway' => function($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new HydratingResultSet();
                $resultSetPrototype->setObjectPrototype(new MessageTo());
                return new TableGateway('messageTo', $dbAdapter, null, $resultSetPrototype);
                 }
            ),
        );
    }
    
    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'msgHelper' => function($sm) {
            $helper = new \Message\View\Helper\Helper();
            $serviceLocator = $sm->getServiceLocator();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
        ));
    }

}
