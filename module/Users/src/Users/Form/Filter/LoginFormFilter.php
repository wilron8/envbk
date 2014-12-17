<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoginFormFilter
 *
 * @author kimsreng
 */

namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;

class LoginFormFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'usr_username',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'usr_password',
            'required' => true,
        ));
    }

}
