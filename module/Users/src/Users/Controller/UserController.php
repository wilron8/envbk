<?php

/**
 * Description of UserController
 *
 * @author kimsreng
 */

namespace Users\Controller;

use Users\Model\UserManager;
use Users\Model\DbEntity\Userjoin;
use Users\Model\DbEntity\UserEmail;
use Common\Mvc\Controller\AuthenticatedController;
use Common\Util\Crypt;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class UserController extends AuthenticatedController {

    protected $nonAuthenticatedActions = ['join', 'signin', 'confirm', 'registration', 'forgot', 'resetChallenge', 'reset', 'getCaptcha'];

    /**
     * Presignup Stage
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function joinAction() {
        if ($this->isUserAuthenticated()) {
            return $this->redirect()->toRoute('feed');
        }

        $dataz = array();
        
        $form = $this->get('PreSignupForm');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
            //$this->view->stat = 'json type';
        }

        $this->initView();
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->view->form = $form;
            $this->view->flashMessages = $this->flashMessenger()->getMessages();
            $this->view->stat = 'xml http request';
        }

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
             $this->view->stat = 'post type';
            //save XY
            $this->get('SessionTable')->setXY($this->params()->fromPost('x'), $this->params()->fromPost('y'));
            if ($form->isValid()) {
                 $this->view->stat = 'form valid stat';
                 $dataz['formz'] = 'form valid';
                if ($this->getUserTable()->getByEmail($form->get('join_email')->getValue())) {
                    $this->view->stat = 'email exist stat';
                    $dataz['formz'] = 'email exist';
                    $this->view->success = false;
                    $this->view->msg = sprintf($this->translate('The email you submitted is already registered! Would you like to %schange your password%s in case you forgot it'), '<a class="forgot_password" href="' . $this->url()->fromRoute('user', array('action' => 'forgot')) . '">', '</a>');
                    return $this->view;
                }
                try {
                    $userjoin = new Userjoin();
                    $userjoin->exchangeArray($form->getData());
                    $this->view->stat = 'email sent stat';
                    $dataz['formz'] = 'email sent';
                    //get language from session
                    // $session = new Container('default');
                    $language = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    list($lang, $country) = explode('_', str_replace('-', '_', $language));

                    $geocountry = $this->getGeoCountry()->getByISO3166(strtoupper($country));
                    $geolang = $this->getGeoLang()->getByISO639(strtoupper($lang));
                    $userjoin->join_langID = $geolang->geoLang_id;
                    $userjoin->join_countryID = $geocountry->geoCountry_id;

                    $this->get('UserjoinTable')->insert($userjoin);
                    $name = $userjoin->join_fName;
                    $email = $userjoin->join_email;
                    $key = $this->get('UserjoinTable')->getByEmail($userjoin->join_email)->join_checkNum;
                    $this->get('NotifyUser')->notifyJoin($name, $email, $key);
                    //   $this->view->email = $userjoin->join_email;
                    $this->view->success = true;
                    $partial = $this->getServiceLocator()->get('viewhelpermanager')->get('partial');
                    $this->view->msg = $partial("users/user/mail-sent", array('email' => $userjoin->join_email));
                    // $this->view->setTemplate('users/user/mail-sent');
                    // return $this->view;
                } catch (\Exception $exc) {
                    $this->view->stat = 'error email stat - '.$exc;
                    $dataz['formz'] = 'error email';
                    $this->get('ErrorMail')->send($exc);
                }
            } else {
                $this->view->stat = 'form invalid stat';
                $dataz['formz'] = 'form is invalid';
                $this->view->success = false;
                $formError = $this->getServiceLocator()->get('viewhelpermanager')->get('FormElementErrors');
                $this->view->msg = $formError($form);
            }
        }

       
        return $this->view;

    }

    public function registrationAction() {
        if ($this->isUserAuthenticated()) {
            return $this->redirect()->toRoute('feed');
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }

        $form = $this->get('SignupForm');
        $this->initView();
        $this->view->is_error = false;
        $container = new Container('default');
        $sData = array();
        $currKey = 'test';// $this->params()->fromRoute('key');
        //$this->view->testval = 'default val';
        if (!$this->request->isPost()) {

            if ($this->params()->fromRoute('key', false)) {
                $userjoin = $this->get('UserjoinTable')->getByCheckNum($this->params()->fromRoute('key'));
                if (!$userjoin) {
                    $this->flashMessenger()->addMessage($this->get('translator')->translate('The key token is invalid. Please signup here.'));
                    return $this->redirect()->toRoute('user', array('action' => 'join'));
                }
                if ($this->getServiceLocator()->get('UserTable')->getByEmail($userjoin->join_email)) {
                    return $this->redirect()->toRoute('user', array('action' => 'join'));
                }
                // redirect to join page if the key is expired
                $seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($userjoin->join_timeStamp);
                if (floor($seconds / 3600) > 24) {
                    $this->get('UserjoinTable')->clearExpiredKeys($userjoin->join_email); // remove expired keys
                    $this->flashMessenger()->addMessage($this->get('translator')->translate('The key token is expired! Please signup again and complete registration within 24 hours'));
                    return $this->redirect()->toRoute('user', array('action' => 'join'));
                }
            } elseif (isset($container->signup)) {//when user click back button from confirm page
                $userjoin = $this->get('UserjoinTable')->getByCheckNum($container->signup['hidden_session']);
                $form->setData($container->signup);
            } else {
                return $this->redirect()->toRoute('user', array('action' => 'join'));
            }

            //$form->get('hidden_session')->setValue($userjoin->join_checkNum);
            //set localiztion, language and country
            $geolang = $this->getGeoLang()->getById($userjoin->join_langID);
            $geoConountry = $this->getGeoCountry()->getById($userjoin->join_countryID);
            $locale = strtolower($geolang->geoLang_ISO639) . '_' . $geoConountry->geoCountry_ISO3166;
            $container->language = $locale;
            $this->getServiceLocator()->get('translator')->setLocale($locale);
            $sData['usr_lang'] = $geolang->geoLang_id;
            $sData['uPhon_countryCode'] = $geoConountry->geoCountry_callingCode;
            $sData['uAddr_country'] = $geoConountry->geoCountry_id;
            if (!isset($container->signup)) {
                // get first and last name from join table
                $name = explode(' ', $userjoin->join_fName);
                // check for japanese space char
                $jName = explode('　', $userjoin->join_fName);
                if (count($jName) > 1) {
                    $name = $jName;
                }
                switch (count($name)) {
                    case 3:
                        $firstName = $name[0];
                        $middleName = $name[1];
                        $lastName = $name[2];

                        break;
                    case 2:
                        $firstName = $name[0];
                        $middleName = "";
                        $lastName = $name[1];
                        break;
                    default:
                        $firstName = $name[0];
                        $middleName = "";
                        $lastName = "";
                        break;
                }
                $sData['usr_fName'] = $firstName;
                $sData['usr_mName'] = $middleName;
                $sData['usr_lName'] = $lastName;
            }
        }
        $form->setData($sData);
        //process post data
        if ($this->request->isPost()) {
            $this->view->status = 1;
            $this->view->isPost = true;
            $post = $this->params()->fromPost();
            //$post = $this->request->getPost();
            $post['captcha']['recaptcha_challenge_field'] = $post['captchaChallenge'];
            $post['captcha']['recaptcha_response_field'] = $post['captchaResponse']; 

            $form->setData($post);
            /*$sData = array();
            $sData['usr_fName'] = $post['usr_fName'];
            $sData['usr_mName'] = $post['usr_mName'];
            $sData['usr_lName'] = $post['usr_lName'];
            $sData['uAddr_address1'] = $post['uAddr_address1'];
            $sData['uAddr_address2'] = $post['uAddr_address2'];
            $sData['uAddr_city'] = $post['uAddr_city'];
            $sData['uAddr_state'] = $post['uAddr_state'];
            $sData['uAddr_ZIP'] = $post['uAddr_ZIP'];
            $sData['uAddr_country'] = $post['uAddr_country'];
            $sData['uPhon_type'] = $post['uPhon_type'];
            $sData['uPhon_countryCode'] = $post['uPhon_countryCode'];
            $sData['uPhon_areaCode'] = $post['uPhon_areaCode'];
            $sData['uPhon_number'] = $post['uPhon_number'];
            $sData['usr_lang'] = $post['usr_lang'];
            $sData['usr_secretQ'] = $post['usr_secretQ'];
            $sData['secretA'] = $post['secretA'];
            $sData['agreement'] = $post['agreement'];

            $sData['captcha']['recaptcha_challenge_field'] = $post['captchaChallenge'];
            $sData['captcha']['recaptcha_response_field'] = $post['captchaResponse']; 
            //$form->get('usr_mName')->setValue('tambaloslos ka');
            //$form->get('confirm_password')->setValue('tambaloslos ka');
            $sData['password'] = 'tambaloslos ka';
            $sData['confirm_password'] = 'tambaloslos ka'; 
//verifyPassword($password, $hash)
           /* $userData = array(
                'usr_fName'    => $post['usr_fName'],
                'usr_mName' => $post['usr_mName'],
                'usr_lName'     => $post['usr_lName'],

            );*/
            
            //$form->setData($sData);

            //$userjoin = $this->get('UserjoinTable')->getByCheckNum($this->params()->fromRoute('key'));
            $userjoin = $this->get('UserjoinTable')->getByCheckNum($post['currkey']);
            if ($form->isValid()) {
                $this->view->status = 1;
                $data = $form->getData();
                $data['usr_password'] = base64_decode($data['password']);
                //check if password is banned
                if ($this->isPasswordBanned($data['usr_password'])) {
                    $this->view->is_error = true;
                    $this->errors[] = $this->translate('Your password is too weak. Please choose another password. We suggest that you use a mix of upper and lower case letters with numbers and punctuation marks. Foreign lanugages are also supported.');
                } else {

                    $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                    $transaction->beginTransaction(); // start the transaction
                    try {
                        $this->view->status = 1;
                        //save user
                        $userManager = $this->getUserManager();
                        $userManager->register($userjoin, $data);

                        //Clear userjoin table
                        $this->get('UserjoinTable')->deleteByEmail($userjoin->join_email);
                        
                        // auto login user and redirect profile page
                        $result = $result = $this->get('Auth')->authenticate($userManager->getUser()->usr_username, $data['usr_password']);
                        if ($result->isValid()) {
                            
                            //send welcome message
                            $this->get('NotifyUser')->notifyWelcome($userManager->getUser());
                            //commint transaction
                            $transaction->commit();
                            $this->view->is_error = false;
                            $redirectURL = $this->getServiceLocator()->get('ViewHelperManager')->get('ServerUrl')->__invoke('/people/'.$userManager->getUser()->usr_id);
                            $this->view->status = $redirectURL;
                            //return $this->redirect()->toRoute('people/profile', array('id' => $userManager->getUser()->usr_id));
                        }
                    } catch (\Exception $exc) {
                                                
                        $this->view->status =  $this->getServiceLocator()->get('translator')->translate('<h3>Opps, we found a problem and will fix it soon. Thanks for helping us find it.</h3>');
                        $this->view->is_error = true;

                        //rollback in case of error
                        $transaction->rollback();
                        // send error messages to admin
                        $this->emailToAdmin($exc);
                        // send message to the user
                        $this->errors[] = $this->getServiceLocator()->get('translator')->translate('We have a small problem with our database and are working to get it fixed. Please come back in about 30 minutes or so and try again.');
                        
                    }
                    // return $this->redirect()->toRoute('user', array('action' => 'confirm'));
                }
            } else {
                $this->view->is_error = true;
                $formError = $this->getServiceLocator()->get('viewhelpermanager')->get('FormElementErrors');
                $this->view->status = $formError($form);
            }
            
            return $this->view;
        }

        $form->isValid();
        $data = $form->getData();
        $data['usr_lang'] = (int) $data['usr_lang'];
        $data['uAddr_country'] = (int) $data['uAddr_country'];
        $data['email'] = $userjoin->join_email;
        $data['fullname'] = $userjoin->join_fName;
        $data['currkey'] = $this->params()->fromRoute('key');
        // var_dump($data);
        $this->view->form = $form;
        $this->view->data = $data;
        $this->view->captchaKey = $this->get('Config')['recaptcha']['public_key'];
        return $this->view;
    }

    public function confirmAction() {
        $form = $this->get('SignupConfirmForm');
        $this->initView();
        $container = new Container('default');
        $errors = array();
        if (isset($container->signup)) {
            $data = $container->signup;
        } else {
            return $this->redirect()->toRoute('user', array('action' => 'join'));
        }
        //
        if ($this->request->isPost()) {
            // go back to signup page when user click back button
            if (isset($this->request->getPost()['back'])) {
                return $this->redirect()->toRoute('user', array('action' => 'registration'));
            }
            // go back to home page when user click back button
            if (isset($this->request->getPost()['cancel'])) {
                return $this->redirect()->toRoute('home');
            }
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                //use transaction 
                $transaction = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
                try {
                    $transaction->beginTransaction(); // start the transaction
                    //save user
                    $userjoin = $this->get('UserjoinTable')->getByCheckNum($data['hidden_session']);
                    $userManager = $this->getUserManager();
                    $userManager->register($userjoin, $data);
                    //commint transaction
                    $transaction->commit();
                    //Clear userjoin table
                    $this->get('UserjoinTable')->deleteByEmail($userjoin->join_email);
                    // auto login user and redirect profile page
                    $result = $this->login($userManager->getUser()->usr_username, $data['usr_password']);
                    if ($result->isValid()) {
                        // destroy registration data
                        $container->offsetUnset('signup');
                        //send welcome message
                        $this->get('NotifyUser')->notifyWelcome($userManager->getUser());
                        return $this->redirect()->toRoute('people/profile', array('id' => $userManager->getUser()->usr_id));
                    }
                } catch (\Exception $exc) {
                    //rollback in case of error
                    $transaction->rollback();
                    // send error messages to admin
                    $this->emailToAdmin($exc);
                    // send message to the user
                    $this->errors[] = $this->getServiceLocator()->get('translator')->translate('We have a small problem with our database and are working to get it fixed. Please come back in about 30 minutes or so and try again.');
                }
            }
        }
        $userjoin = $this->get('UserjoinTable')->getByCheckNum($data['hidden_session']);
        $tem_profile = array(
            'email' => $userjoin->join_email,
            'fullname' => $data['usr_fName'] . ' ' . $data['usr_mName'] . ' ' . $data['usr_lName'],
            'lang' => $this->get('geoLangTable')->getById($data['usr_lang'])->geoLang_name,
            'address' => $data['uAddr_address1'] . ' ' . $data['uAddr_address2'] . ' ' . $data['uAddr_city'] . ' ' . $data['uAddr_state'] . ' ' . $data['uAddr_ZIP'] . ' ' . $this->getGeoCountry()->getById($data['uAddr_country'])->geoCountry_name,
            'phone' => $data['uPhon_number'],
            'errors' => $errors,
        );
        $this->view->form = $form;
        $this->view->profile = $tem_profile;
        return $this->view;
    }

    /**
     * Action to authenticate user
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function signinAction() {

        if ($this->isUserAuthenticated()) {
            if (!$this->getRequest()->isXmlHttpRequest()) { 
                return $this->redirect()->toRoute('feed');
            }            
        }
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }

        $this->initView();
        $form = $this->get('LoginForm');
        $joinForm = new \Users\Form\PreSignupForm();
        $this->view->form = $form;
        $this->view->joinForm = $joinForm;        

        // If request is relogin
        if ($this->getRequest()->isXmlHttpRequest()) {            

            // Access the User Login Table
            $userLoginTable = $this->getServiceLocator()->get('UserLoginTable');
            // Access the User Table
            $userTable = $this->getServiceLocator()->get('UserTable'); 
            // Access the Session Table
            $sessionTable = $this->getServiceLocator()->get('SessionTable'); 
            // Access the Session Manager
            $sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager'); 
            // Get the Current User IP Address
            $userCurrentIPAdd = $userLoginTable->getTheRealUserIP(); 
            // Get the Current User details based on his email
            $currUser = $userTable->getByEmail(strtolower($this->request->getPost('usr_username')));
            // Get the Current User ID
            $currUserID = ($currUser) ? $currUser->usr_id : '';
            // Filtered by Current User ID and his IP in User Login Table
            $whereFields = array('uLgin_userID' => $currUserID, 'uLgin_ip' => $userCurrentIPAdd);
            $currUserLogin = $userLoginTable->showByCustomFields($whereFields);
            // Get the Current User ID in user table
            $currUserIDLogin = ($currUserLogin) ? $currUserLogin->uLgin_userID : '';
            
            if ($this->request->isPost()) {
                $post = $this->request->getPost();
                $form->setData($post);
                // Compare Current User ID to other User ID - this will prevent using other account with the current session
                if((!empty($currUserID) || !empty($currUserIDLogin)) && $currUserID != $currUserIDLogin){
               
                    $statusMsg = $this->translate('Please enter your account login for this session!'); 
                    $this->view->setVariables([
                            'success' => false,
                            'statusmsg' => $statusMsg
                        ]);
                } elseif($form->isValid()) {
                    $result = $this->get('Auth')->authenticate($this->request->getPost('usr_username'), $this->request->getPost('usr_password'), $this->request->getPost('rememberme', false));
                    if ($result->isValid()) {
                            
                            
                            $sessID = $sessionTable->getBySessId($currUserIDLogin);
                            $userLoginListing = $userLoginTable->showByUserDetails($currUserID,'',0);

                            $datarow = array();
                            $uLgin_ip = '';
                            if(count($userLoginListing) > 0){
                                $currCount = 1;
                                foreach($userLoginListing as $row):
                                    if($currCount > 1):
                                        $whereFlds = array('uLgin_id' => $row->uLgin_id);
                                        $userLoginTable->removeByCustomFields($whereFlds);
                                    endif;
                                    $currCount++;
                                endforeach;
                            }

                           //save XY
                            $this->get('SessionTable')->setXY($this->params()->fromPost('xDPI'), $this->params()->fromPost('yDPI'));
                            
                            $statusMsg = $this->translate('Login Success! - Current Session ID: '.$currUserIDLogin .' - Current Session Email: '.$this->laIdentity()->getUsername()); 
                            $this->view->setVariables([
                                'success' => true,
                                'statusmsg' => $statusMsg,
                                'sessioncurr' => $sessionMgr->getId()
                            ]);
                      
                       
                    } else {
                        $statusMsg = $this->translate('Either your email or password is incorrect 2!');
                        $this->view->setVariables([
                            'success' => false,
                            'statusmsg' => $statusMsg
                        ]);
                        
                    }
                } else {
                    

                    $statusMsg = "Either your Email or Password is incorrect!";
                    $this->view->setVariables([
                            'success' => false,
                            'statusmsg' => $statusMsg
                    ]);
                }
            }        
                          
            return $this->view;
        }

        // End If request is relogin

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                $result = $this->get('Auth')->authenticate($this->request->getPost('usr_username'), $this->request->getPost('usr_password'), $this->request->getPost('rememberme', false));
                if ($result->isValid()) {
                    //save XY
                    $this->get('SessionTable')->setXY($this->params()->fromPost('xDPI'), $this->params()->fromPost('yDPI'));
                    //check if the last page is stored in cookie. if so, start that page
                    if (isset($_COOKIE['start_page'])) {
                        $page = $_COOKIE['start_page'];
                        setcookie("start_page", "", time() - 3600, '/');
                        //return $this->redirect()->toUrl($page);
                        return $this->redirect()->toRoute('feed');
                    }
                    return $this->redirect()->toRoute('feed');
                } else {
                    $this->errors[] = $this->translate('Either username or password is incorrectz');
                }
            }
        }

        // Start Remove current session
        $sessionTable = $this->getServiceLocator()->get('SessionTable'); 
        $user_currSession = $sessionTable->getBySessId($_COOKIE['PHPSESSID']);
        $this->view->currsesid = 'No current session id detected!';
        if($user_currSession):
            foreach($user_currSession as $key => $val):
                if($key == "sess_id"):
                    $this->view->currsesid = 'Current Session ID: '.$val;
                endif;
            endforeach;
        endif;
        /*$sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $user_sessionid = $sessionTable->getBySessId($sessionMgr->getId());

        foreach($user_sessionid as $key => $val):
            if($key == "sess_id"):
                if($sessionTable->delete($val)){
                    $this->view->sessidstatus = 'Removed!'; 
                } else {
                    $this->view->sessidstatus = 'Failed!'; 
                }               
                $this->view->sessid = $val;             
            endif;
        endforeach;*/ 
        // End Remove current session
        //$this->view->allsess = $user_sessionid;

        //$sessionTable = $this->getServiceLocator()->get('SessionTable');
        //$usTable = $this->getServiceLocator()->get('UserTable'); 
        //$this->view->sessid = $usTable->fetchAll();
        
        /*if($user_currSession):
            foreach($user_currSession as $key => $val):
                if($key == "sess_id"):
                    $sessionTable->delete($val);
                endif;
            endforeach;
        endif;*/
        
        return $this->view;
    }

    public function forgotAction() {
        if ($this->isUserAuthenticated()) {
            return $this->redirect()->toRoute('feed');
        }
        if ($this->request->isPost()) {
            $form = new \Users\Form\ForgotForm($this->getServiceLocator());
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $user = $this->get('UserTable')->getByEmail($form->get('email')->getValue());
                if ($user) {
                    //save userpassforgot table
                    $userPassF = new \Users\Model\DbEntity\UserPassForgot();
                    $userPassF->usrPassF_usrID = $user->usr_id;
                    $sessId = $this->get('UserPassForgotTable')->insert($userPassF);

                    $this->get('NotifyUser')->notifyForgot($user, $sessId);
                    // show mailsent page
                    $email = $user->usr_email;
                    return new JsonModel([
                        'success' => true,
                        'msg' => sprintf($this->translate('Password recovery email is sent to %s'), $email) . '<br /><br />'
                        . sprintf($this->translate('Please follow the instruction sent from %sjoin@linspira.com%s and complete the recovery.'), '<a href="mailto:join@linspira.com?subject=&amp;body=">', '</a>') . '<br /><br />'
                        . sprintf($this->translate('Not receiving email? Please visit our %sHelp Center%s.'), '<a href="' . $this->url()->fromRoute('help') . '">', '</a>')
                    ]);
                } else {
                    // raise error
                    return new JsonModel([
                        'success' => false,
                        'msg' => $this->translate('This email is not registered. Please check the spelling and try again.')
                        //'msg' => $this->translate('We cannot find this email in the system. Please check the email again')
                    ]);
                }
            }
        }
        return $this->redirect()->toRoute('home');
    }

    /**
     * Action to get user answer their secret question before they continue to reset their password
     * 
     * @return \Zend\View\Model\ViewModel
     * @throws \Exception
     */
    public function resetChallengeAction() {
        $form = new \Users\Form\ForgotQuestionForm($this->getServiceLocator());
        $this->initView();

        // Set 'token expired' to false as default
        $isTokenExpired = false;

        if (!$this->request->isPost()) {
            $token = $this->params()->fromRoute('key', false);//$this->params()->fromQuery('token', false);

            if (!$token) {
                return $this->redirect()->toRoute('home');
            }
            $userPassF = $this->get('UserPassForgotTable')->getBySessId($token);
            

            if (!$userPassF) {                
                $this->errors[] = $this->translate('Token is invalid.');
                $currUsrPassFDateTime = 0;
                $currUserPassFUsrID = "";
            } else {
                $currUsrPassFDateTime = $userPassF->usrPassF_dateTime;
                $currUserPassFUsrID = $userPassF->usrPassF_usrID;
            }
            //check if it is within 24 hours
            //$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($userPassF->usrPassF_dateTime);
            
            $seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($currUsrPassFDateTime);
            if (floor($seconds / 3600) > 24) {
                // Set 'token expired' to true
                $isTokenExpired = true;
                $this->errors[] = $this->translate('Token has expired. Please go to forgot-page to start again.');
            }
            $form->get('user_id')->setValue($currUserPassFUsrID);
            $user = $this->getUserTable()->getById($currUserPassFUsrID);
        }
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $matchedUser = $this->getUserTable()->getById($form->get('user_id')->getValue());

                if ($matchedUser && Crypt::verify($form->get('answer')->getValue(), $matchedUser->usr_secretA)) {
                    $container = new Container('default');
                    $container->reset_id = $matchedUser->usr_id;
                    return $this->redirect()->toRoute('user', array('action' => 'reset'));
                } else {                    
                    $this->errors[] = $this->translate('The answer you provided doesn\'t seem to be correct. Please try again.');
                }
            }
            $user = $this->getUserTable()->getById($form->get('user_id')->getValue());
        }

        $this->view->form = $form;
        $this->view->question = ($user) ? $user->usr_secretQ : "";
        $this->view->isTokenExpired = $isTokenExpired;
        return $this->view;
    }

    /**
     * Action to allow user to reset their password
     * 
     * @return \Zend\View\Model\ViewModel
     * @throws \Exception
     */
    public function resetAction() {
        $this->initView();
        $invalid = false;
        $container = new Container('default');
        if (!isset($container->reset_id)) {
            $this->errors[] = $this->translate('Please go through question-answer step first.');
            $invalid = true;
        }
        $form = new \Users\Form\Filter\ResetPasswordFilter($this->getServiceLocator());
//        if (!$this->request->isPost()) {
//            $form->setData($_POST);
//            $form->isValid();
//        }

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $userTable = $this->getUserTable();
                $user = $userTable->getById($container->reset_id);
                if ($user) {
                    //save old password to pHistory
                    $user->usr_pHistory = $user->usr_password;
                    $user->usr_password = $this->getUserTable()->encryptPassword($form->get('password')->getValue());
                    $userTable->update($user);
                    $container->offsetUnset('reset_id');
                    //send password confirmation email

                    $this->get('NotifyUser')->notifyResetPassword($user);
                    //do auto login
                    $result = $this->get('Auth')->authenticate($user->usr_username, $form->get('password')->getValue());
                    if ($result->isValid()) {
                        return $this->redirect()->toRoute('people/profile', array('id' => $user->usr_id));
                    } else {
                        $this->errors[] = $this->translate('Invalid login.');
                    }
                } else {
                    $this->errors[] = $this->translate('User is invalid.');
                }
            }
        }
        // $this->errors= $form->getMessages());
        $this->view->form = $form;
        $this->view->invalid = $invalid;
        return $this->view;
    }

    public function reloginAction() {
        
        $statusMsg = '';
        if (!$this->isUserAuthenticated()) {
            //return $this->redirect()->toRoute('feed');
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }
        
        $this->initView();
        $form = $this->get('LoginForm');        
        $this->view->form = $form;
        // Access the User Login Table
        $userLoginTable = $this->getServiceLocator()->get('UserLoginTable');
        // Access the User Table
        $userTable = $this->getServiceLocator()->get('UserTable'); 
        // Access the Session Table
        $sessionTable = $this->getServiceLocator()->get('SessionTable'); 
        // Access the Session Manager
        $sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager'); 
        // Get the Current User IP Address
        $userCurrentIPAdd = $userLoginTable->getTheRealUserIP(); 
        // Get the Current User details based on his email
        $currUser = $userTable->getByEmail(strtolower($this->request->getPost('usr_username')));
        // Get the Current User ID
        $currUserID = ($currUser) ? $currUser->usr_id : '';
        // Filtered by Current User ID and his IP in User Login Table
        $whereFields = array('uLgin_userID' => $currUserID, 'uLgin_ip' => $userCurrentIPAdd);
        $currUserLogin = $userLoginTable->showByCustomFields($whereFields);
        // Get the Current User ID in user table
        $currUserIDLogin = ($currUserLogin) ? $currUserLogin->uLgin_userID : '';
        
        $err = 1;

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            // Compare Current User ID to other User ID - this will prevent using other account with the current session
            if((!empty($currUserID) || !empty($currUserIDLogin)) && $currUserID != $currUserIDLogin){
            //if($err == 1){
                $statusMsg = $this->translate('Please enter your account login for this session!'); 
                $this->view->setVariables([
                        'success' => false,
                        'statusmsg' => $statusMsg
                    ]);
            } elseif($form->isValid()) {
                $result = $this->get('Auth')->authenticate($this->request->getPost('usr_username'), $this->request->getPost('usr_password'), $this->request->getPost('rememberme', false));
                if ($result->isValid()) {
                        
                        
                        $sessID = $sessionTable->getBySessId($currUserIDLogin);
                        $userLoginListing = $userLoginTable->showByUserDetails($currUserID,'',0);

                        $datarow = array();
                        $uLgin_ip = '';
                        if(count($userLoginListing) > 0){
                            $currCount = 1;
                            foreach($userLoginListing as $row):
                                if($currCount > 1):
                                    $whereFlds = array('uLgin_id' => $row->uLgin_id);
                                    $userLoginTable->removeByCustomFields($whereFlds);
                                endif;
                                $currCount++;
                            endforeach;
                        }

                        //$this->get('SessionTable')->sniffUser();
                        //$userLoginTable->removeByCustomFields($whereFlds); 
                        //$this->get('Auth')->trackLogin($this->request->getPost('usr_username'), $result);
                        //$login = new \Users\Model\DbEntity\UserLogin(); 
                        //$userLoginTable->insert($login);
                        //$this->getServiceLocator()->get('SessionTable')->sniffUser(); 
                        //$this->get('Auth')->authenticate($this->request->getPost('usr_username'), $this->request->getPost('usr_password'), $this->request->getPost('rememberme', false));
                        //save XY
                        $this->get('SessionTable')->setXY($this->params()->fromPost('xDPI'), $this->params()->fromPost('yDPI'));
                        
                        $statusMsg = $this->translate('Login Success! - Current Session ID: '.$currUserIDLogin .' - Current Session Email: '.$this->laIdentity()->getUsername()); 
                        $this->view->setVariables([
                            'success' => true,
                            'statusmsg' => $statusMsg,
                            'sessioncurr' => $sessionMgr->getId() //$sessID->current()
                        ]);
                  
                   
                } else {
                    $statusMsg = $this->translate('Either your email or password is incorrect 2!');
                    $this->view->setVariables([
                        'success' => false,
                        'statusmsg' => $statusMsg
                    ]);
                    
                }
            } else {

                //$userNameErr = $form->getMessages('usr_username');
                //$statusMsg = $this->formElementErrors($form->get('name'));
                /*$messages = array();
                $errors = $form->getMessages();
                foreach($errors as $key=>$row):
                    if (!empty($row) && $key != 'submit') {
                        foreach($row as $keyer => $rower)
                        {
                            $messages[$key][] = $rower;    
                        }
                    }
                endforeach;
                if (!empty($messages)){ 
                    $statusMsg = $messages;
                }*/

                $statusMsg = "Either your Email or Password is incorrect!";
                $this->view->setVariables([
                        'success' => false,
                        'statusmsg' => $statusMsg
                ]);
            }
        }        
        
       
         
        //$this->view->statusmsg = $statusmsg;
        
        return $this->view;
        /*$jsonData = array();
        $jsonData['statusmsg'] = $statusMsg;
        header("Content-type: application/json");
        echo json_encode($jsonData);*/
    }

    /**
     * Log the user out
     * 
     * @return redirect
     */
    public function logoutAction() {
        $this->initView();

        // Start Remove current user session
        //$sessionTable = $this->getServiceLocator()->get('SessionTable'); 
        // Access the User Login Table
        $userLoginTable = $this->getServiceLocator()->get('UserLoginTable'); 
       // $user_currSession = $sessionTable->getBySessId($_COOKIE['PHPSESSID']);
        // Get the current user IP Address
        $userCurrentIPAdd = $userLoginTable->getTheRealUserIP(); 
        // Remove the current user from the userlogin table
        $whereFlds = array('uLgin_userID' => $this->laIdentity()->getId(),'uLgin_ip' => $userCurrentIPAdd);
        $userLoginTable->removeByCustomFields($whereFlds);        
        $this->view->sessidstatus = $this->laIdentity()->getId();
        /*if($user_currSession):
            foreach($user_currSession as $key => $val):
                if($key == "sess_id"):
                    $sessionTable->delete($val);
                endif;
            endforeach;
        endif;*/
        // End Remove current user session


        
        
        /*$sessionTable = $this->getServiceLocator()->get('SessionTable'); 
        $sessionMgr = $this->getServiceLocator()->get('Zend\Session\SessionManager');
        $user_sessionid = $sessionTable->getBySessId($sessionMgr->getId());
        foreach($user_sessionid as $key => $val):
            if($key == "sess_id"):
                if($sessionTable->delete($val)){
                    $this->view->sessidstatus = 'Removed!'; 
                } else {
                    $this->view->sessidstatus = 'Failed!'; 
                }                 
            endif;
        endforeach;*/
        $this->laIdentity()->clearIdentity();
        $this->getServiceLocator()->get('AuthService')->getStorage()->forgetMe();
        session_regenerate_id(true);
        return $this->redirect()->toRoute('home');
        //return $this->view;
    }

    public function settingsAction() {
        $this->initView();
        $form = new \Users\Form\Filter\AccountingSettingFormFilter($this->getServiceLocator());
        $email = $this->laIdentity()->getUsername();
        $userTable = $this->getUserTable();
        $address = $this->get('UserAddressTable')->getByUserId($this->laIdentity()->getId());
        $user = $userTable->getByEmail($email);
        $phone = $this->get('UserPhoneTable')->getPrimByUserId($this->laIdentity()->getId());
        if (!$this->request->isPost()) {
            //format date to show up in the form
//            if ($user->usr_dob != "") {
//                $user->usr_dob = date('d/m/Y', strtotime($user->usr_dob));
//            }
            $data = $user->getArrayCopy();
            if ($address) {
                $data = array_merge($address->getArrayCopy(), $data);
            }
            if ($phone) {
                $data = array_merge($data, $phone->getArrayCopy());
            }
            $form->setData($data);
        }

        $this->view->form = $form;
        $this->view->user = $user;
        $this->view->emails = $this->get('UserEmailTable')->getByUserId($this->laIdentity()->getId());
        
        $this->view->errormsg = '';

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());

            $oldPassword = $this->request->getPost('myPassword');
            $newPassword = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');     
            $bcrypt = new \Zend\Crypt\Password\Bcrypt();       

           /* if((!empty($newPassword) || !empty($confirmPassword)) && empty($oldPassword)){
                $this->view->errormsg = $this->translate('<li>Current Password is required.</li>');
            } elseif(!empty($oldPassword) && !$this->verifyPassword($oldPassword, $user->usr_password)){
                $this->view->errormsg = $this->translate('<li>Current Password is invalid.</li>');
            } elseif(!empty($newPassword) && empty($confirmPassword)){
                $this->view->errormsg = $this->translate('<li>Confirm Password is required.</li>');
            } elseif ($this->isPasswordBanned($confirmPassword)) {
                $this->view->errormsg = $this->translate('<li>The new password that you have entered has been banned.</li>');
            } elseif ($bcrypt->verify($confirmPassword, $user->usr_pHistory) || $oldPassword == $confirmPassword) {
                $this->view->errormsg = $this->translate('<li>You cannot use your old password again.</li>');
                   
            }*/
            
            /*if( ((!empty($newPassword) || !empty($confirmPassword)) && empty($oldPassword)) || ((empty($newPassword) || empty($confirmPassword)) && !empty($oldPassword)) ) {
                $this->view->errormsg = $this->translate('<li>Current Password, New Password and Retype Password are required.</li>');
            } elseif ( !empty($newPassword) && !$this->verifyPassword($oldPassword, $user->usr_password) ) {
                $this->view->errormsg = $this->translate('<li>Current Password is invalid.</li>');
            } elseif ( $this->isPasswordBanned($confirmPassword) ) {
                $this->view->errormsg = $this->translate('<li>Your password is too weak. Please choose another password. We suggest that you use a mix of upper and lower case letters with numbers and punctuation marks. Foreign lanugages are also supported.</li>');
            } elseif ( !empty($user->usr_pHistory) && $bcrypt->verify($confirmPassword, $user->usr_pHistory) ) {
                $this->view->errormsg = $this->translate('<li>You cannot use your old password again.</li>');
            } else {*/

            // Start Passwords validation
            if ( $confirmPassword !== "" ) {

                if ( $oldPassword === "" || $newPassword === "" ) {

                    $this->view->errormsg = $this->translate('<li>Current Password, New Password and Retype Password are required.</li>');

                } else {// check if pass valid

                    if ( $this->verifyPassword($oldPassword, $user->usr_password) ){ //valid current pass

                        if ( $this->isPasswordBanned($confirmPassword) ) {
                            $this->view->errormsg = $this->translate('<li>Your password is too weak. Please choose another password. We suggest that you use a mix of upper and lower case letters with numbers and punctuation marks. Foreign lanugages are also supported.</li>');
                        }

                        if ( !empty($user->usr_pHistory) && $bcrypt->verify($confirmPassword, $user->usr_pHistory) ) {
                            $this->view->errormsg = $this->translate('<li>You cannot use your old password again.</li>');
                        } else {

                            if ($form->isValid()) {                    
                                
                                if(!empty($confirmPassword) && !empty($newPassword) && !empty($oldPassword)){
                                    $user->usr_pHistory = $user->usr_password;
                                    $user->usr_password = $userTable->encryptPassword($form->get('password')->getValue());
                                    //send password confirmation email
                                    $this->get('NotifyUser')->notifyResetPassword($user);
                                }

                                //format date for mysql
                                if ($user->usr_dob != "") {
                                    $user->usr_dob = date('Y-m-d', strtotime(str_replace('/', '-', $user->usr_dob)));
                                } else {
                                    $user->usr_dob = NULL;
                                }
                                //check if there is an update in security answer
                                if ($form->get('secretA')->getValue() !== '') {
                                    $user->usr_secretA = Crypt::encrypt($form->get('secretA')->getValue());
                                }
                                $user->exchangeArray($form->getValues());
                                if (!$address) {
                                    $address = new \Users\Model\DbEntity\UserAddress();
                                    $address->exchangeArray($form->getValues());
                                    $address->uAddr_descript = "(settingsPage)";
                                    $address->uAddr_userID = $this->laIdentity()->getId();
                                    $address->uAddr_timeStamp = date('Y-m-d H:i:s');
                                    $this->getServiceLocator()->get('UserAddressTable')->insert($address);
                                } else {
                                    $address->exchangeArray($form->getValues());
                                    $this->getServiceLocator()->get('UserAddressTable')->updateProfile($address);
                                }
                                if (!$phone) {
                                    $phone = new \Users\Model\DbEntity\UserPhone();
                                    $phone->uPhon_type = 0;
                                    $phone->uPhon_isSettingContact = 1;
                                    $phone->uPhon_userid = $this->laIdentity()->getId();
                                    $phone->exchangeArray($form->getValues());
                                    $this->get('UserPhoneTable')->insert($phone);
                                } else {
                                    $phone->exchangeArray($form->getValues());
                                    $this->get('UserPhoneTable')->update($phone);
                                }

                                //update table

                                $userTable->update($user);
                                //update user session data to reflect changes
                                $this->get('AuthService')->getIdentity()->usr_lName = $user->usr_lName;
                                $this->get('AuthService')->getIdentity()->usr_fName = $user->usr_fName;
                                $this->get('AuthService')->getIdentity()->usr_mName = $user->usr_mName;

                                $this->success[] = $this->translate('Change has been succesfully saved.');

                            }

                        }

                    } else {
                        $this->view->errormsg = $this->translate('<li>Current Password is invalid.</li>');
                    }
                }
            }// End Passwords validation  

        }
        //make form data available for getData() function
        $form->isValid();
        $data = $form->getValues();
        $data['account'] = $this->laIdentity()->getUsername();
        $data['usr_lang'] = (int) $data['usr_lang'];
        $data['uAddr_country'] = (int) $data['uAddr_country'];
        //$data['myPassword'] = ($this->request->isPost()) ? $this->request->getPost('myPassword') : '';
        $this->view->data = $data;
        return $this->view;
    }

    /**
     * action to send email change request
     */
    public function requestChangeEmailAction() {
        $this->checkAuthentication();
        $form = new \Users\Form\ChangeMailRequestForm($this->getServiceLocator());
        $this->initView();
        $this->view->form = $form;

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {

                //check if the email already exist;
                $userEmailTable = $this->get('UserEmailTable');
                $userJoin = $this->get('UserjoinTable');
                if ($userEmailTable->getByEmail($form->get('email')->getValue()) || $userJoin->getByEmail($form->get('email')->getValue())) {
                    $this->errors[] = $this->translate('This email is already in use. Please choose another one.');
                    return $this->view;
                }

                $user = $this->getUserTable()->getByEmail($this->laIdentity()->getUsername());
                $userEmail = new UserEmail();
                $userEmail->uEmail_userID = $user->usr_id;
                $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
                $userEmail->uEmail_isVerified = 0;
                $userEmail->uEmail_email = $form->get('email')->getValue();
                $userEmailTable->insert($userEmail);

                //send email to new address for the verification
                $mail_config = array(
                    'to_email' => $userEmail->uEmail_email,
                    'to_name' => $user->usr_fName,
                    'subject' => 'Email Change Request on Linspira'
                );
                $key = \base64_encode($userEmail->uEmail_email . '|' . strtotime($userEmail->uEmail_timeStamp));
                $this->sendMail('users/user/_change-email-mail', array('key' => $key, 'name' => $user->usr_fName), $mail_config);

                $this->view->is_success = true;
                return $this->view;
            }
        }
        return $this->view;
    }

    public function changeEmailAction() {
        $invalid = false;
        $success = false;
        $session = new Container('default');
        $this->initView();
        if (!$this->request->isPost()) {
            if (!$this->params()->fromQuery('token', false)) {
                $this->errors[] = $this->translate('The request is invalid.');
                $invalid = true;
                return $this->view;
            }
            list($email, $time) = explode('|', \base64_decode($this->params()->fromQuery('token')));
            $userEmail = $this->get('UserEmailTable')->getOneByCondition(array('uEmail_email' => $email, 'uEmail_timeStamp' => date('Y-m-d H:i:s', $time), 'uEmail_isVerified' => 0));
            if (!$userEmail) {
                $invalid = true;
                $errors[] = $this->translate('The request is invalid.');
            }
            //save user email in session for post validation
            $session->email = $email;
        }

        if ($this->request->isPost()) {
            $userEmail = $this->get('UserEmailTable')->getOneByCondition(array('uEmail_email' => $session->email));
            $user = $this->get('UserTable')->getById($userEmail->uEmail_userID);
            if ($this->verifyPassword($this->params()->fromPost('password'), $user->usr_password)) {
                //set email as verified 
                $userEmail->uEmail_isVerified = 1;
                $this->get('UserEmailTable')->update($userEmail);
                //save to user table as username and primary email
                $user->usr_username = $session->email;
                $user->usr_email = $session->email;
                $this->get('UserTable')->update($user);
                //login or relogin use for new email
                $this->login($session->email, $this->params()->fromPost('password'));
                $success = true;
            } else {
                $errors[] = $this->translate('The password is incorrect. Please try again.');
            }
        }

        $this->view->invalid = $invalid;
        $this->view->success = $success;
        return $this->view;
    }

    /**
     * Function to send email with html content
     * @param string $view view template for mail
     * @param array $data data passed to the view
     * @param array $mail_config sendmail property
     */
    protected function sendMail($view, $data, $mail_config) {
        $this->get('EmailSender')->sendTemplate($view, $data, $mail_config, array('join@linspira.com'), 'Linspira.com');
    }

    /**
     * Function to send error message to admin through email
     * 
     * @param Exception $e
     */
    protected function emailToAdmin($e) {
        $this->getServiceLocator()->get('ErrorMail')->send($e);
    }

    /**
     * check if a given password is banned
     * @param string $password
     * @return boolean
     */
    protected function isPasswordBanned($password) {
        $sql = new \Zend\Db\Sql\Sql($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $select = $sql->select();
        $select->from('bannedPasswords')->where(array('bpwd_text' => $password));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        if (count($results) > 0) {
            // increment the bad password count
            $update = $sql->update('bannedPasswords');
            $update->set(array('bpwd_count' => new \Zend\Db\Sql\Expression('bpwd_count+1')));
            $update->where(array('bpwd_text' => $password));
            $updatestatement = $sql->prepareStatementForSqlObject($update);
            $updatestatement->execute();

            return true;
        } else {
            return false;
        }
    }

    protected function getGeoCountry() {
        return $this->getServiceLocator()->get('geoCountryTable');
    }

    /**
     * 
     * @return \Common\DbTable\geoLangTable
     */
    protected function getGeoLang() {
        return $this->getServiceLocator()->get('geoLangTable');
    }

    protected function getUserTable() {
        return $this->getServiceLocator()->get('UserTable');
    }

    public function getUserManager() {
        return new UserManager($this->getServiceLocator());
    }

    /**
     * 
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    protected function verifyPassword($password, $hash) {
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        return $bcrypt->verify($password, $hash);
    }

}
