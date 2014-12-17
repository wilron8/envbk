<?php

/**
 * Description of ProjectWallSearch
 *
 * @author kimsreng
 */
namespace SearchEngine\Model;

use SearchEngine\Model\AbstractSearch;

class ProjectWallSearch  extends AbstractSearch {
    
    protected $queryFields = ['prjW_comment'];
    protected $tableName = "projectWall";
    protected $timeField = "prjW_timeStamp";
    protected $condition = "proj_isVisible=1";
    
    protected function processSelect(&$select) {
         $select->join('project', 'project.proj_id=projectWall.prjW_projID', array());
    }
    
}
