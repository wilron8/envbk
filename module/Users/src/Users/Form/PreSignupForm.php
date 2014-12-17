<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PreSignupForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class PreSignupForm extends Form {

    //put your code here
    public function __construct($name = NULL, $options = array()) {
        parent::__construct('pre-signup');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'join_email',
            'attributes' => array(
                'type' => 'email',
                'required' => 'required',
                'placeholder'=>'Email',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'join_fName',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder'=>'Name',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Join'
            ),
        ));
         //validator
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'join_email',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
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
            'name' => 'join_fName',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags'
                )
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 140
                    ),
                ),
            ),
        ));
        $this->setInputFilter($filter);
    }

}

?>
