<?php

/**
 * Description of Auth
 *
 * @author kimsreng
 */

namespace Users\Model\Authentication;

class Auth {

    protected $request;
    protected $userTable;
    protected $userLoginTable;
    protected $sessionTable;
    protected $authService;
    protected $sessionMgr;
    protected $geoLangTable;

    public function __construct($serviceLocator) {
        $this->request = $serviceLocator->get('Request');
        $this->userLoginTable = $serviceLocator->get('UserLoginTable');
        $this->userTable = $serviceLocator->get('UserTable');
        $this->sessionTable = $serviceLocator->get('SessionTable');
        $this->authService = $serviceLocator->get('AuthService');
        $this->sessionMgr = $serviceLocator->get('Zend\Session\SessionManager');
        $this->geoLangTable = $serviceLocator->get('geoLangTable');
    }

    public function authenticate($username, $password, $remember = false) {
        $this->authService->getAdapter()
                ->setIdentity($username)
                ->setCredential($password);
        $result =  $this->authService->authenticate();
        if ($result->isValid()) {
            // save last login to user table
            $user = $this->userTable->getByEmail($username);
            $user->usr_lastLogin = date('Y-m-d H:i:s');
            $user->usr_lastIP = $this->request->getServer('REMOTE_ADDR');
            $this->userTable->update($user);
            $ISO639 = $this->geoLangTable->getById($user->usr_lang)->geoLang_ISO639;
            $sessionObject = $this->authService->getAdapter()->getResultRowObject(array(
                'usr_username',
                'usr_id',
                'usr_displayName',
                'usr_fName',
                'usr_lName',
                'usr_lang',
                'usr_icon'
            ));
            $sessionObject->iso639 = $ISO639;
            $storage = $this->authService->getStorage();
            $storage->write($sessionObject);
            //remember user login if remember me is checked
            $storage->setRememberMe($remember);
        }
        // track userLogin
        $this->trackLogin($username, $result);

        return $result;
    }

    public function trackLogin($identity, $result) {
        $user = $this->userTable->getByEmail($identity);
        if ($user) {
            $lastLogin = $this->userLoginTable->getLastLogin($user->usr_id);
            $login = new \Users\Model\DbEntity\UserLogin();
            $login->uLgin_userID = $user->usr_id;
            $login->uLgin_timeStamp = date('Y-m-d H:i:s');
            $login->uLgin_ip = $this->request->getServer('REMOTE_ADDR');
            $login->uLgin_mobile = 0; //TODO: grab parameter from JS to get this info
            if ($lastLogin) {
                $login->uLgin_attempt++;
            } else {
                $login->uLgin_attempt = 0;
            }
            $login->uLgin_fail = !($result->isValid());
            $this->userLoginTable->insert($login);

            //save userId to session table
            $sessId = $this->sessionMgr->getId();
            $sessRow = $this->sessionTable->getBySessId($sessId);
            if ($sessRow) {
                $sessRow->sess_userID = $user->usr_id;
                $sessRow->sess_loginCnt++;
                $this->sessionTable->update($sessRow);
            }
        }
    }

}
