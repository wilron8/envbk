<?php

/**
 * Description of FollowPeopleTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use People\Model\DbEntity\FollowPeople;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;

class FollowPeopleTable {

    protected $tableGateway;
    protected $sm;
    protected $adapter;

    public function __construct(TableGateway $tableGateway, ServiceManager $sm) {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
        $this->adapter = $this->sm->get('Zend\Db\Adapter\Adapter');
    }

    /**
     * Fetch all people who are following or being followed by a person
     * @param type $userID
     * @param type $columns
     */
    public function fetchFiFd($userID, $columns = array('*')) {
        $userID = (int) $userID;
        // select followee
        $sql = new Sql($this->adapter);
        $following = $sql->select();
        $following->from(array('f' => 'followPeople'))
                ->columns(array(new \Zend\Db\Sql\Expression('1 as f')))
                ->join(array('u' => 'user'), 'u.usr_id=f.fp_followeeID', $columns)
                ->where(array('f.fp_followerID' => $userID,'u.usr_isSuspended=0'))
                ->order('usr_displayName');

        //select follower
        $follower = $sql->select();
        $follower->from(array('f' => 'followPeople'))
                ->columns(array(new \Zend\Db\Sql\Expression('2 as f')))
                ->join(array('u' => 'user'), 'u.usr_id=f.fp_followerID', $columns)
                ->where(array('f.fp_followeeID' => $userID))
                ->where("f.fp_followerID NOT IN (SELECT fp_followeeID FROM followPeople WHERE fp_followerID={$userID})") // this line is to prevent people repetition
                ->order('usr_displayName');
        //UNION the results
        $follower->combine($following, "UNION DISTINCT");

        // echo $follower->getSqlString(new \Zend\Db\Adapter\Platform\Mysql());die();
        $statement = $sql->prepareStatementForSqlObject($follower);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }

    public function fetchAllForUser($userID, $paginated = false) {
        $userID = (int) $userID;
        // select followee
        $sql = new Sql($this->adapter);
        $following = $sql->select();
        $following->from(array('f' => 'followPeople'));
        $following->join(array('u' => 'user'), 'u.usr_id=f.fp_followeeID');
        $following->where(array('f.fp_followerID' => $userID));

        //select follower
        $follower = $sql->select();
        $follower->from(array('f' => 'followPeople'));
        $follower->join(array('u' => 'user'), 'u.usr_id=f.fp_followerID');
        $follower->where(array('f.fp_followeeID' => $userID))
                ->where("f.fp_followerID NOT IN (SELECT fp_followeeID FROM followPeople WHERE fp_followerID={$userID})"); // this line is to prevent people repetition
        //UNION the results
        $follower->combine($following, "UNION DISTINCT");


        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $follower, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($follower);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }

    /**
     * Fetch all users whom a user is following
     * 
     * @param type $userId
     * @param type $paginated
     * @return \Zend\Paginator\Paginator
     */
    public function fetchFollowee($userId, $paginated = false) {
        $userID = (int) $userId;
        // select followee
        $sql = new Sql($this->adapter);
        $following = $sql->select();
        $following->from(array('f' => 'followPeople'));
        $following->join(array('u' => 'user'), 'u.usr_id=f.fp_followeeID');
        $following->where(array('f.fp_followerID' => $userID));

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $following, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($following);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Fetch all users who follow a user
     * 
     * @param integer $userId
     * @param boolean $paginated
     * @return \Zend\Paginator\Paginator
     */
    public function fetchFollower($userId, $paginated = false) {
        $userID = (int) $userId;
        //select follower
        $sql = new Sql($this->adapter);
        $follower = $sql->select();
        $follower->from(array('f' => 'followPeople'));
        $follower->join(array('u' => 'user'), 'u.usr_id=f.fp_followerID');
        $follower->where(array('f.fp_followeeID' => $userID));
        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $follower, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($follower);
        $result = $statement->execute();
        return $result;
    }

    public function getById($fp_id) {
        $rowset = $this->tableGateway->select(array('fp_id' => (int) $fp_id));
        $row = $rowset->current();
        return $row;
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    public function insert(FollowPeople $followPeople) {
        return $this->tableGateway->insert($followPeople->getArrayCopy());
    }

    public function update(FollowPeople $followPeople) {
        $this->tableGateway->update($followPeople->getArrayCopy(), array('fp_id' => $followPeople->fp_id));
    }

    public function delete($fp_id) {
        return $this->tableGateway->delete(array('fp_id' => (int) $fp_id));
    }

    /**
     * Function to follow a person
     * 
     * @param integer $followeeId
     * @param integer $followerId
     */
    public function follow($followeeId, $followerId) {
        $follow = new FollowPeople();
        $follow->fp_followeeID = (int) $followeeId;
        $follow->fp_followerID = (int) $followerId;
        $follow->fp_timeStamp = date('Y-m-d H:i:s');
        $this->insert($follow);
        $user = $this->sm->get('UserTable')->getById($followerId);
        $this->sm->get('NotifyManager')->followNotify($follow,$user);
        return true;
    }

    /**
     * Function to unfollow a person
     * 
     * @param integer $followeeId
     * @param integer $followerId
     */
    public function unfollow($followeeId, $followerId) {
        return $this->tableGateway->delete(array('fp_followeeId' => (int) $followeeId, 'fp_followerId' => (int) $followerId));
    }

    public function isFollow($userId) {
        $auth = $this->sm->get('AuthService');
        if (!$auth->hasIdentity()) {
            return false;
        }
        $currentUserId = $auth->getIdentity()->usr_id;
        $row = $this->tableGateway->select(array('fp_followeeID' => $userId, 'fp_followerID' => $currentUserId))->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function getCountFollowers($userID) {
        $sql = new Sql($this->adapter);
        $follower = $sql->select();
        $follower->columns(array(new \Zend\Db\Sql\Expression('COUNT(*) as count')));
        $follower->from(array('f' => 'followPeople'));
        $follower->where(array('f.fp_followeeID' => $userID));
        $statement = $sql->prepareStatementForSqlObject($follower);
        $result = $statement->execute();
        return $result->current()['count'];
    }

    public function getCountFollowees($userID) {
        $sql = new Sql($this->adapter);
        $following = $sql->select();
        $following->columns(array(new \Zend\Db\Sql\Expression('COUNT(*) as count')));
        $following->from(array('f' => 'followPeople'));
        $following->where(array('f.fp_followerID' => $userID));
        $statement = $sql->prepareStatementForSqlObject($following);
        $result = $statement->execute();
        return $result->current()['count'];
    }

    public function isFollowing($followeeId, $followerId) {
        $row = $this->tableGateway->select(array('fp_followeeID' => $followeeId, 'fp_followerID' => $followerId))->current();
        return ($row) ? true : false;
    }

}
