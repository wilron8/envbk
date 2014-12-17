<?php

/**
 * Description of ReportCommentForm
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form;

use Zend\Form\Form;

class ReportCommentForm extends Form {

    public function __construct($sm) {
        parent::__construct('report-comment');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'vp_commentId',
            'attributes' => array(
                'type' => 'hidden',
                'required' => 'required',
            )
        ));
        $this->add(array(
            'name' => 'vp_userId',
            'attributes' => array(
                'type' => 'hidden',
                'required' => 'required',
            )
        ));
        $this->add(array(
            'name' => 'vp_comments',
            'attributes' => array(
                'type' => 'textarea',
                'required' => 'required',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Reasons'),
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Report'),
            ),
        ));
        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'vp_comments',
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
            ),
        ));
        $filter->add(array(
            'name' => 'vp_commentId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $filter->add(array(
            'name' => 'vp_userId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->setInputFilter($filter);
    }

}