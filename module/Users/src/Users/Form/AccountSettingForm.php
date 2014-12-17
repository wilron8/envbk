<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccountSetting
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;
use Zend\Validator;

class AccountSettingForm extends Form {

    public function __construct($sm) {
        parent::__construct('account-setting');
        $this->setAttribute('method', 'post');
        $this->setAttribute('autocomplete', 'off');
		
		// removed redundant fields from password change form 
        $this->add(array(
            'name' => 'usr_fName',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('First Name'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('First Name'),
            ),
        ));
        $this->add(array(
            'name' => 'usr_lName',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Last Name'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Last Name'),
            ),
        ));
        $this->add(array(
            'name' => 'usr_mName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Middle Name'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Middle Name'),
            ),
        ));


//        $this->add(array(
//            'name' => 'usr_dob',
//            'attributes' => array(
//                'type' => 'text',
//                'required' => 'required',
//                'placeholder' => $sm->get('translator')->translate('Date of birth'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('Date of birth'),
//            ),
//        ));

//        $this->add(array(
//            'type' => 'Zend\Form\Element\Select',
//            'name' => 'usr_gender',
//            'attributes' => array(
//                'type' => 'select',
//                'required' => 'required',
//                'placeholder' => $sm->get('translator')->translate('Gender'),
//            ),
//            'options' => array(
//                'label' => $sm->get('translator')->translate('Gender'),
//                'empty_option' => $sm->get('translator')->translate('Select gender'),
//                'value_options' => array('0' => $sm->get('translator')->translate('Female'), '1' => $sm->get('translator')->translate('Male'), '2' => $sm->get('translator')->translate('Not Specified'))
//            ),
//        ));
         $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'usr_lang',
            'attributes' => array(
                'type' => 'select',
                //'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Language'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Language'),
                'empty_option' => $sm->get('translator')->translate('Select Language'),
                'value_options' => $sm->get('geoLangTable')->getSelectLangSupport()
            ),
        ));
		///
		
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => $sm->get('translator')->translate('Password'),
                'autocomplete'=>'off',
				'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Password'),
            )
        ));
        $this->add(array(
            'name' => 'confirm_password',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => $sm->get('translator')->translate('Confirm Password'),
                'autocomplete'=>'off',
				'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Confirm Password')
            )
        ));
        $this->add(array(
            'name' => 'usr_secretQ',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Your secret question'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Question'),
            ),
        ));
        $this->add(array(
            'name' => 'secretA',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Answer'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Answer'),
            ),
        ));
        // Address
        $this->add(array(
            'name' => 'uAddr_address1',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Address1'),
                'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Address1'),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_address2',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Address2 (optional)'),
                'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Address2 (optional)'),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_city',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('City'),
                'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('City'),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_state',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('State'),
                'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('State'),
            ),
        ));
        $this->add(array(
            'name' => 'uAddr_ZIP',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('ZIP code'),
                'class' => 'input01'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('ZIP code'),
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'uAddr_country',
            'attributes' => array(
                'type' => 'select',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Country'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Country'),
                'empty_option' => 'Select Country',
                'value_options' => $sm->get('geoCountryTable')->getSelectOptions()
            ),
        ));
        //phone
        $this->add(array(
            'name' => 'uPhon_type',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Phone Type'),
            )
        ));
         $this->add(array(
            'name' => 'uPhon_countryCode',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Country Code'),
            )
        ));
         $this->add(array(
            'name' => 'uPhon_areaCode',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Area Code'),
            )
        ));
        $this->add(array(
            'name' => 'uPhon_number',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Phone'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Phone'),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Apply')
            ),
        ));
        
         

        // Add validators 
        $filter = new \Zend\InputFilter\InputFilter();
		
        $filter->add(array(
            'name' => 'usr_lName',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Last Name cannot be empty.'),
                        ),
                    ),
                ),
            )
        ));
		
        $filter->add(array(
            'name' => 'usr_fName',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('First Name cannot be empty.'),
                        ),
                    ),
                ),
            )
        ));
        $filter->add(array(
            'name' => 'usr_mName',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            )
        ));
        $filter->add(array(
            'name' => 'usr_dob',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'Date',
                    'options' => array(
                        'format' => 'd/m/Y',
                        'messages' => array(
                            Validator\Date::INVALID_DATE => $sm->get('translator')->translate("Date of birth is not in the correct format. Please use this format DD/MM/YYYY.")
                        )
                    )
                )
            )
        ));
		
        $filter->add(array(
            'name' => 'confirm_password',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'messages' => array(
                        Validator\Identical::NOT_SAME => $sm->get('translator')->translate("Passwords do not match.")
                        )
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
        // Address
        $filter->add(array(
            'name' => 'uAddr_address1',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
        $filter->add(array(
            'name' => 'uAddr_address2',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $filter->add(array(
            'name' => 'uAddr_city',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
        $filter->add(array(
            'name' => 'uAddr_state',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
        $filter->add(array(
            'name' => 'uAddr_ZIP',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
        $filter->add(array(
            'name' => 'uAddr_country',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
        
        $this->setInputFilter($filter);
    }

}

?>
