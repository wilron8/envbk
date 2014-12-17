<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HelpController
 *
 * @author kimsreng
 */

namespace LAhelp\Controller;

use Common\Mvc\Controller\BaseController;
use Zend\View\Model\ViewModel;

class HelpController extends BaseController {

    public function helpAction() {
        return new ViewModel();
    }

}

?>
