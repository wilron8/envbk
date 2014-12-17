<?php

/**
 * Description of LangController
 *
 * @author kimsreng
 */
namespace LangManagement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class LangController extends AbstractActionController{
    
    public function setLangAction(){
        $session = new Container('default');
        $session->language=  $this->params()->fromRoute('lang');
        if($this->request->getServer('HTTP_REFERER')){
           return $this->redirect()->toUrl($this->request->getServer('HTTP_REFERER')); 
        }
        return $this->redirect()->toRoute('home');
    }
}
