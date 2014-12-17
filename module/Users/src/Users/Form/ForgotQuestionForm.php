<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ForgotQuestionForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class ForgotQuestionForm extends Form {

    public function __construct($sm) {
        parent::__construct('forgot-answer');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'answer',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Answer',
                'placeholder'=>$sm->get('translator')->translate('Your answer here'),
            ),
        ));
        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'hidden',
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Next'),
            ),
        ));
        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'answer',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
         $filter->add(array(
            'name' => 'user_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
        $this->setInputFilter($filter);
    }

}

?>
