<?php

/**
 * Description of MailSender
 *
 * @author kimsreng
 */

namespace Common\Mail;

use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail;

class EmailSender {

    protected $mailService = NULL;
    protected $fromEmail = array('info@linspira.com');
    protected $fromName = "Linspira.com";
    protected $viewRenderer;
    protected $secondMail;
    protected $errorEmail;

    public function __construct($mailService, $viewRenderer) {
        if($mailService=== \Common\Mail\MailServer::DOWN_MESSAGE){
            throw new \Exception('Sorry, mail server is temporarily down. Please try again later.');
        }
        $this->mailService = $mailService;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Send email with raw content
     * 
     * @param string $body
     * @param string $subject
     * @param string|array $toEmail
     * @param string $toName
     * @param string|array|null $fromEmail
     * @param string|null $fromName
     * @return boolean
     */
    public function sendPlainText($body, $subject, $toEmail, $toName, $fromEmail = NULL, $fromName = NULL) {
        if ($fromEmail !== NULL) {
            $this->fromEmail = $fromEmail;
        }
        if ($fromName !== NULL) {
            $this->fromName = $fromName;
        }
        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addTo($toEmail, $toName);
        $mail->setSubject($subject);

        $this->send($mail);
    }

    /**
     * Send email with view template
     * 
     * @param file $viewTemplate phtml zend template
     * @param array $viewData data to pass to view
     * @param array $mailConfig i.e array('to_email'=>array(),'to_name'=>'','subject'=>'');
     * @param string|array|null $fromEmail
     * @param string|null $fromName
     * @return boolean
     */
    public function sendTemplate($viewTemplate, $viewData, $mailConfig, $fromEmail = NULL, $fromName = NULL) {

        if ($fromEmail !== NULL) {
            $this->fromEmail = $fromEmail;
        }
        if ($fromName !== NULL) {
            $this->fromName = $fromName;
        }
        // $phpRenderer = new PhpRenderer();
        $content = $this->viewRenderer->render($viewTemplate, $viewData);

        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html));
        $mail = new Mail\Message();

        $mail->setBody($body);
        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addTo($mailConfig['to_email'], $mailConfig['to_name']);
        $mail->setSubject($mailConfig['subject']);
        $this->send($mail);
    }

    public function send($mail) {
//        try {
            // $this->secondMail->send($mail);
            $this->mailService->send($mail);
//        } catch (\Exception $exc) {
//            try {
//                $this->secondMail->send($mail);
//                $this->errorEmail->send($exc);
//            } catch (\Exception $ex) {
//                // echo $ex->getMessage();
//                return false;
//            }
//        }
        return true;
    }

}
