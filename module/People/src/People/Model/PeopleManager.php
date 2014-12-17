<?php

/**
 * Description of PeopleManager
 *
 * @author kimsreng
 */

namespace People\Model;

use Users\Model\DbEntity\User;
use People\Model\DbEntity\UserSkill;
use People\Model\DbEntity\UserCertification;
use People\Model\DbEntity\Education;
use People\Model\DbEntity\Experience;
use Users\Model\DbEntity\UserEmail;
use Users\Model\DbEntity\UserPhone;
use Users\Model\DbEntity\UserAddress;
use Users\Model\DbEntity\UserLang;
use Users\Model\DbTable\UserTable;
use People\Model\DbTable\FollowPeopleTable;
use ProjectManagement\Model\DbTable\ProjectTable;
use ProjectManagement\Model\DbEntity\ProjectWall;
use ProjectManagement\Model\DbTable\ProjectWallTable;

class PeopleManager {

    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface  
     */
    protected $SL = NULL;

    /**
     * @var UserTable 
     */
    protected $UserTable = NULL;

    /**
     * @var FollowPeopleTable 
     */
    protected $FollowPeopleTable = NULL;

    /**
     * @var ProjectTable 
     */
    protected $projectTable = NULL;

    /**
     * @var ProjectWallTable 
     */
    protected $projectWallTable = NULL;

    public function __construct($SL) {
        $this->SL = $SL;
        $this->UserTable = $this->SL->get('UserTable');
        $this->FollowPeopleTable = $this->SL->get('FollowPeopleTable');
        $this->projectTable = $this->SL->get('ProjectTable');
        $this->projectWallTable = $this->SL->get('ProjectWallTable');
    }

    /**
     * 
     * @param \Users\Model\DbEntity\User $user
     * @param integer $followerId
     * @return boolean
     */
    public function follow(User $user, $followerId) {
        if ($this->FollowPeopleTable->isFollowing($user->usr_id, $followerId)) {
            return false;
        }

        if (!$this->FollowPeopleTable->follow($user->usr_id, $followerId)) {
            return false;
        }
        //increment follow count in user table
        $user->usr_followerCnt++;
        $this->UserTable->update($user);
        //update current user followingCnt
        $follower = $this->UserTable->getById($followerId);
        $follower->usr_followingCnt++;
        $this->UserTable->update($follower);
        return true;
    }

    public function unfollow(User $user, $followerId) {
        if (!$this->FollowPeopleTable->isFollowing($user->usr_id, $followerId)) {
            return false;
        }

        if (!$this->FollowPeopleTable->unfollow($user->usr_id, $followerId)) {
            return false;
        }
        //increment follow count in user table
        $user->usr_followerCnt--;
        $this->UserTable->update($user);
        //update current user followingCnt
        $follower = $this->UserTable->getById($followerId);
        $follower->usr_followingCnt--;
        $this->UserTable->update($follower);
        return true;
    }

