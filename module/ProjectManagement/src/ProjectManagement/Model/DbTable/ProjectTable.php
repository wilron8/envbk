<?php

/**
 * Description of ProjectTable
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbTable;

use ProjectManagement\Model\DbEntity\Project;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ProjectTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
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
        $sql = new Sql($this->tableGateway->getAdapter());
        $projects = $sql->select();
        $projects->columns(array('proj_id', 'proj_title', 'proj_img', 'proj_descript', 'proj_hitCnt', 'proj_timeStamp', 'proj_isClosed', 'proj_isVisible'));
        $projects->from(array('p' => 'project'));
        $projects->join(array('pm' => 'projectMember'), 'p.proj_id=pm.pMem_projectID', array('pMem_isPM', 'pMem_memberID'));
        $projects->join('user', 'pm.pMem_memberID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $projects->where(array('pm.pMem_isPM' => 1, 'proj_isVisible' => 1));
        $projects->order('proj_timeStamp DESC');
        $paginatorAdapter = new DbSelect(
                $projects, $this->tableGateway->getAdapter()
        );
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function fetchByIdea($ideaId) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $projects = $sql->select();
        $projects->columns(array('proj_id', 'proj_title', 'proj_img'));
        $projects->from(array('p' => 'project'));
        $projects->where(array('proj_srcIdea' => $ideaId, 'proj_isVisible' => 1));

        $statement = $sql->prepareStatementForSqlObject($projects);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $result = $resultSet->initialize($result);
        return $result;
    }

    public function isIdeaStarted($idea_id) {
        $rowset = $this->tableGateway->select(array('proj_srcIdea' => (int) $idea_id));
        $row = $rowset->current();
        if ($row) {
            return $row->proj_id;
        } else {
            return false;
        }
    }

    /**
     * Fetch all projects created by a user
     * 
     * @param type $userID
     * @param type $paginated
     * @return \Zend\Paginator\Paginator
     */
    public function fetchCreated($userID, $paginated = false) {
        $userID = (int) $userID;

        $sql = new Sql($this->tableGateway->getAdapter());
        $projects = $sql->select();
        $projects->columns(array(new \Zend\Db\Sql\Expression('MIN(proj_id) as proj_id'), 'proj_title', 'proj_img', 'proj_descript', 'proj_progress', 'proj_hitCnt', 'proj_timeStamp', 'proj_isClosed', 'proj_isVisible'));
        $projects->from(array('p' => 'project'));
        $projects->join(array('pm' => 'projectMember'), 'p.proj_id=pm.pMem_projectID', array('pMem_isPM', 'pMem_memberID'));
        $projects->join('user', 'pm.pMem_memberID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $projects->where(array('pm.pMem_memberID' => $userID, 'proj_isVisible' => 1,'pMem_isPM'=>1,'pMem_approvedState'=>1));
        $projects->group('proj_id');
        // select DISTINCT proj_title from project, projectMember where project.proj_id = projectMember.pMem_projectID and projectMember.pMem_memberID = $userID

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $projects, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($projects);
        $result = $statement->execute();
        return $result;
    }
    
    /**
     * Fetch all projects joined and created by a user
     * 
     * @param type $userID
     * @param type $paginated
     * @return \Zend\Paginator\Paginator
     */
    public function fetchCreatedJoined($userID, $paginated = false) {
        $userID = (int) $userID;

        $sql = new Sql($this->tableGateway->getAdapter());
        $projects = $sql->select();
        $projects->columns(array(new \Zend\Db\Sql\Expression('MIN(proj_id) as proj_id'), 'proj_title', 'proj_img', 'proj_descript', 'proj_progress', 'proj_hitCnt', 'proj_timeStamp', 'proj_isClosed', 'proj_isVisible'));
        $projects->from(array('p' => 'project'));
        $projects->join(array('pm' => 'projectMember'), 'p.proj_id=pm.pMem_projectID', array('pMem_isPM', 'pMem_memberID'));
        $projects->join('user', 'pm.pMem_memberID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $projects->where(array('pm.pMem_memberID' => $userID, 'proj_isVisible' => 1,'pMem_approvedState'=>1));
        $projects->group('proj_id');
        // select DISTINCT proj_title from project, projectMember where project.proj_id = projectMember.pMem_projectID and projectMember.pMem_memberID = $userID

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $projects, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($projects);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Get a project object by id
     * 
     * @param integer $proj_id
     * @return Project
     */
    public function getById($proj_id) {
        $rowset = $this->tableGateway->select(array('proj_id' => (int) $proj_id, 'proj_isVisible' => 1));
        $row = $rowset->current();
        return $row;
    }

    public function insert(Project $project) {
        $this->tableGateway->insert($project->getArrayCopy());
        return $this->tableGateway->lastInsertValue;
    }

    public function update(Project $project) {
        $project->proj_lastModified = date('Y-m-d H:i:s');
        $project->proj_memCnt = max($project->proj_memCnt, 0);
        $project->proj_hitCnt = max($project->proj_hitCnt, 0);
        $this->tableGateway->update($project->getArrayCopy(), array('proj_id' => $project->proj_id));
    }

    public function delete($proj_id) {
        $this->tableGateway->update(array('proj_isVisible' => 0), array('proj_id' => (int) $proj_id));
    }
    
    /**
     * Get all in-progress projectId which are solely owned by a user
     * 
     * @param type $userId
     * @return type
     */
    public function getAbsolutelyOwned($userId) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT pMem_projectID FROM projectMember "
                . "WHERE pMem_memberID=? AND pMem_isPM=1 AND proj_isClosed = 0 "
                . "AND pMem_projectID IN (SELECT pMem_projectID from projectMember WHERE pMem_isPM=1 GROUP BY pMem_projectID HAVING COUNT(*)=1)";
        $result = $adapter->query($sql,array($userId));
        return $result;
    }

}
