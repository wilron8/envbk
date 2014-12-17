<?php

/**
 * Description of SkillController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use People\Form\Filter\SkillFilter;
use People\Model\DbEntity\UserSkill;
use People\Model\DbEntity\SkillTag;
use Common\Util\StringHelper;

class SkillController extends AuthenticatedController {

    protected $viewType = self::JSON_MODEL;

    public function getAllAction() {
        $skills = $this->getSkillTable()->fetchAll(null, array('stag_id','stag_text'))->toArray();
        return new JsonModel(array(
            'data' => $skills
        ));
    }

    public function getAction() {
        $skills = $this->getTable()->fetchByUser($this->userId, array('uSkll_id'))->toArray();
        return new JsonModel(array(
            'data' => $skills
        ));
    }

    /**
     * action for users to add skill
     */
    public function createAction() {
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost() && $this->params()->fromPost('stag_text')) {
            $stag_text = StringHelper::toCamel(StringHelper::sanitize($this->params()->fromPost('stag_text')));
            $skill = new SkillTag();
            $skill->stag_text = $stag_text;
            $skill->stag_timeStamp = date('Y-m-d H:i:s');
            $skill->stag_id = $this->getSkillTable()->insert($skill);
            $this->view->data = $skill->output('json');
            $this->view->success = true;
        }

        return $this->view;
    }

    public function deleteAction() {
        if ($this->request->isPost()) {
            $uSkll_id = (int) $this->params()->fromPost('uSkll_id');
            if ($uSkll_id) {
                $Skill = $this->getTable()->getById($uSkll_id);
                if (!$Skill || ($Skill->uSkll_userID != $this->userId)) {
                    return new JsonModel(array(
                        'success' => false
                    ));
                }
                $this->getTable()->delete($uSkll_id);
                return new JsonModel(array(
                    'success' => true
                ));
            } else {
                return new JsonModel(array(
                    'success' => false
                ));
            }
        }
        die('Only Post request is allowed.');
    }

    /**
     * 
     * @return \People\Model\DbTable\UserSkillTable
     */
    public function getTable() {
        return $this->get('UserSkillTable');
    }

    /**
     * 
     * @return \People\Model\DbTable\SkillTagTable
     */
    public function getSkillTable() {
        return $this->get('SkillTagTable');
    }

}
