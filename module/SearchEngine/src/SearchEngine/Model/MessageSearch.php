<?php

/**
 * Description of MessageSearch
 *
 * @author kimsreng
 */
namespace SearchEngine\Model;

use SearchEngine\Model\AbstractSearch;

class MessageSearch  extends AbstractSearch{
   
    protected $queryFields = ['msg_body','msg_subject'];
    protected $tableName = "message";
    protected $timeField = "msg_timeStamp";
    
}
