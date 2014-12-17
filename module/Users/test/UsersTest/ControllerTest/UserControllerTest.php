<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserControllerTest
 *
 * @author kimsreng
 */
namespace UsersTest\ControllerTest;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Session\Container;
class UserControllerTest extends AbstractHttpControllerTestCase{
    //put your code here
    public function setUp() {
        $this->setApplicationConfig(
        include __DIR__.'/../../../../../config/application.config.php'
                );
        parent::setUp();
    }
    public function testJoinActionCanBeAccessed(){
       $this->dispatch('/join','POST',array('join_email'=>'kimsreng1@gmail.com','join_fName'=>'Kimsreng'));
        //$this->assert
        //$this->di
    }
    public function testConfirmAction(){
        $container = new Container('default');
        $container->signup=array();
       // $this->dispatch('/confirm','POST',array('agree'=>1));
       // $this->assertModuleName('Users');
    }
}

?>
