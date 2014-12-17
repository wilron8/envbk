<?php

/**
 * Description of MsgController
 *
 * @author kimsreng
 */

namespace Message\Controller;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\ViewModel;

class MsgController extends AuthenticatedController {

    /**
     * List all the messages the user received
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function messageAction() {
        $messageTable = $this->getMessageTable();
        $paginator = $messageTable->fetchAllByUserId($this->laIdentity()->getId());
        $this->initView();
        $this->view->paginator = $paginator;
        $this->view->flashMessages = $this->flashMessenger()->getMessages();
        return $this->view;
    }

    /**
     * Create a new message
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction() {
        $form = new \Message\Form\CreateMessageFilter($this->getServiceLocator());
        $errors = [];
        if ($this->params()->fromQuery('user')) {
            $form->get('recipients[]')->setValue(array((int) $this->params()->fromQuery('user')));
        }
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                try {
                    $transaction->beginTransaction();
                    $msg = new \Message\Model\DbEntity\Message();
                    $msg->exchangeArray($form->getValues());
                    //var_dump($form->getValues());
                    $msg->msg_senderID = $this->laIdentity()->getId();
                    $msg->msg_timeStamp = date('Y-m-d H:i:s');
                    $msgId = $this->getMessageTable()->insert($msg);
                    $msg->msg_id = $msgId;
                    if ($msg->msg_threadID == NULL) {
                        $msg->msg_threadID = $msgId;
                        $this->getMessageTable()->update($msg);
                    }
                    //save msgTo
                    $recepients = [];
                    $names = [];
                    $sender = $this->get('UserTable')->getById($this->userId);
                    foreach ($form->get('recipients')->getValue() as $recepient) {
                        if ($this->get('UserPolicy')->canReceiveMessage($recepient)) {
                            $msgTo = new \Message\Model\DbEntity\MessageTo();
                            $msgTo->msg2_messageID = $msgId;
                            $msgTo->msg2_recepientID = $recepient;
                            $msgTo->msg2_isVisible = 1;
                            $this->getMessageToTable()->insert($msgTo);
                            $this->get('NotifyManager')->messageNotify($msg,$recepient,$sender);
                            $user = $this->getServiceLocator()->get('UserTable')->getById($recepient);
                            $recepients[$user->usr_id]['email'] = $user->usr_email;
                            $recepients[$user->usr_id]['name'] = $user->getFullName();
                            $names[] = $user->displayName();
                        }
                    }
                    //send email alert to recepients
                    $from = $this->getServiceLocator()->get('UserTable')->getById($this->laIdentity()->getId())->usr_fName;
                    foreach ($recepients as $id => $recepient) {
                        $this->get('NotifyUser')->notifyNewMessage($msg, $from, $recepient['name'], $recepient['email'], $names);
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
                }
            }
        }
        $this->initView();
        $this->view->data = $form->getValues();
        $this->view->form = $form;
        $this->view->errors = $errors;
        return $this->view;
    }

    /**
     * Find received message by keyword
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function findAction() {
        $keyword = $this->params()->fromRoute('keyword'); //keyword is well sql-injection protected by zf2 as shown dbprofiler with pdo-placeholder
        $message_result = $this->getMessageTable()->find($keyword, $this->laIdentity()->getId());

        $this->initView();
        $this->view->messages = $message_result;
        return $this->view;
    }

    /**
     * View the detail of a message
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction() {
        $id = $this->params()->fromRoute('id');

        $msg = $this->getMessageTable()->getById($id);
        $msgTb = $this->getMessageTable()->getByIdTb($id);
        $recepients = $this->getMessageToTable()->getRecipientAsArray($id);

        if (in_array($this->userId, $recepients)) {
            $msgusr = $this->getMessageToTable()->select(array('msg2_recepientID' => $this->laIdentity()->getId(), 'msg2_messageID' => $id))->current();
            $msgusr->msg2_isRead = 1;
            if ($msgusr->msg2_readTime === NULL) {
                $msgusr->msg2_readTime = date('Y-m-d H:i:s');
            }
            $this->getMessageToTable()->update($msgusr);
        }

        $recepients[] = (int) $msg['msg_senderID'];
        $this->initView();
        if (!in_array($this->userId, $recepients)) {
            $this->errors[] = $this->translate('Message is not found.');
            $this->view->notFound = true;
            return $this->view;
        }

        $this->view->msgs = $this->getMessageTable()->getThreadedMsg($msg['msg_threadID'], $this->laIdentity()->getId());
        $this->view->recepients = $recepients;

        //the sender cannot be a recipient

        for ($i = 0; $i < count($recepients); $i++) {
            if ($recepients[$i] == $this->userId) {
                unset($recepients[$i]);
            }
        }
        $data = ['recipients[]' => array_values($recepients), 'msg_subject' => $msgTb->msg_subject];
        $data['msg_threadID'] = $msg['msg_threadID'];
        $this->view->data = $data;
        return $this->view;
    }

    /**
     * View the detail of a message
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function sentItemAction() {
        $id = $this->params()->fromRoute('id');
        //check if the user is the recepient
        $this->initView();
        $msgusr = $this->getMessageTable()->select(array('msg_senderID' => $this->laIdentity()->getId(), 'msg_id' => $id));
        if ($msgusr->current()) {
            $msg = $this->getMessageTable()->getById($id);
            $recepients = $this->getMessageToTable()->getRecepients($id);
            $this->view->msg = $msg;
            $this->view->recepients = $recepients;
        } else {
            $this->errors[] = $this->translate('Message is not found.');
            $this->view->notFound = true;
        }
        return $this->view;
    }

    public function exportThreadAction(){
        
        //TODO
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        // Access the User Table
        /*$userTable = $this->getServiceLocator()->get('UserTable');
        $getCurrU = $userTable->getById('51');
        $mail_config = array(
                    'to_email' => 'wilron8@gmail.com',
                    'to_name' => 'wilfred ronqz',
                    'subject' => 'My subject sample'
                );

        //$body = "<p>Email: {$this->laIdentity()->getUsername()}";
        $body = "<p>Email: {$getCurrU->usr_username}";
        $sendmail = $this->sendMail('message/email/_export_thread', array('body' => $body, 'name' => 'will free'), $mail_config);
        
        $statusMsg = $sendmail;
        $this->view->setVariables([
                                'success' => true,
                                'statusmsg' => $statusMsg
                        ]);*/


