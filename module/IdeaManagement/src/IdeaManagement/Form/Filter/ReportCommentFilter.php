<?php

/**
 * Description of ReportCommentFilter
 *
 * @author kimsreng
 */

namespace IdeaManagement\Form\Filter;

use Zend\InputFilter\InputFilter;

class ReportCommentFilter extends InputFilter {

    public function __construct() {
        $this->add(array(
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
        $this->add(array(
            'name' => 'vp_commentId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
        $this->add(array(
            'name' => 'vp_userId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));
    }

}
