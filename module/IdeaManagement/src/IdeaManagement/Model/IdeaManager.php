<?php

/**
 * Description of IdeaManager
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model;

use IdeaManagement\Model\DbEntity\Idea;
use IdeaManagement\Model\DbEntity\IdeaComment;
use IdeaManagement\Model\DbEntity\IdeaRef;
use IdeaManagement\Model\DbEntity\Category;
use IdeaManagement\Model\DbTable\IdeaTable;
use IdeaManagement\Model\DbTable\IdeaCommentTable;
use IdeaManagement\Model\DbTable\IdeaRefTable;
use IdeaManagement\Model\DbTable\CategoryTable;
use IdeaManagement\Model\DbTable\FollowIdeaTable;
use DocumentManager\Model\ResourceType as Resource;
use Zend\Db\Sql\Sql;

class IdeaManager {

    /**
     *
     * @var IdeaTable
     */
    protected $IdeaTable = NULL;

    /**
     *
     * @var IdeaCommentTable
     */
    protected $IdeaCommentTable = NULL;

    /**
     *
     * @var IdeaRefTable
     */
    protected $IdeaRefTable = NULL;

    /**
     *
     * @var CategoryTable
     */
    protected $CategoryTable = NULL;

    /**
     *
     * @var FollowIdeaTable
     */
    protected $FollowIdeaTable = NULL;

    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface  
     */
    protected $SL = NULL;
    protected $isDirty = false;

    /**
     * Accumulated dirtiness description
     * 
     * @var string 
     */
    protected $dirtyContent = "";

    public function __construct($SL) {
        $this->SL = $SL;
        $this->IdeaTable = $this->SL->get('IdeaTable');
        $this->IdeaCommentTable = $this->SL->get('IdeaCommentTable');
        $this->IdeaRefTable = $this->SL->get('IdeaRefTable');
        $this->CategoryTable = $this->SL->get('CategoryTable');
        $this->FollowIdeaTable = $this->SL->get('FollowIdeaTable');
    }

    /**
     * Mark as dirty if any field changes
     */
    public function setDirty() {
        $this->isDirty = true;
    }

    /**
     * 
     * @param Idea $ideaObject
     * @param integer $userId
     * @param array $newReference
     */
    public function update($ideaObject, $userId, Array $newReference = NULL) {
        $oldCopy = $this->IdeaTable->getById($ideaObject->idea_id);
        //check if the form is dirty
        if ($oldCopy->idea_title != $ideaObject->idea_title) {
            $this->setDirty();
            $this->dirtyContent .="Title changed from <i> $oldCopy->idea_title</i> to <i>$ideaObject->idea_title</i><BR>";
        }

        if ($oldCopy->idea_img != $ideaObject->idea_img) {
            $this->setDirty();
            if ($oldCopy->idea_img == "") {
                $this->dirtyContent .="Idea icon was added.<BR>";
            } else {
                $this->dirtyContent .="The idea icon was changed.<BR>";
            }
        }

        if ($oldCopy->idea_categoryID != $ideaObject->idea_categoryID) {
            $this->setDirty();

            if (($oldCopy->idea_categoryID) && ($ideaObject->idea_categoryID)) {
                //check if a new category is added
                $cat = $this->CategoryTable->getById($ideaObject->idea_categoryID);
                if (!$cat) {
                    $cat = $this->addCategory($ideaObject->idea_categoryID);
                    $ideaObject->idea_categoryID = $cat->cat_id;
                }
                //increment ideaCnt
                $this->upCatIdeaCnt($cat);
                //Previous category
                $oldCat = $this->CategoryTable->getById($oldCopy->idea_categoryID);
                $this->downCatIdeaCnt($oldCat);
                $this->dirtyContent .="Idea category changed from <i>" . $oldCat->cat_text . "</i> to <i>" . $cat->cat_text . "</i><BR>";
            } elseif (($oldCopy->idea_categoryID) && !($ideaObject->idea_categoryID)) {
                $oldCat = $this->CategoryTable->getById($oldCopy->idea_categoryID);
                $this->downCatIdeaCnt($oldCat);
                $this->dirtyContent .="Idea was removed from the category of <i>" . $cat->cat_text . "</i><BR>";
            } elseif (!($oldCopy->idea_categoryID) && ($ideaObject->idea_categoryID)) {
                $cat = $this->CategoryTable->getById($ideaObject->idea_categoryID);
                if (!$cat) {
                    $cat = $this->addCategory($ideaObject->idea_categoryID);
                    $ideaObject->idea_categoryID = $cat->cat_id;
                }
                //increment ideaCnt
                $this->upCatIdeaCnt($cat);
                $this->dirtyContent .="Idea was added in the category of <i>" . $cat->cat_text . "</i><BR>";
            }
        }

        if ($oldCopy->idea_attachment != $ideaObject->idea_attachment) {
            $this->setDirty();
            $this->dirtyContent .="The video has changed.<BR>";
        }

        if ($oldCopy->idea_descript != $ideaObject->idea_descript) {
            $this->setDirty();
            $this->dirtyContent .="Description change from <BR><i>$oldCopy->idea_descript</i><BR> to <BR></i>$ideaObject->idea_descript</i><BR>";
        }

        if ($this->isDirty) {
            $ideaObject->idea_lastModified = date('Y-m-d H:i:s');
            $this->IdeaTable->update($ideaObject);
        }

        //check reference for update
        if ($newReference !== NULL) {
            $this->updateReference($oldCopy->idea_id, $newReference,$userId);
        }

        //add comment to inform about change in idea
        if ($this->isDirty) {

            $comment = new IdeaComment();
            $comment->iComm_ideaId = $oldCopy->idea_id;
            $comment->iComm_timeStamp = date('Y-m-d H:i:s');
            $comment->iComm_userId = $userId;
            $comment->iComm_comment = $this->dirtyContent;
            $comment->iComm_readOnly = 1;
            $this->IdeaCommentTable->addComment($comment);
            //notify user
            $this->SL->get('NotifyManager')->ideaModifiedNotify($ideaObject,  $this->SL->get('UserTable')->getById($userId));
        }
    }

    /**
     * Creat a new idea
     * 
     * @param array $data
     * @param integer $userId
     * @return integer
     */
    public function createIdea($data, $userId) {
        $transaction = $this->SL->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        try {
            $transaction->beginTransaction();
            $idea = new Idea();
            $idea->exchangeArray($data);
            $idea->idea_originator = $userId;
            $idea->idea_isVisible = 1;
            //check if the category already exist
            $cat = $this->CategoryTable->getById($idea->idea_categoryID);
            if (!$cat) {
                $cat = $this->addCategory($idea->idea_categoryID);
            }
            $this->upCatIdeaCnt($cat);
            $idea->idea_categoryID = $cat->cat_id;

            //$data['reference'] = explode('|', $data['reference']);
            if (isset($data['parent'])) {
                $idea_id = $this->IdeaTable->insert($idea, $data['parent']);
            } else {
                $idea_id = $this->IdeaTable->insert($idea);
            }
            $idea->idea_id = $idea_id;
          // save idea reference if any 
            if (count(array_filter($data['reference'])) > 0) {
                foreach ($data['reference'] as $ref) {
                    $idearef = new IdeaRef();
                    $idearef->iRef_newIdea = $idea_id;
                    $idearef->iRef_srcIdea = $ref;
                    $this->IdeaRefTable->insert($idearef);
                    //notify the owner of the idea ref
                    $this->SL->get('NotifyManager')->referenceIdeaNotify($this->IdeaTable->getById($ref),$idea,  $this->SL->get('UserTable')->getById($userId));
                }
            }
            //increment idea count in user table
            $userTable = $this->SL->get('UserTable');
            $user = $userTable->getById($userId);
            $user->usr_ideaCnt++;
            $userTable->update($user);
            //auto follow the idea
            $this->followIdea($idea, $userId);
            $transaction->commit();
            return $idea_id;
        } catch (Exception $exc) {
            $transaction->rollback();
            $this->SL->get('ErrorMail')->send($exc);
        }
    }

    /**
     * 
     * @param integer $ideaId
     * @param array $refList
     */
    public function updateReference($ideaId, array $refList,$userId) {
        $previous_refs = $this->IdeaRefTable->fetchRefAsArray($ideaId);
        $refList = array_filter($refList);
        //check if there is no change in ref
        if ($previous_refs == $refList) {
            return;
        }
        //if there is any change in reference it is tracked by making a comment to the idea
        $sql = new Sql($this->SL->get('Zend\Db\Adapter\Adapter'));
        if (count($previous_refs) == 0) {
            $previous_ref = "No reference";
        } else {
            $select = $sql->select()->from('idea')->where("idea_id IN (" . implode(',', $previous_refs) . ")");
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
            $select = $sql->select()->from('idea')->where("idea_id IN (" . $new_ref_list . ")");
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $title = [];
            foreach ($result as $value) {
                $title[] = $value['idea_title'];
            }
            $new_ref = implode(', ', $title);
        }
        //set dirty 
        $this->setDirty();
        $this->dirtyContent .="Idea reference changed from <i>$previous_ref</i> to <i>$new_ref</i>";


        //delete if there is any deletion
        $delete = array_diff($previous_refs, $refList);
        if (count($delete) > 0) {
            foreach ($delete as $id) {
                $this->IdeaRefTable->deleteByCon(array('iRef_newIdea' => $ideaId, 'iRef_srcIdea' => $id));
            }
        }
        // add new ref if any
        $new = array_diff($refList, $previous_refs);
        if (count($new) > 0) {
            foreach ($new as $n) {
                $ref = new IdeaRef();
                $ref->iRef_newIdea = $ideaId;
                $ref->iRef_srcIdea = $n;
                $this->IdeaRefTable->insert($ref);
                //notify the owner of the idea ref
                $this->SL->get('NotifyManager')->referenceIdeaNotify($this->IdeaTable->getById($n),$this->IdeaTable->getById($ideaId),  $this->SL->get('UserTable')->getById($userId));
            }
        }
    }

    /**
     * 
     * @param \IdeaManagement\Model\DbEntity\Idea $idea
     * @param integer $userId
     */
    public function followIdea(Idea $idea, $userId) {
        //check if the user has already followed the idea
        if ($this->FollowIdeaTable->isUserFollowIdea($userId, $idea->idea_id)) {
            return false;
        }

        if ($this->FollowIdeaTable->followIdea($userId, $idea->idea_id)) {
            //update followCnt
            $idea->idea_followCnt++;
            $this->IdeaTable->update($idea);
        }
        return true;
    }

    /**
     * 
     * @param \IdeaManagement\Model\DbEntity\Idea $idea
     * @param integer $userId
     */
    public function unfollowIdea(Idea $idea, $userId) {
        //check if the user has already followed the idea
        if (!$this->FollowIdeaTable->isUserFollowIdea($userId, $idea->idea_id)) {
            return false;
        }

        if ($this->FollowIdeaTable->unfollowIdea($userId, $idea->idea_id)) {
            //update followCnt
            $idea->idea_followCnt--;
            $this->IdeaTable->update($idea);
        }
        return true;
    }

    /**
     * Add new category
     * 
     * @param string $catText
     * @return \IdeaManagement\Model\DbEntity\Category
     */
    public function addCategory($catText) {
        $cat = new Category();
        $cat->cat_text = $this->SL->get('Util')->helpString()->toCamel($catText);
        $cat->cat_timeStamp = date('Y-m-d H:i:s');
        $cat->cat_isFlagged = 0;
        $cat->cat_id = $this->CategoryTable->insert($cat);
        return $cat;
    }

    /**
     * Increment ideaCnt
     * 
     * @param Category $cat
     */
    public function upCatIdeaCnt($cat) {
        $cat->cat_ideaCnt++;
        $this->CategoryTable->update($cat);
    }

    /**
     * Decrease ideaCnt
     * 
     * @param Category $cat
     */
    public function downCatIdeaCnt($cat) {
        $cat->cat_ideaCnt--;
        $this->CategoryTable->update($cat);
    }

    /**
     * 
     * @param integer $ideaId
     * @param form_upload_field $imageUpload
     * @return boolean
     */
    public function saveIdeaIcon($ideaId, $imageUpload) {
        if ($imageUpload['name'] == "") {
            return false;
        }
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        $directory = $this->SL->get('PathManager')->getIdeaPath($ideaId, Resource::ICON);
        $adapter->setDestination($directory);
        return $adapter->receive($imageUpload['name']);
    }

}
