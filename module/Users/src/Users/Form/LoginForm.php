<?php

// filename : module/Users/src/Users/Form/RegisterForm.php

namespace Users\Form;

use Zend\Form\Form;

class LoginForm extends Form {

    public function __construct($name = NULL) {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');


        $this->add(array(
            'name' => 'usr_username',
            'attributes' => array(
                'type' => 'email',
                'required' => 'required',
                'placeholder' => 'User Name'
            ),
            'options' => array(
                'label' => 'User Name',
            ),
        ));

        $this->add(array(
            'name' => 'usr_password',
            'attributes' => array(
                'type' => 'password',
                'required' => 'required',
                'placeholder' => 'Password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));

        $this->add(array(
            'name' => 'rememberme',
            'attributes' => array(
                'type' => 'Checkbox',
                'id' => 'rememberme',
                'value' => 1
            ),
            'options' => array(
                'label' => 'Remember Login',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login'
            ),
        ));
        //validator
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
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
        $filter->add(array(
            'name' => 'usr_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->setInputFilter($filter);
    }

}
