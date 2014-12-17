<?php

/**
 * Description of IdeaController
 *
 * @author kimsreng
 */

namespace IdeaManagement\Controller;

use IdeaManagement\Model\DbEntity\Idea;
use DocumentManager\Model\ResourceType as Resource;
use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\ViewModel;
//use Users\Model\DbTable\Session;

class IdeaController extends AuthenticatedController {

    protected $nonAuthenticatedActions = ['view', 'evolution'];

    /**
     * List all ideas created by a user with 
     * the page size of 20 items
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function ideaAction() {
        $ideaTable = $this->getIdeaTable();
        $paginator = $ideaTable->fetchAllByUserId($this->laIdentity()->getId(), true);
        $page = $this->params()->fromRoute('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(20);
        //var_dump($paginator);
        return new ViewModel(array(
            'paginator' => $paginator,
        ));
    }

    /**
     * List all ideas created by a user with 
     * the page size of 20 items
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function allAction() {
        $ideas = $this->getFollowIdeaTable()->fetchByFollower($this->userId);
        return new ViewModel(array(
            'paginator' => $ideas,
        ));
    }

    /**
     * Create a new idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function newAction() {
        $form = new \IdeaManagement\Form\Filter\CreateIdeaFilter($this->getServiceLocator());
        $this->initView();
        //set id if it is from evolution page
        if ($this->params()->fromQuery('id')) {
            $idea = $this->getIdeaTable()->getById($this->params()->fromQuery('id'));
            if ($idea) {
                $this->view->parent = $idea;
                $this->view->category = $this->getCategoryTable()->fetchOne(array('cat_id' => $idea->idea_categoryID));
            }
        }
        $errors = [];
        $this->view->form = $form;
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            //upload validation
            $icon = $this->params()->fromFiles('idea_img');
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
                $data = [];
                if (isset($this->view->parent)) {
                    $data['parent'] = $this->view->parent->idea_id;
                }
                if ($icon['name'] != "") {
                    $data['idea_img'] = $icon['name'];
                }

                $data['reference'] = $this->params()->fromPost('reference');
                if (!count($errors) > 0) {
                    $ideaId = $this->getIdeaManager()->createIdea(array_merge($data, $form->getValues()), $this->userId);
                    $this->getIdeaManager()->saveIdeaIcon($ideaId, $icon);
                    // redirect to idea view page
                    return $this->redirect()->toRoute('idea/action-id', array('action' => 'view', 'id' => $ideaId));
                }
            }
        }
        $this->errors = $errors;
        return $this->view;
    }

    /**
     * Ask the user for the confirmation before the idea is finally created
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function confirmAction() {
        $session = new \Zend\Session\Container('default');
        if (!isset($session->idea)) {
            $this->redirect()->toRoute('idea/action', array('action' => 'new'));
        }
        $form = new \IdeaManagement\Form\CreateIdeaConfirmForm($this->getServiceLocator());
        if ($this->request->isPost()) {
            // go back to create idea page when user click back button
            if (isset($this->request->getPost()['back'])) {
                return $this->redirect()->toRoute('idea/action', array('action' => 'new'));
            }
            // go to home page when user click cancel button
            if (isset($this->request->getPost()['cancel'])) {
                //delete icon file if any
                @unlink($this->getPathManager()->getIdeaTempPath() . DIRECTORY_SEPARATOR . $session->idea['idea_img']);
                //delete attach file if any
                @unlink($this->getPathManager()->getIdeaTempPath() . DIRECTORY_SEPARATOR . $session->idea['idea_attachment']);
                $session->offsetUnset('idea');
                return $this->redirect()->toRoute('home');
            }
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $ideaId = $this->createIdea($session);
                if ($ideaId) {
                    // remove data in session
                    $session->offsetUnset('idea');
                    // redirect to idea view page
                    return $this->redirect()->toRoute('idea/action-id', array('action' => 'view', 'id' => $ideaId));
                }
            }
        }
        $categoryTable = $this->getServiceLocator()->get('CategoryTable');
        $id_condition = (count($session->idea['reference']) > 0 ? "idea_id IN (" . implode(',', $session->idea['reference']) . ")" : 'idea_id IN (0)');
        $ideaRef = $this->getServiceLocator()->get('IdeaTable')->fetchAll($id_condition);
        return new ViewModel(array(
            'idea' => $session->idea,
            'form' => $form,
            'categoryTable' => $categoryTable,
            'ideaRef' => $ideaRef
        ));
    }

    /**
     * View detail of an idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function viewAction() {
        $this->initView();
        $id = $this->params()->fromRoute('id');
        $idea = $this->getIdeaTable()->getById($id);
        if (!$idea) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            return $this->view;
        }
        $this->view->originator = $this->getServiceLocator()->get('UserTable')->getById($idea->idea_originator);
        $this->view->categoryTable = $this->getServiceLocator()->get('CategoryTable');
        $this->view->idea = $idea;
        if (!$this->isUserAuthenticated()) {
            $this->view->setTemplate('idea-management/idea/view_public');
            return $this->view;
        }
        if ($this->isUserAuthenticated()) {
            $this->view->userId = $this->laIdentity()->getId();
            //create comment form
            //$form = new \IdeaManagement\Form\CommentForm($this->getServiceLocator());
            //$form->get('iComm_userId')->setValue($this->laIdentity()->getId());
            //$form->get('iComm_ideaId')->setValue($id);
            //$this->view->form = $form;
            $this->view->ideaId = $id;
            //get comment list
            $commentList = $this->getIdeaCommentTable()->getComments($id);
            $this->view->commentList = $commentList;
            $this->view->commentTable = $this->getIdeaCommentTable();
            //check if the user originated the idea
            $this->view->is_originator = $this->laIdentity()->getId() == $idea->idea_originator;
            if ($this->view->is_originator) {
                $this->view->is_follow = false;
                // check if a project is started on this idea
                $projectTable = $this->getServiceLocator()->get('ProjectTable');
                $this->view->is_proejct_started = $projectTable->isIdeaStarted($idea->idea_id);
            } else {
                //check if the user is following the idea
                $this->view->is_proejct_started = false;
                $followIdeaTable = $this->getServiceLocator()->get('FollowIdeaTable');
                $this->view->is_follow = $followIdeaTable->isUserFollowIdea($this->laIdentity()->getId(), $idea->idea_id);
            }
        }

        
        $sessionTable = $this->getServiceLocator()->get('SessionTable');
        $userLoginTable = $this->getServiceLocator()->get('UserLoginTable');   
        $sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager');   
        $userLoginListing = $userLoginTable->showByUserDetails($this->laIdentity()->getId());  
        $userCurrentIPAdd = $userLoginTable->getTheRealUserIP();  

                $datarow = array();
                $uLgin_ip = '';
                if(count($userLoginListing) > 0){
                    foreach($userLoginListing as $row):
                        $datarow[] = $row->uLgin_ip;
                    endforeach;
                }

        $this->view->sessid = $datarow;
        $this->view->userip = $userCurrentIPAdd;
        $this->view->sessmgr = $sessionMgr->getId();

        $dataz['email'] = $this->laIdentity()->getUsername();
        $dataz['user_id'] = $this->laIdentity()->getId();
        $userTable = $this->getServiceLocator()->get('UserTable'); 
        $getCurrUserEmail = $userTable->getByEmail($this->laIdentity()->getUsername()); 
        $this->view->curremail = $getCurrUserEmail;

        //$dataz['user_sessionid'] = $sessionMgr->getId();
        //$dataz['user_identity'] = $sessionTable->getByUserId($this->laIdentity()->getId());
        //$dataz['user_ipchange'] = $sessionTable->hasIpChanged();
        $dataz['user_sessionid'] = $sessionTable->getBySessId($sessionMgr->getId());
        $dataz['user_cookie'] = $_COOKIE['PHPSESSID'];
        $this->view->dataz = $dataz;
        //increment idea hit count
        $this->getIdeaTable()->updateView($idea);

        return $this->view;
    }

    public function partAction() {
        $part = $this->params()->fromRoute('part');
        $id = $this->params()->fromRoute('id');
        $idea = $this->getIdeaTable()->getById($id);
        if (!$part || !$idea) {
            return $this->notFoundAction();
        }
        $this->initView();
        // $this->layout('layout/iframe');
        $this->view->idea = $idea;
        switch ($part) {
            case 'comment':
                $this->view->setTemplate('idea-management/idea/comment-part.phtml');
                break;
            case 'follower':
                $this->view->setTemplate('idea-management/idea/follower-part.phtml');
                break;
            case 'reference':
                $this->view->setTemplate('idea-management/idea/reference-part.phtml');
                $evolutionList = $this->getIdeaTable()->getEvolutionList($idea);
                $this->view->evolutionList = $evolutionList;
                $this->view->categoryTable = $this->getServiceLocator()->get('CategoryTable');
                break;
            case 'project':
                $this->view->setTemplate('idea-management/idea/project-part.phtml');
                break;
            default:
                break;
        }
        $this->view->setTerminal(true);
        return $this->view;
    }

    /**
     * Search for ideas by keyword
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function findAction() {
        $keyword = $this->params()->fromRoute('keyword');
        $searchEngine = new \SearchEngine\Model\SearchEngine($this->getServiceLocator());
        $idea_result = $searchEngine->findIdea($keyword);
        $this->initView();
        $this->view->results = $idea_result;
        $this->view->userId = $this->userId;
        $this->view->followIdeaTb = $this->getFollowIdeaTable();
        return $this->view;
    }

    /**
     * Follow(subscribe) an idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function followAction() {
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            return $this->notFoundAction();
        }
        if ($this->request->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }
        $this->initView();
        //increment idea follow count
        $idea = $this->getIdeaTable()->getById($id);
        if (!$idea) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            return $this->view;
        }
        if ($this->getIdeaManager()->followIdea($idea, $this->laIdentity()->getId())) {
            //notify the creator of the following
            $this->get('NotifyManager')->followIdeaNotify($idea, $this->get('UserTable')->getById($this->userId));
            $this->view->success = true;
            $this->view->msg = $this->translate("You have successfully followed this idea!");
        } else {
            $this->view->msg = $this->translate("It seems that you have already followed this idea!");
            $this->view->success = false;
        }
        if ($this->request->isXmlHttpRequest()) {
            $this->view->followCnt = $idea->idea_followCnt;
        }
        return $this->view;
    }

    /**
     * Unfollow(unsubscribe) an idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function unfollowAction() {
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            return $this->notFoundAction();
        }

        if ($this->request->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }

        $this->initView();

        $idea = $this->getIdeaTable()->getById($id);
        if (!$idea) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            return $this->view;
        }

        if ($this->getIdeaManager()->unfollowIdea($idea, $this->laIdentity()->getId())) {
            $this->view->success = true;
            $this->view->msg = $this->translate("You have successfully unfollowed this idea!");
        } else {
            $this->view->success = false;
            $this->view->msg = $this->translate("It seems that you have not yet followed this idea!");
        }
        if ($this->request->isXmlHttpRequest()) {
            $this->view->followCnt = $idea->idea_followCnt;
        }
        return $this->view;
    }

    /**
     * Hide idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function hideAction() {
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->view->success = false;
            return $this->view;
        }
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $idea = $this->getIdeaTable()->getById($id);
        if (!$idea) {
            // $this->view->notFound = true;
            // $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            $this->view->success = false;
            return $this->view;
        } $this->getIdeaTable()->hideIdea($id);
        $this->view->success = true;
        return $this->view;
    }

    /**
     * 
     * @return type
     */
    public function evolutionAction() {
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            return $this->notFoundAction();
        }
        $this->initView();
        $idea = $this->getIdeaTable()->getById($id);
        if (!$idea) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            return $this->view;
        }
        $evolutionList = $this->getIdeaTable()->getEvolutionList($idea);
        $this->view->evolutionList = $evolutionList;
        $this->view->idea = $idea;

        $this->view->categoryTable = $this->getServiceLocator()->get('CategoryTable');
        return $this->view;
    }

    /**
     * Action to add comment to an idea and redirect the view of that idea with 
     * @return redirect
     */
    public function addCommentAction() {

        if ($this->request->isPost()) {
            $this->viewType = self::JSON_MODEL;
            $this->initView();
            $form = new \IdeaManagement\Form\CommentForm($this->getServiceLocator());

            $post = $this->request->getPost();
            $post['iComm_ideaId'] = $post['id'];
            $post['iComm_comment'] = $post['comment'];
            $form->setData($post);
            if ($form->isValid()) {
                
                $sessionTable = $this->getServiceLocator()->get('SessionTable');
                $userLoginTable = $this->getServiceLocator()->get('UserLoginTable');   
                $sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager');   
                $userLoginListing = $userLoginTable->showByUserDetails($this->laIdentity()->getId());  
                $userCurrentIPAdd = $userLoginTable->getTheRealUserIP();  

                $datarow = array();
                $uLgin_ip = '';
                if(count($userLoginListing) > 0){
                    foreach($userLoginListing as $row):
                        if($userCurrentIPAdd != $row->uLgin_ip){
                            $uLgin_ip .= $row->uLgin_ip; 
                            $datarow[] = $row->uLgin_ip;                           
                        }
                        
                    endforeach;
                }

               // $this->view->sessid = $datarow;
                //$this->view->userip = $userCurrentIPAdd;
                
                $currSessionRow = $sessionTable->getBySessId($sessionMgr->getId());
                if($currSessionRow){
                    //UPDATE user id
                    $currSessionRow->sess_userID = $this->userId;
                    $whereFields = array('sess_PHPSESSID' => $sessionMgr->getId());
                    $sessionTable->updateCurrUserSession($whereFields,$currSessionRow);
                }

                if(count($datarow) > 0){
                    $isError = 1;
                    $errMsg = "The system detected that your account is in used by the another device!";
                } else {
                    $isError = 0;
                     $errMsg = "";
                }                

                if($isError == 1){
                    $this->view->setVariables([
                        'success' => false,
                        'error' => $errMsg, //$formErrors($form),
                        'relogin' => 1
                    ]);
                } else {

                    $ideaComment = new \IdeaManagement\Model\DbEntity\IdeaComment();
                    $ideaComment->exchangeArray($form->getData());
                    $ideaComment->iComm_userId = $this->userId;
                    $this->getIdeaCommentTable()->addComment($ideaComment);
                    //update lastPost in 
                    $formErrors = $this->getViewHelper('formElementErrors');
                    $this->getIdeaTable()->updateLastPost($ideaComment->iComm_ideaId);
                    
                    $this->view->setVariables([
                        'success' => true,
                        'id' => $ideaComment->iComm_id,
                        'dateTime' => $ideaComment->iComm_timeStamp,
                        'relogin' => 0
                    ]);

                }
                
                //return to the idea page with the new comment
                //return $this->redirect()->toRoute('idea/action-id', array('action' => 'view', 'id' => $form->get('iComm_ideaId')->getValue()));
            } else {
                $formErrors = $this->getViewHelper('formElementErrors');
                $this->view->setVariables([
                    'success' => false,
                    'error' => $formErrors($form),
                ]);
            }
            return $this->view;
        }
        //redirect to previous page or else to home page


        if ($this->request->getServer('HTTP_REFERER')) {
            return $this->redirect()->toUrl($this->request->getServer('HTTP_REFERER'));
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

    public function updateAction() {
        $form = new \IdeaManagement\Form\Filter\CreateIdeaFilter($this->getServiceLocator());
        $id = $this->params()->fromRoute('id');
        $idea = $this->getIdeaTable()->getById($id);

        $this->initView();
        $this->view->form = $form;

        if (!$idea) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, but this idea cannot be found.');
            return $this->view;
        }
        //Only allow owners of the idea to update
        if ($idea->idea_originator != $this->laIdentity()->getId()) {
            $this->view->notFound = true;
            $this->errors[] = $this->translate('Sorry, only owners can update their own ideas.');
            return $this->view;
        }
        //set
        $this->view->parent = $this->getIdeaTable()->getAscending($id);
        $this->view->child = $this->getIdeaTable()->getDescending($id);

        $this->view->idea = $idea;
        $data = $idea->getArrayCopy();
        $form->setData(array_merge($data, array('reference[]' => $this->get('IdeaRefTable')->fetchRefAsArray($id))));
//        $form->get('reference')->setValue($this->get('IdeaRefTable')->fetchRefAsArray($id));
//        $form->get('reference')->setAttribute('disabled', 'disabled');
        $oldIcon = $idea->idea_img;
        $errors = [];
        //      $oldAttach = $idea->idea_attachment;
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            //upload validation
            $icon = $this->params()->fromFiles('idea_img');
            if ($icon['name'] != "") {
                $valid_exten = new \Zend\Validator\File\Extension(Resource::getAllowedImages());
                if (!$valid_exten->isValid($icon['name'], $icon)) {
                    $errors[] = $this->translate("Invalid file type. Be sure to upload a GIF, JPG, or PNG image.");
                }
                if ($icon['error'] == 1) {
                    $errors [] = $this->translate("Icon file size is too large. Upto " . \DocumentManager\Model\ImgManager::getMaxUploadMessage() . ' is allowed.');
                }
            }

            if ($form->isValid()) {
                if (!count($errors) > 0) {
                    if ($icon['name'] != "") {
                        $idea->idea_img = $icon['name'];
                    } else {
                        $idea->idea_img = $oldIcon;
                    }
                    $idea->exchangeArray($form->getValues());
                    // $this->getIdeaTable()->update($idea);
                    $reference = $this->params()->fromPost('reference');
                    //prevent the same idea to be used as reference
                    if (in_array($id, $reference)) {
                        unset($reference[array_search($id, $reference)]);
                    } $this->getIdeaManager()->update($idea, $this->userId, $reference);
                    if ($this->getIdeaManager()->saveIdeaIcon($id, $icon)) {
                        $directory = $this->getPathManager()->getIdeaPath($idea->idea_id, Resource::ICON);
                        @unlink($directory . DIRECTORY_SEPARATOR . $oldIcon);
                    }
                    return $this->redirect()->toRoute('idea/action-id', array('action' =>
                                'view', 'id' => $idea->idea_id));
                }
            }
        }
        $this->errors = $errors;
        return $this->view;
    }

    /**
     * Delete a comment and go back to idea view page
     * 
     * @return redirect
     */
    public function deleteCommentAction() {
        $ideaId = $this->params()->fromRoute('id');
        $commentId = $this->params()->fromQuery('comment');
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        $comment = $this->getIdeaCommentTable()->getById($commentId);
        if (!$ideaId || !$comment) {
            //redirect to previous page or else to home page
            $this->view->error = $this->translate("Bad Id");
            return $this->view;
        }
        //make sure user will not delete readonly comment
        if ($comment->iComm_readOnly == 1) {
            $this->view->error = $this->translate("System comment cannot be deleted.");
            return $this->view;
        }
        $idea = $this->getIdeaTable()->getById($ideaId);
        //only allow the owner of the idea or the comment owner to delete the comment
        if ($idea->idea_originator == $this->laIdentity()->getId() || $this->getIdeaCommentTable()->isCommentOwner($this->laIdentity()->getId(), $commentId)) {
            $this->getIdeaCommentTable()->deleteComment($commentId);
            $this->view->success = true;
            return $this->view;
            // return $this->redirect()->toRoute('idea/action-id', array('action' => 'view', 'id' => $ideaId));
        } else {

            $this->view->error = $this->translate("You have no authorization to delete this comment.");
            return $this->view;
        }
    }

    /**
     * Report violation of an idea
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function reportIdeaAction() {
        $form = new \IdeaManagement\Form\Filter\ReportIdeaFilter();
        $ideaId = $this->params()->fromRoute('id');
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data['vp_userId'] = $this->laIdentity()->getId();
            $form->setData($data);
            if ($form->isValid()) {
                $ideaViolation = new \IdeaManagement\Model\DbEntity\ViolationReport();
                $ideaViolation->exchangeArray($form->getValues());
                $this->getReportViolationTable()->createViolationReport($ideaViolation);
                $idea = $this->getIdeaTable()->getById($ideaId);
                $sm = $this->getServiceLocator();
                $mail_config = array(
                    'to_email' => $this->config()->get('adminEmail')['toEmail'],
                    'to_name' => 'Linkaide Admin Team',
                    'subject' => 'Idea Violoation Report'
                );
                $this->sendMail('idea-management/email/_report-idea-email', array('owner' => $sm->get('UserTable')->getById($idea->idea_originator),
                    'idea' => $idea,
                    'report' => $ideaViolation,
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
     * Report violation of a comment
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function reportCommentAction() {
        $form = new \IdeaManagement\Form\Filter\ReportCommentFilter();
        $commentId = $this->params()->fromRoute('id');
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data['vp_userId'] = $this->laIdentity()->getId();
            $form->setData($data);
            if ($form->isValid()) {
                $commentViolation = new \IdeaManagement\Model\DbEntity\ViolationReport();
                $commentViolation->exchangeArray($form->getValues());
                $this->getReportViolationTable()->createViolationReport($commentViolation);
                $comment = $this->getIdeaCommentTable()->getById($commentId);
                if (!$comment) {
                    $this->view->success = false;
                    return $this->view;
                }
                $sm = $this->getServiceLocator();
                $mail_config = array(//TODO : this should be a global config var instead of hard-coded into each controller
                    'to_email' => $this->config()->get('adminEmail')['toEmail'],
                    'to_name' => 'Linspira Admin Team',
                    'subject' => 'Idea Comment Violoation Report'
                );
                $this->sendMail('idea-management/email/_report-comment-email', array('owner' => $sm->get('UserTable')->getById($comment->iComm_userId),
                    'comment' => $comment,
                    'report' => $commentViolation,
                    'idea' => $this->getIdeaTable()->getById($comment->iComm_ideaId),
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
     * 
     * @return \IdeaManagement\Model\DbTable\IdeaTable
     */
    private function getIdeaTable() {
        return $this->getServiceLocator()->get('IdeaTable');
    }

    /**
     * 
     * @return \IdeaManagement\Model\DbTable\CategoryTable
     */
    private function getCategoryTable() {
        return $this->getServiceLocator()->get('CategoryTable');
    }

    private function getFollowIdeaTable() {
        return $this->getServiceLocator()->get('FollowIdeaTable');
    }

    /**
     * 
     * @return \IdeaManagement\Model\DbTable\IdeaCommentTable
     */
    private function getIdeaCommentTable() {
        return $this->getServiceLocator()->get('IdeaCommentTable');
    }

    private function getReportViolationTable() {
        return $this->getServiceLocator()->get('ViolationReportTable');
    }

    /**
     * Create an idea after confirmation is made
     * 
     */
    private function createIdea($data) {
        $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        try {
            $transaction->beginTransaction();
            $idea = new Idea();
            $idea->exchangeArray($data);
            $idea->idea_originator = $this->laIdentity()->getId();
            $idea->idea_timeStamp = date('Y-m-d H:i:s');
            $idea->idea_isVisible = 1;
            //  $data['reference'] = explode('|', $data['reference']);
            if (isset($data['parent'])) {
                $idea_id = $this->getIdeaTable()->insert($idea, $data['parent']);
            } else {
                $idea_id = $this->getIdeaTable()->insert($idea);
            }
            $idea->idea_id = $idea_id;
            //move upload file to idea folder
            if (isset($data['idea_img'])) {
                rename($this->getPathManager()->getIdeaTempPath() . DIRECTORY_SEPARATOR . $data['idea_img'], $this->getPathManager()->getIdeaPath($idea_id, Resource::ICON) . DIRECTORY_SEPARATOR . $data['idea_img']);
            }
            // rename($this->getPathManager()->getIdeaTempPath() . DIRECTORY_SEPARATOR . $session->idea['idea_attachment'], $this->getPathManager()->getIdeaPath($idea_id, Resource::PRESENTATION) . DIRECTORY_SEPARATOR . $session-> idea['idea_attachment']);
            // save idea reference if any 
            if (count($data['reference']) > 0) {
                $ideaRefTable = $this->getServiceLocator()->get('IdeaRefTable');
                foreach ($data['reference'] as $ref) {
                    $idearef = new \IdeaManagement\Model\DbEntity\IdeaRef();
                    $idearef->iRef_newIdea = $idea_id;
                    $idearef->iRef_srcIdea = $ref;
                    $ideaRefTable->insert($idearef);
                }
            }
            //increment idea count in user table
            $userTable = $this->getServiceLocator()->get('UserTable');
            $user = $userTable->getById($this->laIdentity()->getId());
            $user->usr_ideaCnt++;
            $userTable->update($user);
            //auto follow the idea
            $this->getIdeaManager()->followIdea($idea, $this->userId);
            $transaction->commit();
            return $idea_id;
        } catch (Exception $exc) {
            $transaction->rollback();
            $this->getServiceLocator()->get('ErrorMail')->send($exc);
        }
    }

    /**
     * Function to send email with html content
     * @param string $view view template for mail
     * @param array $data data passed to the view
     * @param array $mail_config sendmail property
     */
    protected function sendMail($view, $data, $mail_config) {
        $this->get('EmailSender')->sendTemplate($view, $data, $mail_config, array
            ('system@linspira.com'), 'Linspira.com');
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
     * @return \IdeaManagement\Model\IdeaManager
     */
    public function getIdeaManager() {
        return $this->get('IdeaManager');
    }

}
