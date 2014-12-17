<?php

namespace Users;

use Users\Model\DbEntity\User;
use Users\Model\DbTable\UserTable;
use Users\Model\DbEntity\Userjoin;
use Users\Model\DbTable\UserjoinTable;
use Users\Form\PreSignupForm;
use Users\Form\Filter\PreSignupFilter;
use Users\Model\Authentication\AuthenticationService;
use Users\Model\Authentication\Auth;
use Users\Model\Authentication\AuthAdapter as DbTableAuthAdapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Mvc\ModuleRouteListener;

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

    public function onBootstrap($e) {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
        $this->sniffUserSession($e);
    }

    public function bootstrapSession($e) {
        $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
        $session->start();
        $session->getConfig()->setCookieHttpOnly(true);
        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    public function sniffUserSession($e) {
        $e->getApplication()
                ->getServiceManager()
                ->get('SessionTable')->sniffUser();
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                
                'Users\Model\Authentication\Storage' => function($sm) {
            return new \Users\Model\Authentication\Storage();
        },
                'AuthService' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'usr_username', 'usr_password');

            $authService = new AuthenticationService();
            $authService->setSessionTable($sm->get('SessionTable'));
            $authService->setServiceLocator($sm);
            $authService->setAdapter($dbTableAuthAdapter);
            $authService->setStorage($sm->get('Users\Model\Authentication\Storage'));
            return $authService;
        },
                'Auth' => function($sm){
                    return new Auth($sm);
                },
                //table
                'UserTable' => function($sm) {
            $tableGateway = $sm->get('UserTableGateway');
            $table = new UserTable($tableGateway, $sm);
            return $table;
        },
                'UserTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
        },
                'UserjoinTable' => function($sm) {
            $tableGateway = $sm->get('UserjoinTableGateway');
            $table = new UserjoinTable($tableGateway, $sm);
            return $table;
        },
                'UserjoinTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Userjoin());
            return new TableGateway('userjoin', $dbAdapter, null, $resultSetPrototype);
        },
                'UserAddressTable' => function($sm) {
            $tableGateway = $sm->get('UserAddressTableGateway');
            $table = new \Users\Model\DbTable\UserAddressTable($tableGateway);
            return $table;
        },
                'UserAddressTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new HydratingResultSet();
            $resultSetPrototype->setObjectPrototype(new \Users\Model\DbEntity\UserAddress);
            return new TableGateway('userAddress', $dbAdapter, null, $resultSetPrototype);
        },
                'UserLangTable' => function($sm) {
            $tableGateway = $sm->get('UserLangTableGateway');
            $table = new \Users\Model\DbTable\UserLangTable($tableGateway);
            return $table;
        },
                'UserLangTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\DbEntity\UserLang);
            return new TableGateway('userLang', $dbAdapter, null, $resultSetPrototype);
        },
                'UserPhoneTable' => function($sm) {
            $tableGateway = $sm->get('UserPhoneTableGateway');
            $table = new \Users\Model\DbTable\UserPhoneTable($tableGateway);
            return $table;
        },
                'UserPhoneTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\DbEntity\UserPhone);
            return new TableGateway('userPhone', $dbAdapter, null, $resultSetPrototype);
        },
                'UserEmailTable' => function($sm) {
            $tableGateway = $sm->get('UserEmailTableGateway');
            $table = new \Users\Model\DbTable\UserEmailTable($tableGateway);
            return $table;
        },
                'UserEmailTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new HydratingResultSet();
            $resultSetPrototype->setObjectPrototype(new \Users\Model\DbEntity\UserEmail);
            return new TableGateway('userEmail', $dbAdapter, null, $resultSetPrototype);
        },
                'UserPassForgotTable' => function($sm) {
            $tableGateway = $sm->get('UserPassForgotTableGateway');
            $table = new \Users\Model\DbTable\UserPassForgotTable($tableGateway, $sm);
            return $table;
        },
                'UserPassForgotTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\DbEntity\UserPassForgot);
            return new TableGateway('userPassForgot', $dbAdapter, null, $resultSetPrototype);
        },
                'SessionTable' => function($sm) {
            $tableGateway = $sm->get('SessionTableGateway');
            $table = new \Users\Model\DbTable\SessionTable($tableGateway, $sm);
            return $table;
        },
                'SessionTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\DbEntity\Session);
            return new TableGateway('session', $dbAdapter, null, $resultSetPrototype);
        },
                'UserLoginTable' => function($sm) {
            $tableGateway = $sm->get('UserLoginTableGateway');
            $table = new \Users\Model\DbTable\UserLoginTable($tableGateway);
            return $table;
        },
                'UserLoginTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Users\Model\DbEntity\UserLogin);
            return new TableGateway('userLogin', $dbAdapter, null, $resultSetPrototype);
        },
                //Form
                'PreSignupForm' => function($sm) {
            $form = new PreSignupForm();
            $form->setInputFilter($sm->get('PreSignupFilter'));
            return $form;
        },
                'SignupForm' => function($sm) {
            $form = new \Users\Form\SignupForm($sm);
            $form->setInputFilter($sm->get('SignupFilter'));
            return $form;
        },
                'SignupConfirmForm' => function($sm) {
            $form = new \Users\Form\SignupConfirmForm($sm);
            $form->setInputFilter(new \Users\Form\Filter\SignupConfirmFilter($sm));
            return $form;
        },
                'LoginForm' => function ($sm) {
            $form = new \Users\Form\LoginForm();
            $form->setInputFilter($sm->get('LoginFilter'));
            return $form;
        },
                //Filter
                'PreSignupFilter' => function($sm) {
            return new PreSignupFilter();
        },
                'SignupFilter' => function($sm) {
            return new \Users\Form\Filter\SignupFilter($sm);
        },
                'LoginFilter' => function ($sm) {
            return new \Users\Form\Filter\LoginFilter();
        },
                'Zend\Session\SessionManager' => function ($sm) {
            $config = $sm->get('config');
            if (isset($config['session'])) {
                $session = $config['session'];

                $sessionConfig = NULL;
                if (isset($session['config'])) {
                    $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                    $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                    $sessionConfig = new $class();
                    $sessionConfig->setOptions($options);
                }

                $sessionStorage = NULL;
                if (isset($session['storage'])) {
                    $class = $session['storage'];
                    $sessionStorage = new $class();
                }

                $sessionSaveHandler = NULL;
                if (isset($session['save_handler'])) {
                    // class should be fetched from service manager since it will require constructor arguments
                    $sessionSaveHandler = $sm->get($session['save_handler']);
                }

                $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                if (isset($session['validator'])) {
                    $chain = $sessionManager->getValidatorChain();
                    foreach ($session['validator'] as $validator) {
                        $validator = new $validator();
                        $chain->attach('session.validate', array($validator, 'isValid'));
                    }
                }
            } else {
                $sessionManager = new SessionManager();
            }
            Container::setDefaultManager($sessionManager);
            return $sessionManager;
        },
            ),
            'alias' => array(
                'sessionManager' => 'Zend\Session\SessionManager'
            ),
        );
    }

    public function getControllerPluginConfig() {
        return array(
            'invokables' => array(
                'laIdentity' => 'Users\Plugin\Identity'
            ),
        );
    }

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'laIdentity' => function($sm) {
            $serviceLocator = $sm->getServiceLocator();
            $helper = new \Users\View\Helper\LaIdentity();
            $helper->setAuthService($serviceLocator->get('AuthService'));
            $helper->setService($serviceLocator);
            return $helper;
        },
                'laLoginWidget' => function($sm) {
            $locator = $sm->getServiceLocator();
            $helper = new \Users\View\Helper\LoginWidget();
            $routeMatch = $locator->get('Application')->getMvcEvent()->getRouteMatch();
            $helper->setRouteMatched($routeMatch);
            return $helper;
        }
            ),
            'invokables' => array(
                'laJoinWidget' => 'Users\View\Helper\JoinWidget',
                'displayName'=>'Users\View\Helper\DisplayUserName',
            )
        );
    }

}
