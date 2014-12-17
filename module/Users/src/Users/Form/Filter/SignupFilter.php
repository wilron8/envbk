<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SignupFormFilter
 *
 * @author kimsreng
 */

namespace Users\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class SignupFilter extends InputFilter {

    public function __construct($sm) {


        $this->add(array(
            'name' => 'usr_fName',
            // 'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 12,
                        'messages' => array(
                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('First name is too short.'),
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
            'name' => 'usr_mName',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
                array('name' => 'StringTrim'),
            ),
        ));
        $this->add(array(
            'name' => 'usr_lName',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 128,
                        'messages' => array(
                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('Last Name is too short.'),
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
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 60,
                        'messages' => array(
                            Validator\StringLength::TOO_SHORT => $sm->get('translator')->translate('Password is too short.'),
                        ),
                    ),
                ),
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Password cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'confirm_password',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'messages' => array(
                        Validator\Identical::NOT_SAME => $sm->get('translator')->translate('Passwords do not matched.'),
                        ),
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
        $this->add(array(
            'name' => 'usr_lang',
            'required' => false,
            'filters' => array(
                array('name' => 'int'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Please select your primary language.'),
                        ),
                    ),
                ),
            ),
        ));
        //Telephone
         $this->add(array(
            'name' => 'uPhon_type',
            'required' => false,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));
          $this->add(array(
            'name' => 'uPhon_countryCode',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));
           $this->add(array(
            'name' => 'uPhon_areaCode',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));
        $this->add(array(
            'name' => 'uPhon_number',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Phone number is required.'),
                        ),
                    ),
                ),
                array(
                'name' => 'Regex',
                'options' => array(
                    'pattern' => '/^[+]{0,1}[0-9- .]+$/', 
                    'messages' => array(
                        'regexNotMatch'=>$sm->get('translator')->translate('Invalid phone number. Only numbers (0-9), dash (-), dot (.) and plus (+) symbols are allowed')
                        
                    ),
                ),
            ),
            ),
        ));
        // Address
        $this->add(array(
            'name' => 'uAddr_address1',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Address1 cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_address2',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_city',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('City cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_state',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('State cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_ZIP',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
                
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('ZIP cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_country',
            'required' => true,
            'filters' => array(
                array('name' => 'int')
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Please select your home Country.'),
                        ),
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'agreement',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('You have to agree to the Terms of Service.'),
                        ),
                    ),
                ),
            ),
        ));
    }
    
}

?>
