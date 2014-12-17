<?php

/**
 * Description of PublicFeedController
 *
 * @author kimsreng
 */

namespace Feeder\Controller;

use Common\Mvc\Controller\BaseController;

class PublicFeedController extends BaseController {

    /**
     * @var \IdeaManagement\Model\DbTable\IdeaTable 
     */
    protected $idea;

    public function __construct($idea) {
        $this->idea = $idea;
    }

    public function discoverAction() {

        $this->initView();
        $this->view->hotIdeas = $this->idea->getHotIdeas();
        // $this->view->popularIdeas = $this->idea->getPopularIdeas();
        return $this->view;
    }

}
