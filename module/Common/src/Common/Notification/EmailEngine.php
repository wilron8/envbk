<?php

/**
 * Description of Email
 *
 * @author kimsreng
 */

namespace Common\Notification;

use Common\Mail\EmailSender;

class EmailEngine implements \Common\Notification\NotifyEngine {

    /**
     *
     * @var EmailSender 
     */
    protected $mailService;
    public $config = [];
    public $fromEmail = NULL;
    public $fromName = NULL;
    public $toEmail;
    public $toName;
    public $subject;
    public $view;

    public function __construct($mailService) {
        $this->mailService = $mailService;
    }

    /**
     * 
     * @param array $config 
     */
    public function notify($config) {
        $view = $config['view'];
        $data = $config['data'];
        $cf = $config['config'];
        if (isset($config['from_email'])) {
            $this->fromEmail = $config['from_email'];
        }
        if (isset($config['from_name'])) {
            $this->fromName = $config['from_name'];
        }
        $this->mailService->sendTemplate($view, $data, $cf, $this->fromEmail, $this->fromName);
    }

}
