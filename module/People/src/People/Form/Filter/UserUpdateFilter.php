<?php

/**
 * Description of UserUpdateFilter
 *
 * @author kimsreng
 */

namespace People\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class UserUpdateFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'usr_lName',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 12,
                        'messages' => array(
                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('Last name field is too short.'),
                        ),
                    ),
                ),
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Last Name cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'usr_fName',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 12,
                        'messages' => array(
                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('First name field is too short.'),
                        ),
                    ),
                ),
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('First Name cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'usr_gender',
            'required' => true,
            'filters' => array(
                array('name' => 'Int')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Gender cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'usr_mName',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name' => 'usr_dob',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
        $this->add(array(
            'name' => 'usr_about',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
        $this->add(array(
            'name' => 'usr_displayName',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
    }

}
