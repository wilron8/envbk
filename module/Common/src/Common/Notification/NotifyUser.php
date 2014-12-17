<?php

/**
 * Description of NotifyUser
 *
 * @author kimsreng
 */

namespace Common\Notification;

use Common\Notification\NotifyEngine;

class NotifyUser {

    protected $engine = NULL;
    protected $translator = NULL;

    public function __construct(NotifyEngine $engine, $translator) {
        $this->engine = $engine;
        $this->translator = $translator;
    }

    protected function getTemplate($template) {
        return "common" . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . $template;
    }

    public function notifyJoin($name, $email,$key) {
        $config = [
            'view' => $this->getTemplate('_signup-mail'),
            'data' => [
                'name' => $name,
                'email' => $email,
                'key'=>$key,
            ],
            'config' => [
                'to_email' => $email,
                'to_name' => $name,
                'subject' => $this->translator->translate('Complete Registration on Linspira')
            ]
        ];
        return $this->engine->notify($config);
    }
    
    /**
     * 
     * @param \Users\Model\DbEntity\User $user
     */
    public function notifyWelcome($user){
        $config = [
            'view' => $this->getTemplate('_welcome-email'),
            'data' => [
                'user' => $user,
            ],
            'config' => [
                'to_email' => $user->usr_email,
                'to_name' => $user->getFullName(),
                'subject' => $this->translator->translate('Successful Registration on Linspira')
            ]
        ];
        return $this->engine->notify($config);
    }

     /**
     * 
     * @param \Users\Model\DbEntity\User $user
     * @param string $token
     * @return boolean
     */
    public function notifyForgot($user,$token){
        $config = [
            'view' => $this->getTemplate('_forgot-mail'),
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'config' => [
                'to_email' => $user->usr_email,
                'to_name' => $user->getFullName(),
                'subject' => $this->translator->translate('Forgot Password on Linspira')
            ]
        ];
        return $this->engine->notify($config);
    }

    public function notifyResetPassword($user) {
        $config = [
            'view' => $this->getTemplate('_password-change-confirmation'),
            'data' => [
                'user' => $user,
            ],
            'config' => [
                'to_email' => $user->usr_email,
                'to_name' => $user->getFullName(),
                'subject' => $this->translator->translate('Reset Password on Linspira')
            ]
        ];
        return $this->engine->notify($config);
    }


    /**
     * Notify a project manager about joining request
     * 
     * @param \Users\Model\DbEntity\User $joiningUser
     * @param \Users\Model\DbEntity\User $ProjManager
     * @param \ProjectManagement\Model\DbEntity\Project $project
     * @param integer $pmemId
     */
    public function notifyPrjMemJoin($joiningUser, $ProjManager, $project, $pmemId) {
        $config = [
            'view' => $this->getTemplate('_membership-email'),
            'data' => [
                'owner' => $ProjManager,
                'requester' => $joiningUser,
                'project' => $project,
                'pmemId' => $pmemId,
            ],
            'config' => [
                'to_email' => $ProjManager->usr_email,
                'to_name' => $ProjManager->usr_fName,
                'subject' => $this->translator->translate('Membership Request')
            ]
        ];
      return  $this->engine->notify($config);
    }

    /**
     * 
     * @param \Users\Model\DbEntity\User $joiningUser
     * @param \ProjectManagement\Model\DbEntity\Project $project
     * @param string $reason reason why the request was rejected
     */
    public function notifyPrjMemReject($joiningUser,$project,$reason) {
        $config = [
            'view' => $this->getTemplate('_membership-rejected'),
            'data' => [
                'user' => $joiningUser,
                'project' => $project,
                'reason' => $reason,
            ],
            'config' => [
                'to_email' => $joiningUser->usr_email,
                'to_name' => $joiningUser->usr_fName,
                'subject' => $this->translator->translate('Membership Request')
            ]
        ];
      return  $this->engine->notify($config);
    }

    /**
     * 
     * @param  \Users\Model\DbEntity\User $joiningUser
     * @param \ProjectManagement\Model\DbEntity\Project $project
     */
    public function notifyPrjMemApprove($joiningUser, $project){
        $config = [
            'view' => $this->getTemplate('_membership-approved'),
            'data' => [
                'user' => $joiningUser,
                'project' => $project,
            ],
            'config' => [
                'to_email' => $joiningUser->usr_email,
                'to_name' => $joiningUser->usr_fName,
                'subject' => $this->translator->translate('Membership Request')
            ]
        ];
      return  $this->engine->notify($config);
    }
    
    /**
     * Notify users as a new email is sent to them
     * 
     * @param string $msg
     * @param string $from
     * @param string $name
     * @param string $email
     * @param array $receipients
     */
    public function notifyNewMessage($msg,$from,$name,$email,$receipients){
        $config = [
            'view' => $this->getTemplate('_message-alert'),
            'data' => [
                'msg' => $msg,
                'from' => $from,
                'name' => $name,
                'email' => $email,
                'recepients' => $receipients,
            ],
            'config' => [
                'to_email' => $email,
                'to_name' => $name,
                'subject' => $this->translator->translate('New Message on Linspira.com')
            ]
        ];
      return  $this->engine->notify($config);
    }

}
