<?php

/**
 * Description of SkillForm
 *
 * @author kimsreng
 */

namespace People\Form;

use Zend\Form\Form;
use Zend\Validator;

class SkillForm extends Form {

    public function __construct($sm) {
        parent::__construct('signup');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'stag_text',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Skill'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Skill'),
            ),
        ));

        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();

        $filter->add(array(
            'name' => 'stag_text',
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
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Skill cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));

        $this->setInputFilter($filter);
    }

}
