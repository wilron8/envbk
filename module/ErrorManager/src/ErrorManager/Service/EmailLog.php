<?php

/**
 * Description of EmailLog
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class EmailLog extends AbstractLog {

    protected $mailService;
    protected $adminEmails;
    protected $viewRenderer;

    public function __construct(\Zend\ServiceManager\ServiceLocatorInterface $service, array $config) {
        parent::__construct($service, $config);
        $this->mailService = $service->get('Mail');
        $this->viewRenderer = $service->get('ViewRenderer');
        $this->adminEmails = $service->get('Config')['sysConfig']['adminEmail'];
    }

    public function logException($e) {
        if ($this->mailService !== \Common\Mail\MailServer::DOWN_MESSAGE) {
            $mail = $this->getMailMessage($e);
            $this->mailService->send($mail);
        }
    }

    /**
     * Prepare email message template for sending
     * 
     * @param \Exception $e
     * @return \Zend\Mail\Message
     */
    public function getMailMessage(\Exception $e) {
        $emails = $this->adminEmails;

        $content = $this->viewRenderer->render('error-manager/error/index', array('exception' => $e, 'display_exceptions' => true));
        
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html));
        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom($emails['fromEmail'], 'Linspira.com');
        $mail->addTo($emails['toEmail']);
        $mail->setSubject("Exception Alert");
        return $mail;
    }

}
