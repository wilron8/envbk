<?php

/**
 * Description of ProjectFilter
 *
 * @author kimsreng
 */

namespace ProjectManagement\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class ProjectFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'proj_title',
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
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Project title cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'proj_srcIdea',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'reference',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'members',
            'required' => false,
        ));
    }

}
