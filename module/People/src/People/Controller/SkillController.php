<?php

/**
 * Description of SkillController
 *
 * @author kimsreng
 */

namespace People\Controller;

use Common\Mvc\Controller\AuthenticatedController;
use People\Model\DbEntity\UserSkill;
use People\Model\DbEntity\SkillTag;
use People\Form\SkillForm;

class SkillController extends AuthenticatedController {

    /**
     * action for users to add skill
     */
    public function createAction() {
        $userId = $this->params()->fromRoute('id', false);
        if (!$userId && $this->userId == $userId) {
            return $this->notFoundAction();
        }
        $this->initView();
        $form = new SkillForm($this->getServiceLocator());
        $this->view->form = $form;
        $form->get('uSkll_userID')->setValue($userId);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $userSkill = new UserSkill();
                $userSkill->exchangeArray($form->getData());
                $skill = $this->get('SkillTagTable')->getByTag($form->get('stag_text')->getValue());
                if ($skill) {
                    $userSkill->uSkll_TagID = $skill->stag_id;
                } else {
                    $skill = new SkillTag();
                    $skill->stag_text = $form->get('stag_text')->getValue();
                    $skill->stag_timeStamp = date('Y-m-d H:i:s');
                    $skill_id = $this->get('SkillTagTable')->insert($skill);
                    $userSkill->uSkll_skillTagID = $skill_id;
                }

                $this->getTable()->insert($userSkill);
            }
        }
        $this->view->skillList = $this->getTable()->fetchByUser($userId);
        return $this->view;
    }

    public function deleteAction() {

        $userSkillId = $this->params()->fromRoute('id', false);
        if (!$userSkillId) {
            return $this->notFoundAction();
        }
        $userSkill = $this->getTable()->getById($userSkillId);
        if (!$userSkill) {
            return $this->notFoundAction();
        }
        //Only allow owner to delete
        if (!$userSkill->uSkll_userID == $this->userId) {
            $this->flashMessenger()->addErrorMessage($this->translate('Sorry, you are not allowed to delete this item.'));
        }
        $this->getTable()->delete($userSkillId);
        $this->flashMessenger()->addSuccessMessage($this->translate('The skill is successfully deleted.'));
        return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
    }

    public function getTable() {
        return $this->get('UserSkillTable');
    }

}
