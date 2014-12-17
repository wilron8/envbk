<?php

/**
 * Description of ProjectForm
 *
 * @author kimsreng
 */

namespace ProjectManagement\Form;

use Zend\Form\Form;
use Zend\Validator;

class ProjectForm extends Form {

    public function __construct($sm) {
        parent::__construct('project-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
         $this->add(array(
            'name' => 'proj_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'proj_title',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Title'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Title'),
            ),
        ));
        $this->add(array(
            'name' => 'proj_progress',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $sm->get('translator')->translate('Progress'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Progress'),
            ),
        ));
        $this->add(array(
            'name' => 'proj_img',
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
            'name' => 'proj_srcIdea',
            'attributes' => array(
                'type' => 'select',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Source Idea'),
                'value_options' => $sm->get('IdeaTable')->getSelectOptions()
            ),
        ));
        $this->add(array(
            'name' => 'proj_descript',
            'attributes' => array(
                'type' => 'textarea',
                'placeholder' => $sm->get('translator')->translate('Description'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Description'),
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'members',
            'attributes' => array(
                'type' => 'select',
                'multiple' => 'multiple',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Members'),
                'value_options' => $sm->get('UserTable')->getProjectMemSelectOptions()
            ),
        ));
        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'proj_title',
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
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Project title cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'proj_progress',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            )
        ));
        $filter->add(array(
            'name' => 'proj_srcIdea',
            'required' => false,
        ));
        $filter->add(array(
            'name' => 'reference',
            'required' => false,
        ));
         $filter->add(array(
            'name' => 'members',
            'required' => false,
        ));
        $this->setInputFilter($filter);
    }

}
