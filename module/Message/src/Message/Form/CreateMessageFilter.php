<?php

/**
 * Description of CreateMessageFilter
 *
 * @author kimsreng
 */

namespace Message\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CreateMessageFilter extends InputFilter {

    public function __construct($sm) {
        $this->add(array(
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
        $this->add(array(
            'name' => 'recipients[]',
            'required' => false
        ));
        $this->add(array(
            'name' => 'msg_threadID',
            'required' => false,
            'filters'=>array(
                array(
                    'name'=>'Int'
                )
            )
        ));
        $this->add(array(
            'name' => 'msg_subject',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
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
                            Validator\NotEmpty::IS_EMPTY => $sm->get('translator')->translate('Subject cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
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
            'validators' => array(
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
    }

}
