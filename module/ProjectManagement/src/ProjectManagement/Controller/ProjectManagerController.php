<?php

/**
 * Description of ProjectManager
 *
 * @author kimsreng
 */

namespace ProjectManagement\Controller;

use Common\Mvc\Controller\AuthenticatedController;
use DocumentManager\Model\ResourceType as Resource;
use ProjectManagement\Model\ProjectManager;
use Zend\View\Model\ViewModel;

class ProjectManagerController extends AuthenticatedController {

    /**
     * Display all projects created by a user
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function projectAction() {
        $projectTable = $this->getProjectTable();
        $paginator = $projectTable->fetchCreated($this->laIdentity()->getId(), true);
        $page = $this->params()->fromRoute('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(20);
        return new ViewModel(array(
            'paginator' => $paginator,
            'projMemTable' => $this->getProjectMemberTable(),
        ));
    }

    /**
     * Display all projects created and joined by a user
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function allAction() {
        $projectTable = $this->getProjectTable();
        $paginator = $projectTable->fetchCreatedJoined($this->userId,true);
        $page = $this->params()->fromRoute('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(20);
        return new ViewModel(array(
            'paginator' => $paginator,
            'projMemTable' => $this->getProjectMemberTable(),
        ));
    }

    public function newAction() {
        $form = new \ProjectManagement\Form\Filter\Project($this->get('translator'));
        //set source idea if any
        if ($this->params()->fromQuery('idea')) {
            $form->get('proj_srcIdea')->setValue($this->params()->fromQuery('idea'));
        }
        $this->initView();
        $this->view->form = $form;
        $errors = [];
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            //upload validation
            $icon = $this->params()->fromFiles('proj_img');
            if ($icon['name'] != "") {
                $valid_exten = new \Zend\Validator\File\Extension(Resource::getAllowedImages());
                if (!$valid_exten->isValid($icon['name'], $icon)) {
                    $errors[] = $this->translate("Invalid file type. Be sure to upload a GIF, JPG, or PNG image.");
                }
                if ($icon['error'] == 1) {
                    $errors[] = $this->translate("Icon file size is too large. Upto " . \DocumentManager\Model\ImgManager::getMaxUploadMessage() . ' is allowed.');
                }
            }
            if ($form->isValid()) {
                //save if everything is ok
                if (!count($errors) > 0) {
                    $data = $form->getValues();
                    $data['members'] = json_decode($this->params()->fromPost('members'),true);
                    $data['proj_img'] = $icon['name'];
                    $projMgr = $this->getProjectManager();
                    $projectId = $projMgr->createProject($data, $this->userId);
                    $projMgr->saveIcon($projectId, $icon);
                    return $this->redirect()->toRoute('project/action-id', array('action' => 'view', 'id' => $projectId));
                }
            }
        }
        $this->errors = $errors;
        return $this->view;
    }

    public function viewAction() {
        $project = $this->getProjectTable()->getById($this->params()->fromRoute('id'));
        if (!$project) {
            return $this->displayNotFound();
        }
        $isOwner = $this->getProjectMemberTable()->isOwner($this->laIdentity()->getId(), $project->proj_id);
        $commentList = $this->getProjectWallTable()->getComments($project->proj_id);
        //increment hit count
        $project->proj_hitCnt++;
        $this->getProjectTable()->update($project);
        return new ViewModel(array(
            'project' => $project,
            'isOwner' => $isOwner,
            'members' => $this->getProjectMemberTable()->fetchMembers($project->proj_id),
            'ideaTable' => $this->getServiceLocator()->get('IdeaTable'),
            'projectMember' => $this->getProjectMemberTable(),
            'userId' => $this->laIdentity()->getId(),
            'commentList' => $commentList,
            'commentTable' => $this->getProjectWallTable()
        ));
    }

    /**
     * Search for projects by keyword
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function findAction() {
        $keyword = $this->params()->fromRoute('keyword', false);
        if ($keyword) {
            $searchEngine = new \SearchEngine\Model\SearchEngine($this->getServiceLocator());
            $project_result = $searchEngine->findProject($keyword);
            return new ViewModel(array(
                'results' => $project_result,
            ));
        }
        return $this->displayNotFound();
    }

    public function editAction() {
        
        $id = $this->params()->fromRoute('id', false);
        $project = $this->getProjectTable()->getById($id);
        if (!$project || $project->proj_isClosed || !$this->getProjectMemberTable()->isOwner($this->laIdentity()->getId(), $id)) {
            return $this->displayNotFound();
        }
        $form = new \ProjectManagement\Form\Filter\Project($this->get('translator'));

        $old_icon = $project->proj_img;
        $fields = $project->getArrayCopy();
        // $fields['proj_srcIdea']=(int)$fields['proj_srcIdea'];
        $form->setData(array_merge($fields));
        //$membershipRegStatus = $project->proj_isMemberShipOpen == 1 ? TRUE : FALSE;
        $viewModel = new ViewModel(array(
            'form' => $form,
            'project' => $project,
            //'membershipRegStatus' => $project->proj_isMemberShipOpen
        ));
        $errors = [];
        if ($this->request->isPost()) {

            $form->setData($this->request->getPost());
            //upload validation
            $icon = $this->params()->fromFiles('proj_img');
            if ($icon['name'] != "") {
                $valid_exten = new \Zend\Validator\File\Extension(Resource::getAllowedImages());
                if (!$valid_exten->isValid($icon['name'], $icon)) {
                    $errors[] = $this->translate("Invalid file type. Be sure to upload a GIF, JPG, or PNG image.");
                }
                if ($icon['error'] == 1) {
                    $errors[] = $this->translate("Icon file size is too large. Only " . \DocumentManager\Model\ImgManager::getMaxUploadMessage() . ' is allowed.');
                }
            }
            if ($form->isValid()) {
                if (!$errors > 0) {
                    if ($icon['name'] != "") {
                        $project->proj_img = $icon['name'];
                    } else {
                        $project->proj_img = $old_icon; //keep previous icon
                    }
                    //save project
                    $project->exchangeArray($form->getValues());
                    $members = array_filter(json_decode($this->params()->fromPost('members'),true));
                    $remove = array_filter(json_decode($this->params()->fromPost('removed'),true));
                    
                    $projectMgr = $this->getProjectManager();
                    $projectMgr->updateProject($project, $this->userId, $members,$remove);
                    if ($projectMgr->saveIcon($project->proj_id, $icon)) {
                        @unlink($this->getPathManager()->getProjectPath($project->proj_id, Resource::ICON) . DIRECTORY_SEPARATOR . $old_icon);
                    }
                    //update member
                    //$this->getProjectMemberTable()->updateMembers($this->params()->fromPost('members'), $id);
                    //return print_r($form->getValues());
                    return $this->redirect()->toRoute('project/action-id', array('action' => 'view', 'id' => $id));
                }
            }
        }

        return $viewModel;
    }

    public function removeAction() {
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        $id = $this->params()->fromRoute('id', false);
        $project = $this->getProjectTable()->getById($id);
        if (!$project) {
            return $this->view;
        }
        $result = $this->getProjectManager()->removeProject($project, $this->userId);
        $this->view->success = $result;
        return $this->view;
    }

    /**
     * action to apply for project membership
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function joinAction() {
        $projectId = $this->params()->fromRoute('id');
        if (!$projectId) {
            return $this->displayNotFound();
        }
        $projMgr = $this->getProjectManager();
        $response = $projMgr->joinProject($projectId, $this->userId);
        $this->viewType=self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        switch ($response) {
            case ProjectManager::ALREADY_JOIN:
                $this->view->msg = $this->translate('User has already joined this project or the request is being pended.');
                break;
            case ProjectManager::MEMBERSHIP_CLOSED:
                $this->view->msg = $this->translate('Membership Requests of this project is currently closed.');
                break;
            case ProjectManager::PROJECT_NOT_EXIST:
                $this->view->msg = $this->translate('The requested project does not exit.');
                break;
            case ProjectManager::SUCCESS:
                $this->view->success = true;
                $this->view->owner = $projMgr->projectOwner;
                $this->view->msg = sprintf($this->translate('Your request is already sent to %s for approval.'), $projMgr->projectOwner->usr_fName);
                break;
            default :
                $this->view->msg = $this->translate('Error.');
        }

        return $this->view;
    }

    /**
     * Close a project
     * 
     * @return JsonModel
     */
    public function closeAction() {
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost()) {
            $post = $this->params()->fromPost();
            $filter = new \Zend\Filter\StripTags(array(
                'allowTags' => \Common\Policy\Filter::$allowedTags,
                'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
            ));
            $post['outcome'] = $filter->filter($post['outcome']);
            $result = $this->getProjectManager()->closeProject($post, $this->userId);
            if ($result) {
                $this->view->success = true;
            }
        }
        return $this->view;
    }

    /**
     * Approve a membership request
     */
    public function approveAction() {
        $pMemId = $this->params()->fromRoute('id');
        if (!$pMemId) {
            return $this->displayNotFound();
        }
        //make sure that the logged-in user is the project owner
        $pm = $this->getProjectMemberTable()->getById($pMemId);
        if(!($pm && $this->getProjectMemberTable()->getPM($pm->pMem_projectID)['usr_id']==$this->userId)){
            return $this->notFoundAction();
        }
        
        $result = $this->getProjectManager()->approveMembership($pMemId, $this->userId);
        $this->initView();
        $this->view->success = $result;
        return $this->view;
    }

    /**
     * Reject a membership request
     */
    public function rejectAction() {
        $pMemId = $this->params()->fromRoute('id');
        if (!$pMemId) {
            return $this->displayNotFound();
        }
        //make sure that the logged-in user is the project owner
        $pmem = $this->getProjectMemberTable()->getById($pMemId);
        if(!($pmem && $this->getProjectMemberTable()->getPM($pmem->pMem_projectID)['usr_id']==$this->userId)){
            return $this->notFoundAction();
        }
        
        $this->initView();

        $this->view->project = $this->getProjectTable()->getById($pmem->pMem_projectID);
        $this->view->user = $this->get('UserTable')->getById($pmem->pMem_memberID);
        if ($this->request->isPost()) {
            $filter = new \Zend\Filter\StripTags(array(
                'allowTags' => \Common\Policy\Filter::$allowedTags,
                'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
            ));
            $reason = $filter->filter($this->params()->fromPost('reason'));
            $result = $this->getProjectManager()->rejectMembership($pMemId, $reason, $this->userId);

            $this->view->success = $result;
        }

        return $this->view;
    }

    public function reportProjectAction() {
        $form = new \ProjectManagement\Form\ReportProjectForm($this->getServiceLocator());
        $projectId = $this->params()->fromRoute('id');
        $form->get('vp_projID')->setValue($projectId);
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $this->getProjectManager()->createProjectViolation($form->getData(), $this->userId, $projectId);
//                $ideaViolation = new \IdeaManagement\Model\DbEntity\ViolationReport();
//                $ideaViolation->exchangeArray($form->getData());
//                $ideaViolation->vp_userId = $this->userId;
//                $this->getReportViolationTable()->createViolationReport($ideaViolation);
//                $project = $this->getProjectTable()->getById($projectId);
//                $mail_config = array(
//                    'to_email' => $this->config()->get('adminEmail')['toEmail'],
//                    'to_name' => 'Linkaide Admin Team',
//                    'subject' => 'Project Violoation Report'
//                );
//                $this->sendMail('project-management/project-manager/email/_report-project-email', 
//                        array('owner' => $this->getProjectMemberTable()->getPM($projectId),
//                    'project' => $project,
//                    'report' => $ideaViolation,
//                    'reporter' => $this->get('UserTable')->getById($this->userId))
//                        , $mail_config);
                $this->view->success = true;
                return $this->view;
            }
        }
        //$this->view->form = $form;
        // $this->view->project = $this->getProjectTable()->getById($projectId);
        return $this->view;
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectTable
     */
    private function getProjectTable() {
        return $this->getServiceLocator()->get('ProjectTable');
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectMemberTable
     */
    private function getProjectMemberTable() {
        return $this->getServiceLocator()->get('ProjectMemberTable');
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectWallTable
     */
    private function getProjectWallTable() {
        return $this->getServiceLocator()->get('ProjectWallTable');
    }

    private function getReportViolationTable() {
        return $this->getServiceLocator()->get('ViolationReportTable');
    }

    /**
     * Display prject not found view
     * 
     * @return view
     */
    protected function displayNotFound() {
        if ($this->view == NULL) {
            $this->initView();
        }
        $this->view->setTemplate('project-management/project-manager/_message_not_found');
        return $this->view;
    }

    /**
     * 
     * @return \DocumentManager\Model\PathManager
     */
    protected function getPathManager() {
        return $this->get('PathManager');
    }

    /**
     * 
     * @return \ProjectManagement\Model\ProjectManager
     */
    protected function getProjectManager() {
        return $this->get('ProjectManager');
    }

}
