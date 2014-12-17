<?php

/**
 * Description of EducationController
 *
 * @author kimsreng
 */

namespace People\Controller;

use People\Model\DbTable\CvTable;
use People\Form\EducationForm;
use Common\Mvc\Controller\BaseController;

class EducationController extends BaseController {


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
                $education = new \People\Model\Education();
                $education->exchangeArray($form->getData());
                if ($this->getTable()->insert($education, $userId)) {
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
        $education = $table->getById($this->params()->fromRoute('id'));

        //allow only owner to update their education
        if (!$education || $this->getCv()->getUserByCv($education->ed_cvID) !== $this->laIdentity()->getId()) {
            return $this->notFoundAction();
        }

        $this->initView();

        $form = $this->getForm();
        $form->bind($education);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $table->update($education);
                $this->flashMessenger()->addSuccessMessage($this->translate('The education is successfully saved.'));
                return $this->redirect()->toRoute('people/action-id', array('action' => 'edit', 'id' => $this->laIdentity()->getId()));
            }
        }
        $this->view->form = $form;
        $this->view->userId=  $this->laIdentity()->getId();
        return $this->view;
    }

    public function deleteAction() {
        $userEducationId = $this->params()->fromRoute('id', false);
        if (!$userEducationId) {
            return $this->notFoundAction();
        }
        $userEducation = $this->getTable()->getById($userEducationId);
        if (!$userEducation) {
            return $this->notFoundAction();
        }
        //Only allow owner to delete
        if (!$this->getCv()->getUserByCv($userEducation->ed_cvID) == $this->laIdentity()->getId()) {
            $this->flashMessenger()->addErrorMessage($this->translate('Sorry, you are not allowed to delete this education.'));
            return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
        }
        $this->getTable()->delete($userEducationId);
        $this->flashMessenger()->addSuccessMessage($this->translate('The education is successfully deleted.'));
        return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
    }

    public function getAction() {
        
    }

    private function getTable() {
        return $this->getServiceLocator()->get('EducationTable');
    }

    private function getForm() {
        $form = new EducationForm($this->getServiceLocator());
        return $form;
    }

    private function getCv() {
        return new CvTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
    }

}
