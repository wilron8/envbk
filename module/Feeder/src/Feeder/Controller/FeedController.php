<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FeedController
 *
 * @author kimsreng
 */

namespace Feeder\Controller;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\ViewModel;
use Feeder\DbTable\NotifyTable;

class FeedController extends AuthenticatedController {

    public function feedAction() {
        if ($this->request->isXmlHttpRequest()) {
            $this->viewType = self::JSON_MODEL;
        }
        $this->initView();
        $notifications = $this->getNtfyTable()->getAll($this->userId);
        $this->view->notifications = $notifications;
        return $this->view;
    }

    public function feedLinkAction() {
        $feed = $this->getNtfyTable()->getById($this->params()->fromRoute('id'));
        if ($feed) {
            $this->getNtfyTable()->markAsRead($feed);
        }
       return $this->redirect()->toUrl($this->get('Util')->helpString()->decode($this->params()->fromRoute('url')));
    }

    public function newsFeedAction() {
        $feed = $this->get('Feed')->getFeed($this->userId, 32, 0);
        return new ViewModel(['feed' => $feed]);
    }

    /**
     * @return NotifyTable
     */
    private function getNtfyTable() {
        return $this->get('NotifyTable');
    }

}
