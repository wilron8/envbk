<?php

/**
 * Description of AuthenticationService
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace Users\Model\Authentication;

use Zend\Authentication\AuthenticationService as Service;
use People\Model\UserInfo;

class AuthenticationService extends Service {

    /**
     * @var \Users\Model\DbTable\SessionTable
     */
    protected $sessionTable = NULL;
    protected $serviceLocaotor = NULL;
    protected $user = NULL;

    public function setServiceLocator($serviceLocator) {
        $this->serviceLocaotor = $serviceLocator;
    }

    public function setSessionTable($sessionTable) {
        $this->sessionTable = $sessionTable;
    }

    /**
     * check if user is logged-in and session store the same IP;
     * 
     * @return boolean
     */
    public function hasIdentity() {
        $id_flag = FALSE; // assume not logged in!

        if (!$this->getStorage()->isEmpty()) {
            if ($this->sessionTable->hasIpChanged()) {
                $this->clearIdentity();
                session_regenerate_id();
            } else {
                $id_flag = TRUE;
            }
        }
        return $id_flag;
    }

    /**
     * Get logged in user Information
     * 
     * @return \People\Model\UserInfo
     * @throws Exception
     */
    public function getUser() {
        if ($this->hasIdentity()) {
            if($this->user === NULL){
                $this->user = new UserInfo($this->getIdentity()->usr_id, $this->serviceLocaotor);
            }
            return $this->user;
        }
        throw new Exception('User is not logged in.');
    }

}
