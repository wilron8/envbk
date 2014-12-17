<?php

/**
 * Description of Module
 *
 * @author kimsreng
 */

namespace DocumentManager;

use DocumentManager\Model\PathManager;
use DocumentManager\View\Helper\PathManager as PathManagerView;
use DocumentManager\Model\DbEntity\DocFile;
use DocumentManager\Model\DbEntity\DocFolder;
use DocumentManager\Model\DbEntity\DocMetaServer;
use DocumentManager\Model\DbEntity\DocMetaUser;
use DocumentManager\Model\DbTable\DocFileTable;
use DocumentManager\Model\DbTable\DocFolderTable;
use DocumentManager\Model\DbTable\DocMetaServerTable;
use DocumentManager\Model\DbTable\DocMetaUserTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\HydratingResultSet;

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
                //service
                'PathManager'=>function($sm){
                    $pathManager = PathManager::getInstance($sm->get('DocMetaServerTable')->getServer());
                    return $pathManager;
                },
                'DocFileTable' => function($sm) {
                    $tableGateway = $sm->get('DocFileTableGateway');
                    $table = new DocFileTable($tableGateway);
                    return $table;
                },
                'DocFileTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setObjectPrototype(new DocFile());
                    return new TableGateway('docFile', $dbAdapter, null, $resultSetPrototype);
                },
               'DocFolderTable' => function($sm) {
                    $tableGateway = $sm->get('DocFolderTableGateway');
                    $table = new DocFolderTable($tableGateway);
                    return $table;
                },
                'DocFolderTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setObjectPrototype(new DocFolder());
                    return new TableGateway('docFolder', $dbAdapter, null, $resultSetPrototype);
                },
               'DocMetaServerTable' => function($sm) {
                    $tableGateway = $sm->get('DocMetaServerTableGateway');
                    $table = new DocMetaServerTable($tableGateway);
                    return $table;
                },
                'DocMetaServerTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setObjectPrototype(new DocMetaServer());
                    return new TableGateway('docMetaServer', $dbAdapter, null, $resultSetPrototype);
                },
               'DocMetaUserTable' => function($sm) {
                    $tableGateway = $sm->get('DocMetaUserTableGateway');
                    $table = new DocMetaUserTable($tableGateway);
                    return $table;
                },
                'DocMetaUserTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setObjectPrototype(new DocMetaUser());
                    return new TableGateway('docMetaUser', $dbAdapter, null, $resultSetPrototype);
                }
            )
        );
    }
    
    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'pathManager' => function($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $helper = new PathManagerView();
                    $helper->setPathManager($serviceLocator->get('PathManager'));
                    return $helper;
                }
             ));
    }
    

}
