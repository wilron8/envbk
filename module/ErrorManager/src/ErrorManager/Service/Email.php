<?php

/**
 * This class is used to report error message through email
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
//use Common\Util\Crypt;

class Email {

    protected $mailService;
    protected $adminEmails;
    protected $viewRenderer;

    public function __construct($mailService, $viewRenderer, $adminEmail) {
        $this->mailService = $mailService;
        $this->adminEmails = $adminEmail;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Send error message to admin through email
     * //TODO: check for email server connectivity and status before submitting!
     * 
     * @param \Exception $e
     */
    public function send(\Exception $e) {
        if ($this->mailService === \Common\Mail\MailServer::DOWN_MESSAGE) {
            $this->logError($e);
        } else {
            $mail = $this->getMailMessage($e);
            $this->mailService->send($mail);
        }

        //echo $this->viewRenderer->render('error-manager/error/index', array('exception' => $e, 'display_exceptions' => true));
        //var_dump($mail);
        //$this->mailService->send($mail);
        /*
          try{
          } catch (\Exception $err){
          echo $this->viewRenderer->render('error-manager/error/index',array('exception'=>$e,'display_exceptions'=>true));
          }
         */
    }

    /**
     * Prepare email message template for sending
     * 
     * @param \Exception $e
     * @return \Zend\Mail\Message
     */
    public function getMailMessage(\Exception $e) {
        $emails = $this->adminEmails;        
        //$bcrypt = new \Common\Util\Crypt;
        
        $content = $this->viewRenderer->render('error-manager/error/index', array('exception' => $e, 'display_exceptions' => true));
        //echo $content;
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html));

        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom($emails['fromEmail'], 'Linspira.com');
        $mail->addTo($emails['toEmail']);
        $mail->setSubject("Message From Admin");
        return $mail;
    }

    protected function logError($e) {
        if (!is_dir(ROOT_PATH . '/data/log')) {
            mkdir(ROOT_PATH . '/data/log',0744);
        }
        $handle = fopen(ROOT_PATH . '/data/log/application.log', 'a');
        fwrite($handle, '--------------------------**************--------------------------------'.PHP_EOL);
        fwrite($handle, 'URI: '.$_SERVER['REQUEST_URI'].PHP_EOL);
        fwrite($handle, 'Date: '.date('Y-m-d H:i:s').PHP_EOL);
        fwrite($handle, 'Message: '.$e->getMessage().PHP_EOL);
        fwrite($handle, $e->getTraceAsString().PHP_EOL);
        fclose($handle);
    }

}
