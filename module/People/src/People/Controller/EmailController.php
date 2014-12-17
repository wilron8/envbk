<?php

/**
 * Description of EmailController
 *
 * @author kimsreng
 */

namespace People\Controller;

use Users\Model\DbEntity\UserEmail;
use People\Form\UserEmail as Form;
use Zend\Validator\EmailAddress;
use Common\Mvc\Controller\AuthenticatedController;

class EmailController extends AuthenticatedController {

    protected $viewType = self::JSON_MODEL;

    public function createAction() {
        $form = new Form($this->get('translator'));
        //  $emailValidator = new EmailAddress();
        //  $email = $this->params()->fromRoute('email');
        //  $this->viewType = self::VIEW_MODEL;
        $this->initView();
        // $this->view->form = $form;
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                //check if the email already exist;
                $userEmailTable = $this->get('UserEmailTable');
                $userJoin = $this->get('UserjoinTable');
                if ($userEmailTable->getByEmail($form->get('uEmail_email')->getValue()) || $userJoin->getByEmail($form->get('uEmail_email')->getValue())) {
                    $this->errors[] = $this->translate('This email is already in use. Please choose another one.');
                    $this->view->errors = $this->errors;
                    $this->view->success = false;
                    return $this->view;
                }
                $userEmail = new UserEmail();
                $userEmail->exchangeArray($form->getData());
                $userEmail->uEmail_isVerified = 0;
                $userEmail->uEmail_isPrivateOnly = 1;
                $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
                $userEmail->uEmail_userID = $this->userId;
                if ($userEmail->uEmail_emailType == 2) {
                    $userEmail->uEmail_isMobile = 1;
                }

                $this->getTable()->insert($userEmail);
                //verify email
                //send email to new address for the verification
                $user = $this->get('UserTable')->getById($this->userId);
                $mail_config = array(
                    'to_email' => $userEmail->uEmail_email,
                    'to_name' => $user->getFullName(),
                    'subject' => 'Email Confirmation'
                );
                $email = \base64_encode($userEmail->uEmail_email . '|' . strtotime($userEmail->uEmail_timeStamp));
                $this->get('EmailSender')->sendTemplate('people/email/_verify_email', array('email' => $email, 'user' => $user), $mail_config);
                $this->view->success = true;
            } else {
                $this->view->success = false;
                $this->view->messages = $form->getMessages();
            }
        }

        return $this->view;
    }

    public function createPublicAction() {
        $form = new Form($this->get('translator'));
        $this->viewType = self::VIEW_MODEL;
        $this->initView();
        $this->view->form = $form;
        $this->view->userId = $this->userId;
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                //check if the email already exist;
                $userEmailTable = $this->get('UserEmailTable');
                $userJoin = $this->get('UserjoinTable');
                if ($userEmailTable->getByEmail($form->get('uEmail_email')->getValue()) || $userJoin->getByEmail($form->get('uEmail_email')->getValue())) {
                    $this->errors[] = $this->translate('This email is already in use. Please choose another one.');
                    return $this->view;
                }
                $userEmail = new UserEmail();
                $userEmail->exchangeArray($form->getData());
                $userEmail->uEmail_isVerified = 0;
                $userEmail->uEmail_isPrivateOnly = 0;
                $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
                $userEmail->uEmail_userID = $this->userId;
                if ($userEmail->uEmail_emailType == 2) {
                    $userEmail->uEmail_isMobile = 1;
                }

                $this->getTable()->insert($userEmail);
                //verify email
                //send email to new address for the verification
                $user = $this->get('UserTable')->getById($this->userId);
                $mail_config = array(
                    'to_email' => $userEmail->uEmail_email,
                    'to_name' => $user->getFullName(),
                    'subject' => 'Email Confirmation'
                );
                $email = \base64_encode($userEmail->uEmail_email . '|' . strtotime($userEmail->uEmail_timeStamp));
                $this->get('EmailSender')->sendTemplate('people/email/_verify_email', array('email' => $email, 'user' => $user), $mail_config);
                $this->view->success = true;
            } else {
                $this->view->success = false;
                //$this->view->messages = $form->getMessages();
            }
        }

        return $this->view;
    }

    public function deleteAction() {
        $emailValidator = new EmailAddress();
        $email = $this->params()->fromPost('email');
        $this->initView();
        if ($emailValidator->isValid($email)) {
            $userEmail = $this->getTable()->getByEmail($email);
            if ($userEmail) {
                if ($userEmail->uEmail_email === $this->laIdentity()->getUsername()) {
                    $this->view->success = false;
                    $this->view->errors = array($this->translate('Primary email cannot be deleted.'));
                } else {
                    $this->getTable()->delete($userEmail->uEmail_id);
                    $this->view->success = true;
                }
                //   return $this->redirect()->toRoute('user', array('action' => 'settings'));
            } else {
                $this->view->success = false;
                $this->view->errors = array($this->translate('There is no such email.'));
            }
        } else {
            $this->view->success = false;
            $this->view->errors = $emailValidator->getMessages();
        }
        return $this->view;
    }

    public function confirmAction() {
        $this->viewType = static::VIEW_MODEL;
        $this->initView();
        if (!$this->params()->fromRoute('email', false)) {
            $this->errors[] = $this->translate('The request is invalid.');
            return $this->view;
        }
        list($email, $time) = explode('|', \base64_decode($this->params()->fromRoute('email')));
        $userEmail = $this->getTable()->getOneByCondition(array('uEmail_email' => $email, 'uEmail_timeStamp' => date('Y-m-d H:i:s', $time), 'uEmail_isVerified' => 0));
        if (!$userEmail) {
            $this->errors[] = $this->translate('The request is invalid.');
        } else {
            $userEmail->uEmail_isVerified = 1;
            $this->getTable()->update($userEmail);
            $this->success[] = $this->translate('Your email is successfully confirmed!');
        }
        return $this->view;
    }

    public function sendVerificationAction() {
        $emailValidator = new EmailAddress();
        $email = trim($this->params()->fromPost('email'));
        $this->initView();
        if ($emailValidator->isValid($email)) {
            $userEmail = $this->getTable()->getByEmail($email);
            if ($userEmail) {
                //send email to new address for the verification
                $user = $this->get('UserTable')->getById($this->userId);
                $mail_config = array(
                    'to_email' => $email,
                    'to_name' => $user->getFullName(),
                    'subject' => 'Email Confirmation'
                );
                $email = \base64_encode($userEmail->uEmail_email . '|' . strtotime($userEmail->uEmail_timeStamp));
                $this->get('EmailSender')->sendTemplate('people/email/_verify_email', array('email' => $email, 'user' => $user), $mail_config);
                // $this->flashMessenger()->addInfoMessage(sprintf($this->translate("Verification email is sent to %s."),$email));
                // return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
                $this->view->success = true;
            }
        }
        return $this->view;
    }

    public function setPrimaryAction() {
        $emailValidator = new EmailAddress();
        $email = trim($this->params()->fromPost('email'));
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        if ($emailValidator->isValid($email)) {
            $emailRow = $this->getTable()->getByEmail($email);
            if ($emailRow->uEmail_isVerified == 0) {
                $this->view->success = false;
                $this->view->errors = array($this->translate("Only verified email can be made primary."));
                return $this->view;
            }
            if ($emailRow) {
                $user = $this->getUser();
                $old_email = $user->usr_email;
                $user->usr_email = $email;
                $user->usr_username = $email;
                $this->get('UserTable')->update($user);
                $this->view->success = true;
                //update username session
                $this->get('AuthService')->getIdentity()->usr_username = $email;
                //send email to confirm the changes
                $mail_config = array(
                    'to_email' => $old_email,
                    'to_name' => $user->getFullName(),
                    'subject' => 'Primary Email Chage'
                );
                $this->get('EmailSender')->sendTemplate('people/email/_primary_email_change', array('email' => $old_email, 'user' => $user), $mail_config);
            } else {
                $this->view->success = false;
                $this->view->errors = array($this->translate('Email cannot be found.'));
            }
        } else {
            $this->view->success = false;
            $this->view->errors = $emailValidator->getMessages();
        }
        return $this->view;
    }

    /**
     * 
     * @return \Users\Model\DbTable\UserEmailTable
     */
    protected function getTable() {
        return $this->get('UserEmailTable');
    }

    /**
     * 
     * @return \Users\Model\DbEntity\User
     */
    protected function getUser() {
        return $this->get('UserTable')->getById($this->userId);
    }

}
