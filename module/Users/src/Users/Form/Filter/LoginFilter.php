<?php
namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;

class LoginFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'usr_username',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'EmailAddress',
                    'options' => array(
                        'domain' => true,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name'       => 'usr_password',
            'required'   => true,
        ));
    }
}
