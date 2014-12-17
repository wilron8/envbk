<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ForgotForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class ForgotForm extends Form {

    public function __construct($sm) {
        parent::__construct('forgot');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Email'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Email'),
            ),
        ));
         $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => $sm->get('translator')->translate('Next'),
            ),
        )); 
         
         //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'email',
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
        $this->setInputFilter($filter);
    }

}

?>
