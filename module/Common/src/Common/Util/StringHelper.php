<?php

/**
 * Description of StringHelper
 *
 * @author kimsreng
 */

namespace Common\Util;

use Zend\Filter\StripTags;
use Zend\Filter\StringTrim;
use Zend\Validator\EmailAddress;
use Zend\Validator\Regex;

class StringHelper {

    /**
     * Convert text into camel case
     * 
     * @param string $text
     * @return string
     */
    public static function toCamel($text) {
        $output = ucwords($text);
        return $output;
    }

    /**
     * Clean string of html tags
     * 
     * @param string $string
     * @return string
     */
    public static function sanitize($string, Array $allowedTags = Array(), Array $allowedAttribute = []) {
        $strip = new StripTags(['allowTags' => $allowedTags, 'allowAttribs' => $allowedAttribute]);
        $trim = new StringTrim();
        return $strip->filter($trim->filter($string));
    }

    public static function isPhone($string) {
        $validator = new Regex('/^[+]{0,1}[0-9- .]+$/');
        return $validator->isValid($string);
    }

    public static function isEmail($string) {
        $email = new EmailAddress();
        return $email->isValid($string);
    }

    /**
     * Use base64 to encode string
     * 
     * @param string $string
     * @return string
     */
    public function encode($string) {
        return \base64_encode($string);
    }

    /**
     * Use base64 to decode string
     * 
     * @param string $string
     * @return string
     */
    public function decode($string) {
        return \base64_decode($string);
    }

}
