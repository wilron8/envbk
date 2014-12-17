<?php

/**
 * Description of UserEmail
 *
 * @author kimsreng
 */

namespace People\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class UserEmail extends Form {

    public function __construct($translator) {
        parent::__construct('signup');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'uEmail_email',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => $translator->translate('Email'),
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'uEmail_emailType',
            'attributes' => array(
                'type' => 'select',
            ),
            'options' => array(
                'label' => $translator->translate('Type'),
                'value_options' => array('0' => $translator->translate('Home'), '1' => $translator->translate('Work'), '2' => $translator->translate('Mobile'), '3' => $translator->translate('Others'))
            ),
        ));

        // Add validators 
        $filter = new InputFilter();
        $filter->add(array(
            'name' => 'uEmail_email',
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
