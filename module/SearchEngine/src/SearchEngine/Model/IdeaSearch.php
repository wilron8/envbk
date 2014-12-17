<?php

/**
 * Description of IdeaSearch
 *
 * @author kimsreng
 */

namespace SearchEngine\Model;

use SearchEngine\Model\AbstractSearch;

class IdeaSearch extends AbstractSearch {

    protected $queryFields = ['idea_title', 'idea_descript', 'idea_attachment'];
    protected $tableName = "idea";
    protected $timeField = "idea_timeStamp";
    protected $condition = "idea_isVisible=1";

}
