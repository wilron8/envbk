<?php

/**
 * Description of AddressController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Common\DbEntity\geoCountry;
use Zend\View\Model\JsonModel;
use Common\Util\StringHelper;

class AddressController extends AuthenticatedController {

    protected $table = NULL;

    public function getAction() {
        $address = $this->getTable()->getProfileAddresses($this->userId);
        return new JsonModel(array(
            'data' => $address->toArray()
        ));
    }

    public function createCountryAction() {
        $this->viewType = self::JSON_MODEL;
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost() && $this->params()->fromPost('geoCountry_roman')) {
            $name = StringHelper::toCamel(StringHelper::sanitize($this->params()->fromPost('geoCountry_roman')));
            $country = new geoCountry();
            $country->geoCountry_roman = $name;
            $country->geoCountry_isVisible = 1;
            $country->geoCountry_continent=1; //CONFIRM: if the value is 0 as default, it would not show up in the country list query as the query joined with continent
            $country->geoCountry_id = $this->get('geoCountryTable')->insert($country);
            
            $this->view->data = $country->output('country');
            $this->view->success = true;
            //Notify admin for a new country added by user
            $user = $this->get('UserTable')->getById($this->userId);
            $this->get('NotifyAdmin')->notifyNewCountry($user, $country);
        }

        return $this->view;
    }

    public function updateAction() {
        if ($this->request->isPost()) {
            $filter = new AddressFilter($this->get('translator'));
            $filter->setData($this->request->getPost());
            if ($filter->isValid()) {
                $addr_id = (int) $this->params()->fromPost('uAddr_id');
                $addr = $this->getTable()->getById($addr_id);
                //allow only owner to update
                if ($addr->uAddr_userID == $this->userId) {
                    $addr->exchangeArray($filter->getValues());
                    $this->getTable()->update($addr);
                    return new JsonModel(array(
                        'success' => true
                    ));
                }
            }
            return new JsonModel(array(
                'success' => false
            ));
        }
        die();
    }

    public function deleteAction() {
        if ($this->request->isPost()) {
            $addr_id = (int) $this->params()->fromPost('id');
            $addr = $this->getTable()->getById($addr_id);
            //allow only owner to delete
            if ($addr->uAddr_userID == $this->userId) {

                $this->getTable()->delete($addr_id);

                return new JsonModel(array(
                    'success' => true
                ));
            }
            return new JsonModel(array(
                'success' => false
            ));
        }
    }

    /**
     * 
     * @return \Users\Model\DbTable\UserAddressTable
     */
    protected function getTable() {
        if ($this->table == NULL) {
            $this->table = $this->get('UserAddressTable');
        }
        return $this->table;
    }

}
