<?php

/**
 * Description of LangController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Users\Model\DbEntity\UserLang;
use Zend\View\Model\JsonModel;

class LangController extends AuthenticatedController {

    protected $viewType = self::JSON_MODEL;

    public function getAction() {
        $langs = $this->getTable()->getByUserId($this->userId, array('uLang_id'))->toArray();
        return new JsonModel(array(
            'data' => $langs
        ));
    }

    public function createAction() {
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost()) {
            $langId = (int) $this->params()->fromPost('lang');
            if ($this->getTable()->getByLang($langId)) {
                $this->view->msg = $this->translate("This language already exists.");
            } else {
                $lang = new UserLang();
                $lang->uLang_userID = $this->userId;
                $lang->uLang_lang = $langId;
                $this->getTable()->insert($lang);
                $this->view->success = true;
            }
        }
        return $this->view;
    }

    public function deleteAction() {
        if ($this->request->isPost()) {
            $langId = (int) $this->params()->fromPost('uLang_id');
            if ($langId) {
                $lang = $this->getTable()->getById($langId);
                if (!$lang || ($lang->uLang_userID != $this->userId)) {
                    return new JsonModel(array(
                        'success' => false
                    ));
                }
                $this->getTable()->delete($langId);
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
     * @return \Users\Model\DbTable\UserLangTable
     */
    protected function getTable() {
        return $this->get('UserLangTable');
    }

}
