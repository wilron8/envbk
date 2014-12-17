<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChangeMailRequestForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class ChangeMailRequestForm extends Form {

    public function __construct($sm) {
        parent::__construct('account-setting');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
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
                'value' => $sm->get('translator')->translate('Submit'),
            ),
        )); 
        //Add validation
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
