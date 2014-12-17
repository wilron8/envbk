<?php

/**
 * Description of Storage
 *
 * @author kimsreng
 */
namespace Users\Model\Authentication;

use Zend\Authentication\Storage\Session;
 
class Storage extends Session
{
    public function setRememberMe($rememberMe = false, $time = 1209600/**Two weeks**/)
    {
         if ( $rememberMe ) {
             $this->session->getManager()->rememberMe($time);
         }
    }
     
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    } 
}
