<?php

/**
 * Description of MsgApiController
 *
 * @author kimsreng
 */

namespace Message\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class MsgApiController extends AbstractActionController {

    /**
     * Allow only authenticated users to reach this controller
     * @param \Zend\Mvc\MvcEvent $e
     * @return type
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        if (!$this->laIdentity()->hasIdentity()) {
            die(json_encode(array('error' => 'Please login first.')));
        }
        return parent::onDispatch($e);
    }

    public function messageAction() {
        $messageTable = $this->getMessageTable();
        $messages = $messageTable->fetchAllByUserId($this->laIdentity()->getId());
        $sentMessages = $messageTable->fetchSentMessages($this->laIdentity()->getId());
        return new JsonModel(array(
            'rcv' => $this->toArray($messages),
            'sent' => $sentMessages->toArray(),
        ));
    }

    public function viewAction() {
        $id = $this->params()->fromQuery('id');
        //check if the user is the recepient
        $msgusr = $this->getMessageToTable()->select(array('msg2_recepientID' => $this->laIdentity()->getId(), 'msg2_messageID' => $id))->current();
        if ($msgusr) {
            $msg = $this->getMessageTable()->getById($id);
            $msgTb = $this->getMessageTable()->getByIdTb($id);
            $msgTb->msg_isRead = 1;
            $this->getMessageTable()->update($msgTb);
            $msgusr->msg2_isRead = 1;
            $this->getMessageToTable()->update($msgusr);
            $recepients = $this->getMessageToTable()->getRecepients($id);
            return new JsonModel(array(
                'msg' => $msg,
                'recepients' => $this->toArray($recepients),
            ));
        }
    }

    public function newAction() {
        $filter = new \Message\Form\CreateMessageFilter($this->getServiceLocator());
        $errors = [];
        if ($this->request->isPost()) {
            if (isset($this->request->getPost()['cancel'])) {
                return $this->redirect()->toRoute('message');
            }
            $filter->setData($this->request->getPost());
            if ($filter->isValid()) {
                $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                try {
                    $transaction->beginTransaction();
                    $msg = new \Message\Model\Message();
                    $msg->exchangeArray($this->request->getPost());
                    $msg->msg_senderID = $this->laIdentity()->getId();
                    $msg->msg_timeStamp = date('Y-m-d H:i:s');
                    $msgId = $this->getMessageTable()->insert($msg);
                    $msg->msg_id = $msgId;
                    //save msgTo
                    $recepients = array();
					// TODO: change this to accept text with semi-colon delimitor
					//$recipients = explode( $this->request->getPost('recepient'), ";");
                    $names = array();
                    foreach ($this->request->getPost('recepient') as $recepient) {
                        $msgTo = new \Message\Model\MessageTo();
                        $msgTo->msg2_messageID = $msgId;
                        $msgTo->msg2_recepientID = $recepient;
                        $this->getMessageToTable()->insert($msgTo);

                        $user = $this->getServiceLocator()->get('UserTable')->getById($recepient);
                        $recepients[$user->usr_id]['email'] = $user->usr_email;
                        $recepients[$user->usr_id]['name'] = $user->getFullName();
                        $names[] = $user->displayName();
                    }
                    //send email alert to recepients
                    $from = $this->getServiceLocator()->get('UserTable')->getById($this->laIdentity()->getId())->usr_fName;// display name
                    foreach ($recepients as $id => $recepient) {
                        $mail_config = array(
                            'to_email' => $recepient['email'],
                            'to_name' => $recepient['name'],
                            'subject' => 'New Message on Linkaide.com'
                        );
                        $this->sendMail('message/msg/_message-alert', array('msg' => $msg, 'from' => $from, 'name' => $recepient['name'], 'email' => $recepient['email'], 'recepients' => $names), $mail_config);
                    }
                    $this->flashMessenger()->addMessage($this->getServiceLocator()->get('translator')->translate('Message was successfully sent!'));
                    //commit transaction
                    $transaction->commit();
                    return $this->redirect()->toRoute('message');
                } catch (\Exception $exc) {
                    //rollback in case of error
                    $transaction->rollback();
                    // send error messages to admin
                    $this->getServiceLocator()->get('ErrorMail')->send($exc);
                    // send message to the user
                    $errors[] = $this->getServiceLocator()->get('translator')->translate('We have a small problem with our database and are working to get it fixed. Please come back in about 30 minutes or so and try again.');
                    return new JsonModel(array(
                        'success' => false,
                    ));
                }
            }
        }
        return new JsonModel(array(
            'success' => true,
        ));
    }

    private function getMessageTable() {
        return $this->getServiceLocator()->get('MessageTable');
    }

    private function getMessageToTable() {
        return $this->getServiceLocator()->get('MessageToTable');
    }

    /**
     * Convert Zend\Db\Adapter\Driver\Pdo\Result to array
     * 
     * @param type Zend\Db\Adapter\Driver\Pdo\Result
     * @return array
     */
    private function toArray($resultSet) {
        $data = [];
        foreach ($resultSet as $row) {
            $data[] = $row;
        }
        return $data;
    }

}
