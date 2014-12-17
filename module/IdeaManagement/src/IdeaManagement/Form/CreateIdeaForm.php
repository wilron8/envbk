<?php

/**
 * Description of CreateIdeaForm
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form;

use Zend\Form\Form;
use Zend\Validator;

class CreateIdeaForm extends Form {

    public function __construct($sm) {
        parent::__construct('create-idea');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'idea_title',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Title'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Title'),
            ),
        ));
        $this->add(array(
            'name' => 'idea_img',
            'attributes' => array(
                'type' => 'file',
                'placeholder' => $sm->get('translator')->translate('Icon'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Icon'),
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'idea_categoryID',
            'attributes' => array(
                'type' => 'select',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Category'),
                'empty_option' => '',
                'value_options' => $sm->get('CategoryTable')->getSelectOptions()
            ),
        ));
        $this->add(array(
            'name' => 'idea_attachment',
            'attributes' => array(
                'type' => 'file',
                'placeholder' => $sm->get('translator')->translate('Presentation'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Presentation'),
            ),
        ));
        $this->add(array(
            'name' => 'idea_descript',
            'attributes' => array(
                'type' => 'textarea',
                'placeholder' => $sm->get('translator')->translate('Description'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Description'),
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
               array(
                    'name' => 'StripTags',
                    'options' => array(
                        'allowTags' => \Common\Policy\Filter::$allowedTags,
                        'allowAttribs' => \Common\Policy\Filter::$allowedAttr,
                    ),
                ),
			)
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'reference',
            'attributes' => array(
                'type' => 'select',
                'multiple'=>'multiple',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('References'),
                'value_options' => $sm->get('IdeaTable')->getSelectOptions()
            ),
        ));

        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'idea_title',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name'=>'StripTags'),
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
        $filter->add(array(
            'name' => 'idea_categoryID',
            'required' => false,
        ));
        $filter->add(array(
            'name' => 'reference',
            'required' => false,
        ));
        $this->setInputFilter($filter);
    }

}
