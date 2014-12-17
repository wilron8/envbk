<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexControllerTest
 *
 * @author kimsreng
 */

namespace ApplicationTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase {

    //put your code here
    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include 'module.config.php'
        );
        parent::setUp();
    }

    public function testAccess() {
      //  ini_set('memory_limit', '512M');
       $this->dispatch('/');
    }

//    public function testIndexAccess() {
//        $this->dispatch('/');
//        $this->assertModuleName('Application');
//    }
}

?>
