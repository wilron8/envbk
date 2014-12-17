<?php

/**
 * Description of AddressFilter
 *
 * @author kimsreng
 */

namespace People\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class AddressFilter extends InputFilter {

    public function __construct($translator) {
        $this->add(array(
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
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('City cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'uAddr_country',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Country cannot be empty.'),
                        ),
                    ),
                )
            ),
        ));
    }

}
