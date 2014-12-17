<?php

/**
 * Filter used to strip out multiple redundant tags with just a single one
 *
 * @author kimsreng
 */

namespace Common\Filter;

class SingleTag implements \Zend\Filter\FilterInterface {

    /**
     * Tags to be filtered to a single element only
     * 
     * @var array 
     */
    protected $tags = [];

    public function __construct($options = null) {

        if (array_key_exists('tags', $options)) {
            $this->tags = $options['tags'];
        }
    }

    public function filter($value) {
        if (count($this->tags) > 0) {
            foreach ($this->tags as $tag) {
                $value = $this->processTag($value, $tag);
            }
        }
        return $value;
    }

    protected function processTag($html, $tag) {
        $html = preg_replace("/($tag)+/i", "$tag", $html);
        return $html;
    }

}
