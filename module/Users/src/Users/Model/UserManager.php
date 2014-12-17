<?php

/**
 * 
 *
 * @author kimsreng
 */

namespace Users\Model;

use Users\Model\DbEntity\User;
use Users\Model\DbEntity\UserAddress;
use Users\Model\DbEntity\UserEmail;
use Users\Model\DbEntity\UserLang;
use Users\Model\DbEntity\UserPhone;
use Users\Model\DbEntity\Userjoin;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserManager {

    /**
     * @var \Users\Model\DbEntity\User 
     */
    protected $userInstance = NULL;

    /**
     * @var \Users\Model\DbTable\UserTable
     */
    protected $userTable = NULL;
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        $this->userTable = $this->serviceLocator->get('UserTable');
    }

    /**
     * 
     * @param \Users\Model\DbEntity\Userjoin $userJoin
     * @param type $data
     */
    public function register(Userjoin $userJoin, $data) {
        $userjoin = $userJoin;
        $this->userInstance = new User();
        $this->userInstance->exchangeArray($data);
        //encrypt usr_scretA
        if($this->userInstance->usr_secretA!=""){
            $this->userInstance->usr_secretA = \Common\Util\Crypt::encrypt($this->userInstance->usr_secretA);
        }
        $this->userInstance->usr_email = $userjoin->join_email;
        $this->userInstance->usr_username = $userjoin->join_email;
        $this->userInstance->usr_isEmailVerified = 1;
        $this->userInstance->usr_joinDate = date('Y-m-d H:i:s');
        $this->userInstance->usr_displayName = $this->userInstance->usr_fName . ' ' . ($this->userInstance->usr_mName ? $this->userInstance->usr_mName . ' ' : '') . $this->userInstance->usr_lName;
        $this->userInstance->usr_id = $this->userTable->insert($this->userInstance);
        //save address
        $address = new UserAddress();
        $address->exchangeArray($data);
        $geoCountry = $this->serviceLocator->get('geoCountryTable')->getById($address->uAddr_country);
        $address->uAddr_userID = $this->userInstance->usr_id;
        $address->uAddr_descript = "(settingsPage)";
        $address->uAddr_timeStamp = date('Y-m-d H:i:s');
        if ($geoCountry) {
            $address->uAddr_TZ = $geoCountry->geoCountry_tz;
            $address->uAddr_TZwDST = $geoCountry->geoCountry_dst;
        }

        $this->serviceLocator->get('UserAddressTable')->insert($address);
        // save address for profile
        $pAddress = new UserAddress();
        $pAddress->exchangeArray($address->getArrayCopy());
        $pAddress->uAddr_descript = "";
        $this->serviceLocator->get('UserAddressTable')->insert($pAddress);
        //save lang
        $userLang = new UserLang;
        $userLang->exchangeArray($data);
        $userLang->uLang_userID = $this->userInstance->usr_id;
        $userLang->uLang_lang = $this->userInstance->usr_lang;
        $this->serviceLocator->get('UserLangTable')->insert($userLang);
        //save phone
        $userPhone = new UserPhone();
        // $userPhone->uPhon_countryCode=$geoCountry->geoCountry_callingCode;
        $userPhone->uPhon_isPrimary = 1;
        $userPhone->uPhon_isSettingContact = 1;
        $userPhone->exchangeArray($data);
        $userPhone->uPhon_userid = $this->userInstance->usr_id;
        $this->serviceLocator->get('UserPhoneTable')->insert($userPhone);
        //save email
        $userEmail = new UserEmail;
        $userEmail->uEmail_userID = $this->userInstance->usr_id;
        $userEmail->uEmail_email = $userjoin->join_email;
        $userEmail->uEmail_isVerified = 1;
        $userEmail->uEmail_emailType = 0;
        $userEmail->uEmail_isMobile = 0;
        $userEmail->uEmail_isPrivateOnly = 1;
        $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
        $publicEmail = clone $userEmail;
        $publicEmail->uEmail_isPrivateOnly = 0;
        $this->serviceLocator->get('UserEmailTable')->insert($userEmail);
        $this->serviceLocator->get('UserEmailTable')->insert($publicEmail);
    }

    public function update(User $user = NULL) {
        if ($user) {
            $this->userInstance = $user;
        }
        $this->userTable->update($this->userInstance);
    }

    public function getUser() {
        return $this->userInstance;
    }

}
