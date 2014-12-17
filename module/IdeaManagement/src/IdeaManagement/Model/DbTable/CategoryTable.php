<?php

/**
 * Description of CategoryTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use Zend\Db\TableGateway\TableGateway;
use Common\DbTable\AbstractTable;
use Common\DbEntity\EntityInterface;

class CategoryTable extends AbstractTable {

    protected $primaryKey = "cat_id";
    protected $table = "category";
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function update(EntityInterface $entity) {
        //make sure cat_ideaCnt is not less than 0
        $entity->cat_ideaCnt = max($entity->cat_ideaCnt, 0);
        parent::update($entity);
    }

    public function getSelectOptions() {
        $rows = $this->tableGateway->select();
        $options = array();
        foreach ($rows as $row) {
            $options[$row->cat_id] = $row->cat_text;
        }
        return $options;
    }

}
