<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author kimsreng
 */

namespace Users\Model\DbEntity;

class User {

    public $usr_id = NULL;
    public $usr_username = NULL;
    public $usr_password = NULL;
    public $usr_pHistory = NULL;
    public $usr_fName = NULL;
    public $usr_mName = NULL;
    public $usr_lName = NULL;
    public $usr_displayName = NULL;
    public $usr_gender = 2;
    public $usr_dob = NULL;
    public $usr_secretQ = NULL;
    public $usr_secretA = NULL;
    public $usr_email = NULL;
    public $usr_about = NULL;
    public $usr_lang = NULL;
    public $usr_icon = NULL;
    public $usr_interest = NULL;
    public $usr_isEmailVerified = NULL;
    public $usr_isSuspended = 0;
    public $usr_isTerminated = 0;
    public $usr_suspendDate = NULL;
    public $usr_suspendDuration = NULL;
    public $usr_offenseCount = 0;
    public $usr_lastLogin = NULL;
    public $usr_lastIP = NULL;
    public $usr_joinDate = NULL;
    public $usr_isDeclined = NULL;
    public $usr_declineDate = NULL;
    public $usr_declineReason = NULL;
    public $usr_adminComment = NULL;
    public $usr_followerCnt = NULL;
    public $usr_followingCnt = NULL;
    public $usr_ideaCnt = NULL;
    public $usr_projCnt = NULL;

    function exchangeArray($data) {
        $this->usr_id = (isset($data['usr_id'])) ? $data['usr_id'] : $this->usr_id;
        $this->usr_username = (isset($data['usr_username'])) ? $data['usr_username'] : $this->usr_username;
        $this->usr_password = (isset($data['usr_password'])) ? $data['usr_password'] : $this->usr_password;
        $this->usr_pHistory = (isset($data['usr_pHistory'])) ? $data['usr_pHistory'] : $this->usr_pHistory;
        $this->usr_fName = (isset($data['usr_fName'])) ? $data['usr_fName'] : $this->usr_fName;
        $this->usr_mName = (isset($data['usr_mName'])) ? $data['usr_mName'] : $this->usr_mName;
        $this->usr_lName = (isset($data['usr_lName'])) ? $data['usr_lName'] : $this->usr_lName;
        $this->usr_displayName = (isset($data['usr_displayName'])) ? $data['usr_displayName'] : $this->usr_displayName;
        $this->usr_gender = (isset($data['usr_gender'])) ? $data['usr_gender'] : $this->usr_gender;
        $this->usr_dob = (isset($data['usr_dob'])) ? $data['usr_dob'] : $this->usr_dob;
        $this->usr_secretQ = (isset($data['usr_secretQ'])) ? $data['usr_secretQ'] : $this->usr_secretQ;
        $this->usr_secretA = (isset($data['usr_secretA'])) ? $data['usr_secretA'] : $this->usr_secretA;
        $this->usr_email = (isset($data['usr_email'])) ? $data['usr_email'] : $this->usr_email;
        $this->usr_about = (isset($data['usr_about'])) ? $data['usr_about'] : $this->usr_about;
        $this->usr_lang = (isset($data['usr_lang'])) ? $data['usr_lang'] : $this->usr_lang;
        $this->usr_icon = (isset($data['usr_icon'])) ? $data['usr_icon'] : $this->usr_icon;
        $this->usr_interest = (isset($data['usr_interest'])) ? $data['usr_interest'] : $this->usr_interest;
        $this->usr_isEmailVerified = (isset($data['usr_isEmailVerified'])) ? $data['usr_isEmailVerified'] : $this->usr_isEmailVerified;
        $this->usr_isTerminated = (isset($data['usr_isTerminated'])) ? $data['usr_isTerminated'] : $this->usr_isTerminated;
        $this->usr_isSuspended = (isset($data['usr_isSuspended'])) ? $data['usr_isSuspended'] : $this->usr_isSuspended;
        $this->usr_suspendDate = (isset($data['usr_suspendDate'])) ? $data['usr_suspendDate'] : $this->usr_suspendDate;
        $this->usr_suspendDuration = (isset($data['usr_suspendDuration'])) ? $data['usr_suspendDuration'] : $this->usr_suspendDuration;
        $this->usr_offenseCount = (isset($data['usr_offenseCount'])) ? $data['usr_offenseCount'] : $this->usr_offenseCount;
        $this->usr_lastLogin = (isset($data['usr_lastLogin'])) ? $data['usr_lastLogin'] : $this->usr_lastLogin;
        $this->usr_lastIP = (isset($data['usr_lastIP'])) ? $data['usr_lastIP'] : $this->usr_lastIP;
        $this->usr_joinDate = (isset($data['usr_joinDate'])) ? $data['usr_joinDate'] : $this->usr_joinDate;
        $this->usr_isDeclined = (isset($data['usr_isDeclined'])) ? $data['usr_isDeclined'] : $this->usr_isDeclined;
        $this->usr_declineDate = (isset($data['usr_declineDate'])) ? $data['usr_declineDate'] : $this->usr_declineDate;
        $this->usr_declineReason = (isset($data['usr_declineReason'])) ? $data['usr_declineReason'] : $this->usr_declineReason;
        $this->usr_adminComment = (isset($data['usr_adminComment'])) ? $data['usr_adminComment'] : $this->usr_adminComment;
        $this->usr_followerCnt = (isset($data['usr_followerCnt'])) ? $data['usr_followerCnt'] : $this->usr_followerCnt;
        $this->usr_followingCnt = (isset($data['usr_followingCnt'])) ? $data['usr_followingCnt'] : $this->usr_followingCnt;
        $this->usr_ideaCnt = (isset($data['usr_ideaCnt'])) ? $data['usr_ideaCnt'] : $this->usr_ideaCnt;
        $this->usr_projCnt = (isset($data['usr_projCnt'])) ? $data['usr_projCnt'] : $this->usr_projCnt;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    /**
     * Get full name
     * 
     * @return string
     */
    public function getFullName() {
        if ($this->usr_mName != "") {
            return $this->usr_fName . ' ' . $this->usr_mName . ' ' . $this->usr_lName;
        }
        return $this->usr_fName . ' ' . $this->usr_lName;
    }

    /**
     * Determine name to display
     * 
     * @return string
     */
    public function displayName() {
        if ($this->usr_displayName != "") {
            return $this->usr_displayName;
        }
        return $this->usr_fName . ' ' . $this->usr_lName;
    }

    /**
     * Get name to display
     * 
     * @param array|object $nameArray take user data from array when it is queried by sql select
     * @return string
     */
    public static function getDisplayName($nameArray) {

        if (is_array($nameArray)) {
            if ($nameArray['usr_displayName'] != "") {
                return $nameArray['usr_displayName'];
            }
            return $nameArray['usr_fName'] .' '. ($nameArray['usr_mName']!=""?$nameArray['usr_mName'].' ' :""). $nameArray['usr_lName'];
        } elseif (is_object($nameArray)) {
             if ($nameArray->usr_displayName != "") {
                return $nameArray->usr_displayName;
            }
            return $nameArray->usr_fName. ' ' .($nameArray->usr_mName!=""?$nameArray->usr_mName.' ':''). $nameArray->usr_lName;
        }
    }

}

?>
