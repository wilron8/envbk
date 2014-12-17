<?php

/**
 * Description of ExperienceFilter
 *
 * @author kimsreng
 */

namespace People\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class ExperienceFilter extends InputFilter {

    public function __construct($translator) {
        $this->add(array(
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
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Experience Name cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'xp_jobTitle',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name' => 'xp_fromDate',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('From Field cannot be empty.'),
                        ),
                    ),
                ),
//                array(
//                    'name' => 'Date',
//                    'options' => array(
//                        'format' => 'Y-m-d',
//                        'messages' => array(
//                            Validator\Date::INVALID_DATE => $sm->get('translator')->translate("From field is not in the correct format. Please use this format YYYY-MM-DD.")
//                        )
//                    )
//                ),
            )
        ));
        $this->add(array(
            'name' => 'xp_toDate',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
//            'validators' => array(
//                array(
//                    'name' => 'Date',
//                    'options' => array(
//                        'format' => 'Y-m-d',
//                        'messages' => array(
//                            Validator\Date::INVALID_DATE => $sm->get('translator')->translate("To field is not in the correct format. Please use this format YYYY-MM-DD.")
//                        )
//                    )
//                )
//            )
        ));
        $this->add(array(
            'name' => 'xp_descript',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            )
        ));
    }

}
