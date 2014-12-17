<?php

/**
 * Description of DisplayFilterErrors
 *
 * @author kimsreng
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class DisplayFilterErrors extends AbstractHelper {

    /**
     * 
     * @param array $errors
     * @return string
     */
    public function __invoke($filter) {

        if (!$filter instanceof \Zend\InputFilter\InputFilter) {
            throw new Exception(get_class($filter) . ' is not InputFilter instance');
        }
        if (!count($filter->getMessages())) {
            return "";
        }
        $vm = new ViewModel(array(
            'errors' => $filter->getMessages()
        ));
        $vm->setTemplate("common/helper/filter-errors.phtml");
        return $this->getView()->render($vm);
    }

}
