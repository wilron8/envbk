<?php

/**
 *
 * @author kimsreng
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ListSuccess extends AbstractHelper {

    /**
     * 
     * @param array $errors
     * @return string
     */
    public function __invoke($success) {
        if (!is_array($success)) {
            return "";
        }
        $vm = new ViewModel(array(
            'success' => $success
        ));
        $vm->setTemplate("common/helper/list-success.phtml");
        return $this->getView()->render($vm);
    }

}
