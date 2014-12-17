<?php

/**
 *
 * @author kimsreng
 */
namespace SearchEngine\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class AdvancedSearch extends InputFilter {

    public function __construct($translator) {

        $this->add(array(
            'name' => 'keyword',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));

        $this->add(array(
            'name' => 'older',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));

        $this->add(array(
            'name' => 'newer',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'required' => true,
//            'filters' => array(
//                array('name' => 'StringTrim'),
//                array('name' => 'StripTags')
//            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Type cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
    }

}
