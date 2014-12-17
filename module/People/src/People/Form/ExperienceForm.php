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

class ExperienceForm extends Form {

    public function __construct($sm) {
        parent::__construct('signup');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'xp_id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'xp_name',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Name'),
            ),
        ));
        $this->add(array(
            'name' => 'xp_fromDate',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('From'),
            ),
        ));
        $this->add(array(
            'name' => 'xp_toDate',
            'attributes' => array(
                'type' => 'text',
                'id'=>'toDate',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('To'),
            ),
        ));
        $this->add(array(
            'name' => 'xp_descript',
            'attributes' => array(
                'type' => 'textarea',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('About'),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Save')
            ),
        ));

        // Add validators 
        $filter = new InputFilter();
        $filter->add(array(
            'name' => 'xp_name',
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
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Experience Name cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'xp_fromDate',
            'required' => true,
           'filters' => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('From Field cannot be empty.'),
                        ),
                    ),
                ),
                array(
                    'name' => 'Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                        'messages' => array(
                            Validator\Date::INVALID_DATE => $sm->get('translator')->translate("From field is not in the correct format. Please use this format YYYY-MM-DD.")
                        )
                    )
                ),
            )
        ));
        $filter->add(array(
            'name' => 'xp_toDate',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                        'messages' => array(
                            Validator\Date::INVALID_DATE => $sm->get('translator')->translate("To field is not in the correct format. Please use this format YYYY-MM-DD.")
                        )
                    )
                )
            )
        ));
        $filter->add(array(
            'name' => 'xp_descript',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim')
            )
        ));
        $this->setInputFilter($filter);
    }

}
