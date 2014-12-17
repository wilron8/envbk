<?php

/**
 * Description of StripString
 *
 * @author kimsreng
 */
namespace Common\Filter;

class StripString  implements \Zend\Filter\FilterInterface {

    /**
     * list of strings to strip away
     * 
     * @var array 
     */
    protected $strings = [];

    public function __construct($options = null) {

        if (array_key_exists('strings', $options)) {
            $this->strings = $options['strings'];
        }
    }

    public function filter($value) {
        if (count($this->strings) > 0) {
            foreach ($this->strings as $str) {
                $value = str_replace($str, '', $value);
            }
        }
        return $value;
    }

}
