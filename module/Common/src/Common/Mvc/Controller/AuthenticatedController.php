<?php

/**
 * Description of AuthenticatedController
 *
 * @author kimsreng
 */

namespace Common\Mvc\Controller;

use Common\Mvc\Controller\BaseController;
use People\Model\UserInfo;

class AuthenticatedController extends BaseController {

    /**
     * This var would hold any actions that dont need authentication
     * 
     * @var array 
     */
    protected $nonAuthenticatedActions = [];

    /**
     * Currently logged-in user id
     * @var integer 
     */
    public $userId = NULL;
    protected $user = NULL;

    /**
     * Allow only authenticated users to reach this page
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $routeMatched = $e->getRouteMatch();
        if (!in_array($routeMatched->getParam('action'), $this->nonAuthenticatedActions)) {
            if (!$this->laIdentity()->hasIdentity()) {
                //save last page for next login 
                if ($_SERVER['REQUEST_URI'] == '/logout') {
                    $url = $this->url()->fromRoute('feed', array(), array('canonical' => true));
                } else {
                    $url = $this->getCurrentUrl();
                }
                setcookie('start_page', $url, time() + 60 * 60 * 24 * 7, '/', null, false, true);

                return $this->redirect()->toRoute('user', array('action' => 'signin'));
            }
            $this->userId = $this->laIdentity()->getId();
        }
        return parent::onDispatch($e);
    }

    protected function getCurrentUrl() {
        $pageURL = "http" . (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "s" : "") . "://" . $_SERVER["SERVER_NAME"];

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= ":" . $_SERVER["SERVER_PORT"];
        }
        $pageURL .= $_SERVER["REQUEST_URI"];

        return $pageURL;
    }

    /**
     * Get user info
     * 
     * @return UserInfo
     */
    public function getUser() {
        if ($this->user === NULL) {
            $this->user = $this->get('AuthService')->getUser();
        }
        return $this->user;
    }

}
