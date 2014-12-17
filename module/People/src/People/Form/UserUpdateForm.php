<?php

/**
 * Description of UserUpdateForm
 *
 * @author kimsreng
 */

namespace People\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class UserUpdateForm extends Form {

    public function __construct($sm) {
        parent::__construct('signup');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

//        $this->add(array(
//            'name' => 'usr_fName',
//            'attributes' => array(
//                'type' => 'text',
//                'required' => 'required',
//                'placeholder' => $sm->get('translator')->translate('First Name'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('First Name'),
//            ),
//        ));
//        $this->add(array(
//            'name' => 'usr_lName',
//            'attributes' => array(
//                'type' => 'text',
//                'required' => 'required',
//                'placeholder' => $sm->get('translator')->translate('Last Name'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('Last Name'),
//            ),
//        ));
//        $this->add(array(
//            'name' => 'usr_mName',
//            'attributes' => array(
//                'type' => 'text',
//                'placeholder' => $sm->get('translator')->translate('Middle Name (optional)'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('Middle Name (optional)'),
//            ),
//        ));
        $this->add(array(
            'name' => 'usr_icon',
            'attributes' => array(
                'type' => 'file',
                'placeholder' => $sm->get('translator')->translate('Icon'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Icon'),
            ),
        ));
        $this->add(array(
            'name' => 'usr_about',
            'attributes' => array(
                'type' => 'textarea',
                'placeholder' => $sm->get('translator')->translate('About'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('About'),
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'usr_gender',
            'attributes' => array(
                'type' => 'select',
                //'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Gender'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Gender'),
                'value_options' => [0 => $sm->get('translator')->translate('Female'),
                    1 => $sm->get('translator')->translate('Male'),
                    2 => $sm->get('translator')->translate('Not specified')]
            ),
        ));
        $this->add(array(
            'name' => 'usr_displayName',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Displayed Name'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Displayed Name'),
            ),
        ));
        $this->add(array(
            'name' => 'dob_year',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Date of Birth(year)'),
            ),
        ));
        $this->add(array(
            'name' => 'dob_month',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Date of Birth(month)'),
            ),
        ));
        $this->add(array(
            'name' => 'dob_day',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Date of Birth(day)'),
            ),
        ));

//        $this->add(array(
//            'name' => 'usr_dob',
//            'attributes' => array(
//                'type' => 'text',
//                'placeholder' => $sm->get('translator')->translate('Date of Birth'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('Date of Birth'),
//            ),
//        ));


        // Add validators 
        $filter = new InputFilter();
//        $filter->add(array(
//            'name' => 'usr_lName',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim')
//            ),
//            'validators' => array(
//                array(
//                    'name' => 'StringLength',
//                    'options' => array(
//                        'encoding' => 'UTF-8',
//                        'min' => 2,
//                        'max' => 12,
//                        'messages' => array(
//                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('Last name field is too short.'),
//                        ),
//                    ),
//                ),
//                array(
//                    'name' => 'NotEmpty',
//                    'options' => array(
//                        'messages' => array(
//                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Last Name cannot be empty.'),
//                        ),
//                    ),
//                ),
//            ),
//        ));
//        $filter->add(array(
//            'name' => 'usr_fName',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim')
//            ),
//            'validators' => array(
//                array(
//                    'name' => 'StringLength',
//                    'options' => array(
//                        'encoding' => 'UTF-8',
//                        'min' => 2,
//                        'max' => 12,
//                        'messages' => array(
//                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('First name field is too short.'),
//                        ),
//                    ),
//                ),
//                array(
//                    'name' => 'NotEmpty',
//                    'options' => array(
//                        'messages' => array(
//                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('First Name cannot be empty.'),
//                        ),
//                    ),
//                ),
//            ),
//        ));
//        $filter->add(array(
//            'name' => 'usr_mName',
//            'required' => false,
//            'filters' => array(
//                array('name' => 'StringTrim')
//            )
//        ));
        $filter->add(array(
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

        $filter->add(array(
            'name' => 'dob_year',
            'required' => false,
            'filters' => array(
                array('name' => 'int')
            )
        ));
        $filter->add(array(
            'name' => 'dob_month',
            'required' => false,
            'filters' => array(
                array('name' => 'int')
            )
        ));
        $filter->add(array(
            'name' => 'dob_day',
            'required' => false,
            'filters' => array(
                array('name' => 'int')
            )
        ));
        $filter->add(array(
            'name' => 'usr_about',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
        $filter->add(array(
            'name' => 'usr_displayName',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->setInputFilter($filter);
    }

}