        if ($this->request->isPost()): 

            // Access the User Table
            $userTable = $this->getServiceLocator()->get('UserTable');
            $getCurrUser = $userTable->getById($this->laIdentity()->getId());
            

            $url = $this->getServiceLocator()->get('ViewHelperManager')->get('serverUrl')->__invoke();

            $mail_config = array(
                                    'to_email' => $getCurrUser->usr_username,
                                    'to_name' => ucfirst($getCurrUser->usr_fName.' '.$getCurrUser->usr_lName)
                                ); 

            $post = $this->params()->fromPost();
            $selectedMsgID = $post['msgid'];
            if(empty($selectedMsgID)){

                $statusMsg = $this->translate('Please select message to export!');
                $this->view->setVariables([
                            'success' => false,
                            'statusmsg' => $statusMsg
                    ]);

            } else {

                $msgExp = explode(",", $selectedMsgID);

                if(count($msgExp) > 0) {
                    //$checkUID = '';
                    for($i=0;$i<count($msgExp);$i++){

                        $msgIdSenderExp = explode("|", $msgExp[$i]);

                        if(count($msgIdSenderExp) > 0){
                            // Get all threads under the current message ID
                            //$msgMain = $this->getMessageTable()->select(array('msg_threadID' => $msgIdSenderExp[0]));
                            $msgMain = $this->getMessageTable()->getThreadedMsg($msgIdSenderExp[0], $this->laIdentity()->getId());
                            
                            if($msgMain){
                                //$bodyContent = '';
                                //foreach($msgMain as $msgMainFld):

                                    //Remove comparing current user id
                                    
                                    //Current User is the Sender
                                    //if($msgMainFld->msg_senderID === $this->laIdentity()->getId()){
                                                                        
                                    $messageMain = $this->getMessageTable()->select(array('msg_threadID' => $msgIdSenderExp[0]))->current();
                                    //$recipients = $this->getMessageToTable()->getRecepients($msgId);//Replace the msg ID
                                    //$recepients = $this->getMessageToTable()->getRecipientAsArray($msgIdSenderExp[0]);
                                    //$recepients[] = (int) $messageMain->msg_senderID;

                                    //if ($msg){
//$this->view->msgs = $this->getMessageTable()->getThreadedMsg($msg['msg_threadID'], $this->laIdentity()->getId());
                                        $mail_config['subject'] = $messageMain->msg_subject;
                                        /*$bodyContent .= '<div class="sender">
                                                        
                                                        <img class="to" src="'.$url.'/images/arrow_right.png" />
                                                        '.$this->msgHelper()->getRecipients($msgMainFld['msg_id']).'</div>';
                                        //$bodyContent .= '.$this->usrHelper()->getIcon($msgMainFld).'
                                        $bodyContent .= "<p>{$msgMainFld['msg_body']}</p>";
                                        $bodyContent .= "<br>"; */             

                                    //} 
                                    
                                   

                                //endforeach;

                                 $sendmail = $this->sendMail('message/email/_export_thread', array('msgMain' => $msgMain), $mail_config);  
                                    $this->view->setVariables([
                                                        'success' => true,
                                                        'statusmsg' => 1 
                                                ]);
                            
                            }
                            // End fetching all threads under the current message ID
                           
                        }

                    }                    

                } else {

                    $statusMsg = $this->translate('No message to export!');
                    $this->view->setVariables([
                                'success' => false,
                                'statusmsg' => $statusMsg
                        ]);

                }                

            }            

