<?php

/**
 * Description of userFollow
 *
 * @author kimsreng
 */
namespace People\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class FollowUser  extends AbstractHelper{
    
     /**
     *Service manager instance
     * @var Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLocator;
    
    public function __invoke($userID) {
        $currentUserId= $this->serviceLocator->get('AuthService')->getIdentity()->usr_id;
        if($currentUserId==$userID){
            return '';
        }
        $vm = new ViewModel(array(
            'followTable' =>  $this->serviceLocator->get('followPeopleTable'),
            'userId'=>$userID
        ));
        $vm->setTemplate("people/helper/follow-user.phtml");
        return $this->getView()->render($vm);
    }
    
     /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
        $this->serviceLocator=$serviceLocator;
    }
}
