<?php

/**
 * Description of Crypt
 *
 * @author kimsreng
 */
namespace Common\Util;

use Zend\Crypt\Password\Bcrypt;

class Crypt {
    
    public static function getCrypt(){
        return new Bcrypt();
    }

    public static function encrypt($password) {
        $bcrypt = self::getCrypt();
        $encrypted = $bcrypt->create($password);
        return $encrypted;
    }

    public static function verify($password,$hash) {
        $bcrypt = self::getCrypt();
        return $bcrypt->verify($password, $hash);
    }

}
