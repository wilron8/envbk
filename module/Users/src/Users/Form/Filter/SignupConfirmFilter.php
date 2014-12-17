<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SignupConfirmFilter
 *
 * @author kimsreng
 */

namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class SignupConfirmFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'agree',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Please confirm your agreement to Term of Service.'),
                        ),
                    ),
                ),
            )
        ));
    }

}

?>
