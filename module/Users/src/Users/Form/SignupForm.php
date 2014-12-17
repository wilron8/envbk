<?php

/**
 * Description of SignUp
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class SignupForm extends Form {

    protected $captcha;

    public function __construct($sm) {
        parent::__construct('signup');
        $this->setAttribute('method', 'post');

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
                'placeholder' => $sm->get('translator')->translate('Middle Name (optional)'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Middle Name (optional)'),
            ),
        ));
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
                'value_options' => $sm->get('geoLangTable')->getSelectOptions()
            ),
        ));
         // phone
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
        // Address
        $this->add(array(
            'name' => 'uAddr_address1',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Address1'),
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
        //
         $this->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => array(
                'label' => $sm->get('translator')->translate('Please verify you are human.'),
                'captcha' => array(
                    'class' => 'ReCaptcha',
                    'options'=>$sm->get('Config')['recaptcha']
                ),
            ),
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
            'name' => 'usr_secretA',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Answer'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Answer'),
            ),
        ));
        $this->add(array(
            'name' => 'agreement',
            'attributes' => array(
                'type' => 'checkbox',
            )
        ));
       
    }

}

?>
