<?php

/**
 * Description of ProjectRoleController
 *
 * @author kimsreng
 */

namespace ProjectManagement\Controller;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Where;

class ProjectRoleController extends AuthenticatedController {

    public function getAction() {
       
        $where = new Where();
        $where->equalTo('pRole_isVisible', 1)
                ->equalTo('pRole_lang', $this->getUser()->usr_lang);
        if($this->params()->fromQuery('query')){
            $where->like('pRole_title', "%".$this->params()->fromQuery('query')."%");
        } 
        
        $roles = $this->getTable()->fetchAll($where);
        return new JsonModel(['data' => $roles->toArray()]);
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectRolesTable
     */
    public function getTable() {
        return $this->get('ProjectRolesTable');
    }

}
