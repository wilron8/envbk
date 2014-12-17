<?php

/**
 * Description of CreateIdeaConfirmForm
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form;

use Zend\Form\Form;
use Zend\Validator;

class CreateIdeaConfirmForm extends Form{

    public function __construct($sm) {
        parent::__construct('create-idea');
        $this->setAttribute('method', 'post');
        
         $this->add(array(
            'name' => 'agree',
            'attributes' => array(
                'type' => 'Checkbox',
                'value'=>'1'
            ),
             'options' => array(
                'label' => $sm->get('translator')->translate('Agree to the terms and conditions of Linkaide'),
            ),
        ));
        $this->add(array(
            'name' => 'publish',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Publish'),
            ),
        ));
        
         $this->add(array(
            'name' => 'back',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Back'),
            ),
        ));
         
          $this->add(array(
            'name' => 'cancel',
            'attributes' => array(
                'type' => 'submit',
                'value' => $sm->get('translator')->translate('Cancel'),
            ),
        ));
          
           //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'agree',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Please agree to the terms and conditions of Linkaide before you continue.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->setInputFilter($filter);
    }

}
