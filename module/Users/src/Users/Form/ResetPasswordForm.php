<?php

/**
 * Description of ResetPasswordForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class ResetPasswordForm extends Form{

    public function __construct($sm) {
        parent::__construct('reset-password');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Password'),
            ),
            'options' => array(
                'label' =>$sm->get('translator')->translate('Password'),
            ),
        ));
        $this->add(array(
            'name' => 'confirm_password',
            'attributes' => array(
                'type' => 'password',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Confirm Password'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Confirm Password'),
            ),
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
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
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
        $filter->add(array(
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
        $this->setInputFilter($filter);
    }

}

?>
