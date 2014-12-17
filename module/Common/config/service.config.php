<?php
use Common\Notification\EmailEngine;
use Common\Notification\NotifyUser;
use Common\Notification\NotifyAdmin;
use Common\Mail\EmailSender;
use Common\Mail\MailServer;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return [
        'factories' => array(
            'Mail' => function($sm) {
                /// http://support.godaddy.com/help/article/4714/setting-up-your-email-address-with-imap
                $server = new MailServer($sm->get('Config')['mailServer'], $sm->get('geoCountryTable'),$sm->get('geoContinentTable'));
                return $server->getServer();
            },
            'EmailSender' => function($sm) {
                $emailSender = new EmailSender($sm->get('Mail'), $sm->get('ViewRenderer'));
                return $emailSender;
            },
            'EmailEngine'=>function($sm){
                $engine = new EmailEngine($sm->get('EmailSender'));
                return $engine;
            },
            'NotifyUser'=>function($sm){
                $notify = new NotifyUser($sm->get('EmailEngine'),$sm->get('translator'));
                return $notify;
            },
            'NotifyAdmin'=>function($sm){
                $notify = new NotifyAdmin($sm->get('EmailEngine'),$sm->get('translator'),$sm->get('Config')['sysConfig']);
                return $notify;
            },
            'geoContinentTable' => function($sm) {
                $tableGateway = $sm->get('geoContinentTableGateway');
                $table = new \Common\DbTable\geoContinentTable($tableGateway);
                return $table;
            },
            'geoContinentTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new \Common\DbEntity\geoContinent());
                return new TableGateway('geoContinent', $dbAdapter, null, $resultSetPrototype);
            },
            'geoCountryTable' => function($sm) {
                $tableGateway = $sm->get('geoCountryTableGateway');
                $table = new \Common\DbTable\geoCountryTable($tableGateway);
                return $table;
            },
            'geoCountryTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new HydratingResultSet();
                $resultSetPrototype->setObjectPrototype(new \Common\DbEntity\geoCountry());
                return new TableGateway('geoCountry', $dbAdapter, null, $resultSetPrototype);
            },
            'geoStateCityTable' => function($sm) {
                $tableGateway = $sm->get('geoStateCityTableGateway');
                $table = new \Common\DbTable\geoStateCityTable($tableGateway);
                return $table;
            },
            'geoStateCityTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new \Common\DbEntity\geoStateCity());
                return new TableGateway('geoStateCity', $dbAdapter, null, $resultSetPrototype);
            },
            'geoCityTable' => function($sm) {
                $tableGateway = $sm->get('geoCityTableGateway');
                $table = new \Common\DbTable\geoCityTable($tableGateway);
                return $table;
            },
            'geoCityTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new \Common\DbEntity\geoCity());
                return new TableGateway('geoCity', $dbAdapter, null, $resultSetPrototype);
            },
            'geoLangTable' => function($sm) {
                $tableGateway = $sm->get('geoLangTableGateway');
                $table = new \Common\DbTable\geoLangTable($tableGateway);
                return $table;
            },
            'geoLangTableGateway' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new \Common\DbEntity\geoLang);
                return new TableGateway('geoLang', $dbAdapter, null, $resultSetPrototype);
            },
            'Util' => function($sm){
                $util = new \Common\Util\Util();
                $util->setViewHelperManager($sm->get('ViewHelperManager'));
                return $util;
            }
        ),
        'invokables' => [
            'ClientSniffer' => 'Common\ClientSniffer\Sniffer'
        ]
    ];


