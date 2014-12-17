<?php

/**
 * Description of ProjectSearch
 *
 * @author kimsreng
 */

namespace SearchEngine\Model;

use SearchEngine\Model\AbstractSearch;

class ProjectSearch extends AbstractSearch {

    protected $queryFields = ['proj_title','proj_descript'];
    protected $tableName = "project";
    protected $timeField = "proj_timeStamp";
    protected $condition = "proj_isVisible=1";

}
