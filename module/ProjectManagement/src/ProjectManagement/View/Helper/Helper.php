<?php

/**
 * Description of Helper
 *
 * @author kimsreng
 */

namespace ProjectManagement\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

class Helper extends AbstractHelper {

    protected $user = NULL;
    protected $idea = NULL;
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

    public function getOwner($projectId, $sameCopy = false) {
        if ($sameCopy) {
            if ($this->user) {
                return $this->user;
            }
        }
        $sql = new Sql($this->sl->get('Zend\Db\Adapter\Adapter'));
        $project = $sql->select();
        $project->columns(array());
        $project->from(array('pm' => 'projectMember'));
        $project->join('user', 'pm.pMem_memberID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $project->where(array('pm.pMem_projectID' => $projectId,'pMem_isPM'));
        $statement = $sql->prepareStatementForSqlObject($project);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        if ($sameCopy) {
            $this->user = $result->current();
            return $this->user;
        }
        return $result->current();
    }
    public function getIdea($ideaId, $sameCopy = false) {
        if ($sameCopy) {
            if ($this->idea) {
                return $this->idea;
            }
        }
        $sql = new Sql($this->sl->get('Zend\Db\Adapter\Adapter'));
        $idea = $sql->select();
        $idea->from('idea');
        $idea->where(array('idea_id' => $ideaId));
        $statement = $sql->prepareStatementForSqlObject($idea);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        if ($sameCopy) {
            $this->idea = $result->current();
            return $this->idea;
        }
        return $result->current();
    }
    
    public function buildMenu($projectObject,$isOwner,$user) {
        $isMembershipOpen = ($projectObject->proj_isMemberShipOpen == 1) ? true : false;
        $vm = new ViewModel(array(
            'project' => $projectObject,
            'isOwner'=>$isOwner,
            'membershipStatus' => $isMembershipOpen,
            'user'=>$user,
            'MemberTable'=>  $this->sl->get('ProjectMemberTable'),
        ));
        $vm->setTemplate("project-management/helper/project-menu.phtml");
        return $this->getView()->render($vm);
    }
    
    public function getIcon($projectObject) {
        $params = []; //should be new array();
        if (is_array($projectObject)) {
            $params['proj_id'] = $projectObject['proj_id'];
            $params['proj_img'] = $projectObject['proj_img'];
        } elseif (is_object($projectObject)) {
            $params['proj_id'] = $projectObject->proj_id;
            $params['proj_img'] = $projectObject->proj_img;
        } else {
            throw new \Exception('Project must be either array or object');
        }
        
        $vm = new ViewModel($params);
        $vm->setTemplate("project-management/helper/project-icon.phtml");
        return $this->getView()->render($vm);
    }
    
    public function getComment($projectId){
        $commentList = $this->sl->get('ProjectWallTable')->getComments($projectId);
        $project = $this->sl->get('ProjectTable')->getById($projectId);
        $vm = new ViewModel(array(
            'commentList' => $commentList,
            'project' => $project,
            'commentTable' => $this->sl->get('ProjectWallTable'),
        ));
        $vm->setTemplate("project-management/helper/project-comment.phtml");
        return $this->getView()->render($vm);
    }
    
    public function isMember($projectId,$userId){
        return $this->sl->get('ProjectMemberTable')->isMember($userId,$projectId);
    }

}