    /**
     * Get user ready for image uri and profile uri
     * 
     * @param array $users
     */
    public function processUser(Array $users) {
        $url = $this->SL->get('ViewHelperManager')->get('url');
        $path = $this->SL->get('ViewHelperManager')->get('PathManager');
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]['usr_displayName'] == '') {
                $users[$i]['usr_displayName'] = $users[$i]['usr_fName'] . " " . ($users[$i]['usr_mName'] != "" ? $users[$i]['usr_mName'] . " " : "") . $users[$i]['usr_lName'];
            }
            $users[$i]['usr_icon'] = ($users[$i]['usr_icon'] === "" || $users[$i]['usr_icon'] === NULL) ? "/images/photo001.png" : $url('process-image', array('path' => $path()->buildUserRoutePath($users[$i]['usr_id'], \DocumentManager\Model\ResourceType::ICON, $users[$i]['usr_icon'])));
            $users[$i]['url'] = $url('people/profile', ['id' => $users[$i]['usr_id']]);
        }
        return $users;
    }

    /**
     * Update user profile-related info
     * 
     * @param array $data
     * @param integer $userId currently logged-in user
     */
    public function processUserInfo($data, $userId) {
        $this->processCert(json_decode($data['c_cert'], true), json_decode($data['del_cert'], true), $userId);
        $this->processSkill(json_decode($data['c_skill'], true), json_decode($data['del_skill'], true), $userId);
        $this->processLang(json_decode($data['c_lang'], true), json_decode($data['del_lang'], true), $userId);
        $this->processEducation(json_decode($data['c_ed'], true), json_decode($data['del_ed'], true), $userId);
        $this->processExperience(json_decode($data['c_xp'], true), json_decode($data['del_xp'], true), $userId);
        $this->processAddress(json_decode($data['c_address'], true), json_decode($data['del_address'], true), $userId);
        $this->processContact(json_decode($data['c_contact'], true), json_decode($data['del_contact'], true), $userId);
    }

    /**
     * check for skills new/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processSkill(array $change, array $remove, $userID) {
        // add
        if (count($change) > 0) {
            foreach ($change as $value) {
                $skill = new UserSkill();
                $skill->uSkll_userID = $userID;
                $skill->uSkll_TagID = $value['stag_id'];
                $this->SL->get('UserSkillTable')->insert($skill);
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->SL->get('UserSkillTable')->delete($r['uSkll_id']);
            }
        }
    }

    /**
     * check for address new/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processAddress(array $change, array $remove, $userID) {
        // add
        if (count($change) > 0) {
            foreach ($change as $value) {
                if ($value['uAddr_id'] == 0) {
                    $addr = new UserAddress();
                    $addr->exchangeArray($value);
                    $addr->uAddr_userID = $userID;
                    $addr->uAddr_timeStamp = date('Y-m-d H:i:s');
                    $this->SL->get('UserAddressTable')->insert($addr);
                } else {
                    $addr = $this->SL->get('UserAddressTable')->getById($value['uAddr_id']);
                    $addr->exchangeArray($value);
                    $this->SL->get('UserAddressTable')->update($addr);
                }
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->SL->get('UserAddressTable')->delete($r['uAddr_id']);
            }
        }
    }

    /**
     * check for certificate new/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processCert(array $change, array $remove, $userID) {
        // add
        if (count($change) > 0) {
            foreach ($change as $value) {
                $cert = new UserCertification();
                $cert->uCert_userID = $userID;
                $cert->uCert_TagID = $value['cert_id'];
                $this->SL->get('UserCertificateTable')->insert($cert);
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->SL->get('UserCertificateTable')->delete($r['uCert_id']);
            }
        }
    }

    /**
     * check for languages new/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processLang(array $change, array $remove, $userID) {
        // add
        if (count($change) > 0) {
            foreach ($change as $value) {
                $lang = new UserLang();
                $lang->uLang_userID = $userID;
                $lang->uLang_lang = $value['geoLang_id'];
                $this->SL->get('UserLangTable')->insert($lang);
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->SL->get('UserLangTable')->delete($r['uLang_id']);
            }
        }
    }

    /**
     * check for education new/update/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processEducation(array $change, array $remove, $userID) {
        // add or update
        if (count($change) > 0) {
            foreach ($change as $value) {
                if ($value['ed_id'] == 0) {
                    $this->getEdMgr()->create($value, $userID);
                } else {
                    $this->getEdMgr()->update($value, $userID);
                }
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->getEdMgr()->delete($r['ed_id'], $userID);
            }
        }
    }

    /**
     * check for experience new/update/delete from json post
     * 
     * @param array $change
     * @param array $remove
     * @param integer $userID
     */
    public function processExperience(array $change, array $remove, $userID) {
        // add or update
        if (count($change) > 0) {
            foreach ($change as $value) {
                if ($value['xp_id'] == 0) {
                    $this->getXpMgr()->create($value, $userID);
                } else {
                    $this->getXpMgr()->update($value, $userID);
                }
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->getXpMgr()->delete($r['xp_id'], $userID);
            }
        }
    }

    public function processContact(array $change, array $remove, $userID) {
        // add or update
        if (count($change) > 0) {
            foreach ($change as $value) {
                if ($value['id'] == 0) {
                    $this->getContactMgr()->create($value, $userID);
                } else {
                    $this->getContactMgr()->update($value, $userID);
                }
            }
        }
        //remove 
        if (count($remove) > 0) {
            foreach ($remove as $r) {
                $this->getContactMgr()->delete($r, $userID);
            }
        }
    }

    /**
     * @return \People\Model\EducationManager
     */
    protected function getEdMgr() {
        return $this->SL->get('EducationManager');
    }

    /**
     * @return \People\Model\ExperienceManager
     */
    protected function getXpMgr() {
        return $this->SL->get('ExperienceManager');
    }

    /**
     * @return \People\Model\ContactManager
     */
    protected function getContactMgr() {
        return $this->SL->get('ContactManager');
    }

    /**
     * 
     * @param User $user
     */
    public function terminateUser($user, $reason) {
        //update user with null information
        $user->usr_username = 'DELETED@LINSPIRA.COM';
        $user->usr_password = 'NULL';
        $user->usr_fName = NULL;
        $user->usr_mName = NULL;
        $user->usr_lName = NULL;
        $user->usr_displayName = 'DELETED USER';
        $user->usr_gender = NULL;
        $user->usr_dob = NULL;
        $user->usr_secretQ = NULL;
        $user->usr_secretA = NULL;
        $user->usr_email = 'DELETED@LINSPIRA.COM';
        $user->usr_about = NULL;
        $user->usr_icon = NULL;
        $user->usr_isEmailVerified = NULL;
        $user->usr_isBlocked = '1';
        $user->usr_blockedDate = date("Y-m-d H:i:s");
        $user->usr_blockedDuration = '-1';
        $user->usr_followingCnt = '0';
        $user->usr_adminComment = 'User explicitly requested to leave.  Reason: ' . $reason;
        $this->UserTable->update($user);
        //close all in-progress projects by this user
        $toCloseProjects = $this->projectTable->getAbsolutelyOwned($user->usr_id);
        if (count($toCloseProjects) > 0) {
            foreach ($toCloseProjects as $projId) {
                $project = $this->projectTable->getById($projId);
                $project->proj_isClosed = 1;
                $project->proj_isSuccess = 0;
                $project->proj_lastModified = date('Y-m-d H:i:s');
                $this->projectTable->update($project);
                //add message to project wall
                $wall = new ProjectWall();
                $wall->prjW_comment = "Automatically closed due to PM account termination.";
                $wall->prjW_userid = $user->usr_id;
                $wall->prjW_readOnly = 1;
                $wall->prjW_timeStamp = date("Y-m-d H:i:s");
                $wall->prjW_projID = $projId;
                $this->projectWallTable->insert($wall);
            }
        }
    }

}
