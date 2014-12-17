<?php

/**
 * Description of ExperienceController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use People\Form\Filter\ExperienceFilter as Filter;
use People\Model\DbEntity\Experience as Model;
use People\Model\DbTable\CvTable;

class ExperienceController extends AuthenticatedController {

    public function getAction() {
        $data = $this->getTable()->fetchByUser($this->userId)->toArray();
        for ($i = 0; $i < count($data); $i++) {
            list($year, $month, $day) = explode('-', $data[$i]['xp_fromDate']);
            $data[$i]['xp_fromDate'] = $year . '.' . $month;
            list($year, $month, $day) = explode('-', $data[$i]['xp_toDate']);
            if ($year == "9999") {
                $data[$i]['xp_toDate'] = "Present";
            } else {
                $data[$i]['xp_toDate'] = $year . '.' . $month;
            }
        }
        return new JsonModel(array(
            'data' => $data
        ));
    }

    public function createAction() {
        $filter = new Filter($this->getServiceLocator());
        if ($this->request->isPost()) {
            $filter->setData($this->request->getPost());
            if ($filter->isValid()) {
                $experience = new Model();
                $experience->exchangeArray($filter->getValues());
                $experience->xp_fromDate = $this->processFromDate($experience->xp_fromDate);
                $experience->xp_toDate = $this->processToDate($experience->xp_toDate);
                if ($this->getTable()->insert($experience, $this->userId)) {
                    return new JsonModel(array(
                        'success' => true
                    ));
                }
            } else {
                return new JsonModel(array(
                    'success' => false,
                    'messages' => $filter->getMessages(),
                ));
            }
        }
        die();
    }

    public function updateAction() {
        if (!$this->params()->fromPost('xp_id')) {
            return $this->notFoundAction();
        }
        $table = $this->getTable();
        $experience = $table->getById($this->params()->fromPost('xp_id'));

        //allow only owner to update their experience
        if (!$experience || $this->getCv()->getUserByCv($experience->xp_cvID) !== $this->laIdentity()->getId()) {
            return new JsonModel(array(
                'success' => false,
                'messages' => 'Only owner can update.'
            ));
        }

        $filter = new Filter($this->getServiceLocator());
        if ($this->request->isPost()) {
            $filter->setData($this->request->getPost());
            if ($filter->isValid()) {
                $experience->exchangeArray($filter->getValues());
                $experience->xp_fromDate = $this->processFromDate($experience->xp_fromDate);
                $experience->xp_toDate = $this->processToDate($experience->xp_toDate);

                $this->getTable()->update($experience);
                return new JsonModel(array(
                    'success' => true
                ));
            } else {
                return new JsonModel(array(
                    'success' => false,
                    'messages' => $filter->getMessages(),
                ));
            }
        }
    }

    public function deleteAction() {
        $xpId = $this->params()->fromPost('xp_id');
        if (!$xpId) {
            return $this->notFoundAction();
        }
        $table = $this->getTable();
        $experience = $table->getById($xpId);

        //allow only owner to update their experience
        if (!$experience || $this->getCv()->getUserByCv($experience->xp_cvID) !== $this->laIdentity()->getId()) {
            return new JsonModel(array(
                'success' => false,
                'messages' => 'Only owner can delete.'
            ));
        }
        if ($this->getTable()->delete($xpId)) {
            return new JsonModel(array(
                'success' => true,
            ));
        } else {
            return new JsonModel(array(
                'success' => false,
                'messages' => 'Deletion failed.'
            ));
        }
    }

    /**
     * 
     * @return \People\Model\DbTable\ExperienceTable
     */
    protected function getTable() {
        return $this->get('ExperienceTable');
    }

    private function getCv() {
        return new CvTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
