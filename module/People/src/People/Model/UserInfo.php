<?php

/**
 * This class would wrap up all information about a particular person
 *
 * @author kimsreng
 */

namespace People\Model;

use Users\Model\DbEntity\User;

class UserInfo {

    protected $service = NULL;
    protected $user = NULL;
    protected $emails = NULL;
    protected $educations = NULL;
    protected $experience = NULL;
    protected $skills = NULL;
    protected $certificates = NULL;
    protected $phones = NULL;
    protected $languages = NULL;
    protected $mainLang = NULL;
    protected $ideas = NULL;
    protected $projects = NULL;

    /**
     * 
     * @param mixed $user it can be a user instance or just an id
     */
    public function __construct($user, $serviceLocator) {
        $this->service = $serviceLocator;
        if ($user instanceof User) {
            $this->user = $user;
        } else {
            $this->user = $this->service->get('UserTable')->getById($user);
        }
    }

    public function getEmail() {
        if ($this->emails == NULL) {
            $this->emails = $this->service->get('UserEmailTable')->getByUserId($this->user->usr_id);
        }
        return $this->emails;
    }

    public function getPhone() {
        if ($this->phones == NULL) {
            $this->phones = $this->service->get('UserPhoneTable')->getByUserId($this->user->usr_id);
        }
        return $this->phones;
    }

    public function getEducation() {
        if ($this->educations == NULL) {
            $this->educations = $this->service->get('EducationTable')->fetchByUser($this->user->usr_id);
        }
        return $this->educations;
    }

    public function getExperience() {
        if ($this->experience == NULL) {
            $this->experience = $this->service->get('ExperienceTable')->fetchByUser($this->user->usr_id);
        }
        return $this->experience;
    }

    public function getLang() {
        if ($this->languages == NULL) {
            $this->languages = $this->service->get('UserLangTable')->getByUserId($this->user->usr_id);
        }
        return $this->languages;
    }

    public function getCertificate() {
        if ($this->certificates == NULL) {
            $this->certificates = $this->service->get('UserCertificateTable')->getByUserId($this->user->usr_id);
        }
        return $this->certificates;
    }

    public function getSkill() {
        if ($this->skills == NULL) {
            $this->skills = $this->service->get('UserSkillTable')->fetchByUser($this->user->usr_id);
        }
        return $this->skills;
    }

    public function getIdea() {
        if ($this->ideas == NULL) {
            $this->ideas = $this->service->get('IdeaTable')->fetchAllByUserId($this->user->usr_id);
        }
        return $this->ideas;
    }

    public function getProject() {
        if ($this->emails == NULL) {
            $this->emails = $this->service->get('ProjectTable')->fetchCreated($this->user->usr_id);
        }
        return $this->emails;
    }

    public function getMainLang() {
        if ($this->mainLang == NULL) {
            $this->mainLang = $this->service->get('geoLangTable')->getById($this->user->usr_lang);
        }
        return $this->mainLang;
    }

    /**
     * Access user-related property
     * 
     * @param string $name
     * @return string
     */
    public function __get($name) {
        if (property_exists($this->user, $name)) {
            return $this->user->$name;
        }

        if (property_exists($this->getMainLang(), $name)) {
            return $this->mainLang->$name;
        }
        throw new \Exception("$name  property does not exist.");
    }

}
