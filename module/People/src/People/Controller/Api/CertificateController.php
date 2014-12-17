<?php

/**
 * Description of CertificateController
 *
 * @author kimsreng
 */

namespace People\Controller\Api;

use Common\Mvc\Controller\AuthenticatedController;
use Zend\View\Model\JsonModel;
use People\Model\DbEntity\UserCertification;
use People\Model\DbEntity\CertificateTag;
use People\Form\Filter\CertificateFilter;
use Common\Util\StringHelper;

class CertificateController extends AuthenticatedController {

    protected $viewType = self::JSON_MODEL;

    public function getAllAction() {
        $certificates = $this->getCertificateTable()->fetchAll(null, array('cert_id', 'cert_text'));
        return new JsonModel(array(
            'data' => $certificates->toArray()
        ));
    }

    public function getAction() {
        $certificates = $this->getTable()->fetchByUser($this->userId, array('uCert_id'));
        return new JsonModel(array(
            'data' => $certificates->toArray()
        ));
    }

    public function createAction() {
        $this->initView();
        $this->view->success = false;
        if ($this->request->isPost() && $this->params()->fromPost('cert_text')) {
            $cert_text = StringHelper::toCamel(StringHelper::sanitize($this->params()->fromPost('cert_text')));
            $cert = new CertificateTag();
            $cert->cert_text = $cert_text;
            $cert->cert_timeStamp = date('Y-m-d H:i:s');
            $cert->cert_id = $this->getCertificateTable()->insert($cert);
            $this->view->data = $cert->output('json');
            $this->view->success = true;
        }

        return $this->view;
    }

    public function deleteAction() {
        if ($this->request->isPost()) {
            $uCert_id = (int) $this->params()->fromPost('uCert_id');
            if ($uCert_id) {
                $cert = $this->getTable()->getById($uCert_id);
                if (!$cert || ($cert->uCert_userID != $this->userId)) {
                    return new JsonModel(array(
                        'success' => false
                    ));
                }
                $this->getTable()->delete($uCert_id);
                return new JsonModel(array(
                    'success' => true
                ));
            } else {
                return new JsonModel(array(
                    'success' => false
                ));
            }
        }
        die('Only Post request is allowed.');
    }

    /**
     * 
     * @return \People\Model\DbTable\UserCertificationTable
     */
    protected function getTable() {
        return $this->get('UserCertificateTable');
    }

    /**
     * 
     * @return \People\Model\DbTable\CertificateTagTable
     */
    protected function getCertificateTable() {
        return $this->get('CertificateTagTable');
    }

}
