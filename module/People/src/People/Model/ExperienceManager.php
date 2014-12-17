<?php

/**
 * Description of ExperienceManager
 *
 * @author kimsreng
 */

namespace People\Model;

use People\Form\Filter\ExperienceFilter as Filter;
use People\Model\DbEntity\Experience as Model;
use People\Model\DbTable\CvTable;
use People\Model\DbTable\ExperienceTable;

class ExperienceManager {

    /**
     * @var ExperienceTable
     */
    protected $xpTable;

    /**
     * @var CvTable 
     */
    protected $cvTable;
    protected $translator;

    public function __construct($xpTable, $cvTable, $translator) {
        $this->xpTable = $xpTable;
        $this->cvTable = $cvTable;
        $this->translator = $translator;
    }

    /**
     * Create new experience
     * 
     * @param array $data
     * @param integer $userId
     * @return boolean
     */
    public function create($data, $userId) {
        $filter = new Filter($this->translator);
        $filter->setData($data);
        if ($filter->isValid()) {
            $experience = new Model();
            $experience->exchangeArray($filter->getValues());
            $experience->xp_fromDate = $this->processFromDate($experience->xp_fromDate);
            $experience->xp_toDate = $this->processToDate($experience->xp_toDate);

            if ($this->xpTable->insert($experience, $userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update experience
     * 
     * @param array $data
     * @param integer $userId
     * @return boolean
     */
    public function update($data, $userId) {
        $xp = $this->xpTable->getById($data['xp_id']);

        //allow only owner to update their experience
        if (!$xp || $this->cvTable->getUserByCv($xp->xp_cvID) !== $userId) {
            return false;
        }

        $filter = new Filter($this->translator);
        $filter->setData($data);
        if ($filter->isValid()) {
            $xp->exchangeArray($filter->getValues());
            $xp->xp_fromDate = $this->processFromDate($xp->xp_fromDate);
            $xp->xp_toDate = $this->processToDate($xp->xp_toDate);

            $this->xpTable->update($xp);
            return true;
        }

        return false;
    }

    /**
     * Delete experience by id
     * 
     * @param type $xpId
     * @param type $userId
     * @return boolean
     */
    public function delete($xpId, $userId) {

        $xp = $this->xpTable->getById($xpId);

        //allow only owner to delete their experience
        if (!$xp || $this->cvTable->getUserByCv($xp->xp_cvID) !== $userId) {
            return false;
        }
        if ($this->xpTable->delete($xpId)) {
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
