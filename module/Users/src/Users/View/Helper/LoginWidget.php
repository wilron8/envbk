<?php

/**
 * Description of laLoginWidget
 *
 * @author kimsreng
 */

namespace Users\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class LoginWidget extends AbstractHelper {

    protected $routeMatched;

    public function __invoke() {
        //return empty string in signin page
        if ($this->routeMatched != NULL) {
            if ($this->routeMatched->getParam('controller') == 'Users\Controller\User' && $this->routeMatched->getParam('action') == 'signin') {
                return "";
            }
        }

        $vm = new ViewModel(array(
            'form' => new \Users\Form\LoginForm()
        ));
        $vm->setTemplate("users/helper/login.phtml");
        return $this->getView()->render($vm);
    }

    public function setRouteMatched($routeMatched) {
        $this->routeMatched = $routeMatched;
    }

}
