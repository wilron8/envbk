<?php

/**
 * Controller plugin to get identity data across all controllers
 *
 * @author kimsreng
 */
namespace Users\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Identity extends AbstractPlugin{
    protected $auth;
    public function setController(\Zend\Stdlib\DispatchableInterface $controller) {
        parent::setController($controller);
        $this->auth=  $this->getController()->getServiceLocator()->get('AuthService');
    }
    public function getId(){
       return $this->auth->getIdentity()->usr_id;
    }
    public function getUsername(){
       return $this->auth->getIdentity()->usr_username;
    }
    public function hasIdentity(){
        return $this->auth->hasIdentity();
    }
    public function clearIdentity(){
        $this->auth->clearIdentity();
    }
    public function getAuthService(){
        return $this->auth;
    }
}
