<?php

/**
 * Description of SkillTagTable
 *
 * @author kimsreng
 */

namespace People\Model\DbTable;

use People\Model\DbEntity\SkillTag;
use Zend\Db\TableGateway\TableGateway;
use Common\DbTable\AbstractTable;

class SkillTagTable extends AbstractTable{

    protected $table="skillTag";
    protected $primaryKey = 'stag_id';
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getByTag($tag) {
        return $this->tableGateway->select(array('stag_text' => $tag))->current();
    }

    public function getSelectOptions() {
        $result = $this->tableGateway->select();
        $options = [];
        foreach ($result as $r) {
            $options[$r->stag_id] = $r->stag_text;
        }
        return $options;
    }

}
