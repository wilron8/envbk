<?php

/**
 * Description of CreateIdeaFilter
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CreateIdeaFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
            'name' => 'idea_title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array(
                    'name' => 'StripTags',
                    'options' => array(
                        'allowTags' => \Common\Policy\Filter::$allowedTags,
                        'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
                    ),
                ),
                array(
                    'name'=>'Common\Filter\StripString',
                    'options'=>array(
                        'strings'=>['"']
                    )
                )
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Idea title cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'idea_categoryID',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'idea_descript',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array(
                    'name' => 'StripTags',
                    'options' => array(
                        'allowTags' => \Common\Policy\Filter::$allowedTags,
                        'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
                    ),
                )
			)
        ));
        $this->add(array(
            'name' => 'idea_attachment',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name' => 'idea_legalAccept',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('You must agree to Terms of Service to publish your idea.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'reference[]',
            'required' => false,
        ));
    }

}
