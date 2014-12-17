<?php

/**
 * This class contains configuration for Zend/Filter
 *
 * @author kimsreng
 */
namespace Common\Policy;

class Filter {

    /**
     * Html Tags to be allowed in StripTags filter
     * 
     * @var array 
     */
    static $allowedTags = ['a', 'li', 'lo', 'ul', 'em', 'B', 'U', 'i', 'center', 'font', 'BR', 'div', 'span'];

    /**
     * Html attributes to be allowed in StripTags filter
     * 
     * @var array 
     */
    static $allowedAttr = ['align', 'style', 'color'];

}
