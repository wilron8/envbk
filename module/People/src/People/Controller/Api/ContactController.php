<?php

/**
 * Description of ContactController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Users\Model\DbEntity\UserEmail;
use Users\Model\DbEntity\UserPhone;
use Zend\Validator\EmailAddress;
use Zend\Validator\Regex;
use Zend\View\Model\JsonModel;

class ContactController extends AuthenticatedController {

    public function getAction() {
        $emails = $this->getTable()->getByUserId($this->userId, false);
        $phones = $this->getPhoneTable()->getByUserId($this->userId);

        $data = [];
        foreach ($emails as $value) {
            $da = [];
            $da['id'] = $value->uEmail_id;
            $da['type'] = 99;
            $da['value'] = $value->uEmail_email;
            $data[] = $da;
        }

        foreach ($phones as $value) {
            $da['id'] = $value->uPhon_id;
            $da['type'] = (int) $value->uPhon_type;
            $da['value'] = $value->uPhon_number;
            $data[] = $da;
        }

        return new JsonModel(array(
            'data' => $data,
        ));
    }

    public function createAction() {
        if ($this->request->isPost()) {
            $item = $this->params()->fromPost();
            $emailV = new EmailAddress();
            $validator = new Regex('/^[+]{0,1}[0-9- .]+$/');
            if ($item['type'] == 99) {
                $value = trim($item['value']);
                if ($emailV->isValid($value)) {
                    $userEmail = new UserEmail();
                    $userEmail->uEmail_userID = $this->userId;
                    $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
                    $userEmail->uEmail_isPrivateOnly = false;
                    $userEmail->uEmail_email = $value;
                    $this->getTable()->insert($userEmail);
                } else {
                    $errors[] = "$value is not a valid email address";
                }
            } else {
                $value = trim($item['value']);
                if ($validator->isValid($value)) {
                    $userPhone = new UserPhone();
                    $userPhone->uPhon_userid = $this->userId;
                    $userPhone->uPhon_type = $item['type'];
                    $userPhone->uPhon_number = $value;
                    $this->getPhoneTable()->insert($userPhone);
                } else {
                    $errors[] = "$value is an invalid phone number. Only numbers (0-9), dash (-), dot (.) and plus (+) symbols are allowed";
                }
            }
            if (count($errors) > 0) {
                return new JsonModel(array(
                    'success' => false,
                    'errors' => $errors,
                ));
            }
            return new JsonModel(array(
                'success' => true
            ));
        }
        die();
    }

    public function updateAction() {
        if ($this->request->isPost()) {
            $item = $this->params()->fromPost();
            $emailV = new EmailAddress();
            $validator = new Regex('/^[+]{0,1}[0-9- .]+$/');
            $errors=[];
            if ($item['type'] == 99) {
                $value = trim($item['value']);
                if ($emailV->isValid($value)) {
                    $uEmail = $this->getTable()->getById($item['id']);
                    if ($uEmail) {
                        $uEmail->uEmail = $value;
                        $this->getTable()->update($uEmail);
                    }
                } else {
                    $errors[] = "{$item['value']} is not a valid email address";
                }
            } else {
                $value = trim($item['value']);
                if ($validator->isValid($value)) {
                    $uPhone = $this->getPhoneTable()->getById($item['id']);
                    if ($uPhone) {
                        $uPhone->uPhon_number = $value;
                        $uPhone->uPhon_type = $item['type'];
                        $this->getPhoneTable()->update($uPhone);
                    }
                } else {
                    $errors[] = "$value is an invalid phone number. Only numbers (0-9), dash (-), dot (.) and plus (+) symbols are allowed";
                }
            }
            if (count($errors) > 0) {
                return new JsonModel(array(
                    'success' => false,
                    'errors' => $errors,
                ));
            }
            return new JsonModel(array(
                'success' => true
            ));
        }
        die();
    }

    public function deleteAction() {
        if ($this->request->isPost()) {
            $item = $this->params()->fromPost();
            if ($item['type'] == 99) {
                $this->getTable()->delete($item['id']);
            } else {
                $this->getPhoneTable()->delete($item['id']);
            }
            return new JsonModel(array(
                'success' => true
            ));
        }
        die();
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
     * @return \Users\Model\DbTable\UserPhoneTable
     */
    protected function getPhoneTable() {
        return $this->get('UserPhoneTable');
    }

}