        endif;


        return $this->view; 
       
    }

    public function remSelectedMsgAction(){
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        //$statusMsg = $this->translate('No Message selected!');
        //$this->view->setVariables([
        //                    'success' => false,
        //                    'statusmsg' => $statusMsg
        //            ]);

        if ($this->request->isPost()):            
            
            $post = $this->params()->fromPost();
            $selectedMsgID = $post['msgid'];
            if(empty($selectedMsgID)){

                $statusMsg = $this->translate('Please select message to remove!');
                $this->view->setVariables([
                            'success' => false,
                            'statusmsg' => $statusMsg
                    ]);

            } else {

                $msgExp = explode(",", $selectedMsgID);

                if(count($msgExp) > 0) {
                    //$checkUID = '';
                    for($i=0;$i<count($msgExp);$i++){

                        $msgIdSenderExp = explode("|", $msgExp[$i]);

                        if(count($msgIdSenderExp) > 0){
                            // Get all threads under the current message ID
                            $msgMain = $this->getMessageTable()->select(array('msg_threadID' => $msgIdSenderExp[0]));

                            if($msgMain){
                                foreach($msgMain as $msgMainFld):

                                    
                                    //Current User is the Sender
                                    if($msgMainFld->msg_senderID === $this->laIdentity()->getId()){
                                                                        
                                        $msg = $this->getMessageTable()->select(array('msg_id' => $msgMainFld->msg_id, 'msg_senderID' => $this->userId))->current();
                                        if ($msg){
                                            //$checkUID .= 'The receiver of message ID# '.$msgIdSenderExp[0].' is '.$msgIdSenderExp[1].'<br>';
                                            
                                            $this->getMessageTable()->delete($msgMainFld->msg_id);
                                            //$statusMsg = $this->translate($checkUID);
                                            $this->view->setVariables([
                                                        'success' => true,
                                                        'statusmsg' => 1 //$statusMsg
                                                ]);

                                        } /*else {
                                            $checkUID .= 'The receiver of message - Error<br>';
                                        }*/
                                    //Current User is the Receiver
                                    } else {                                
                                        
                                        $msg = $this->getMessageToTable()->select(array('msg2_recepientID' => $this->laIdentity()->getId(), 'msg2_messageID' => $msgMainFld->msg_id));

                                        if ($msg->current()){
                                            // Find the message ID in MessageTo table
                                            $msgToTbl = $this->getMessageToTable()->select(array('msg2_recepientID' => $this->laIdentity()->getId(), 'msg2_messageID' => $msgMainFld->msg_id))->current();
                                            
                                            if($msgToTbl){                                               
                                                $msgToJson = json_encode($msgToTbl);
                                                $currMsgToFld = json_decode($msgToJson,true);
                                                $this->getMessageToTable()->delete($currMsgToFld['msg2_id']);
                                            }
                                            
                                            //$checkUID .= 'The sender of message ID# '.$msgIdSenderExp[0].' is '.$msgIdSenderExp[1].'<br>';
                                            //$statusMsg = $this->translate($checkUID);
                                            $this->view->setVariables([
                                                        'success' => true,
                                                        'statusmsg' => 1//$statusMsg
                                                ]);
                                            
                                            
                                            
                                        } else {
                                            //$checkUID .= "The sender of message - Error - <br>".$this->laIdentity()->getId();
                                        }

                                    } 

                                endforeach;
                            
                            }
                            // End fetching all threads under the current message ID
                           
                        }

                    }

                    /*$statusMsg = $this->translate($checkUID);
                    $this->view->setVariables([
                                'success' => true,
                                'statusmsg' => $statusMsg
                        ]);*/

                } else {

                    $statusMsg = $this->translate('No message to remove!');
                    $this->view->setVariables([
                                'success' => false,
                                'statusmsg' => $statusMsg
                        ]);

                }
                

            }
            


        endif;

        return $this->view;       

    }

    /**
     * Hide a message2
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteMsg2Action() {
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $id = $this->params()->fromRoute('id');
        $msg = $this->getMessageToTable()->select(array('msg2_recepientID' => $this->laIdentity()->getId(), 'msg2_id' => $id));
        $this->view->success = false;
        if ($msg->current()) {
            $this->getMessageToTable()->delete($id);
            $this->view->success = true;
            //return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
        } else {
            return $this->displayNotFound();
        }
        return $this->view;
    }

    /**
     * Hide a message
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        $msg = $this->getMessageTable()->select(array('msg_id' => $id, 'msg_senderID' => $this->userId))->current();
        if ($msg) {
            $this->getMessageTable()->delete($id);
            $this->view->success = true;
            ///  return $this->redirect()->toUrl($this->request->getServer()->get('HTTP_REFERER'));
        } else {
            return $this->displayNotFound();
        }
        return $this->view;
    }

    /**
     * Reply to a message with the to-field pre-populated
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function replyAction() {
        $form = new \Message\Form\CreateMessageForm($this->getServiceLocator());
        $errors = [];
        if (!$this->request->isPost()) {
            $id = $this->params()->fromRoute('id');
            $msg = $this->getMessageTable()->getById($id);
            if (!$msg) {
                return $this->displayNotFound();
            }
            $form->get('recepient')->setValue($msg['msg_senderID']);
            $form->get('msg_subject')->setValue($msg['msg_subject']);
        }
        if ($this->request->isPost()) {
            if ($this->params()->fromPost('cancel')) {
                return $this->redirect()->toRoute('message/action-id', array('action' => 'view', 'id' => $this->params()->fromRoute('id')));
            }
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                try {
                    $transaction->beginTransaction();
                    $msg = new \Message\Model\DbEntity\Message();
                    $msg->exchangeArray($form->getData());
                    $msg->msg_senderID = $this->laIdentity()->getId();
                    $msg->msg_timeStamp = date('Y-m-d H:i:s');
                    $msgId = $this->getMessageTable()->insert($msg);
                    //save msgTo
                    foreach ($form->get('recepient')->getValue() as $recepient) {
                        $msgTo = new \Message\Model\DbEntity\MessageTo();
                        $msgTo->msg2_messageID = $msgId;
                        $msgTo->msg2_recepientID = $recepient;
                        $this->getMessageToTable()->insert($msgTo);
                    }
                    //commint transaction
                    $transaction->commit();
                    $this->flashMessenger()->addMessage($this->getServiceLocator()->get('translator')->translate('Message was successfully sent!'));
                    return $this->redirect()->toRoute('message');
                } catch (\Exception $exc) {
                    //rollback in case of error
                    $transaction->rollback();
                    // send error messages to admin
                    $this->getServiceLocator()->get('ErrorMail')->send($exc->getMessage());
                    // send message to the user
                    $errors[] = $this->getServiceLocator()->get('translator')->translate('We have a small problem with our database and are working to get it fixed. Please come back in about 30 minutes or so and try again.');
                }
            }
        }

        $this->initView();
        $this->view->form = $form;
        $this->view->errors = $errors;
        return $this->view;
    }

    /**
     * Forward message to other users with the subject and body pre-populated
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function forwardAction() {
        $form = new \Message\Form\CreateMessageForm($this->getServiceLocator());
        $errors = [];
        $id = $this->params()->fromRoute('id', false);
        $msg = $this->getMessageTable()->getById($id);
        if (!$msg) {
            return $this->displayNotFound();
        }
        $msg['msg_subject'] = 'FW: ' . $msg['msg_subject'];
        $form->setData($msg);

        $this->initView();
        $this->view->form = $form;
        $this->view->errors = $errors;
        return $this->view;
    }

    /**
     * Reply all to a message with the to-field pre-populated
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function replyAllAction() {
        $form = new \Message\Form\CreateMessageForm($this->getServiceLocator());
        $errors = [];
        if (!$this->request->isPost()) {
            $id = $this->params()->fromRoute('id');
            $recepients = $this->getMessageToTable()->getRecepients($id);
            $recepientId = [];
            foreach ($recepients as $rp) {
                if ($rp['msg2_recepientID'] != $this->laIdentity()->getId()) {// exclude the user from the list
                    $recepientId[] = $rp['msg2_recepientID'];
                }
            }
            $msg = $this->getMessageTable()->getById($id);
            if (!$msg) {
                return $this->displayNotFound();
            }
            // add sender to recepientID
            $recepientId[] = $msg['msg_senderID'];
            $form->get('recepient')->setValue($recepientId);
            $form->get('msg_subject')->setValue($msg['msg_subject']);
        }
        if ($this->request->isPost()) {
            if ($this->params()->fromPost('cancel')) {
                return $this->redirect()->toRoute('message/action-id', array('action' => 'view', 'id' => $this->params()->fromRoute('id')));
            }
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                try {
                    $transaction->beginTransaction();
                    $msg = new \Message\Model\DbEntity\Message();
                    $msg->exchangeArray($form->getData());
                    $msg->msg_senderID = $this->laIdentity()->getId();
                    $msg->msg_timeStamp = date('Y-m-d H:i:s');
                    $msgId = $this->getMessageTable()->insert($msg);
                    //save msgTo
                    foreach ($form->get('recepient')->getValue() as $recepient) {
                        $msgTo = new \Message\Model\DbEntity\MessageTo();
                        $msgTo->msg2_messageID = $msgId;
                        $msgTo->msg2_recepientID = $recepient;
                        $this->getMessageToTable()->insert($msgTo);
                    }

                    //commint transaction
                    $transaction->commit();
                    $this->flashMessenger()->addMessage($this->getServiceLocator()->get('translator')->translate('Message was successfully sent!'));
                    return $this->redirect()->toRoute('message');
                } catch (\Exception $exc) {
                    //rollback in case of error
                    $transaction->rollback();
                    // send error messages to admin
                    $this->getServiceLocator()->get('ErrorMail')->send($exc->getMessage());
                    // send message to the user
                    $errors[] = $this->getServiceLocator()->get('translator')->translate('We have a small problem with our database and are working to get it fixed. Please come back in about 30 minutes or so and try again.');
                }
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'errors' => $errors,
        ));
    }

    public function getRecipientAction() {
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $users = $this->get('FollowPeopleTable')->fetchFiFd($this->userId, array('usr_displayName', 'usr_icon', 'usr_id', 'usr_fName', 'usr_lName', 'usr_mName'));
        $this->view->data = $this->getPeopleManager()->processUser($users->toArray());
        return $this->view;
    }

    private function getMessageTable() {
        return $this->getServiceLocator()->get('MessageTable');
    }

    private function getMessageToTable() {
        return $this->getServiceLocator()->get('MessageToTable');
    }

    /**
     * @return \People\Model\PeopleManager
     */
    private function getPeopleManager() {
        return $this->getServiceLocator()->get('PeopleManager');
    }

    /**
     * Function to send email with html content
     * @param string $view view template for mail
     * @param array $data data passed to the view
     * @param array $mail_config sendmail property
     */
    protected function sendMail($view, $data, $mail_config) {
        $this->get('EmailSender')->sendTemplate($view, $data, $mail_config, array('info@linspira.com'), 'Linspira.com');
    }

    /**
     * Display message not found view
     * 
     * @return view
     */
    protected function displayNotFound() {
        if ($this->view == NULL) {
            $this->initView();
        }
        $this->view->setTemplate('message/msg/_message_not_found');
        return $this->view;
    }

}
