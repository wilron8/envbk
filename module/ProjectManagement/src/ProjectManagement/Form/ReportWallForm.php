<?php

/**
 * Description of ReportIdeaForm
 *
 * @author kimsreng
 */

namespace ProjectManagement\Form;

use Zend\Form\Form;

class ReportWallForm extends Form {

    public function __construct($sm) {
        parent::__construct('report-project');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'vp_prjwId',
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
                array('name' => 'StripTags'),
            ),
        ));
        $filter->add(array(
            'name' => 'vp_prjwId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->setInputFilter($filter);
    }

}
