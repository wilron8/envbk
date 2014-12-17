<?php

/**
 * Description of SessionTable
 *
 * @author kimsreng
 */

namespace Users\Model\DbTable;

use Users\Model\DbEntity\Session;
use Zend\Db\TableGateway\TableGateway;

class SessionTable {

    protected $tableGateway;
    protected $sm;

    public function __construct(TableGateway $tableGateway, $sm) {
        $this->tableGateway = $tableGateway;
        $this->sm = $sm;
    }

    public function fetchAll() {
        return $this->tableGateway->select();
    }

    public function getByUserId($userID) {
        return $this->tableGateway->select(array('sess_userID' => (int) $userID));
    }

    public function getBySessId($sess_PHPSESSID) {
        $rowset = $this->tableGateway->select(array('sess_PHPSESSID' => $sess_PHPSESSID));
        return $rowset->current();
    }    

    //start wilron8
    /**
     * Update Current User Session
     * @author wilron8
     * @param $whereFields (array)
     * @param $userSession (object)
     */
    public function updateCurrUserSession($whereFields,$userSession){
        $this->tableGateway->update($userSession->getArrayCopy(), $whereFields);
    }
    //end wilron8

    /**
     * Get session list by condition as key/value array
     * @param array $condition
     * @return rowset
     */
    public function fetchByCondition(array $condition) {
        $rowset = $this->tableGateway->select($condition);
        return $rowset;
    }

    public function sniffUser() {
        $request = $this->sm->get('Request');
        $sessId = $this->sm->get('Zend\Session\SessionManager')->getId();
        $row = $this->getBySessId($sessId);
        if (!$row) {
            $sniffer = $this->sm->get('ClientSniffer');
            $session = new Session();
            $session->sess_IP = $request->getServer('REMOTE_ADDR');
            $session->sess_referrer = $request->getServer('HTTP_REFERER');
            $session->sess_port = $request->getServer('REMOTE_PORT');
            $session->sess_timeStamp = date('Y-m-d H:i:s');
            $session->sess_PHPSESSID = $sessId;
            $session->sess_browser = $sniffer->get_browserName();
            $session->sess_browserVer = $sniffer->get_browserVer();
            $session->sess_OS = $sniffer->get_osName();
            $session->sess_OSmake = $sniffer->get_osMake();
            $session->sess_OSver = $sniffer->get_osVer();
            $session->sess_isMobile = $sniffer->isUserMobile();
            $this->insert($session);
        } else {//check for ip change
            if ($row->sess_IP !== $request->getServer('REMOTE_ADDR')) {
                $this->sm->get('AuthService')->clearIdentity();
                session_regenerate_id(true);
            }
        }
    }

    public function setXY($x, $y) {
        $sessId = $this->sm->get('Zend\Session\SessionManager')->getId();
        $row = $this->getBySessId($sessId);
        if ($row) {
            $row->sess_logicalXDPI = $x;
            $row->sess_logicalYDPI = $y;
            $this->update($row);
        }
    }

    public function insert(Session $session) {
        $this->tableGateway->insert($session->getArrayCopy());
    }

    public function update(Session $session) {
        $this->tableGateway->update($session->getArrayCopy(), array('sess_id' => $session->sess_id));
    }

    public function delete($sess_id) {
        $this->tableGateway->delete(array('sess_id' => (int) $sess_id));
    }

    /**
     * function to check if ip is the same for the remembered session
     * 
     * @return boolean
     */
    public function hasIpChanged() {
        $sessId = $this->sm->get('Zend\Session\SessionManager')->getId();
        $row = $this->getBySessId($sessId);
        if ($row) {
            return !($row->sess_IP == $_SERVER['REMOTE_ADDR']);
        }
        return false;
    }

}

?>
