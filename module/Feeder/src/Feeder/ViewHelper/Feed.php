<?php

/**
 * Description of Feed
 *
 * @author kimsreng
 */

namespace Feeder\ViewHelper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class Feed extends AbstractHelper {

    protected $sl = NULL;
    protected $ideaTable = NULL;
    protected $followIdeaTable = NULL;
    protected $userTable = NULL;
    protected $projectTable = NULL;
    protected $projectMemberTable = NULL;
    protected $projectWallTable = NULL;
    protected $ideaCommentTable = NULL;
    protected $ideaRefTable = NULL;

    public function __invoke() {
        return $this;
    }

    /**
     * set service manager
     * 
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->sl = $serviceLocator;
        $this->userTable = $this->sl->get('UserTable');
        $this->projectTable = $this->sl->get('ProjectTable');
        $this->ideaTable = $this->sl->get('IdeaTable');
        $this->ideaCommentTable = $this->sl->get('IdeaCommentTable');
        $this->projectWallTable = $this->sl->get('ProjectWallTable');
        $this->projectMemberTable = $this->sl->get('ProjectMemberTable');
        $this->followIdeaTable = $this->sl->get('FollowIdeaTable');
        $this->ideaRefTable = $this->sl->get('IdeaRefTable');
    }

    public function render($type, $id) {
        $type = (int) $type;
        switch ($type) {
            case 1:
                return $this->createIdea($id);
            case 2://not implemented 'cause it is already reflected in comment
                return $this->modifiedIdea($id);
            case 3:
                return $this->commentedIdea($id);
            case 4:
                return $this->followIdea($id);
            case 5:
                return $this->referenceIdea($id);
            case 6:
                return $this->projectOnIdea($id);
            case 7:
                return $this->evolveIdea($id);
            case 100:
                return $this->startProject($id);
            case 101:
                return $this->commentedProject($id);
            case 102:
                return $this->joinProject($id);
            default:
                break;
        }
    }

    /**
     * When people you follow create ideas
     * 
     * @param type $id
     */
    public function createIdea($id) {
        $idea = $this->ideaTable->getById($id);
        $user = $this->userTable->getById($idea->idea_originator);
        $vm = new ViewModel(array(
            'idea' => $idea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/create_idea.phtml");
        return $this->getView()->render($vm);
    }

    public function modifiedIdea($id) {
        
    }

    public function commentedIdea($id) {
        $iCom = $this->ideaCommentTable->getById($id);
        $idea = $this->ideaTable->getById($iCom->iComm_ideaId);
        $user = $this->userTable->getById($iCom->iComm_userId);
        $vm = new ViewModel(array(
            'iCom' => $iCom,
            'idea' => $idea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/commented_idea.phtml");
        return $this->getView()->render($vm);
    }

    public function followIdea($id) {
        $ideaFollow = $this->followIdeaTable->getById($id);
        $idea = $this->ideaTable->getById($ideaFollow->fi_ideaID);
        $user = $this->userTable->getById($ideaFollow->fi_userID);
        $vm = new ViewModel(array(
            'ideaFollow' => $ideaFollow,
            'idea' => $idea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/follow_idea.phtml");
        return $this->getView()->render($vm);
    }

    public function referenceIdea($id) {
        $iRef = $this->ideaRefTable->getById($id);
        $idea = $this->ideaTable->getById($iRef->iRef_srcIdea);
        $newIdea = $this->ideaTable->getById($iRef->iRef_newIdea);
        $user = $this->userTable->getById($newIdea->idea_originator);
        $vm = new ViewModel(array(
            'iRef' => $iRef,
            'newIdea' => $newIdea,
            'idea' => $idea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/reference_idea.phtml");
        return $this->getView()->render($vm);
    }

    public function projectOnIdea($id) {
        $project = $this->projectTable->getById($id);
        $pMem = $this->projectMemberTable->getPM($project->proj_id);
        $idea = $this->ideaTable->getById($project->proj_srcIdea);
        $user = $this->userTable->getById($pMem['usr_id']);
        $vm = new ViewModel(array(
            'project' => $project,
            'idea' => $idea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/project_on_idea.phtml");
        return $this->getView()->render($vm);
    }
    
    public function evolveIdea($id) {
        $idea = $this->ideaTable->getById($id);
        $parentIdea = $this->ideaTable->getById($idea->idea_nodeParent);
        $user = $this->userTable->getById($idea->idea_originator);
        $vm = new ViewModel(array(
            'idea' => $idea,
            'parentIdea'=>$parentIdea,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/evolve_idea.phtml");
        return $this->getView()->render($vm);
    }

    public function startProject($id) {
        $project = $this->projectTable->getById($id);
        $pMem = $this->projectMemberTable->getPM($project->proj_id);
        $user = $this->userTable->getById($pMem['usr_id']);
        $vm = new ViewModel(array(
            'project' => $project,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/start_project.phtml");
        return $this->getView()->render($vm);
    }

    public function commentedProject($id) {
        $pWall = $this->projectWallTable->getById($id);
        $project = $this->projectTable->getById($pWall->prjW_projID);
        $user = $this->userTable->getById($pWall->prjW_userid);
        $vm = new ViewModel(array(
            'pWall' => $pWall,
            'project' => $project,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/commented_project.phtml");
        return $this->getView()->render($vm);
    }

    public function joinProject($id) {
        $pMem = $this->projectMemberTable->getById($id);
        $project = $this->projectTable->getById($pMem->pMem_projectID);
        $user = $this->userTable->getById($pMem->pMem_memberID);
        $vm = new ViewModel(array(
            'pMem' => $pMem,
            'project' => $project,
            'user' => $user
        ));
        $vm->setTemplate("feeder/helper/join_project.phtml");
        return $this->getView()->render($vm);
    }

}
