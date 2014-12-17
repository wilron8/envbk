<?php

/**
 * Description of ProjectMemberTable
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbTable;

use ProjectManagement\Model\DbEntity\ProjectMember;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class ProjectMemberTable {

    protected $tableGateway;
    protected $sm;

    public function __construct(TableGateway $tableGateway, $sm) {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
    }

    /**
     * Select one row
     * 
     * @param mixed $where
     * @return entity object
     */
    public function fetchOne($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select()->current();
        } else {
            return $this->tableGateway->select($where)->current();
        }
    }

    public function fetchAll() {
        $rowset = $this->tableGateway->select();
        return $rowset;
    }

    /**
     * 
     * @param type $where
     * @return result set
     */
    public function select($where = NULL) {
        if ($where == NULL) {
            return $this->tableGateway->select();
        } else {
            return $this->tableGateway->select($where);
        }
    }

    public function getById($pMem_id) {
        $rowset = $this->tableGateway->select(array('pMem_id' => (int) $pMem_id));
        $row = $rowset->current();
        return $row;
    }

    public function insert(ProjectMember $projectMember) {
        $this->tableGateway->insert($projectMember->getArrayCopy());
        return $this->tableGateway->lastInsertValue;
    }

    public function update(ProjectMember $projectMember) {
        $this->tableGateway->update($projectMember->getArrayCopy(), array('pMem_id' => $projectMember->pMem_id));
    }

    public function delete($pMem_id) {
        $this->tableGateway->delete(array('pMem_id' => (int) $pMem_id));
    }

    /**
     * Delete with conditions
     * 
     * @param mixed $where
     * @return Boolean
     */
    public function deleteWhere($where) {
        return $this->tableGateway->delete($where);
    }

    public function fetchMembers($projectId, $columns = NULL) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('projectMember');
        if ($columns !== NULL && is_array($columns)) {
            $select->columns($columns);
        }
        $select->join('user', 'user.usr_id=projectMember.pMem_memberID', array('usr_id', 'usr_icon', 'usr_displayName', 'usr_fName', 'usr_mName', 'usr_lName'));
        $select->where(array('pMem_projectID' => $projectId, 'pMem_approvedState' => 1));
        $select->order('pMem_isPM DESC');
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    /**
     * Get list of members as array to fill multi dropdown list
     * 
     * @param integer $projectId
     * @return array
     */
    public function fetchMembersAsArray($projectId) {
        $members = $this->fetchMembers($projectId);
        $list = [];
        foreach ($members as $mem) {
            $list[] = (int) $mem['usr_id'];
        }
        return $list;
    }

    /**
     * Method to add new members or delete old members
     * 
     * @param array $members
     * @param integer $projectId
     */
    public function updateMembers(Array $members, $projectId) {
        $previous_members = $this->fetchMembersAsArray($projectId);

        //check if there is no change in members
        if ($previous_members == $members) {
            return;
        }
        // access project table to increase/decrease memCnt
        $project = $this->sm->get('ProjectTable')->getById($projectId);

        //delete if there is any deletion
        $delete = array_diff($previous_members, $members);
        if (count($delete) > 0) {
            foreach ($delete as $id) {
                $where = new Where();
                $where->equalTo('pMem_memberID', $id)
                        ->equalTo('pMem_projectID', $projectId)
                        ->notEqualTo('pMem_isPM', 1); //disable deletion of Project Manager
                if ($this->tableGateway->delete($where)) {
                    $project->proj_memCnt--;
                }
            }
        }
        // add new members if any
        $new = array_diff($members, $previous_members);
        if (count($new) > 0) {
            foreach ($new as $n) {
                $member = new ProjectMember();
                $member->pMem_memberID = $n;
                $member->pMem_projectID = $projectId;
                $member->pMem_isPM = 0;
                $member->pMem_dateTime = date('Y-m-d');
                $member->pMem_approvedState = 1;
                $this->insert($member);
                $project->proj_memCnt++;
            }
        }
        //update project table
        $this->sm->get('ProjectTable')->update($project);
    }

    /**
     * Check if a user is a member of a project
     * 
     * @param integer $userId
     * @param integer $projectId
     * @return boolean
     */
    public function isMember($userId, $projectId) {
        $rowset = $this->tableGateway->select(array('pMem_memberID' => $userId, 'pMem_projectID' => $projectId, 'pMem_approvedState' => 1));
        $row = $rowset->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function hasJoined($userId, $projectId) {
        $rowset = $this->tableGateway->select(array('pMem_memberID' => $userId, 'pMem_projectID' => $projectId));
        $row = $rowset->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a user is the owner of a project
     * 
     * @param integer $userId
     * @param integer $projectId
     * @return boolean
     */
    public function isOwner($userId, $projectId) {
        $rowset = $this->tableGateway->select(array('pMem_memberID' => $userId, 'pMem_projectID' => $projectId, 'pMem_isPM' => 1));
        $row = $rowset->current();
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a project manager information by a project ID
     * 
     * @param integer $projectId
     * @return Array Object
     */
    public function getPM($projectId) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('projectMember');
        $select->columns(array());
        $select->join('user', 'user.usr_id=projectMember.pMem_memberID');
        $select->where(array('pMem_projectID' => $projectId, 'pMem_isPM' => 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }
    
    /**
     * Get all project managers
     * 
     * @param type $projectId
     * @return result set
     */
    public function allPM($projectId) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('projectMember');
        $select->columns(array());
        $select->join('user', 'user.usr_id=projectMember.pMem_memberID');
        $select->where(array('pMem_projectID' => $projectId, 'pMem_isPM' => 1));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Fetch project member row by user id and project id
     * 
     * @param integer $userId
     * @param integer $projectId
     * @return row
     */
    public function fetchMembership($userId, $projectId) {
        return $this->fetchOne(['pMem_memberID' => $userId, 'pMem_projectID' => $projectId]);
    }

    /**
     * Create a project manager
     * 
     * @param integer $projectId
     * @param integer $managerId
     * @param array $data
     */
    public function createManager($projectId, $managerId, $data = NULL) {
        $member = new ProjectMember();
        $member->pMem_memberID = $managerId;
        $member->pMem_projectID = $projectId;
        $member->pMem_isPM = 1;
        $member->pMem_dateTime = date('Y-m-d H:i:s');
        $member->pMem_approvedState = 1;
        $member->pMem_docManagerAccess = 1;
        $member->pMem_toolBoxAccess = 1;
        if ($data != NULL) {
            $member->exchangeArray($data);
        }

        $this->insert($member);
    }

}
