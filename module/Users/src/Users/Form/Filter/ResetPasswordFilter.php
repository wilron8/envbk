<?php

/**
 * Description of ResetPasswordFilter
 *
 * @author kimsreng
 */

namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class ResetPasswordFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 60,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'confirm_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'messages' => array(
                            Validator\Identical::NOT_SAME => $sm->get('translator')->translate("Passwords do not match.")
                        )
                    )
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 60,
                    ),
                ),
            ),
        ));
    }

}
