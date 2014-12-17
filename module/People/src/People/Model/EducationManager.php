<?php

/**
 * Description of EducationManager
 *
 * @author kimsreng
 */

namespace People\Model;

use People\Form\Filter\EducationFilter as Filter;
use People\Model\DbEntity\Education as Model;
use People\Model\DbTable\CvTable;
use People\Model\DbTable\EducationTable;

class EducationManager {

    /**
     * @var EducationTable 
     */
    protected $edTable;

    /**
     * @var CvTable 
     */
    protected $cvTable;
    protected $translator;

    public function __construct($edTable, $cvTable, $translator) {
        $this->edTable = $edTable;
        $this->cvTable = $cvTable;
        $this->translator = $translator;
    }

    /**
     * Create new education
     * 
     * @param array $data
     * @param integer $userId
     * @return boolean
     */
    public function create($data, $userId) {
        $filter = new Filter($this->translator);
        $filter->setData($data);
        if ($filter->isValid()) {
            $education = new Model();
            $education->exchangeArray($filter->getValues());
            $education->ed_fromDate = $this->processFromDate($education->ed_fromDate);
            $education->ed_toDate = $this->processToDate($education->ed_toDate);

            if ($this->edTable->insert($education, $userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update education
     * 
     * @param array $data
     * @param integer $userId
     * @return boolean
     */
    public function update($data, $userId) {
        $education = $this->edTable->getById($data['ed_id']);

        //allow only owner to update their education
        if (!$education || $this->cvTable->getUserByCv($education->ed_cvID) !== $userId) {
            return false;
        }

        $filter = new Filter($this->translator);
        $filter->setData($data);
        if ($filter->isValid()) {
            $education->exchangeArray($filter->getValues());
            $education->ed_fromDate = $this->processFromDate($education->ed_fromDate);
            $education->ed_toDate = $this->processToDate($education->ed_toDate);

            $this->edTable->update($education);
            return true;
        }

        return false;
    }

    /**
     * Delete education by id
     * 
     * @param type $edId
     * @param type $userId
     * @return boolean
     */
    public function delete($edId, $userId) {

        $education = $this->edTable->getById($edId);

        //allow only owner to update their education
        if (!$education || $this->cvTable->getUserByCv($education->ed_cvID) !== $userId) {
            return false;
        }
        if ($this->edTable->delete($edId)) {
            return true;
        }
        return false;
    }

    protected function processToDate($date) {
        if ($date == '' || strtolower($date) === "present") {
            return "9999-01-01";
        }
        if (stristr('.', $date)) {
            list($year, $month) = explode('.', $date);
        } else {
            $year = $date;
            $month = 01;
        }
        return $year . '-' . $month . '-01';
    }

    protected function processFromDate($date) {
        if (stristr('.', $date)) {
            list($year, $month) = explode('.', $date);
        } else {
            $year = $date;
            $month = 01;
        }
        return $year . '-' . $month . '-01';
    }

}
