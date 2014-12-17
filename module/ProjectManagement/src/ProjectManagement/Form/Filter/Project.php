<?php

/**
 * Description of Project
 *
 * @author kimsreng
 */

namespace ProjectManagement\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Project extends InputFilter {

    public function __construct($translator) {
        $this->add(array(
            'name' => 'proj_title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array(
                    'name' => 'StripTags'
                ),
                array(
                    'name' => 'Common\Filter\StripString',
                    'options' => array(
                        'strings' => ['"']
                    )
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Project title cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'proj_srcIdea',
            'required' => false
        ));
        $this->add(array(
            'name' => 'proj_descript',
            'required' => false
        ));
        $this->add(array(
            'name' => 'proj_img',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            )
        ));
        $this->add(array(
            'name' => 'proj_progress',
            'required' => true,
            'filters' => array(
                array('name' => 'Int')
            )
        ));
        $this->add(array(
            'name' => 'proj_isWallPublic',
            'required' => false,
            'filters' => array(
                array('name' => 'Int')
            )
        ));
        $this->add(array(
            'name' => 'proj_isWallMemWritable',
            'required' => false,
            'filters' => array(
                array('name' => 'Int')
            )
        ));

        $this->add(array(
            'name' => 'proj_isMemberShipOpen',
            'required' => false,
            'filters' => array(
                array('name' => 'Int')
            )
        ));
    }

}
