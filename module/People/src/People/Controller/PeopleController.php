<?php

/**
 * Description of PeopleController
 *
 * @author kimsreng
 */

namespace People\Controller;

use ImageManager\Model\PathManager;
use DocumentManager\Model\ResourceType as Resource;
use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class PeopleController extends AuthenticatedController {

    protected $nonAuthenticatedActions = ['show'];

    /**
     * Provide users list as json for client script
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function getUserAction() {
        $users = $this->getUserTable()->fetchAll(null, array('usr_id', 'usr_icon', 'usr_displayName', 'usr_fName', 'usr_mName', 'usr_lName'))->toArray();
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['usr_displayName'] == '') {
                $users[$i]['usr_displayName'] = $users[$i]['usr_fName'] . " " . ($users[$i]['usr_mName'] != "" ? $users[$i]['usr_mName'] . " " : "") . $users[$i]['usr_lName'];
            }
            $users[$i]['usr_icon'] = ($users[$i]['usr_icon'] === "" || $users[$i]['usr_icon'] === NULL) ? "/images/photo001.png" : $this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildUserRoutePath($users[$i]['usr_id'], \DocumentManager\Model\ResourceType::ICON, $users[$i]['usr_icon'])));
        }
        //add a blank one to the top
        array_unshift($users, ['id' => 0, 'usr_displayName' => '', 'usr_icon' => '/images/photo001.png']);
        return new JsonModel(array(
            'data' => $users,
        ));
    }

    /**
     * Show a user's profile
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction() {
        $user = $this->get('UserTable')->getById($this->params()->fromRoute('id'));
        $this->initView();
        if (!$user) {
            return $this->return404($this->translate("Sorry, we cannot find the person you're looking for."));
        }
        if ($this->isUserAuthenticated()) {
            $this->view->isOwner = ($user->usr_id === $this->laIdentity()->getId());
            $this->view->userId = $this->laIdentity()->getId();
        } else {
            $this->view->setTemplate('people/people/show_public');
        }

        $this->view->user = $user;
        return $this->view;
    }

    /**
     * List all the people a user is following or who are followig the user
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction() {

        $userTable = $this->getServiceLocator()->get('FollowPeopleTable');
        $page = $this->params()->fromRoute('page', 1);
        if ($this->params()->fromRoute('id', null) != NULL) {
            $id = $this->params()->fromRoute('id');
        } else {
            $id = $this->laIdentity()->getId();
        }
        $followers = $userTable->fetchFollower($id);
        $followees = $userTable->fetchFollowee($id);
        return new ViewModel(array(
            'followers' => $followers,
            'followees' => $followees,
        ));
    }

    /**
     * Action to edit user profile
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $userId = $this->params()->fromRoute('id', false);
        if (!$userId || $userId != $this->laIdentity()->getId()) {
            return $this->notFoundAction();
        }
        $user = $this->getUserTable()->getById($userId);
        $oldIcon = $user->usr_icon;
        $form = new \People\Form\UserUpdateForm($this->getServiceLocator());
        $dob = [];
        $this->initView();
        $this->view->form = $form;
        $this->view->user = $user;
        $this->view->skillList = $this->getUserSkillTable()->fetchByUser($userId);
        $this->view->education = $this->getEducationTable()->fetchByUser($userId);
        $this->view->experience = $this->getExperienceTable()->fetchByUser($userId);
        $this->view->emails = $this->get('UserEmailTable')->getByUserId($this->laIdentity()->getId(), false);
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            //
            $errors = false;
            $validIcon = false;
            $icon = $this->params()->fromFiles('usr_icon');
            // var_dump($_FILES);
            if ($icon['name'] != "") {
                $formErrors = array();
                $valid_exten = new \Zend\Validator\File\Extension(Resource::getAllowedImages());
                if (!$valid_exten->isValid($icon['name'], $icon)) {
                    $formErrors[] = $translator->translate("Invalid icon file type. Be sure to upload a GIF, JPG, or PNG image.");
                }
                if ($icon['error'] == 1) {
                    $formErrors[] = $translator->translate("Icon file size is too large. Upto " . \DocumentManager\Model\ImgManager::getMaxUploadMessage() . ' is allowed.');
                }
                if (count($formErrors) > 0) {
                    $errors = true;
                    $form->setMessages(array('usr_icon' => $formErrors));
                    $validIcon = false;
                } else {
                    $validIcon = true;
                }
            }
            if ($form->isValid()) {
                $postData = $form->getData();
                $user->exchangeArray($postData);
                if ($validIcon) { //TODO: have ImgManager handle this section
                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setDestination($this->getPathManager()->getUserPath($this->userId, Resource::ICON));
                    if ($adapter->receive($icon['name'])) {
                        @unlink($this->getPathManager()->getUserPath($this->userId, Resource::ICON) . DIRECTORY_SEPARATOR . $oldIcon); //remove old icon file
                        $user->usr_icon = $icon['name']; // add new icon
                        $this->get('AuthService')->getIdentity()->usr_icon = $icon['name'];
                    }
                } else {
                    $user->usr_icon = $oldIcon; //keep previous icon
                }
                if (!$errors) {

                    //if ($postData['dob_year'] && $postData['dob_month'] && $postData['dob_day']) {
                    $postData['dob_year'] = ($postData['dob_year'] !== '') ? $postData['dob_year'] : '0000';
                    $postData['dob_month'] = ($postData['dob_month'] !== '') ? $postData['dob_month'] : '0000';
                    $postData['dob_day'] = ($postData['dob_day'] !== '') ? $postData['dob_day'] : '0000';
                    $user->usr_dob = "{$postData['dob_year']}-{$postData['dob_month']}-{$postData['dob_day']}";
                    //}
                    $this->getUserTable()->update($user);
                    //update user session data to reflect changes
                    $this->get('AuthService')->getIdentity()->usr_displayName = $user->usr_displayName;
                    //save other data
                    $post = $this->params()->fromPost();
                    //var_dump($post);die();
                    $this->getPeopleManager()->processUserInfo($post, $this->userId);

                    // $this->redirect()->toRoute('people/profile', array('id' => $userId));
                }
            }
        }

        if ($user->usr_dob !== NULL) {
            list($year, $month, $day) = explode('-', $user->usr_dob);
            if ((int) $year !== 0) {
                $dob['dob_year'] = (int) $year;
            }
            if ((int) $month !== 0) {
                $dob['dob_month'] = (int) $month;
            }
            if ((int) $day !== 0) {
                $dob['dob_day'] = (int) $day;
            }
        }
        if ($user->usr_gender == null) {
            $user->usr_gender = 2;
        }
        $form->setData(array_merge($user->getArrayCopy(), $dob));
        //fill data in form
        $form->isValid();
        $data = $form->getData();
        $data['usr_gender'] = (int) $data['usr_gender'];
        //  $data['usr_dob'] = date('d/m/Y', strtotime($data['usr_dob']));
        $this->view->data = $data;
        return $this->view;
    }

    public function findAction() {
        $searchEngine = new \SearchEngine\Model\SearchEngine($this->getServiceLocator());
        $keyword = $this->params()->fromRoute('keyword');
        $people_result = $searchEngine->findPeople($keyword);
        return new ViewModel(array(
            'people' => $people_result,
        ));
    }

    public function followAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id || !$this->get('UserPolicy')->canBeFollowed($id)) {
            return $this->return404("This user cannot be followed");
        }

        if ($this->request->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }

        $this->initView();
        $user = $this->getUserTable()->getById($id);
        if (!$user) {
            $this->view->notFound = true;
            $this->msg = $this->translate('Sorry, but this user cannot be found.');
            return $this->view;
        }
        if ($this->getPeopleManager()->follow($user, $this->laIdentity()->getId())) {
            $this->view->success = true;
            $this->view->msg = $this->translate("You have successfully followed this user!");
        } else {
            $this->view->msg = $this->translate("It seems that you have already followed this user!");
            $this->view->success = false;
        }
        return $this->view;
    }

    public function unfollowAction() {
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->notFoundAction();
        }

        if ($this->request->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }

        $this->initView();
        $user = $this->getUserTable()->getById($id);
        if (!$user) {
            $this->view->notFound = true;
            $this->msg = $this->translate('Sorry, but this user cannot be found.');
            return $this->view;
        }
        if ($this->getPeopleManager()->unfollow($user, $this->laIdentity()->getId())) {
            $this->view->success = true;
            $this->view->msg = $this->translate("You have successfully unfollowed this user!");
        } else {
            $this->view->msg = $this->translate("It seems that you have not yet followed this user!");
            $this->view->success = false;
        }
        return $this->view;
    }

    public function followingAction() {
        // $followees = $this->get('FollowPeopleTable')->fetchFollowee($this->userId);
        return new ViewModel(array(
            //   'followees' => $followees,
            'userId' => $this->userId,
        ));
    }

    public function followersAction() {
        // $followers = $this->get('FollowPeopleTable')->fetchFollower($this->userId);
        return new ViewModel(array(
            //  'followers' => $followers,
            'userId' => $this->userId,
        ));
    }

    public function terminateAction() {
        $user = $this->getUserTable()->getById($this->userId);
        if ($this->getPeopleManager()->terminateUser($user, $this->params()->fromPost('reason'))) {
            return new JsonModel(['success' => true]);
        }
        return new JsonModel(['success' => false]);
    }

    public function partAction() {
        $part = $this->params()->fromRoute('part');
        $id = $this->params()->fromRoute('id');
        $user = $this->getUserTable()->getById($id);
        if (!$user) {
            return $this->notFoundAction();
        }
        $this->initView();
        $this->view->setTerminal(true);
        $this->view->user = $user;
        switch ($part) {
            case 'following':
                $this->view->setTemplate('people/people/show/following.phtml');
                break;
            case 'follower':
                $this->view->setTemplate('people/people/show/follower.phtml');
                break;
            case 'idea':
                $this->view->setTemplate('people/people/show/idea.phtml');
                break;
            case 'project':
                $this->view->setTemplate('people/people/show/project.phtml');
                break;
            default:
                return $this->notFoundAction();
        }
        return $this->view;
    }

    private function getFollowTable() {
        return $this->getServiceLocator()->get('FollowPeopleTable');
    }

    /**
     * 
     * @return \Users\Model\DbTable\UserTable
     */
    private function getUserTable() {
        return $this->getServiceLocator()->get('UserTable');
    }

    private function getUserSkillTable() {
        return $this->getServiceLocator()->get('UserSkillTable');
    }

    private function getEducationTable() {
        return $this->getServiceLocator()->get('EducationTable');
    }

    private function getExperienceTable() {
        return $this->getServiceLocator()->get('ExperienceTable');
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
     * @return \People\Model\PeopleManager
     */
    protected function getPeopleManager() {
        return $this->get('PeopleManager');
    }

}

?>
