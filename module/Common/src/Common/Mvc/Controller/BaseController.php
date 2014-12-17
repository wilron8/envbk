<?php

/**
 * Base controller to provide some handy functions
 *
 * @author kimsreng
 */

namespace Common\Mvc\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController {

    const VIEW_MODEL = "ViewModel";
    const JSON_MODEL = "JsonModel";

    /**
     * @var Zend\Session\Container 
     */
    private $session = NULL;

    /**
     * @var Zend\View\Model\ViewModel 
     */
    protected $view = NULL;

    /**
     * View instance option (ViewModel or JsonModel)
     * 
     * @var string
     */
    protected $viewType = self::VIEW_MODEL;

    /**
     * This array variable used to hold accumulated errors so that we can get easily in view
     * 
     * @var array 
     */
    public $errors = [];

    /**
     * This array variable used to hold accumulated success alert so that we can get easily in view
     * 
     * @var array 
     */
    public $success = [];

    /**
     * Shortcut function get message translated
     * 
     * @param string $message
     * @return string
     */
    protected function translate($message) {
        return $this->get('translator')->translate($message);
    }

    /**
     * Shortcut function to get serivce through service locator
     * 
     * @param string $serviceId
     * @return service object
     */
    protected function get($serviceId) {
        return $this->getServiceLocator()->get($serviceId);
    }

    /**
     * Get view helper in controller
     * 
     * @param string $viewHelper
     * @return ViewHelper
     */
    public function getViewHelper($viewHelper) {
        $helper = $this->get('viewhelpermanager')->get($viewHelper);
        return $helper;
    }

    /**
     * 
     * @return Zend\Session\Container
     */
    protected function getSession() {
        if ($this->session == NULL) {
            $this->session = new Zend\Session\Container('default');
        }
        return $this->session;
    }

    /**
     * Instantiate a view that is equipped with errors and success variable
     * viewType should be assign before calling this function
     */
    protected function initView() {
        $view = "\Zend\View\Model\\" . $this->viewType;
        $this->view = new $view();
        if ($this->viewType == self::VIEW_MODEL) {
            $this->view->context = $this;
        }
    }

    /**
     * check if user has already authenticated otherwise redirect to signin page
     * 
     * @return redirection
     */
    protected function checkAuthentication() {
        if (!$this->get('AuthService')->hasIdentity()) {
            return $this->redirect()->toRoute('user', array('action' => 'signin'));
        }
    }

    /**
     * check if user is authenticated
     * 
     * @return boolean
     */
    protected function isUserAuthenticated() {
        return $this->get('AuthService')->hasIdentity();
    }

    /**
     * Return the page with 404 custom message
     * 
     * @param string $message
     * @return ViewResponse
     */
    protected function return404($message = "") {
        if ($this->view==NULL) {
            $this->initView();
        }
        
        if ($this->viewType != self::JSON_MODEL) {
            //TODO:Custom style is needed :)
            $this->view->setTemplate('common/http-error/404');
        } else {
            $this->view->success = false;
        }
        
        if ($message == "") {
            $message = $this->translate("Sorry, the page you're looking for cannot be found.");
        }
        $this->view->msg = $message;

        return $this->view;
    }

    /**
     * Convert Zend\Db\Adapter\Driver\Pdo\Result to array
     * 
     * @param type Zend\Db\Adapter\Driver\Pdo\Result
     * @return array
     */
    protected function toArray($resultSet) {
        $data = [];
        foreach ($resultSet as $row) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Function to send email with html content
     * @param string $view view template for mail
     * @param array $data data passed to the view
     * @param array $mail_config sendmail property
     */
    protected function sendMail($view, $data, $mail_config) {

        $this->get('EmailSender')->sendTemplate($view, $data, $mail_config, array('info@linspira.com'), 'Linspira.com');
        //IT SEEMS THAT SEND MAIL FUNCTION DOES NOT RETURN BOOLEAN
        /*
          if (!$this->get('EmailSender')->sendTemplate($view, $data, $mail_config, array('info@linspira.com'), 'Linspira.com')) {
          echo "Hmm, it seems that we are experiencing a technical issue with our back-end email provider. We will address this as quickly as possible. We thank you for your patience and apologize for the inconvenience.";
          }
         */
    }

}
