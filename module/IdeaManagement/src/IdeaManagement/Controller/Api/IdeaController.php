<?php

/**
 * Description of IdeaController
 *
 * @author kimsreng
 */

namespace IdeaManagement\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use DocumentManager\Model\ResourceType as Resource;

class IdeaController extends AuthenticatedController {

    public function getAction() {
        $data = $this->getTable()->fetchAll(null, array('idea_id', 'idea_title', 'idea_img'))->toArray();
        //turn idea_img field into url for img src
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['idea_img'] = ($data[$i]['idea_img']==="" || $data[$i]['idea_img']===NULL)?"/images/lightBulb.svg":$this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildIdeaRoutePath($data[$i]['idea_id'], Resource::ICON, $data[$i]['idea_img'])));
            $data[$i]['url'] = $this->url()->fromRoute('idea/action-id', array('action' => 'view', 'id' => $data[$i]['idea_id']));
        }

        return new JsonModel(array(
            'data' => $data
        ));
    }

    public function getCategoryAction() {
        return new JsonModel(array(
            'data' => $this->getCatTable()->fetchAll(null, array('cat_id', 'cat_text'))->toArray()
        ));
    }

    public function getIdeaRefAction() {
        $id = (int) $this->params()->fromQuery('id');
        if ($id) {

            $data = $this->getRefTable()->getByNewIdea($id)->toArray();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['idea_img'] = ($data[$i]['idea_img']==="" || $data[$i]['idea_img']===NULL)?"/images/lightBulb.svg":$this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildIdeaRoutePath($data[$i]['idea_id'], Resource::ICON, $data[$i]['idea_img'])));
                $data[$i]['url'] = $this->url()->fromRoute('idea/action-id', array('action' => 'view', 'id' => $data[$i]['idea_id']));
            }
            return new JsonModel(array(
                'data' => $data,
            ));
        }
        die();
    }

    public function getProjectAction() {
        $id = (int) $this->params()->fromQuery('id');
        if ($id) {
            $projects = $this->getProjTable()->fetchByIdea($id)->toArray();
            for ($i = 0; $i < count($projects); $i++) {
                $projects[$i]['proj_img'] = ($projects[$i]['proj_img']==="" || $projects[$i]['proj_img']===NULL)?"/images/Project.svg":$this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildProjectRoutePath($projects[$i]['proj_id'], Resource::ICON, $projects[$i]['proj_img'])));
                $projects[$i]['url'] = $this->url()->fromRoute('project/action-id', array('action' => 'view', 'id' => $projects[$i]['proj_id']));
            }
            return new JsonModel(array(
                'data' => $projects
            ));
        }
        die();
    }
    
    public function getCommentAction(){
        $id = (int) $this->params()->fromQuery('id');
        if ($id) {
            $projects = $this->getProjTable()->fetchByIdea($id)->toArray();
            for ($i = 0; $i < count($projects); $i++) {
                $projects[$i]['proj_img'] = $this->url()->fromRoute('process-image', array('path' => $this->getPathManager()->buildProjectRoutePath($projects[$i]['proj_id'], Resource::ICON, $projects[$i]['proj_img'])));
                $projects[$i]['url'] = $this->url()->fromRoute('project/action-id', array('action' => 'view', 'id' => $projects[$i]['proj_id']));
            }
            return new JsonModel(array(
                'data' => $projects
            ));
        }
        die();
    }

    /**
     * 
     * @return \IdeaManagement\Model\DbTable\IdeaTable
     */
    protected function getTable() {
        return $this->get('IdeaTable');
    }

    /**
     * 
     * @return \IdeaManagement\Model\DbTable\IdeaRefTable
     */
    protected function getRefTable() {
        return $this->get('IdeaRefTable');
    }

    /**
     * 
     * @return \IdeaManagement\Model\DbTable\CategoryTable
     */
    protected function getCatTable() {
        return $this->get('CategoryTable');
    }

    /**
     * 
     * @return \ProjectManagement\Model\DbTable\ProjectTable
     */
    protected function getProjTable() {
        return $this->get('ProjectTable');
    }

    /**
     * 
     * @return \DocumentManager\Model\PathManager
     */
    public function getPathManager() {
        return $this->get('PathManager');
    }

}
