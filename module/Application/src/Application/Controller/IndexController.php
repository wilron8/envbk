<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Common\Mvc\Controller\BaseController;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    public function indexAction()
    {
        if($this->isUserAuthenticated()){
            return $this->redirect()->toRoute('feed');
        }
        $service = $this->getServiceLocator();
        $service->get('translator')->setLocale('en_US'); //TODO: this must be set to user's setting
        return new ViewModel();
    }
    
    public function testEmailAction(){ //TODO: should be updated to reflect the new regional email server logic
        set_time_limit(0);
        $testServer = new \Common\Mail\TestMailServer();
		var_dump($testServer);

        echo "<HR><br />server 10.0 ";
        echo ($testServer->test($this->get('Config')['mailServer']['list'][10][0]) )? "Success" : "Failed";
		var_dump($testServer);

        echo "<HR><br />server 10.1 ";
        echo ($testServer->test($this->get('Config')['mailServer']['list'][10][1]) )? "Success" : "Failed";
		var_dump($testServer);

        echo "<HR><br />server 8.0 ";
        echo ($testServer->test($this->get('Config')['mailServer']['list'][8][0]) )? "Success" : "Failed";
		var_dump($testServer);


        die();
    }
}
