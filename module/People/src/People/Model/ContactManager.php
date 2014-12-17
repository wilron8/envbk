<?php

/**
 * Description of ContactManager
 *
 * @author kimsreng
 */

namespace People\Model;

use Users\Model\DbEntity\UserEmail;
use Users\Model\DbEntity\UserPhone;
use Users\Model\DbTable\UserEmailTable;
use Users\Model\DbTable\UserPhoneTable;
use Common\Util\StringHelper;

class ContactManager {

    /**
     * @var UserEmailTable
     */
    protected $uEmailTable;

    /**
     * @var UserPhoneTable
     */
    protected $uPhoneTable;

    public function __construct($uEmailTable, $uPhoneTable) {
        $this->uEmailTable = $uEmailTable;
        $this->uPhoneTable = $uPhoneTable;
    }

    public function create($data, $userId) {
        if ($data['type'] == 99) {
            $value = trim($data['value']);
            if (StringHelper::isEmail($value)) {
                $userEmail = new UserEmail();
                $userEmail->uEmail_userID = $userId;
                $userEmail->uEmail_timeStamp = date('Y-m-d H:i:s');
                $userEmail->uEmail_isPrivateOnly = false;
                $userEmail->uEmail_email = $value;
                $this->uEmailTable->insert($userEmail);
                return true;
            }
        } else {
            $value = trim($data['value']);
            if (StringHelper::isPhone($value)) {
                $userPhone = new UserPhone();
                $userPhone->uPhon_userid = $userId;
                $userPhone->uPhon_type = $data['type'];
                $userPhone->uPhon_number = $value;
                $this->uPhoneTable->insert($userPhone);
                return true;
            }
        }
        return false;
    }

    public function update($data, $userId) {
        if ($data['type'] == 99) {
            $value = trim($data['value']);
            if (StringHelper::isEmail($value)) {
                $uEmail = $this->uEmailTable->getById($data['id']);
                if ($uEmail && $uEmail->uEmail_userID == $userId) {
                    $uEmail->uEmail = $value;
                    $this->uEmailTable->update($uEmail);
                    return true;
                }
            }
        } else {
            $value = trim($data['value']);
            if (StringHelper::isPhone($value)) {
                $uPhone = $this->uPhoneTable->getById($data['id']);
                if ($uPhone && $uPhone->uPhon_userid == $userId) {
                    $uPhone->uPhon_number = $value;
                    $uPhone->uPhon_type = $data['type'];
                    $this->uPhoneTable->update($uPhone);
                    return true;
                }
            }
        }
    }

    public function delete($data, $userId) {

        if ($data['type'] == 99) {
            $uEmail = $this->uEmailTable->getById($data['id']);
            if ($uEmail && $uEmail->uEmail_userID == $userId) {
                $this->uEmailTable->delete($data['id']);
                return true;
            }
        } else {
            $uPhone = $this->uPhoneTable->getById($data['id']);
            if ($uPhone && $uPhone->uPhon_userid == $userId) {
                $this->uPhoneTable->delete($data['id']);
                return true;
            }
        }
        return false;
    }

}
