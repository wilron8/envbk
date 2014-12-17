<?php

use People\Model\DbEntity\FollowPeople;
use People\Model\DbTable\FollowPeopleTable;
use People\Model\DbTable\CvTable;
use People\Policy\Policy;
use People\Model\PeopleManager;
use People\Model\EducationManager;
use People\Model\ExperienceManager;
use People\Model\ContactManager;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return [
    'factories' => array(
        'PeopleManager' => function($sm) {
            $manager = new PeopleManager($sm);
            return $manager;
        },
        'EducationManager' => function($sm){
            $ed = new EducationManager($sm->get('EducationTable'), $sm->get('CvTable'), $sm->get('translator'));
            return $ed;
        },
        'ExperienceManager' => function($sm){
            $xp = new ExperienceManager($sm->get('ExperienceTable'), $sm->get('CvTable'), $sm->get('translator'));
            return $xp;
        },
        'ContactManager' => function($sm){
            $c = new ContactManager($sm->get('UserEmailTable'), $sm->get('UserPhoneTable'));
            return $c;
        },
        'CvTable' => function($sm){
            return new CvTable($sm->get('Zend\Db\Adapter\Adapter'));
        },
        'FollowPeopleTable' => function($sm) {
            $tableGateway = $sm->get('FollowPeopleTableGateway');
            $table = new FollowPeopleTable($tableGateway, $sm);
            return $table;
        },
        'FollowPeopleTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new FollowPeople());
            return new TableGateway('followPeople', $dbAdapter, null, $resultSetPrototype);
        },
        'SkillTagTable' => function($sm) {
            $tableGateway = $sm->get('SkillTagTableGateway');
            $table = new \People\Model\DbTable\SkillTagTable($tableGateway);
            return $table;
        },
        'SkillTagTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new HydratingResultSet();
            $resultSetPrototype->setObjectPrototype(new \People\Model\DbEntity\SkillTag());
            return new TableGateway('skillTag', $dbAdapter, null, $resultSetPrototype);
        },
        'UserSkillTable' => function($sm) {
            $tableGateway = $sm->get('UserSkillTableGateway');
            $table = new \People\Model\DbTable\UserSkillTable($tableGateway);
            return $table;
        },
        'UserSkillTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \People\Model\DbEntity\UserSkill());
            return new TableGateway('userSkill', $dbAdapter, null, $resultSetPrototype);
        },
        'EducationTable' => function($sm) {
            $tableGateway = $sm->get('EducationTableGateway');
            $table = new \People\Model\DbTable\EducationTable($tableGateway);
            return $table;
        },
        'EducationTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \People\Model\DbEntity\Education());
            return new TableGateway('education', $dbAdapter, null, $resultSetPrototype);
        },
        'ExperienceTable' => function($sm) {
            $tableGateway = $sm->get('ExperienceTableGateway');
            $table = new \People\Model\DbTable\ExperienceTable($tableGateway);
            return $table;
        },
        'ExperienceTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \People\Model\DbEntity\Experience());
            return new TableGateway('experience', $dbAdapter, null, $resultSetPrototype);
        },
        'CertificateTagTable' => function($sm) {
            $tableGateway = $sm->get('CertificateTagTableGateway');
            $table = new \People\Model\DbTable\CertificateTagTable($tableGateway);
            return $table;
        },
        'CertificateTagTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \People\Model\DbEntity\CertificateTag());
            return new TableGateway('CertificateTag', $dbAdapter, null, $resultSetPrototype);
        },
        'UserCertificateTable' => function($sm) {
            $tableGateway = $sm->get('UserCertificateTableGateway');
            $table = new \People\Model\DbTable\UserCertificationTable($tableGateway);
            return $table;
        },
        'UserCertificateTableGateway' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \People\Model\DbEntity\UserCertification());
            return new TableGateway('userCertification', $dbAdapter, null, $resultSetPrototype);
        },
        'UserPolicy' => function($sm){
            $policy = new Policy($sm->get('UserTable'));
            return $policy;
        }
    )
];
