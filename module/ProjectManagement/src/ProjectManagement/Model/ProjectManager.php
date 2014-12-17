<?php

/**
 * Class to wrap around project management logic
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model;

use ProjectManagement\Model\DbTable\ProjectTable;
use ProjectManagement\Model\DbTable\ProjectWallTable;
use ProjectManagement\Model\DbTable\ProjectMemberTable;
use ProjectManagement\Model\DbTable\ProjectRolesTable;
use ProjectManagement\Model\DbTable\ProjectPersonTypeTable;
use ProjectManagement\Model\DbEntity\ProjectMember;
use ProjectManagement\Model\DbEntity\Project;
use ProjectManagement\Model\DbEntity\ProjectWall;
use IdeaManagement\Model\DbEntity\ViolationReport;
use DocumentManager\Model\ResourceType as Resource;

class ProjectManager {

    const ALREADY_JOIN = 'joined';
    const PROJECT_NOT_EXIST = 'non-project';
    const SUCCESS = 'success';
    const MEMBERSHIP_CLOSED = 'closed-membership';
    /**
     *
     * @var ProjectTable 
     */
    protected $ProjectTable = NULL;

    /**
     *
     * @var ProjectWallTable 
     */
    protected $ProjectWallTable = NULL;

    /**
     *
     * @var ProjectMemberTable 
     */
    protected $ProjectMemberTable = NULL;

    /**
     *
     * @var ProjectRolesTable 
     */
    protected $ProjectRolesTable = NULL;

    /**
     *
     * @var ProjectPersonTypeTable 
     */
    protected $ProjectPersonTypeTable = NULL;

    /**
     * User object representing owner of the project so that it can be accessed outside the class to void 
     * repetative query
     * 
     * @param type User
     */
    public $projectOwner = NULL;

    /**
     * A flag to check if project information is updated
     * 
     * @var boolean 
     */
    protected $isDirty = false;

    /**
     * A string containing accumulated notification of a project change
     * 
     * @var string 
     */
    protected $dirtyContent = "";

    /**
     *
     * @var \Common\Notification\NotifyUser 
     */
    protected $notifyUser = NULL;

    /**
     *
     * @var \Common\Notification\NotifyAdmin 
     */
    protected $notifyAdmin = NULL;

    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface  
     */
    protected $SL;

    public function __construct($SL) {
        $this->SL = $SL;
        $this->ProjectTable = $this->SL->get('ProjectTable');
        $this->ProjectMemberTable = $this->SL->get('ProjectMemberTable');
        $this->ProjectWallTable = $this->SL->get('ProjectWallTable');
        $this->notifyUser = $this->SL->get('NotifyUser');
        $this->notifyAdmin = $this->SL->get('NotifyAdmin');
    }

    /**
     * Create a new project
     * 
     * @param type $data
     * @param type $managerId
     * @return integer
     */
    public function createProject($data, $managerId) {
        $project = new Project();
        $project->exchangeArray($data);
        $project->proj_timeStamp = date('Y-m-d H:i:s');
        $projectId = $this->ProjectTable->insert($project);
        $project->proj_id = $projectId;
        // save members
        $hasManager = false;
        if (count($data['members']) > 0) {
            foreach ($data['members'] as $mem) {
                //do not add project manager again
                if ($mem['pMem_isPM'] == 1) {
                    $hasManager = true;
                }
                //ignor blank member row
                if ($mem['pMem_memberID'] == 0) {
                    continue;
                }
                $member = new ProjectMember();
                $member->exchangeArray($mem);
                $member->pMem_projectID = $projectId;
                $member->pMem_dateTime = date('Y-m-d H:i:s');
                $this->ProjectMemberTable->insert($member);
                //increment member count
                $project->proj_memCnt++;
            }
        }

        if (!$hasManager) {
            $pmRow = $this->ProjectMemberTable->fetchMembership($managerId, $projectId);
            if ($pmRow) {//if the user is already the member, just upgrade him to be the manager. otherwise, no one can update the project
                $pmRow->pMem_isPM = 1;
                $this->ProjectMemberTable->update($pmRow);
            } else {
                $this->ProjectMemberTable->createManager($projectId, $managerId);
                $project->proj_memCnt++;
            }
        }

        //save projMemcnt
        $this->ProjectTable->update($project);
        //increment project count in user table
        $userTable = $this->SL->get('UserTable');
        $user = $userTable->getById($managerId);
        $user->usr_projCnt++;
        $userTable->update($user);
        return $projectId;
    }

    /**
     * Join a user on request
     * 
     * @param type $projectId
     * @param type $userId
     * @return boolean
     */
    public function joinProject($projectId, $userId) {
        //check if the project is available(exist and not yet closed)
        $project = $this->ProjectTable->getById($projectId);
        if (!$project) {
            return self::PROJECT_NOT_EXIST;
        }

        // Check if membership is closed
        if ($project->proj_isMemberShipOpen == 0) {
            return self::MEMBERSHIP_CLOSED;
        }

        //check if the user is already a member
        if ($this->ProjectMemberTable->isMember($userId, $projectId)) {
            return self::ALREADY_JOIN;
        }
        // temporary add user to member table
        $member = new ProjectMember();
        $member->pMem_projectID = $projectId;
        $member->pMem_memberID = $userId;
        $pmemId = $this->ProjectMemberTable->insert($member);
        if (!$pmemId) {
            return false;
        }
        $user = $this->SL->get('UserTable')->getById($userId);

        //fill up notify table
        $this->SL->get('NotifyManager')->joinProject($project, $user);
        //send email to project Manager 
        $ownerId = $this->ProjectMemberTable->select(array('pMem_projectID' => $projectId, 'pMem_isPM' => '1'))->current()->pMem_memberID;
        $this->projectOwner = $this->SL->get('UserTable')->getById($ownerId);
        //notify the manager about the request
        $this->notifyUser->notifyPrjMemJoin($user, $this->projectOwner, $project, $pmemId);
        return self::SUCCESS;
    }

    /**
     * Approve membership
     * 
     * @param type $pMemId id of the projectMember row
     * @param type $userId Project Manager Id
     * @return boolean
     */
    public function approveMembership($pMemId, $userId) {
        $pmem = $this->ProjectMemberTable->getById($pMemId);
        if (!$pmem) {
            return false;
        }
        //make sure the approved one not reapproved
        if ($pmem->pMem_approvedState == 2) {
            return false;
        }
        //make sure only project manager can approve a membership
        $PmId = $this->ProjectMemberTable->fetchOne(array('pMem_projectID' => $pmem->pMem_projectID, 'pMem_isPM' => 1))->pMem_memberID;
        if ($PmId != $userId) {
            return false;
        }

        $pmem->pMem_approvedState = 1;
        $pmem->pMem_dateTime = date('Y-m-d H:i:s');
        $this->ProjectMemberTable->update($pmem);
        $project = $this->ProjectTable->getById($pmem->pMem_projectID);
        $user = $this->SL->get('UserTable')->getById($pmem->pMem_memberID);
        //notify the user about the approval
        $this->notifyUser->notifyPrjMemApprove($user, $project);
        //notify all members
        $this->SL->get('NotifyManager')->memberProject($project, $user, $this->SL->get('UserTable')->getById($userId));
        //Notify the join in project wall
        $wallContent = \Users\Model\DbEntity\User::getDisplayName($user) . " has joined the project";
        $this->createComment($pmem->pMem_projectID, $userId, $wallContent);
        return true;
    }

    /**
     * Reject a pending membership
     * 
     * @param integer $pMemId
     * @param string $reason
     * @param integer $userId
     * @return boolean
     */
    public function rejectMembership($pMemId, $reason, $userId) {
        $pmem = $this->ProjectMemberTable->getById($pMemId);
        if (!$pmem) {
            return false;
        }
        //only pMem_approvedState=0 can be rejected
        if ($pmem->pMem_approvedState != 0) {
            return false;
        }
        //make sure only project manager can approve a membership
        $PmId = $this->ProjectMemberTable->fetchOne(array('pMem_projectID' => $pmem->pMem_projectID, 'pMem_isPM' => 1))->pMem_memberID;
        if ($PmId != $userId) {
            return false;
        }

        $pmem->pMem_approvedState = 2;
        $pmem->pMem_rejectText = $reason;
        $this->ProjectMemberTable->update($pmem);
        $project = $this->ProjectTable->getById($pmem->pMem_projectID);
        $user = $this->SL->get('UserTable')->getById($pmem->pMem_memberID);
        //Notify the requesting user
        $this->notifyUser->notifyPrjMemReject($user, $project, $reason);

        return true;
    }

    /**
     * Update a project
     * 
     * @param Project $project
     * @param integer $pmId id of the project manager
     * @param Array $members list of current members
     * @param Array $removed list of removed members
     * @return boolean
     */
    public function updateProject($project, $pmId, $members, $removed) {
        //make sure only project manager can update the project
        if (!$this->ProjectMemberTable->isOwner($pmId, $project->proj_id)) {
            return false;
        }
        //a closed project cannot be updated
        if ($project->proj_isClosed == 1) {
            return false;
        }

        //get Old copy of the project 
        $oldCopy = $this->ProjectTable->getById($project->proj_id);

        if ($oldCopy->proj_title !== $project->proj_title) {
            $this->isDirty = true;
            $this->dirtyContent .= "Project title changed from <i>$oldCopy->proj_title</i> to <i>$project->proj_title</i><BR>";
        }

        if ($oldCopy->proj_img !== $project->proj_img) {
            $this->isDirty = true;
            if ($oldCopy->proj_img == "") {
                $this->dirtyContent .= "Project icon was added<BR>";
            } else {
                $this->dirtyContent .= "Project icon was changed<BR>";
            }
        }

        if ((int) $oldCopy->proj_progress !== (int) $project->proj_progress) {
            $this->isDirty = true;
            $this->dirtyContent .= "Project progressed from <i>" . number_format($oldCopy->proj_progress) . "%</i> to <i>$project->proj_progress%</i><BR>";
        }

        if ($oldCopy->proj_srcIdea !== $project->proj_srcIdea) {
            $this->isDirty = true;
            $oldIdea = $this->SL->get('IdeaTable')->getById($oldCopy->proj_srcIdea);
            $newIdea = $this->SL->get('IdeaTable')->getById($project->proj_srcIdea);
            $this->dirtyContent .= "Project source idea changed from <i>$oldIdea->idea_title</i> to <i>$newIdea->idea_title</i><BR>";
        }

        if ($oldCopy->proj_descript !== $project->proj_descript) {
            $this->isDirty = true;
            $this->dirtyContent .= "Project description changed from <blockquote><i>$oldCopy->proj_descript</i></blockquote> to <blockquote><i>$project->proj_descript</i></blockquote><BR>";
        }
        //update project
        $this->ProjectTable->update($project);

        $manager = $this->SL->get('UserTable')->getById($pmId);

        if ($this->isDirty) {
            $this->SL->get('NotifyManager')->modifiedProject($project, $manager);
        }
        //update members
        $this->processMembers($members, $removed, $project->proj_id, $manager);


        if ($this->isDirty) {
            $this->createComment($project->proj_id, $pmId, $this->dirtyContent);
        }

        return true;
    }

    /**
     * Method to add new members or delete old members
     * 
     * @param array $members
     * @param integer $projectId
     */
    public function processMembers(Array $members, Array $removed, $projectId, $user) {

        if (count($members) > 0) {
            // access project table to increase/decrease memCnt
            $project = $this->ProjectTable->getById($projectId);
            $new = [];
            $update = [];
            foreach ($members as $value) {
                if ($value['pMem_memberID'] == 0) {
                    continue;
                }
                if ($value['pMem_id'] == 0) {
                    $new[] = $value;
                } else {
                    $update[] = $value;
                }
            }
            if (count($new) > 0) {
                $this->addNewMember($project, $new, $user);
            }
            if (count($update) > 0) {
                $this->updateMember($update);
            }
        }
        if (count($removed) > 0) {
            $this->removeMember($project, $removed);
        }
    }

    /**
     * 
     * @param array $members
     */
    public function updateMember(Array $members) {
        foreach ($members as $value) {
            $mem = $this->ProjectMemberTable->getById($value['pMem_id']);
            if ($mem) {
                $value['pMem_role'] = $this->getProjectRole($value['pMem_role']);
                $mem->exchangeArray($value);
                $this->ProjectMemberTable->update($mem);
            }
        }
    }

    /**
     * 
     * @param \ProjectManagement\Model\DbEntity\Project $project
     * @param array $members
     * @return array list of user ids that were added to the project
     */
    public function addNewMember(Project $project, Array $members, $user) {
        if (count($members) > 0) {
            $this->isDirty = true;
            $added_members = [];
            foreach ($members as $n) {
                $member = new ProjectMember();
                $n['pMem_role'] = $this->getProjectRole($n['pMem_role']);
                $member->exchangeArray($n);
                $member->pMem_projectID = $project->proj_id;
                if ($n['pMem_approvedState'] == 1 && $user->usr_id != $n['pMem_memberID']) {
                    $this->SL->get('NotifyManager')->memberProject($project, $this->SL->get('UserTable')->getById($n['pMem_memberID']), $user);
                }
                $this->ProjectMemberTable->insert($member);
                $project->proj_memCnt++;
                $added_members[] = $n['pMem_memberID'];
            }
            $this->ProjectTable->update($project);
            $users = $this->SL->get('UserTable')->fetchAll("usr_id IN (" . implode(',', $added_members) . ")");
            $names = [];
            $url = $this->SL->get('Util')->viewHelper('url');
            foreach ($users as $value) {
                $names[] = "<a href='" . $url('people/profile', ['id' => $value['usr_id']]) . "'>" . \Users\Model\DbEntity\User::getDisplayName($value) . '</a>';
            }
            $names_string = implode(',', $names);
            $is = count($names) > 1 ? 'are' : 'is';
            $this->dirtyContent .= $names_string . " " . $is . " added as member of the project.";

            return $added_members;
        }
        return [];
    }

    /**
     * Remove members from a project
     * 
     * @param \ProjectManagement\Model\DbEntity\Project $project
     * @param array $members
     * @return array list of user ids that were removed from the project
     */
    public function removeMember(Project $project, Array $members) {
        if (count($members) > 0) {

            $del_users = []; //store users who are really removed from project
            foreach ($members as $mem) {
                $where = new \Zend\Db\Sql\Where();
                $where->equalTo('pMem_memberID', $mem['pMem_memberID'])
                        ->equalTo('pMem_projectID', $project->proj_id)
                        ->notEqualTo('pMem_isPM', 1); //disable deletion of Project Manager

                if ($this->ProjectMemberTable->deleteWhere($where)) {
                    $project->proj_memCnt--;
                    $del_users[] = $mem['pMem_memberID'];
                }
            }
            if (count($del_users) > 0) {
                $this->isDirty = true;
                $users = $this->SL->get('UserTable')->fetchAll("usr_id IN (" . implode(',', $del_users) . ")");
                $names = [];
                $url = $this->SL->get('Util')->viewHelper('url');
                foreach ($users as $value) {
                    $names[] = "<a href='" . $url('people/profile', ['id' => $value['usr_id']]) . "'>" . \Users\Model\DbEntity\User::getDisplayName($value) . '</a>';
                }
                $names_string = implode(',', $names);
                $is = count($names) > 1 ? 'are' : 'is';
                $this->dirtyContent .= $names_string . " " . $is . " no longer member of the project.";

                $this->ProjectTable->update($project);
            }

            return $del_users;
        }
        return [];
    }

    /**
     * 
     * @param array $data
     * @param integer $userId project manager id
     * @return boolean
     */
    public function closeProject(Array $data, $userId) {
        $project = $this->ProjectTable->getById($data['proj_id']);
        if (!$project) {
            return false;
        }
        //only allow project manager to close the project
        if (!$this->ProjectMemberTable->isOwner($userId, $project->proj_id)) {
            return false;
        }

        //update proj_progress to the time it is closed
        $project->proj_progress = $data['proj_progress'];
        $project->proj_isClosed = 1;
        $project->proj_isSuccess = ($project->proj_progress == 100);
        $this->ProjectTable->update($project);

        //add projectWall as notification
        $status = $project->proj_isSuccess ? "success" : "failure";
        $content = "The project was closed with " . $status . '.<BR>';
        $content .= $data['outcome'];
        $this->createComment($project->proj_id, $userId, $content);
        return true;
    }

    /**
     * 
     * @param \ProjectManagement\Model\DbEntity\Project $project
     * @param type $userId
     * @return boolean
     */
    public function removeProject(Project $project, $userId) {
        //only project owner can remove a project
        if (!$this->ProjectMemberTable->isOwner($userId, $project->proj_id)) {
            return false;
        }
        //closed project cannot be remove
        if ($project->proj_isClosed == 1) {
            return false;
        }

        $project->proj_isVisible = 0;
        $this->ProjectTable->update($project);
        return true;
    }

    /**
     * Save project icon if any
     * 
     * @param integer $projectId
     * @param Form FileUpload $imageField
     * @return boolean
     */
    public function saveIcon($projectId, $imageField) {
        if ($imageField['name'] == "") {
            return false;
        }
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        $directory = $this->SL->get('PathManager')->getProjectPath($projectId, Resource::ICON);
        $adapter->setDestination($directory);
        return $adapter->receive($imageField['name']);
    }

    /**
     * Create a projectwall as readonly
     * 
     * @param integer $projectId
     * @param integer $userId
     * @param string $content
     */
    public function createComment($projectId, $userId, $content) {

        $wall = new ProjectWall();
        $wall->prjW_comment = $content;
        $wall->prjW_projID = $projectId;
        $wall->prjW_userid = $userId;
        $wall->prjW_timeStamp = date('Y-m-d H:i:s');
        $wall->prjW_readOnly = 1;
        return $this->ProjectWallTable->insert($wall);
    }

    public function createProjectViolation($data, $userId, $projectId) {
        $violation = new ViolationReport();
        $violation->exchangeArray($data);
        $violation->vp_userId = $userId;
        if ($this->SL->get('ViolationReportTable')->createViolationReport($violation)) {
            $project = $this->ProjectTable->getById($projectId);
            $owner = $this->ProjectMemberTable->getPM($projectId);
            $reporter = $this->SL->get('UserTable')->getById($userId);
            $this->notifyAdmin->reportProject($project, $owner, $reporter, $violation);
        }
    }

    /**
     * return role id or create a new role if the title is passed in
     * 
     * @param int|string $role
     */
    public function getProjectRole($role) {
       
        if (!(strlen($role) > 0 && $role != '0')) {
            return NULL;
        }
        //check if it is number
        if ((int) $role != 0) {
            return $role;
        }
        $roleTable = $this->SL->get("ProjectRolesTable");
        $text = $this->SL->get('Util')->helpString()->toCamel($role);
        
        return $roleTable->getId($text, $this->SL->get('AuthService')->getUser()->usr_lang);
    }

}
