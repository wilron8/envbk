<?php

/**
 * Description of ProjectPersonType
 *
 * @author kimsreng
 */

namespace ProjectManagement\Model\DbEntity;

class ProjectPersonType {

    public $prjPType_id = NULL;
    public $prjPType_PM = NULL;
    public $prjPType_t = NULL;
    public $prjPType_dTime = NULL;
    public $prjPType_reportType = NULL;

    public function exchangeArray($data) {
        $this->prjPType_id = (isset($data['prjPType_id'])) ? $data['prjPType_id'] : $this->prjPType_id;
        $this->prjPType_PM = (isset($data['prjPType_PM'])) ? $data['prjPType_id'] : $this->prjPType_PM;
        $this->prjPType_t = (isset($data['prjPType_t'])) ? $data['prjPType_t'] : $this->prjPType_t;
        $this->prjPType_dTime = (isset($data['prjPType_dTime'])) ? $data['prjPType_dTime'] : $this->prjPType_dTime;
        $this->prjPType_reportType = (isset($data['prjPType_reportType'])) ? $data['prjPType_reportType'] : $this->prjPType_reportType;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

}
