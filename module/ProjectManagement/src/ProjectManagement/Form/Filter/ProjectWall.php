<?php

/**
 *
 * @author kimsreng
 */

namespace ProjectManagement\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class ProjectWall extends InputFilter {

    public function __construct($translator) {
        $this->add(array(
            'name' => 'prjW_comment',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
                array(
                    'name' => 'StripTags',
                    'options' => array(
                        'allowTags' => \Common\Policy\Filter::$allowedTags,
                        'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
                    )
                ),
                array(
                    'name' => 'Common\Filter\SingleTag',
                    'options' => array(
                        'tags' => array('<br>', '\r', '\n')
                    )
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Comment cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'prjW_projID',
            'required' => true,
//            'validators' => array(
//                array(
//                    'name' => 'NotEmpty',
//                    'options' => array(
//                        'messages' => array(
//                            Validator\Digits::NOT_DIGITS => $translator->translate('Project id must be a number.'),
//                        ),
//                    ),
//                ),
//            ),
        ));
    }

}
