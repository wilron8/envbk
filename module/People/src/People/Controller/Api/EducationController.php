<?php

/**
 * Description of EducationController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use People\Form\Filter\EducationFilter as Filter;
use People\Model\DbEntity\Education as Model;
use People\Model\DbTable\CvTable;

class EducationController extends AuthenticatedController {

    public function getAction() {
        $data = $this->getTable()->fetchByUser($this->userId)->toArray();
        for ($i = 0; $i < count($data); $i++) {
            list($year, $month, $day) = explode('-', $data[$i]['ed_fromDate']);
            $data[$i]['ed_fromDate'] = $year . '.' . $month;
            list($year, $month, $day) = explode('-', $data[$i]['ed_toDate']);
            if ($year == "9999") {
                $data[$i]['ed_toDate'] = "Present";
            } else {
                $data[$i]['ed_toDate'] = $year . '.' . $month;
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
                $education = new Model();
                $education->exchangeArray($filter->getValues());
                $education->ed_fromDate = $this->processFromDate($education->ed_fromDate);
                $education->ed_toDate = $this->processToDate($education->ed_toDate);

                if ($this->getTable()->insert($education, $this->userId)) {
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
        return new JsonModel(array(
            'success' => FALSE
        ));
    }

    public function updateAction() {
        if (!$this->params()->fromPost('ed_id')) {
            return $this->notFoundAction();
        }
        $table = $this->getTable();
        $education = $table->getById($this->params()->fromPost('ed_id'));

        //allow only owner to update their education
        if (!$education || $this->getCv()->getUserByCv($education->ed_cvID) !== $this->laIdentity()->getId()) {
            return new JsonModel(array(
                'success' => false,
                'messages' => 'Only owner can update.'
            ));
        }

        $filter = new Filter($this->getServiceLocator());
        if ($this->request->isPost()) {
            $filter->setData($this->request->getPost());
            if ($filter->isValid()) {
                $education->exchangeArray($filter->getValues());
                $education->ed_fromDate = $this->processFromDate($education->ed_fromDate);
                $education->ed_toDate = $this->processToDate($education->ed_toDate);

                $this->getTable()->update($education);
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
        $edId = $this->params()->fromPost('ed_id');
        if (!$edId) {
            return $this->notFoundAction();
        }
        $table = $this->getTable();
        $education = $table->getById($edId);

        //allow only owner to update their education
        if (!$education || $this->getCv()->getUserByCv($education->ed_cvID) !== $this->laIdentity()->getId()) {
            return new JsonModel(array(
                'success' => false,
                'messages' => 'Only owner can delete.'
            ));
        }
        if ($this->getTable()->delete($edId)) {
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
     * @return \People\Model\DbTable\EducationTable
     */
    protected function getTable() {
        return $this->get('EducationTable');
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
