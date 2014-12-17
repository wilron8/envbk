<?php

/**
 * Description of IdeaCommentSearch
 *
 * @author kimsreng
 */

namespace SearchEngine\Model;

use SearchEngine\Model\AbstractSearch;

class IdeaCommentSearch extends AbstractSearch {

    protected $queryFields = ['iComm_comment'];
    protected $tableName = "ideaComment";
    protected $timeField = "iComm_timeStamp";
    protected $condition = "idea_isVisible=1";

    protected function processSelect(&$select) {
        $select->join('idea', 'idea.idea_id=ideaComment.iComm_ideaId', array());
    }

}
