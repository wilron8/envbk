<?php

/**
 * Description of ProjectWallController
 *
 * @author kimsreng
 */

namespace ProjectManagement\Controller;

use ProjectManagement\Form\Filter\ProjectWall as Filter;
use ProjectManagement\Model\DbEntity\ProjectWall;
use Common\Mvc\Controller\AuthenticatedController;

class ProjectWallController extends AuthenticatedController {

    protected $viewType = self::JSON_MODEL;

    public function createAction() {
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost()) {
            $filter = new Filter($this->get('translator'));
            $post = $this->request->getPost();
            $post['prjW_comment'] = $post['comment'];
            $post['prjW_projID'] = $post['id'];
            $filter->setData($post);
            $this->initView();
            if ($filter->isValid()) {
                $wall = new ProjectWall();
                $wall->exchangeArray($filter->getValues());
                $wall->prjW_timeStamp = date('Y-m-d H:i:s');
                $wall->prjW_userid = $this->userId;
                $id = $this->getTable()->insert($wall);
                //fill up notify
                $this->get('NotifyManager')->commentProject($this->getProjectTable()->getById($wall->prjW_projID), $wall, $this->get('UserTable')->getById($this->userId));
                $this->view->success = true;
                $this->view->setVariables([
                    'id' => $id,
                    'dateTime' => $wall->prjW_timeStamp,
                ]);
            } else {
                $this->view->error = $filter->getMessages();
            }
        }

        return $this->view;
    }

    public function removeAction() {
        $projectId = $this->params()->fromRoute('id');
        $commentId = $this->params()->fromRoute('comment');
        $this->initView();
        $this->view->success = false;
        $comment = $this->getTable()->getById($commentId);
        if (!$projectId || !$comment) {
            //redirect to previous page or else to home page
            $this->view->error = $this->translate("Bad Id");
            return $this->view;
        }
        //make sure user will not delete readonly comment
        if ($comment->prjW_readOnly == 1) {
            $this->view->error = $this->translate("System comment cannot be deleted.");
            return $this->view;
        }
        //only allow the owner of the project or the comment owner to delete the comment
        if ($this->getProjectMember()->isOwner($this->userId, $projectId) || ($this->userId == $comment->prjW_userid)) {
            $result = $this->getTable()->delete($commentId);
            $this->view->success = $result;
            return $this->view;
        } else {
            $this->view->error = $this->translate("You have no authorization to delete this comment.");
            return $this->view;
        }
    }

    public function reportAction() {
        $form = new \ProjectManagement\Form\ReportWallForm($this->getServiceLocator());
        $wallId = $this->params()->fromRoute('id');
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data['vp_userId'] = $this->laIdentity()->getId();
            $form->setData($data);
            if ($form->isValid()) {
                $commentViolation = new \IdeaManagement\Model\DbEntity\ViolationReport();
                $commentViolation->exchangeArray($form->getData());
                $this->getReportViolationTable()->createViolationReport($commentViolation);
                $wall = $this->getTable()->getById($wallId);
                if (!$wall) {
                    $this->view->success = false;
                    return $this->view;
                }
                $sm = $this->getServiceLocator();
                $mail_config = array(
                    'to_email' => $this->config()->get('adminEmail')['toEmail'],
                    'to_name' => 'Linspira Admin Team',
                    'subject' => 'Project Wall Violoation Report'
                );
                $this->sendMail('project-management/project-manager/email/_report-wall-email', array('owner' => $sm->get('UserTable')->getById($wall->prjW_userid),
                    'comment' => $wall,
                    'report' => $commentViolation,
                    'project' => $this->getProjectTable()->getById($wall->prjW_projID),
                    'reporter' => $sm->get('UserTable')->getById($this->laIdentity()->getId()))
                        , $mail_config);
                $this->view->success = true;
                return $this->view;
            }
        }
        $this->view->success = false;
        return $this->view;
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectWallTable
     */
    private function getTable() {
        return $this->get('ProjectWallTable');
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectTable
     */
    private function getProjectTable() {
        return $this->get('ProjectTable');
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectMemberTable
     */
    private function getProjectMember() {
        return $this->get('ProjectMemberTable');
    }

    /**
     * @return \IdeaManagement\Model\DbTable\ViolationReportTable
     */
    private function getReportViolationTable() {
        return $this->getServiceLocator()->get('ViolationReportTable');
    }

}
