<?php

/**
 * Description of IdeaTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use IdeaManagement\Model\DbEntity\Idea;
use IdeaManagement\Model\DbEntity\IdeaRef;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class IdeaTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($where = NULL, $columns = NULL, $paginated = false) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('idea');

        if ($where !== NULL) {
            $select->where($where);
        }
        if ($columns !== NULL) {
            $select->columns($columns);
        }

        $select->order('idea_lastAccess DESC');

        if ($paginated) {
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Idea());
            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        $output = $resultSet->initialize($result);
        return $output;
    }

    public function fetchAllByUserId($userID, $paginated = false) {
        $userID = (int) $userID;
        // select owned-ideas
        $sql = new Sql($this->tableGateway->getAdapter());
        $ownIdea = $sql->select();
        $ownIdea->from(array('i' => 'idea'));
        $ownIdea->join('user', 'i.idea_originator=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $ownIdea->join('category', 'i.idea_categoryID=category.cat_id', array('cat_text'), Select::JOIN_LEFT);
        $ownIdea->where(array('i.idea_originator' => $userID, 'i.idea_isVisible' => 1));
        $ownIdea->order('idea_lastAccess DESC');
        //select followed-ideas
//        $followedIdea = $sql->select();
//        $followedIdea->from(array('i' => 'idea'));
//        $followedIdea->where("i.idea_id IN (SELECT fi_ideaID FROM followIdea WHERE fi_userID={$userID})");
        //UNION the results
//        $followedIdea->combine($ownIdea, "UNION DISTINCT");

        if ($paginated) {
            $paginatorAdapter = new DbSelect(
                    $ownIdea, $this->tableGateway->getAdapter()
            );
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        $statement = $sql->prepareStatementForSqlObject($ownIdea);
        $result = $statement->execute();
        return $result;
    }

    /**
     * 
     * @param integer $idea_id
     * @return \IdeaManagement\Model\DbEntity\Idea
     */
    public function getById($idea_id) {
        $id = (int) $idea_id;
        $rowset = $this->tableGateway->select(array('idea_id' => $id, 'idea_isVisible' => 1));
        $row = $rowset->current();
        return $row;
    }

    public function insert(Idea $idea, $parent_id = NULL) {
        if ($parent_id == NULL) {
            $idea->idea_nodeDepth = 0;
            $idea->idea_nodeParent = 0;
            $idea->idea_nodeLeft = 0;
            $idea->idea_nodeRight = 1;
        } else {
            $parent = $this->getById($parent_id);
            if ($parent->idea_nodeDepth == 0) {
                $root_parent = $parent->idea_id;
            } else {
                $root_parent = $parent->idea_nodeParent;
            }
            // update tree for new insertion
            $sqlleft = "UPDATE idea SET idea_nodeLeft=idea_nodeLeft+2 "
                    . "WHERE idea_nodeParent={$root_parent} "
                    . "AND idea_nodeLeft >= {$parent->idea_nodeRight};";
            $statement = $this->tableGateway->getAdapter()->query($sqlleft);
            $statement->execute();

            $sqlright = "UPDATE idea SET idea_nodeRight=idea_nodeRight+2 "
                    . "WHERE (idea_nodeParent={$root_parent} OR idea_id={$root_parent}) "
                    . "AND idea_nodeRight >= {$parent->idea_nodeRight};";
            $statement = $this->tableGateway->getAdapter()->query($sqlright);
            $statement->execute();

            //
            $idea->idea_nodeParent = $root_parent;
            $idea->idea_nodeLeft = $parent->idea_nodeRight;
            $idea->idea_nodeRight = $parent->idea_nodeRight + 1;
            $idea->idea_nodeDepth = $parent->idea_nodeDepth + 1;
        }
        $idea->idea_timeStamp = date('Y-m-d H:i:s');
        $idea->idea_lastModified = date('Y-m-d H:i:s');
        $idea->idea_lastAccess = date('Y-m-d H:i:s');
        $data = $idea->getArrayCopy();
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(Idea $idea) {

        //prevent hitCnt and followCnt from less than 0
        $idea->idea_hitCnt = max($idea->idea_hitCnt, 0);
        $idea->idea_followCnt = max($idea->idea_followCnt, 0);
        return $this->tableGateway->update($idea->getArrayCopy(), array('idea_id' => $idea->idea_id));
//        if ($immediateParentId == NULL) {
//            return;
//        }
//
//        $oldImmediate = $this->getImmediateParent($idea->idea_id);
//
//        //check if the immediate parent id has changed
//        if ($oldImmediate && ($oldImmediate['idea_id'] === $immediateParentId)) {
//            return;
//        }
//        //move from previous position
//        $this->removeOldPosition($idea);
//        //add node to new position
//        if ($immediateParentId == 0) {
//            $this->insertToNewPosition($idea);
//        } else {
//            $this->insertToNewPosition($idea, $this->getById($immediateParentId));
//        }
    }

    /**
     * Hide idea
     * 
     * @param integer $ideaId
     */
    public function hideIdea($ideaId) {
        if ($this->getById($ideaId)) {
            $this->tableGateway->update(array('idea_isVisible' => 0), array('idea_id' => $ideaId));
        } else {
            throw new \Exception('We cannot find this Idea.');
        }
    }

    public function delete($idea_id) {
        $this->tableGateway->delete(array('idea_id' => (int) $idea_id));
    }

    public function getSelectOptions() {
        $rows = $this->tableGateway->select();
        $options = array();
        foreach ($rows as $row) {
            $options[$row->idea_id] = $row->idea_title;
        }
        return $options;
    }

    public function getImmediateParent($ideaId) {
        $idea = $this->getById($ideaId);
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select('idea');
        $depth = $idea->idea_nodeDepth - 1;
        if ($idea->idea_nodeParent == 0) {
            return false;
        }
        $select->where("(idea_nodeLeft < {$idea->idea_nodeLeft})");
        $select->where("(idea_nodeRight > {$idea->idea_nodeRight})");
        if ($idea->idea_nodeDepth == 1) {
            $select->where("(idea_id = {$idea->idea_nodeParent})");
        } else {
            $select->where("(idea_nodeParent = {$idea->idea_nodeParent})");
        }
        $select->where("(idea_nodeDepth = {$depth})");
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result->current();
    }

    public function getEvolutionList(Idea $idea) {
        $sql = new Sql($this->tableGateway->getAdapter());

        $select = $sql->select('idea');
        $select->join('user', 'idea.idea_originator=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));

        //check if the idea is the single top node
        if ($idea->idea_nodeParent == 0 && $idea->idea_nodeRight == 1) {
            $select->where("idea_id ={$idea->idea_id}");
        } elseif ($idea->idea_nodeParent == 0 && $idea->idea_nodeRight > 1) {
            $bottomDepth = $idea->idea_nodeDepth + 3;
            $select->where("(idea_nodeLeft BETWEEN {$idea->idea_nodeLeft} AND {$idea->idea_nodeRight})");
            $select->where("(idea_nodeParent ={$idea->idea_id} OR idea_id={$idea->idea_id})");
            $select->where("idea_nodeDepth <={$bottomDepth}");
            $select->order(array('idea_nodeLeft', 'idea_nodeDepth'));
        } else {
            $bottomDepth = $idea->idea_nodeDepth + 3;
            $topDepth = ($idea->idea_nodeDepth - 3) >= 0 ? ($idea->idea_nodeDepth - 3) : 0;
            $select->where("(idea_nodeLeft BETWEEN {$idea->idea_nodeLeft} AND {$idea->idea_nodeRight} OR idea_nodeLeft <= {$idea->idea_nodeLeft} AND idea_nodeRight >={$idea->idea_nodeRight})");
            $select->where("(idea_nodeParent ={$idea->idea_nodeParent} OR idea_id={$idea->idea_nodeParent})");
            $select->where("(idea_nodeDepth BETWEEN {$topDepth} AND {$bottomDepth})");
            $select->order(array('idea_nodeLeft', 'idea_nodeDepth'));
        }

        //
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    /**
     * 
     * @param integer $ideaId
     * @param array $refList
     * @param \IdeaManagement\Model\DbTable\IdeaRefTable $refTable
     */
    public function updateReference($ideaId, $userID, array $refList, \IdeaManagement\Model\DbTable\IdeaRefTable $refTable, \IdeaManagement\Model\DbTable\IdeaCommentTable $commentTable) {
        $previous_refs = $refTable->fetchRefAsArray($ideaId);
        $refList = array_filter($refList);
        //check if there is no change in ref
        if ($previous_refs == $refList) {
            return;
        }
        //if there is any change in reference it is tracked by making a comment to the idea
        $sql = new Sql($this->tableGateway->getAdapter());
        if (count($previous_refs) == 0) {
            $previous_ref = "No reference";
        } else {
            $select = $sql->select()->from('ideaReference')->columns(array())
                            ->join(array('i' => 'idea'), 'i.idea_id=ideaReference.iRef_srcIdea', array('idea_title'))->where("iRef_newIdea IN (" . implode(',', $previous_refs) . ")");
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $title = [];
            foreach ($result as $value) {
                $title[] = $value['idea_title'];
            }
            $previous_ref = implode(', ', $title);
        }


        if (count($refList) == 0) {
            $new_ref = "No reference";
        } else {
            $new_ref_list = implode(',', $refList);
            $select = $sql->select()->from('ideaReference')->columns(array())
                            ->join(array('i' => 'idea'), 'i.idea_id=ideaReference.iRef_srcIdea', array('idea_title'))->where("iRef_newIdea IN (" . $new_ref_list . ")");
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $title = [];
            foreach ($result as $value) {
                $title[] = $value['idea_title'];
            }
            $new_ref = implode(', ', $title);
        }

        $comment = new \IdeaManagement\Model\DbEntity\IdeaComment();
        $comment->iComm_ideaId = $ideaId;
        $comment->iComm_timeStamp = date('Y-m-d H:i:s');
        $comment->iComm_userId = $userID;
        $comment->iComm_comment = "There is a reference change:($previous_ref)=>($new_ref).";
        $comment->iComm_readOnly = 1;
        $commentTable->addComment($comment);

        //delete if there is any deletion
        $delete = array_diff($previous_refs, $refList);
        if (count($delete) > 0) {
            foreach ($delete as $id) {
                $refTable->deleteByCon(array('iRef_newIdea' => $ideaId, 'iRef_srcIdea' => $id));
            }
        }
        // add new ref if any
        $new = array_diff($refList, $previous_refs);
        if (count($new) > 0) {
            foreach ($new as $n) {
                $ref = new IdeaRef();
                $ref->iRef_newIdea = $ideaId;
                $ref->iRef_srcIdea = $n;
                $refTable->insert($ref);
            }
        }
    }

    public function isRoot($ideaId) {
        $idea = $this->getById($ideaId);
        if ($idea->idea_nodeParent == 0) {
            return true;
        }
        return false;
    }

    public function removeOldPosition(\IdeaManagement\Model\DbEntity\Idea $idea) {
        // reset previous parent node level
        $width = $this->getListWidth($idea);
        // prepare parent node
        if ($idea->idea_nodeDepth == 0) {
            $root_parent = $idea->idea_id;
        } else {
            $root_parent = $idea->idea_nodeParent;
        }
        $sqlleft = "UPDATE idea SET idea_nodeLeft=idea_nodeLeft-{$width} "
                . "WHERE idea_nodeParent={$root_parent} "
                . "AND idea_nodeLeft >= {$idea->idea_nodeRight};";
        $statement = $this->tableGateway->getAdapter()->query($sqlleft);
        $statement->execute();

        $sqlright = "UPDATE idea SET idea_nodeRight=idea_nodeRight-$width "
                . "WHERE (idea_nodeParent={$root_parent} OR idea_id={$root_parent}) "
                . "AND idea_nodeRight >= {$idea->idea_nodeRight};";
        $statement = $this->tableGateway->getAdapter()->query($sqlright);
        $statement->execute();
    }

    public function insertToNewPosition(\IdeaManagement\Model\DbEntity\Idea $idea, \IdeaManagement\Model\DbEntity\Idea $parentIdea = NULL) {

        if ($parentIdea == NULL) {
            $difference = $idea->idea_nodeLeft;
            $depth = $idea->idea_nodeDepth;
            $root_parent = $idea->idea_id;
            $idea_parent = $idea->idea_id;
            $sql = "UPDATE idea SET idea_nodeLeft = idea_nodeLeft - {$difference}, "
                    . "idea_nodeRight = idea_nodeRight - {$difference}, "
                    . "idea_nodeParent={$root_parent}, "
                    . "idea_nodeDepth = idea_nodeDepth - {$depth} "
                    . "WHERE (idea_nodeLeft >= {$idea->idea_nodeLeft} AND idea_nodeRight <= {$idea->idea_nodeRight}) "
                    . "AND (idea_nodeParent={$idea_parent} OR idea_id={$idea_parent});"
                    . "UPDATE idea SET idea_nodeParent=0 WHERE idea_id={$idea->idea_id}";
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $statement->execute();
        }

        $width = $this->getListWidth($idea);
        // prepare parent node
        if ($parentIdea->idea_nodeDepth == 0) {
            $root_parent = $parentIdea->idea_id;
        } else {
            $root_parent = $parentIdea->idea_nodeParent;
        }
        $sqlleft = "UPDATE idea SET idea_nodeLeft=idea_nodeLeft+{$width} "
                . "WHERE idea_nodeParent={$root_parent} "
                . "AND idea_nodeLeft >= {$parentIdea->idea_nodeRight};";
        $statement = $this->tableGateway->getAdapter()->query($sqlleft);
        $statement->execute();

        $sqlright = "UPDATE idea SET idea_nodeRight=idea_nodeRight+$width "
                . "WHERE (idea_nodeParent={$root_parent} OR idea_id={$root_parent}) "
                . "AND idea_nodeRight >= {$parentIdea->idea_nodeRight};";
        $statement = $this->tableGateway->getAdapter()->query($sqlright);
        $statement->execute();

        ////////add new node
        if ($idea->idea_nodeLeft >= $parentIdea->idea_nodeRight) {
            $difference = $idea->idea_nodeLeft - $parentIdea->idea_nodeRight;
        } else {
            $difference = $parentIdea->idea_nodeRight - $idea->idea_nodeLeft;
        }
        if ($idea->idea_nodeDepth >= $parentIdea->idea_nodeDepth) {
            $depth = $idea->idea_nodeDepth - $parentIdea->idea_nodeDepth;
        } else {
            $depth = $parentIdea->idea_nodeDepth - $idea->idea_nodeDepth;
        }

        if ($idea->idea_nodeParent == 0) {
            $idea_parent = $idea->idea_id;
        } else {
            $idea_parent = $idea->idea_nodeParent;
        }
        $sql = "UPDATE idea set idea_nodeLeft = idea_nodeLeft + {$difference}, "
                . "idea_nodeRight = idea_nodeRight + {$difference}, "
                . "idea_nodeParent={$root_parent}, "
                . "idea_nodeDepth = idea_nodeDepth+{$depth} "
                . "WHERE (idea_nodeLeft >= {$idea->idea_nodeLeft} AND idea_nodeRight <= {$idea->idea_nodeRight}) "
                . "AND (idea_nodeParent={$idea_parent} OR idea_id={$idea_parent})";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $statement->execute();
    }

    public function getListWidth(\IdeaManagement\Model\DbEntity\Idea $idea) {
        return ($idea->idea_nodeRight - $idea->idea_nodeLeft) + 1;
    }

    /**
     * Get the parent idea as acending
     * 
     * @param integer $idea_id
     * @return boolean
     */
    public function getAscending($idea_id) {
        $row = $this->tableGateway->select(array('idea_id' => $idea_id))->current();
        if ($row && $row->idea_nodeParent !== 0) {
            $parent = $this->tableGateway->select(array('idea_id' => $row->idea_nodeParent))->current();
            return $parent;
        }
        return false;
    }

    /**
     * Get the child idea as descending
     * 
     * @param integer $idea_id
     * @return boolean
     */
    public function getDescending($idea_id) {
        $row = $this->tableGateway->select(array('idea_nodeParent' => $idea_id))->current();
        if ($row) {
            return $row;
        }
        return false;
    }

    public function getFollower($ideaId) {
        $ideaId = (int) $ideaId;
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from(array('fi' => 'followIdea'));
        $select->join('user', 'fi.fi_userID=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'));
        $select->where(array('fi_ideaID' => $ideaId));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Update the idea as it is viewed
     * 
     * @param type $idea
     */
    public function updateView($idea) {
        $idea->idea_hitCnt++;
        $idea->idea_lastAccess = date('Y-m-d H:i:s');
        $this->update($idea);
    }

    /**
     * Update idea as new post is created
     * 
     * @param integer $ideaId
     */
    public function updateLastPost($ideaId) {
        $idea = $this->getById($ideaId);
        $idea->idea_lastPost = date('Y-m-d H:i:s');
        $this->update($idea);
    }

    /**
     * Get hot ideas
     */
    public function getHotIdeas($limit = 12) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('idea')
                ->join('user', 'idea.idea_originator=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'))
                ->order('idea_hitCnt DESC')
                ->order('idea_lastModified DESC')
                ->where('user.usr_isSuspended=0')
                ->limit($limit);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

    /**
     * Get hot ideas
     */
    public function getPopularIdeas($limit = 12) {
        $sql = new Sql($this->tableGateway->getAdapter());
        $select = $sql->select();
        $select->from('idea')
                ->join('user', 'idea.idea_originator=user.usr_id', array('usr_id', 'usr_lName', 'usr_fName', 'usr_mName', 'usr_displayName', 'usr_icon'))
                ->order('idea_hitCnt DESC')
                ->where('user.usr_isSuspended=0')
                ->limit($limit);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        return $result;
    }

}
