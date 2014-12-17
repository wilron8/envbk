<?php

/**
 * Description of NotifyAdmin
 *
 * @author kimsreng
 */

namespace Common\Notification;

use Common\Notification\NotifyEngine;

class NotifyAdmin {

    protected $user = NULL;
    protected $engine = NULL;
    protected $emailPath;
    protected $translator = NULL;
    protected $config;

    public function __construct(NotifyEngine $engine, $translator, $adminConfig) {
        $this->engine = $engine;
        $this->translator = $translator;
        $this->config = $adminConfig;
    }

    protected function getTemplate($template) {
        return "common" . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $template;
    }

    /**
     * Send an alert to admin about a violation report of a project
     * 
     * @param object $project
     * @param object $owner
     * @param object $reportingUser
     * @param object $violation
     * @return boolean
     */
    public function reportProject($project, $owner,$reportingUser, $violation) {
        
        $config = [
            'view' => $this->getTemplate('_report-project-email'),
            'data' => [
                'owner'=>$owner,
                'project' => $project,
                'report' => $violation,
                'reporter' => $reportingUser,
            ],
            'config' => [
                'to_email' => $this->config['adminEmail']['toEmail'],
                'to_name' => "Admin Team",
                'subject' => $this->translator->translate('Project Violoation Report')
            ]
        ];
        return $this->engine->notify($config);
    }
    
    /**
     * Alert admin user when a user inputs a new country
     * 
     * @param type $user
     * @param type $country
     * @return boolean
     */
    public function notifyNewCountry($user,$country){
        $config = [
            'view' => $this->getTemplate('_new_country'),
            'data' => [
                'user'=>$user,
                'country' => $country
            ],
            'config' => [
                'to_email' => $this->config['adminEmail']['toEmail'],
                'to_name' => "Admin Team",
                'subject' => $this->translator->translate('New Country')
            ]
        ];
        return $this->engine->notify($config);
    }
    
    

}
