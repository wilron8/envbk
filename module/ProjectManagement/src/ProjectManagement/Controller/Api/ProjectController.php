<?php

/**
 * Description of ProjectController
 *
 * @author kimsreng
 */

namespace ProjectManagement\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;

class ProjectController extends AuthenticatedController {

    public function getMemberAction() {
        $id = (int) $this->params()->fromQuery('id');
        if ($id) {
            $users = $this->getProjectMemberTable()->fetchMembers($id)->toArray();
            for ($i = 0; $i < count($users); $i++) {
                if ($users[$i]['usr_displayName'] == '') {
                    $users[$i]['usr_displayName'] = $users[$i]['usr_fName'] . " " . ($users[$i]['usr_mName'] != "" ? $users[$i]['usr_mName'] . " " : "") . $users[$i]['usr_lName'];
                }
                $users[$i]['usr_icon'] = $this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildUserRoutePath($users[$i]['usr_id'], \DocumentManager\Model\ResourceType::ICON, $users[$i]['usr_icon'])));
            }

            return new JsonModel(array(
                'data' => $users,
            ));
        }
        die();//exit if no id is supplied
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectTable
     */
    private function getProjectTable() {
        return $this->getServiceLocator()->get('ProjectTable');
    }

    /**
     * @return \ProjectManagement\Model\DbTable\ProjectMemberTable
     */
    private function getProjectMemberTable() {
        return $this->getServiceLocator()->get('ProjectMemberTable');
    }
    
    /**
     * 
     * @return \DocumentManager\Model\PathManager
     */
    protected function getPathManager() {
        return $this->get('PathManager');
    }

}
