<?php

/**
 * Description of userFollow
 *
 * @author kimsreng
 */
namespace IdeaManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class FollowIdea  extends AbstractHelper{
    
     /**
     *Service manager instance
     * @var Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLocator;
    
    public function __invoke($userID,$ideaID) {
        $idea= $this->serviceLocator->get('IdeaTable')->getById($ideaID);
        
        $vm = new ViewModel(array(
            'followTable' =>  $this->serviceLocator->get('followIdeaTable'),
            'projectTable' =>  $this->serviceLocator->get('projectTable'),
            'userId'=>$userID,
            'ideaId'=>$ideaID,
            'isOwner'=>($this->serviceLocator->get('AuthService')->getIdentity()->usr_id==$idea->idea_originator)
        ));
        $vm->setTemplate("idea-management/helper/follow-idea.phtml");
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
