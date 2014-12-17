<?php

/**
 * Description of ForgotQuestionFilter
 *
 * @author kimsreng
 */
namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;

class ForgotQuestionFilter extends InputFilter{
    public function __construct() {
         $this->add(array(
            'name' => 'answer',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
         $this->add(array(
            'name' => 'user_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
    }
}
