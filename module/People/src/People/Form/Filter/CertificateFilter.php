<?php

/**
 * Description of CertificateFilter
 *
 * @author kimsreng
 */

namespace People\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CertificateFilter  extends InputFilter{
    public function __construct($translator) {
        $this->add(array(
            'name' => 'cert_text',
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
                            Validator\NotEmpty::IS_EMPTY => $translator->translate('Certificate cannot be empty.'),
                        ),
                    ),
                ),
            ),
        ));
    }
}
