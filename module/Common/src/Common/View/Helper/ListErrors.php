<?php

/**
 *
 * @author kimsreng
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ListErrors extends AbstractHelper {

    /**
     * 
     * @param array $errors
     * @return string
     */
    public function __invoke($errors) {
        if (!is_array($errors)) {
            return "";
        }
        $vm = new ViewModel(array(
            'errors' => $errors
        ));
        $vm->setTemplate("common/helper/list-errors.phtml");
        return $this->getView()->render($vm);
    }

}
