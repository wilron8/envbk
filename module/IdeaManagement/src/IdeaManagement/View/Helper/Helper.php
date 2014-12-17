<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helper
 *
 * @author kimsreng
 */

namespace IdeaManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class Helper extends AbstractHelper {

    protected $sl;

    public function __invoke() {
        return $this;
    }

    /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->sl = $serviceLocator;
    }

    public function getReference($idea_id) {
        $ideaRef = $this->sl->get('IdeaRefTable')->getByNewIdea($idea_id);
        $vm = new ViewModel(array(
            'ideaRef' => $ideaRef
        ));
        $vm->setTemplate("idea-management/helper/idea-reference.phtml");
        return $this->getView()->render($vm);
    }

    public function getEvolution($ideaObject) {
        $table = $this->sl->get('IdeaTable');
        $ascending = $table->getAscending($ideaObject->idea_id);
        $descending = $table->getDescending($ideaObject->idea_id);
        $vm = new ViewModel(array(
            'ascending' => $ascending,
            'descending' => $descending,
        ));
        $vm->setTemplate("idea-management/helper/idea-evolution.phtml");
        return $this->getView()->render($vm);
    }

    public function getProject($ideaObject) {
        $projects = $this->sl->get('ProjectTable')->fetchByIdea($ideaObject->idea_id)->toArray();
        $vm = new ViewModel(array(
            'projects' => $projects
        ));
        $vm->setTemplate("idea-management/helper/idea-project.phtml");
        return $this->getView()->render($vm);
    }

    public function getComment($ideaObject) {
        $commentList = $this->sl->get('IdeaCommentTable')->getComments($ideaObject->idea_id);
        $commentTable = $this->sl->get('IdeaCommentTable');
        $userId = $this->sl->get('AuthService')->getIdentity()->usr_id;
        $vm = new ViewModel(array(
            'commentList' => $commentList,
            'commentTable' => $commentTable,
            'idea' => $ideaObject,
            'userId' => $userId,
        ));
        $vm->setTemplate("idea-management/helper/idea-comment.phtml");
        return $this->getView()->render($vm);
    }

    public function getCommentGuest($ideaObject) {
        $commentList = $this->sl->get('IdeaCommentTable')->getComments($ideaObject->idea_id, 5);
        $commentTable = $this->sl->get('IdeaCommentTable');
        $vm = new ViewModel(array(
            'commentList' => $commentList,
            'commentTable' => $commentTable,
            'idea' => $ideaObject
        ));
        $vm->setTemplate("idea-management/helper/idea-comment-guest.phtml");
        return $this->getView()->render($vm);
    }

    public function commentMenu($commentObj, $ideaObj) {
        $currentUserId = $this->sl->get('AuthService')->getIdentity()->usr_id;
        $vm = new ViewModel(array(
            'comment' => $commentObj,
            'userId' => $currentUserId,
            'ideaObj' => $ideaObj
        ));
        $vm->setTemplate("idea-management/helper/comment-menu.phtml");
        return $this->getView()->render($vm);
    }

    public function buildMenu($ideaObject, $originator, $categoryTable) {
        $vm = new ViewModel(array(
            'idea' => $ideaObject,
            'isOwner' => $this->sl->get('AuthService')->hasIdentity() && $this->sl->get('AuthService')->getIdentity()->usr_id == $originator->usr_id,
            'originator' => $originator,
            'categoryTable' => $categoryTable
        ));
        $vm->setTemplate("idea-management/helper/idea-menu.phtml");
        return $this->getView()->render($vm);
    }

    public function followButton($idea) {
        $auth = $this->sl->get("AuthService");
        $userId = $auth->hasIdentity() ? $auth->getIdentity()->usr_id : false;
        $vm = new ViewModel(array(
            'userId' => $userId,
            'idea' => $idea,
            'followTable' => $this->sl->get('followIdeaTable'),
        ));
        $vm->setTemplate("idea-management/helper/follow-button.phtml");
        return $this->getView()->render($vm);
    }

    public function getIcon($ideaObject) {
        $params = [];
        if (is_array($ideaObject)) {
            $params['idea_id'] = $ideaObject['idea_id'];
            $params['idea_img'] = $ideaObject['idea_img'];
        } elseif (is_object($ideaObject)) {
            $params['idea_id'] = $ideaObject->idea_id;
            $params['idea_img'] = $ideaObject->idea_img;
        } else {
            throw new \Exception('Idea must be either array or object');
        }

        $vm = new ViewModel($params);
        $vm->setTemplate("idea-management/helper/idea-icon.phtml");
        return $this->getView()->render($vm);
    }

    public function getFollower($ideaId) {
        $vm = new ViewModel(array(
            'users' => $this->sl->get('IdeaTable')->getFollower($ideaId),
        ));
        $vm->setTemplate("idea-management/helper/following-user.phtml");
        return $this->getView()->render($vm);
    }

    public function isFollowed($ideaId, $userId) {
        $followTable = $this->sl->get("FollowIdeaTable");
        return $followTable->isUserFollowIdea($userId, $ideaId);
    }

}
