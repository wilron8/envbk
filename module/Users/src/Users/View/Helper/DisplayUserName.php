<?php

/**
 * Description of DisplayUserName
 *
 * @author kimsreng
 */

namespace Users\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DisplayUserName extends AbstractHelper {

    public function __invoke($nameArray) {
        if (is_array($nameArray)) {
            if ($nameArray['usr_displayName'] != "") {
                return $nameArray['usr_displayName'];
            }
            return $nameArray['usr_fName'] . ' ' . $nameArray['usr_lName'];
        }elseif (is_object($nameArray)) {
            if($nameArray->usr_displayName!=""){
                return $nameArray->usr_displayName;
            }
            return $nameArray->usr_fName . ' '.($nameArray->usr_mName===""||$nameArray->usr_mName===NULL?"":$nameArray->usr_mName.' ') . $nameArray->usr_lName;
        }
    }

}
