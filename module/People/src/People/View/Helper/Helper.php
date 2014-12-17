<?php

/**
 * Description of Helper
 *
 * @author kimsreng
 */

namespace People\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;

class Helper extends AbstractHelper {

    protected $sl;
    protected static $instance = null;
    protected $ideas = NULL;
    protected $followings = NULL;
    protected $followers = NULL;
    protected $projects = NULL;

    public function __invoke() {
        return $this;
    }

    /**
     * Get service manager
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->sl = $serviceLocator;
    }

    public function translate($message) {
        return $this->sl->get('translator')->translate($message);
    }

    public function getGender($genderId) {
        switch ($genderId) {
            case 0:
                return $this->translate("Female");
            case 1:
                return $this->translate("Male");
            case 2:
                return $this->translate("Not Specified");
            default:
                return "";
        }
    }

    public function getMonth($month) {
        $month = (int) $month;
        switch ($month) {
            case 1:
                return $this->translate("January");
            case 2:
                return $this->translate("February");
            case 3:
                return $this->translate("March");
            case 4:
                return $this->translate("April");
            case 5:
                return $this->translate("May");
            case 6:
                return $this->translate("June");
            case 7:
                return $this->translate("July");
            case 8:
                return $this->translate("August");
            case 9:
                return $this->translate("September");
            case 10:
                return $this->translate("October");
            case 11:
                return $this->translate("November");
            case 12:
                return $this->translate("December");
            default:
                return "";
        }
    }

    public function formatDob($dob) {
        if ($dob === '' || $dob === null) {
            return $this->translate('(not defined)');
        }
        list($date, $time) = explode(' ', $dob);
        list($year, $month, $day) = explode('-', $date);
        return ((int) $year === 0 ? "" : $year . " ") . ((int) $month === 0 ? "" : $this->getMonth($month) . " ") . ((int) $day === 0 ? "" : $day . " ");
    }

    public function getLangList($userID) {
        $list = $this->sl->get('UserLangTable')->getByUserId($userID, array('uLang_id'));
        $langList = '';
        foreach ($list as $l) {
            $langList .= $l->geoLang_name . '<BR>';
        }
        return $langList;
    }

    public function getLocationList($userId) {
        $address = $this->sl->get('UserAddressTable')->getProfileAddresses($userId);
        $coutries = $this->sl->get('geoCountryTable')->fetchAll()->toArray();
        $addressList = '';
        foreach ($address as $v) {
            $addressList .= $v->uAddr_city . ' / ' . $this->getVByK($coutries, $v->uAddr_country, 'geoCountry_id', 'geoCountry_name') . '<BR>';
        }
        return $addressList;
    }

    public function getSkillList($userId) {
        $skills = $this->sl->get('UserSkillTable')->fetchByUser($userId, array('uSkll_id'));
        $skillList = '';
        foreach ($skills as $v) {
            $skillList .= $v->stag_text . '<BR>';
        }
        return $skillList;
    }

    public function getCertList($userId) {
        $certs = $this->sl->get('UserCertificateTable')->fetchByUser($userId);
        $certList = '';
        foreach ($certs as $v) {
            $certList .= $v->cert_text . '<BR>';
        }
        return $certList;
    }

    public function getContactList($userId) {
        $emails = $this->sl->get('UserEmailTable')->getByUserId($userId, false);
        $phones = $this->sl->get('UserPhoneTable')->getByUserId($userId);

        $data = [];
        foreach ($emails as $value) {
            $da = [];
            $da['id'] = $value->uEmail_id;
            $da['type'] = 'Email';
            $da['value'] = $value->uEmail_email;
            $data[] = $da;
        }

        foreach ($phones as $value) {
            $da['id'] = $value->uPhon_id;
            $da['type'] = ($value->uPhon_type && isset($this->getContactType()[$value->uPhon_type])) ? $this->getContactType()[$value->uPhon_type] : '';
            $da['value'] = $value->uPhon_number;
            $data[] = $da;
        }
        $vm = new ViewModel(array(
            'data' => $data
        ));
        $vm->setTemplate("people/helper/user-contactList.phtml");
        return $this->getView()->render($vm);
    }

    public function getEdList($userId) {
        $data = $this->sl->get('EducationTable')->fetchByUser($userId)->toArray();
        for ($i = 0; $i < count($data); $i++) {
            list($year, $month, $day) = explode('-', $data[$i]['ed_fromDate']);
            $data[$i]['ed_fromDate'] = $year . '.' . $month;
            list($year, $month, $day) = explode('-', $data[$i]['ed_toDate']);
            if ($year == "9999") {
                $data[$i]['ed_toDate'] = "Present";
            } else {
                $data[$i]['ed_toDate'] = $year . '.' . $month;
            }
        }
        $vm = new ViewModel(array(
            'data' => $data
        ));
        $vm->setTemplate("people/helper/user-edList.phtml");
        return $this->getView()->render($vm);
    }

    public function getXpList($userId) {
        $data = $this->sl->get('ExperienceTable')->fetchByUser($userId)->toArray();
        for ($i = 0; $i < count($data); $i++) {
            list($year, $month, $day) = explode('-', $data[$i]['xp_fromDate']);
            $data[$i]['xp_fromDate'] = $year . '.' . $month;
            list($year, $month, $day) = explode('-', $data[$i]['xp_toDate']);
            if ($year == "9999") {
                $data[$i]['xp_toDate'] = "Present";
            } else {
                $data[$i]['xp_toDate'] = $year . '.' . $month;
            }
        }
        $vm = new ViewModel(array(
            'data' => $data
        ));
        $vm->setTemplate("people/helper/user-xpList.phtml");
        return $this->getView()->render($vm);
    }

    public function getFollowers($userId) {
        if ($this->followers == NULL) {
            $this->followers = $this->sl->get('FollowPeopleTable')->fetchFollower($userId);
        }
        $vm = new ViewModel(array(
            'data' => $this->followers,
            'userId' => $userId,
        ));
        $vm->setTemplate("people/helper/user-followers.phtml");
        return $this->getView()->render($vm);
    }

    public function countFollower($userId) {
        if ($this->followers == NULL) {
            $this->followers = $this->sl->get('FollowPeopleTable')->fetchFollower($userId);
        }
        return count($this->followers);
    }

    public function getFollowing($userId) {
        if ($this->followings == NULL) {
            $this->followings = $this->sl->get('FollowPeopleTable')->fetchFollowee($userId);
        }
        $vm = new ViewModel(array(
            'data' => $this->followings,
            'userId' => $userId,
        ));
        $vm->setTemplate("people/helper/user-following.phtml");
        return $this->getView()->render($vm);
    }

    public function countFollowing($userId) {
        if ($this->followings == NULL) {
            $this->followings = $this->sl->get('FollowPeopleTable')->fetchFollowee($userId);
        }
        return count($this->followings);
    }

    public function getIdeas($userId) {
        if ($this->ideas == NULL) {
            $this->ideas = $this->sl->get('IdeaTable')->fetchAllByUserId($userId);
        }
        $vm = new ViewModel(array(
            'data' => $this->ideas,
            'userId' => $userId,
        ));
        $vm->setTemplate("people/helper/user-ideas.phtml");
        return $this->getView()->render($vm);
    }

    public function countIdea($userId) {
        if ($this->ideas == NULL) {
            $this->ideas = $this->sl->get('IdeaTable')->fetchAllByUserId($userId);
        }
        return count($this->ideas);
    }

    public function getProjects($userId) {
        if ($this->projects == NULL) {
            $this->projects = $this->sl->get('ProjectTable')->fetchCreated($userId);
        }
        $vm = new ViewModel(array(
            'data' => $this->projects,
            'userId' => $userId,
        ));
        $vm->setTemplate("people/helper/user-projects.phtml");
        return $this->getView()->render($vm);
    }

    public function countProject($userId) {
        if ($this->projects == NULL) {
            $this->projects = $this->sl->get('ProjectTable')->fetchCreated($userId);
        }
        return count($this->projects);
    }

    public function getCurrentJob($userId) {
        $experience = $this->sl->get('ExperienceTable')->fetchByUser($userId, array('xp_toDate' => '9999-01-01'))->current();
        if ($experience) {
            return $experience->xp_jobTitle . ' @ ' . $experience->xp_name;
        } else {
            return "(No employment specified)";
        }
    }

    public function follow($userId) {
        $auth = $this->sl->get('AuthService');
        $currentUserId = $auth->hasIdentity() ? $auth->getIdentity()->usr_id : false;
        if ($currentUserId == $userId) {
            return '';
        }
        $vm = new ViewModel(array(
            'followTable' => $this->sl->get('followPeopleTable'),
            'userId' => $userId
        ));
        $vm->setTemplate("people/helper/user-follow.phtml");
        return $this->getView()->render($vm);
    }

    public function getContactType() {
        return [
            0 => 'Home Phone',
            1 => 'Home Fax',
            2 => 'Work Phone',
            3 => 'Work Fax',
            4 => 'Mobile',
            5 => 'Others',
        ];
    }

    public function getIcon($userObject, Array $options = NULL) {

        $params = [];
        if (is_array($userObject)) {
            $params['usr_id'] = $userObject['usr_id'];
            $params['usr_icon'] = $userObject['usr_icon'];
        } elseif (is_object($userObject)) {
            $params['usr_id'] = $userObject->usr_id;
            $params['usr_icon'] = $userObject->usr_icon;
        } else {
            throw new \Exception('User must be either array or object');
        }
        
        $params['user'] = $userObject;
        $vm = new ViewModel($params);
        $vm->setTemplate("people/helper/user-icon.phtml");
        return $this->getView()->render($vm);
    }

    public function isCurrentUser($userId) {
        return ($this->sl->get('AuthService')->hasIdentity() && $userId == $this->sl->get('AuthService')->getIdentity()->usr_id);
    }

    /**
     * Get an field value from a list (used this function to avoid repetitive database query
     * 
     * @param type $list
     * @param type $id
     * @param type $idField
     * @param type $valueField
     * @return string
     */
    protected function getVByK($list, $id, $idField, $valueField) {

        foreach ($list as $l) {
            if ($l[$idField] == $id) {
                return $l[$valueField];
            }
        }
        return "";
    }

}
