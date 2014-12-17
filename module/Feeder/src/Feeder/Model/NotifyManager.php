<?php

/**
 * Description of NotifyManager
 *
 * @author kimsreng
 */

namespace Feeder\Model;

use Feeder\DbTable\NotifyTable;
use Feeder\DbEntity\Notify;
use People\Model\DbEntity\FollowPeople;

class NotifyManager {

    protected $notifyTable = NULL;
    protected $viewHelper = NULL;
    protected $sm = NULL;

    public function __construct(NotifyTable $notifyTable, $viewHelper, $sm) {
        $this->notifyTable = $notifyTable;
        $this->viewHelper = $viewHelper;
        $this->sm = $sm;
    }

    public function followNotify(FollowPeople $followPeople, $user) {
        $url = ['route' => 'people/profile', 'params' => ['id' => $followPeople->fp_followerID], 'query' => []];
        $this->createNtfy($url, $followPeople->fp_followeeID, $user, 'has followed you.');
    }

    public function messageNotify($message, $userId, $user) {
        $url = ['route' => 'message/action-id', 'params' => ['action' => 'view', 'id' => $message->msg_id], 'query' => []];
        $this->createNtfy($url, $userId, $user, 'has sent you a message.');
    }

    public function ideaModifiedNotify($idea, $user) {
        $ideaHelper = $this->viewHelper->get('ideaHelper');
        $url = ['route' => 'idea/action-id', 'params' => ['action' => 'view', 'id' => $idea->idea_id], 'query' => []];
        $followers = $this->sm->get('FollowIdeaTable')->allFollower($idea->idea_id);
        $msg = "has modified the idea " . $ideaHelper()->getIcon($idea) . ' ' . $idea->idea_title;
        foreach ($followers as $f) {
            if ($f->fi_userID == $user->usr_id) {
                continue;
            }
            $this->createNtfy($url, $f->fi_userID, $user, $msg);
        }
    }

    public function followIdeaNotify($idea, $user) {
        $ideaHelper = $this->viewHelper->get('ideaHelper');
        $url = ['route' => 'idea/action-id', 'params' => ['action' => 'view', 'id' => $idea->idea_id], 'query' => []];
        $msg = "has followed the idea " . $ideaHelper()->getIcon($idea) . ' ' . $idea->idea_title;

        $this->createNtfy($url, $idea->idea_originator, $user, $msg);
    }

    public function referenceIdeaNotify($idea, $newIdea, $user) {
        $ideaHelper = $this->viewHelper->get('ideaHelper');
        $url = ['route' => 'idea/action-id', 'params' => ['action' => 'view', 'id' => $newIdea->idea_id], 'query' => []];
        $msg = "has reference your idea " . $ideaHelper()->getIcon($idea) . ' ' . $idea->idea_title . ' originating ' . $ideaHelper()->getIcon($newIdea) . ' ' . $newIdea->idea_title;
        $this->createNtfy($url, $idea->idea_originator, $user, $msg);
    }

    public function joinProject($project, $user) {
        $projHelper = $this->viewHelper->get('projHelper');
        $url = ['route' => 'project/action-id', 'params' => ['action' => 'view', 'id' => $project->proj_id], 'query' => []];
        $msg = "has asked to join your project " . $projHelper()->getIcon($project) . ' ' . $project->proj_title;
        $pms = $this->sm->get('ProjectMemberTable')->allPM($project->proj_id);
        foreach ($pms as $pm) {
            $this->createNtfy($url, $pm['usr_id'], $user, $msg);
        }
    }

    public function commentProject($project, $wall, $user) {
        $projHelper = $this->viewHelper->get('projHelper');
        $url = ['route' => 'project/action-id', 'params' => ['action' => 'view', 'id' => $project->proj_id], 'query' => []];
        $msg = "has posted to wall of the project " . $projHelper()->getIcon($project) . ' ' . $project->proj_title . '<BR><BR>' . '"' . $wall->prjW_comment . '"';
        $members = $this->sm->get('ProjectMemberTable')->fetchMembers($project->proj_id);
        foreach ($members as $m) {
            if ($m['usr_id'] === $user->usr_id) {
                continue;
            }
            $this->createNtfy($url, $m['usr_id'], $user, $msg);
        }
    }

    public function memberProject($project, $member, $user) {
        $projHelper = $this->viewHelper->get('projHelper');
        $usrHelper = $this->viewHelper->get('usrHelper');
        $displayName = $this->viewHelper->get('displayName');
        $url = ['route' => 'project/action-id', 'params' => ['action' => 'view', 'id' => $project->proj_id], 'query' => []];
        $msg = "has added " . $usrHelper()->getIcon($member) . " " . $displayName($member) . " to the project " . $projHelper()->getIcon($project) . ' ' . $project->proj_title;
        $members = $this->sm->get('ProjectMemberTable')->fetchMembers($project->proj_id);
        foreach ($members as $m) {
            if ($m['usr_id'] === $user->usr_id || $m['usr_id'] == $member->usr_id) {
                continue;
            }
            $this->createNtfy($url, $m['usr_id'], $user, $msg);
        }
    }
    
    public function modifiedProject($project, $user) {
        $projHelper = $this->viewHelper->get('projHelper');
        $url = ['route' => 'project/action-id', 'params' => ['action' => 'view', 'id' => $project->proj_id], 'query' => []];
        $msg = "has updated the project " . $projHelper()->getIcon($project) . ' ' . $project->proj_title;
        $members = $this->sm->get('ProjectMemberTable')->fetchMembers($project->proj_id);
        foreach ($members as $m) {
            if ($m['usr_id'] === $user->usr_id) {
                continue;
            }
            $this->createNtfy($url, $m['usr_id'], $user, $msg);
        }
    }

    /**
     * Basic function used to create a notify item
     * 
     * @param array $url
     * @param integer $userId
     * @param object $user
     * @param string $msg
     */
    protected function createNtfy($url, $userId, $user, $msg) {
        $urlHelper = $this->viewHelper->get('url');
        $usrHelper = $this->viewHelper->get('usrHelper');
        $displayName = $this->viewHelper->get('displayName');
        $ntfy = new Notify();
        $ntfy->ntfy_timeStamp = date('Y-m-d H:i:s');
        $ntfy->ntfy_userID = $userId;
        $ntfy->ntfy_URL = $urlHelper($url['route'], $url['params'], $url['query']);
        $ntfy->ntfy_body = $usrHelper()->getIcon($user) . " " . $displayName($user) . ' ' . $msg;
        $this->notifyTable->insert($ntfy);
    }

}
