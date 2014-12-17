<?php

/**
 * Hold all policy configuration related to project management
 *
 * @author kimsreng
 */

namespace ProjectManagement\Policy;

class Policy {

    /**
     * @var \ProjectManagement\Model\DbTable\ProjectTable 
     */
    protected $projectTb;

    /**
     * @var \ProjectManagement\Model\DbTable\ProjectMemberTable 
     */
    protected $memberTb;

    public function __construct($projectTable, $projecMemberTable) {
        $this->projectTb = $projectTable;
        $this->memberTb = $projecMemberTable;
    }
    
    public function isPublic($project){
        
    }
    /**
     * Check if a member is allowed to write to wall
     * 
     * @param integer|object|array $user
     * @return boolean 
     */
    public function canWriteWall($user, $projectId = NULL) {
        if (is_object($user)) {
            if ($user->pMem_wallWrite == 1) {
                return true;
            }
            return false;
        }
        if (is_array($user)) {
            if ($user['pMem_wallWrite'] == 1) {
                return true;
            }
            return false;
        }
        if ($user && $projectId != NULL) {
            $mem = $this->memberTb->fetchMembership($user, $projectId);
            if ($mem && $mem->pMem_wallWrite == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a member is allowed to access toolbox
     * 
     * @param integer|object|array $user
     * @return boolean 
     */
    public function hasToolboxAccess($user, $projectId = NULL) {
        if (is_object($user)) {
            if ($user->pMem_toolBoxAccess == 1) {
                return true;
            }
            return false;
        }
        if (is_array($user)) {
            if ($user['pMem_toolBoxAccess'] == 1) {
                return true;
            }
            return false;
        }
        if ($user && $projectId != NULL) {
            $mem = $this->memberTb->fetchMembership($user, $projectId);
            if ($mem && $mem->pMem_toolBoxAccess == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a member is allowed to access Doc
     * 
     * @param integer|object|array $user
     * @return boolean 
     */
    public function hasDocMgrAccess($user, $projectId = NULL) {
        if (is_object($user)) {
            if ($user->pMem_docManagerAccess == 1) {
                return true;
            }
            return false;
        }
        if (is_array($user)) {
            if ($user['pMem_docManagerAccess'] == 1) {
                return true;
            }
            return false;
        }
        if ($user && $projectId != NULL) {
            $mem = $this->memberTb->fetchMembership($user, $projectId);
            if ($mem && $mem->pMem_docManagerAccess == 1) {
                return true;
            }
        }
        return false;
    }

}
