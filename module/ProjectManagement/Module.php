<?php

namespace ProjectManagement;

use ProjectManagement\Model\DbEntity\Project;
use ProjectManagement\Model\DbTable\ProjectTable;
use ProjectManagement\Model\DbEntity\ProjectMember;
use ProjectManagement\Model\DbTable\ProjectMemberTable;
use ProjectManagement\Model\ProjectManager;
use ProjectManagement\Policy\Policy;
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
        return array(
            'factories' => array(
                'ProjectTable' => function($sm) {
            $tableGateway = $sm->get('ProjectTableGateway');
            $table = new ProjectTable($tableGateway);
            return $table;
        },
                'ProjectManager' => function($sm) {
            $manager = new ProjectManager($sm);
            return $manager;
        },
                'ProjectPolicy' => function($sm) {
            $policy = new Policy($sm->get('ProjectTable'), $sm->get('ProjectMemberTable'));
            return $policy;
        },
                'ProjectTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Project());
            return new TableGateway('project', $dbAdapter, null, $resultSetPrototype);
        },
                'ProjectMemberTable' => function($sm) {
            $tableGateway = $sm->get('ProjectMemberTableGateway');
            $table = new ProjectMemberTable($tableGateway, $sm);
            return $table;
        },
                'ProjectMemberTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ProjectMember());
            return new TableGateway('projectMember', $dbAdapter, null, $resultSetPrototype);
        },
                'ProjectPersonTypeTable' => function($sm) {
            $tableGateway = $sm->get('ProjectPersonTypeTableGateway');
            $table = new \ProjectManagement\Model\DbTable\ProjectPersonTypeTable($tableGateway);
            return $table;
        },
                'ProjectPersonTypeTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \ProjectManagement\Model\DbEntity\ProjectPersonType());
            return new TableGateway('projectPersonType', $dbAdapter, null, $resultSetPrototype);
        },
                'ProjectRolesTable' => function($sm) {
            $tableGateway = $sm->get('ProjectRolesTableGateway');
            $table = new \ProjectManagement\Model\DbTable\ProjectRolesTable($tableGateway);
            return $table;
        },
                'ProjectRolesTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \ProjectManagement\Model\DbEntity\ProjectRoles());
            return new TableGateway('projectRoles', $dbAdapter, null, $resultSetPrototype);
        },
                'ProjectWallTable' => function($sm) {
            $tableGateway = $sm->get('ProjectWallTableGateway');
            $table = new \ProjectManagement\Model\DbTable\ProjectWallTable($tableGateway);
            return $table;
        },
                'ProjectWallTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \ProjectManagement\Model\DbEntity\ProjectWall());
            return new TableGateway('projectWall', $dbAdapter, null, $resultSetPrototype);
        }
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'projHelper' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \ProjectManagement\View\Helper\Helper();
            $helper->setServiceLocator($serviceLocator);
            return $helper;
        },
                'projPolicy' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \ProjectManagement\View\Helper\Policy();
            $helper->setPolicy($serviceLocator->get('ProjectPolicy'));
            return $helper;
        },
        ));
    }

}
