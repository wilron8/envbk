<?php

/**
 * Description of MenuWidget
 *
 * @author kimsreng
 */

namespace MenuManager\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class MenuWidget extends AbstractHelper {

    /**
     * Service manager instance
     * @var Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $serviceLocator;

    public function __invoke() {
        $vm = new ViewModel();
        if ($this->serviceLocator->get('AuthService')->hasIdentity()) {
            $userId = $this->serviceLocator->get('AuthService')->getIdentity()->usr_id;
            //messages
            $messageTable = $this->serviceLocator->get('MessageTable');
            $messages = $messageTable->fetchAllByUserId($userId);
            $vm->setVariable('messages', $messages);
            //ideas
            $ideaTable = $this->serviceLocator->get('IdeaTable');
            $ideas = $ideaTable->fetchAllByUserId($userId);
            $vm->setVariable('ideas', $ideas);
            //projects
            $projectTable = $this->serviceLocator->get('ProjectTable');
            $projects = $projectTable->fetchCreated($userId);
            $vm->setVariable('projects', $projects);
            //people
            $followPeopleTable = $this->serviceLocator->get('FollowPeopleTable');
            $people = $followPeopleTable->fetchAllForUser($userId);
            $vm->setVariable('people', $people);
            $vm->followers = $followPeopleTable->getCountFollowers($userId);
            $vm->following = $followPeopleTable->getCountFollowees($userId);
        }
        $vm->setTemplate("menu-manager/helper/menu-widget.phtml");
        return $this->getView()->render($vm);
    }

    /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

}
