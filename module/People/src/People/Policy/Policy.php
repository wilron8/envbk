<?php

/**
 * Description of Policy
 *
 * @author kimsreng
 */

namespace People\Policy;

class Policy {
    
    /**
     * Number of days to suspend a user
     */
    const SUSPEND_DURATION=30;

    protected $userTable;

    public function __construct($userTable) {
        $this->userTable = $userTable;
    }

    protected function getUser($id) {
        return $this->userTable->getById($id);
    }

    /**
     * 
     * @param integer|User $user
     */
    public function canReceiveMessage($user) {
        if (!is_object($user)) {
            //in case an id is passed
            $user = $this->getUser($user);
        }

        if ($user->usr_isSuspended == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $user
     */
    public function canFollow($user) {
        
    }

    /**
     * 
     * @param type $user
     */
    public function canBeFollowed($user) {
        if (!is_object($user)) {
            //in case an id is passed
            $user = $this->getUser($user);
        }

        if ($user->usr_isSuspended == 0) {
            return true;
        } else {
            return false;
        }
    }

}
