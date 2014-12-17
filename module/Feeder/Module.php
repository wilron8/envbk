<?php

namespace Feeder;

use Feeder\DbEntity\Notify;
use Feeder\DbTable\NotifyTable;
use Feeder\Model\Feed;
use Feeder\ViewHelper\Feed as FeedHelper;
use Feeder\Model\NotifyManager;
use Zend\Db\ResultSet\ResultSet;
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
        return [
            'factories' => [
                'NotifyTable' => function($sm) {
            $tableGateway = $sm->get('NotifyTableGateway');
            $table = new NotifyTable($tableGateway);
            return $table;
        },
                'NotifyTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Notify());
            return new TableGateway('notify', $dbAdapter, null, $resultSetPrototype);
        },
                'Feed' => function($sm) {
            return new Feed($sm->get('Zend\Db\Adapter\Adapter'));
        },
                'NotifyManager'=>function($sm){
                    $mgr = new NotifyManager($sm->get('NotifyTable'), $sm->get('ViewHelperManager'),$sm);
                    return $mgr;
                }
            ]
        ];
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'feed' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new FeedHelper();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        })
        );
    }

}
