<?php

/**
 * Description of ProjectRolesTable
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbTable;

use Common\DbTable\AbstractTable;
use ProjectManagement\Model\DbEntity\ProjectRoles;

class ProjectRolesTable extends AbstractTable {

    protected $table = "projectRoles";
    protected $primaryKey = "pRole_id";

    /**
     * Check if a title is exist, if not a new record is inserted and return the last id
     * 
     * @param string $roleText
     * @return integer last insert id
     */
    public function getId($roleText, $roleLang) {

        $row = $this->fetchOne(['pRole_title' => $roleText]);
        if ($row) {
            return $row['pRole_id'];
        }
        return $this->createFromTitle($roleText, $roleLang);
    }

    /**
     * Create a new record from title
     * 
     * @param string $title
     * @return integer
     */
    public function createFromTitle($title, $roleLang) {
        $role = new ProjectRoles();
        $role->pRole_title = $title;
        $role->pRole_lang = $roleLang;
        $role->pRole_timeStamp = date("Y-m-d H:i:s");
        $role->pRole_isVisible = 1;
        return $this->insert($role);
    }
}
