<?php

/**
 * Description of laIdentity
 *
 * @author kimsreng
 */

namespace Users\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use People\Model\UserInfo;
use Zend\ServiceManager\ServiceManager;

class LaIdentity extends AbstractHelper {

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var ServiceManager 
     */
    protected $service;

    /**
     * @var UserInfo
     */
    protected $user = NULL;

    public function __invoke() {
        if ($this->getAuthService()->hasIdentity()) {
            if ($this->user == NULL) {
                $this->user = new UserInfo($this->getAuthService()->getIdentity()->usr_id, $this->service);
            }
            return $this->user;
        } else {
            return false;
        }
    }

    /**
     * Get authService.
     *
     * @return AuthenticationService
     */
    public function getAuthService() {
        return $this->authService;
    }

    /**
     * Set authService.
     *
     * @param AuthenticationService $authService
     * @return \Users\View\Helper\LaIdentity
     */
    public function setAuthService(AuthenticationService $authService) {
        $this->authService = $authService;
        return $this;
    }

    public function setService($service) {
        $this->service = $service;
    }

}
