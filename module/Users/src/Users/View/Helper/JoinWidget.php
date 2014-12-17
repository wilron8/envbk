<?php

/**
 * Description of JoinWidget
 *
 * @author kimsreng
 */

namespace Users\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class JoinWidget extends AbstractHelper {

    public function __invoke() {
        $vm = new ViewModel(array(
            'form' => new \Users\Form\PreSignupForm(),
        ));
        $vm->setTemplate("users/helper/join.phtml");
        return $this->getView()->render($vm);
    }

}
