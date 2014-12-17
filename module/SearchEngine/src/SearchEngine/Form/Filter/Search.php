<?php

/**
 *
 * @author kimsreng
 */
namespace SearchEngine\Form\Filter;

use Zend\InputFilter\InputFilter;

class Search extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'keyword',
            'required' => false,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags')
            ),
        ));
    }

}
