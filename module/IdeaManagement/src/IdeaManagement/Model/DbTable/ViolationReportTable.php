<?php

/**
 * Description of ViolationReportTable
 *
 * @author kimsreng
 */

namespace IdeaManagement\Model\DbTable;

use IdeaManagement\Model\DbEntity\ViolationReport;
use Zend\Db\TableGateway\TableGateway;

class ViolationReportTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function createViolationReport(ViolationReport $violation) {
        $violation->vp_timeStamp = date('Y-m-d H:i:s');
        return $this->tableGateway->insert($violation->getArrayCopy());
    }

}
