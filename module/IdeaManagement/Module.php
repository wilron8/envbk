<?php

namespace IdeaManagement;

use IdeaManagement\Policy\Policy;
use IdeaManagement\Model\DbEntity\Idea;
use IdeaManagement\Model\DbTable\IdeaTable;
use IdeaManagement\Model\DbEntity\Category;
use IdeaManagement\Model\DbTable\CategoryTable;
use IdeaManagement\Model\DbEntity\IdeaRef;
use IdeaManagement\Model\DbTable\IdeaRefTable;
use IdeaManagement\Model\DbEntity\FollowIdea;
use IdeaManagement\Model\DbTable\FollowIdeaTable;
use IdeaManagement\Model\IdeaManager;
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
                'IdeaTable' => function($sm) {
            $tableGateway = $sm->get('IdeaTableGateway');
            $table = new IdeaTable($tableGateway);
            return $table;
        },
                'IdeaManager' => function($sm) {
            $manager = new IdeaManager($sm);
            return $manager;
        },
                'IdeaTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Idea());
            return new TableGateway('idea', $dbAdapter, null, $resultSetPrototype);
        },
                'CategoryTable' => function($sm) {
            $tableGateway = $sm->get('CategoryTableGateway');
            $table = new CategoryTable($tableGateway);
            return $table;
        },
                'CategoryTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new HydratingResultSet();
            $resultSetPrototype->setObjectPrototype(new Category());
            return new TableGateway('category', $dbAdapter, null, $resultSetPrototype);
        },
                'IdeaRefTable' => function($sm) {
            $tableGateway = $sm->get('IdeaRefTableGateway');
            $table = new IdeaRefTable($tableGateway);
            return $table;
        },
                'IdeaRefTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new IdeaRef());
            return new TableGateway('ideaReference', $dbAdapter, null, $resultSetPrototype);
        },
                'FollowIdeaTable' => function($sm) {
            $tableGateway = $sm->get('FollowIdeaTableGateway');
            $table = new FollowIdeaTable($tableGateway);
            return $table;
        },
                'FollowIdeaTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new FollowIdea());
            return new TableGateway('followIdea', $dbAdapter, null, $resultSetPrototype);
        },
                'IdeaCommentTable' => function($sm) {
            $tableGateway = $sm->get('IdeaCommentTableGateway');
            $table = new \IdeaManagement\Model\DbTable\IdeaCommentTable($tableGateway);
            return $table;
        },
                'IdeaCommentTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \IdeaManagement\Model\DbEntity\IdeaComment());
            return new TableGateway('ideaComment', $dbAdapter, null, $resultSetPrototype);
        },
                'ViolationReportTable' => function($sm) {
            $tableGateway = $sm->get('ViolationReportTableGateway');
            $table = new \IdeaManagement\Model\DbTable\ViolationReportTable($tableGateway);
            return $table;
        },
                'ViolationReportTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \IdeaManagement\Model\DbEntity\ViolationReport());
            return new TableGateway('violationReport', $dbAdapter, null, $resultSetPrototype);
        },
                'IdeaPolicy' => function($sm) {
            $policy = new Policy($sm->get('UserTable'), $sm->get('IdeaTable'));
            return $policy;
        }
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'followIdeaButton' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \IdeaManagement\View\Helper\FollowIdea();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
                'youtube' => function($sm) {
            $helper = new \IdeaManagement\View\Helper\Youtube();
            return $helper;
        },
                'ideaHelper' => function($sm) {
            $helper = new \IdeaManagement\View\Helper\Helper();
            $serviceLocator = $sm->getServiceLocator();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
                'ideaPolicy' => function($sm) {
            $policy = new \IdeaManagement\View\Helper\Policy();
            $policy->setPolicy($sm->getServiceLocator()->get('IdeaPolicy'));
            return $policy;
        },
        ));
    }

}
