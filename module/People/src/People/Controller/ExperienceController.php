<?php

/**
 * Description of ExperienceController
 *
 * @author kimsreng
 */

namespace People\Controller;

use People\Model\DbTable\CvTable;
use People\Form\ExperienceForm;
use Common\Mvc\Controller\BaseController;

class ExperienceController extends BaseController {

    public function createAction() {
        $userId = $this->params()->fromRoute('id', false);
        if (!$userId || $userId != $this->laIdentity()->getId()) {
            return $this->notFoundAction();
        }

        $form = $this->getForm();
        $this->initView();

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $experience = new \People\Model\Experience();
                $experience->exchangeArray($form->getData());
                if ($this->getTable()->insert($experience, $userId)) {
                    return $this->redirect()->toRoute('people/action-id', array('action' => 'edit', 'id' => $userId));
                }
            }
        }
        $this->view->form = $form;
        return $this->view;
    }

    public function updateAction() {
        if (!$this->params()->fromRoute('id')) {
            return $this->notFoundAction();
        }
        $table = $this->getTable();
        $experience = $table->getById($this->params()->fromRoute('id'));

        //allow only owner to update their experience
        if (!$experience || $this->getCv()->getUserByCv($experience->xp_cvID) !== $this->laIdentity()->getId()) {
            return $this->notFoundAction();
        }

        $this->initView();

        $form = $this->getForm();
        $form->bind($experience);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $table->update($experience);
                return $this->redirect()->toRoute('people/action-id', array('action' => 'edit', 'id' => $this->laIdentity()->getId()));
            }
        }
        $this->view->form = $form;
        $this->view->userId = $this->laIdentity()->getId();
        return $this->view;
    }

    public function deleteAction() {
        $userExperienceId = $this->params()->fromRoute('id', false);
        if (!$userExperienceId) {
            return $this->notFoundAction();
        }
        $userExperience = $this->getTable()->getById($userExperienceId);
        if (!$userExperience) {
            return $this->notFoundAction();
        }
        //Only allow owner to delete
        if (!$this->getCv()->getUserByCv($userExperience->xp_cvID) == $this->laIdentity()->getId()) {
            $this->flashMessenger()->addErrorMessage($this->translate('Sorry, you are not allowed to delete this experience.'));
            return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
        }
        $this->getTable()->delete($userExperienceId);
        $this->flashMessenger()->addSuccessMessage($this->translate('The experience is successfully deleted.'));
        return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
    }

    private function getTable() {
        return $this->getServiceLocator()->get('ExperienceTable');
    }

    private function getForm() {
        $form = new ExperienceForm($this->getServiceLocator());
        return $form;
    }

    private function getCv() {
        return new CvTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
    }

}
