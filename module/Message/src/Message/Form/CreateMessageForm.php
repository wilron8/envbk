<?php

/**
 * Description of CreateMessageForm
 *
 * @author kimsreng
 */
namespace Message\Form;

use Zend\Form\Form;
use Zend\Validator;
class CreateMessageForm extends Form{
      public function __construct($sm) {
        parent::__construct('create-message');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
           'type' => 'Zend\Form\Element\Select',
            'name' => 'recepient',
            'attributes' => array(
                'type' => 'select',
                'multiple'=>'multiple',
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('To'),
                'value_options' => $sm->get('UserTable')->getProjectMemSelectOptions()
            ),
        ));
        $this->add(array(
            'name' => 'msg_subject',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Subject'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Subject'),
            ),
        ));
        $this->add(array(
            'name' => 'msg_body',
            'attributes' => array(
                'type' => 'textarea',
                'required' => 'required',
                'placeholder' => $sm->get('translator')->translate('Message Body'),
            ),
            'options' => array(
                'label' => $sm->get('translator')->translate('Message Body'),
            ),
        ));
        
        //Add filter
        $filter = new \Zend\InputFilter\InputFilter();
        $filter->add(array(
            'name' => 'recipients',
            'required' => true,
            'validators'=>array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Recipient cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'recipients[]',
            'required' => false
        ));
        $filter->add(array(
            'name' => 'msg_subject',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators'=>array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Subject cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
         $filter->add(array(
            'name' => 'msg_body',
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
             'validators'=>array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Message body cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->setInputFilter($filter);
      }
}
