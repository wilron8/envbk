<?php

/**
 * Description of PeopleSearch
 *
 * @author kimsreng
 */
namespace SearchEngine\Model;

class PeopleSearch extends AbstractSearch {

    protected $queryFields=['usr_fName','usr_lName','usr_displayName','usr_mName','usr_email','usr_about'];
    protected $tableName="user";
    protected $timeField = "usr_joinDate";
    protected $condition = "usr_isSuspended=0";

}
