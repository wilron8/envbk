<?php

/**
 * Description of CommentForm
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form;

use Zend\Form\Form;

class CommentForm extends Form {

    public function __construct($sm) {
        parent::__construct('create-comment');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'iComm_ideaId',
            'attributes' => array(
                'type' => 'hidden',
                'required' => 'required'
            )
        ));
        $this->add(array(
            'name' => 'iComm_comment',
            'attributes' => array(
                'type' => 'textarea',
                'required' => 'required'
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Comment')
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Comment')
            ),
        ));
        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'iComm_comment',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array(
                    'name' => 'StripTags',
                    'options' => array(
                        'allowTags' => \Common\Policy\Filter::$allowedTags,
                        'allowAttribs' =>  \Common\Policy\Filter::$allowedAttr
                    )
                ),
                array(
                    'name'=>'Common\Filter\SingleTag',
                    'options'=>array(
                        'tags'=>array('<br>','\r','\n')
                    )
                )
            )
        ));
        $filter->add(array(
            'name' => 'iComm_ideaId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int')
            )
        ));
        $this->setInputFilter($filter);
    }

}
