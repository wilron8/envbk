<?php
/**
 * Link Aide . com
 *
 * @link      http://www.Linspira.com/sys/ for the canonical source
 * @copyright Copyright (c) 2011-2013 LinkAide.com
 * @license   IP of Richie Bartlett, Jr. (RichieBartlett.com)
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;


class SysController extends AbstractActionController
{
	
	/*
	 * This function will return connectivity status
	 *
	 * dbConn: {1 | 0}
	*/
    private function getDBstatus() {
		$adapter = $this->getServiceLocator()->get('evitzDB');
		$dbConn = $adapter->getDriver()->getConnection()->connect()->isConnected();
		return($dbConn);
	}


	/*
	 * This function will return the Json Model for the AJAX call
	 *
	 * Function will return all rows & columns for the given table.
	*/
    private function buildJsonFromTable($dataSrc = NULL, $jsonName = NULL) {

		$DBstatus = false;
		$dataArray = array();
		$dataCnt = 0;

		if ($dataSrc) {
			$DBstatus = true;

			foreach($dataSrc as $rowData)
			{
				$dataArray[] = $rowData;
				$dataCnt++;
			}
			
		}//end if dataSrc
		$result= new JsonModel(array(
			'success' => ( $DBstatus && $this->getDBstatus() ),
			'total' => $dataCnt,
			'dt' => date(DATE_ATOM),
			"$jsonName" => $dataArray
		));

		return($result);
	}//end function buildJsonFromTable

	/*
	 * This function will return connectivity status
	 *
	 * DBstatus: {1 | 0}
	*/
    public function sysAction() {
		$this->createHeader();

		$result= new JsonModel(array(
			'DBstatus' => ($this->getDBstatus()===true? 1:0), //must be integer, boolean not acceptable
			'dt' => date(DATE_ATOM)
		));
		
		return($result);
	
    }//end function sysAction
	
	/*
	 * This function will return a list of known languages from the geoLang table where isVisible = true
	 *
	 * langs: {...}
	*/
    public function listLangAction() {
		$this->createHeader();

		$dataSrc = $this->getGeoLang()->getSelectJSON();

		$result = $this->buildJsonFromTable($dataSrc, 'langs');
		return($result);
	}//end  function listLangAction
	
	/*
	 * This function will return a list of system UI supported languages from the geoLang table WHERE geoLang_isVisible = 1 AND geoLang_isSupported = 1
	 *
	 * langs: {...}
	*/
    public function sysLangAction() {
		$this->createHeader();

		$dataSrc = $this->getGeoLang()->getSysLangJSON();

		$result = $this->buildJsonFromTable($dataSrc, 'langs');
		return($result);
	}//end  function listLangAction
      
      /*
	 * This function will return a list of known countries from the geoCountry table
	 *  -- this is current as of 2011 UN recognized governments.
	 *
	 * nations: {...}
	*/
    public function listCountryAction() {
		$this->createHeader();

//		$dataSrc = $this->getGeoCountry()->fetchAll();
		$dataSrc = $this->getGeoCountry()->getSelectJSON();

		$result = $this->buildJsonFromTable($dataSrc, 'nations');
		return($result);

	}//end  function listCountriesAction
	
	/*
	 * This function will return a list of known Provences/States from the geoStateCity table
	 *
	 * stateCity: {...}
	*/
    public function listStateProvAction() {
		$this->createHeader();

		$dataSrc = "";
		$country = $this->params()->fromRoute('id'); //geoStateCity_ISO3166
		
		if ( empty($country) || $country === NULL ) {
			$country = $this->params()->fromQuery('country');
		}
		
		if ( !empty($country) ) { //ISO3166 ID to use in the where clause
			$dataSrc = $this->getGeoStateCity()->getSelectJSON($country);
		}

		$result = $this->buildJsonFromTable($dataSrc, 'stateCity');
		return($result);
	}//end function listStateProvAction
	
	/*
	 * This function will return a list of known cities from the geoCity table
	 *
	 * city: {...}
	*/
    public function listCityAction() {
		$this->createHeader();

		$dataSrc = "";
		$country = $this->params()->fromRoute('id'); // geocity_ISO3166
		$prov = $this->params()->fromRoute('key'); //geocity_ISO3166_2
		
		if ( !empty($country) ) { //country ID to use in the where clause
			if ( empty($prov) || $prov === NULL ) {
				$prov = $this->params()->fromQuery('prov');
			}
			$dataSrc = $this->getGeoCity()->getSelectJSON($country, $prov);
		}

		$result = $this->buildJsonFromTable($dataSrc, 'city');
		return($result);
	}//end  function listCityAction
	
	/*
	 * This function will return a list of Phone area codes for a given city from the geoCity table
	 *
	 * areaCodes: {...}
	*/
    public function listAreaCodesAction() {
		$this->createHeader();

		$dataSrc = "";
		$country = $this->params()->fromRoute('id'); // geocity_ISO3166
		$prov = $this->params()->fromRoute('key'); //geocity_ISO3166_2
		$city = $this->params()->fromRoute('key2'); //geoCity_cityName
		$zip = $this->params()->fromQuery('zip'); //geoCity_PostalCode
		

		if ( empty($city) || $city === NULL || strtolower($city) == "null" ) {
			$city = $this->params()->fromQuery('city');
		}
		
		if ( !empty($country) && !empty($prov) ) {
			$dataSrc = $this->getGeoCity()->getAreaCodesJSON($country, $prov, $city, $zip);
		}

		$result = $this->buildJsonFromTable($dataSrc, 'areaCodes');
		return($result);
	}//end  function listCityAction
	
	/*
	 * This function will return a list of postal/zip codes for a given city from the geoCity table
	 *
	 * zipCodes: {...}
	*/
    public function listZipCodesAction() {
		$this->createHeader();

		$dataSrc = "";
		$country = $this->params()->fromRoute('id'); // geoCity_ISO3166
		$prov = $this->params()->fromRoute('key'); //geoCity_ISO3166_2
		$city = $this->params()->fromRoute('key2'); //geoCity_cityName

		if ( empty($city) || $city === NULL || strtolower($city) == "null" ) {
			$city = $this->params()->fromQuery('city');
		}
		
		
		if ( !empty($country) && !empty($prov) && !empty($city) ) {
			$dataSrc = $this->getGeoCity()->getZipCodesJSON($country, $prov, $city);
		}

		$result = $this->buildJsonFromTable($dataSrc, 'zipCodes');
		return($result);
	}//end  function listCityAction


	/*
	 * This function will return password acceptance status
	 *
	 * PWstatus: {1 | 0}
	*/
    public function checkUserPassAction() {
		$this->createHeader();
		$pw = $this->params()->fromRoute('key'); // password

		$result= new JsonModel(array(
			'PWstatus' => ($this->isPasswordBanned(base64_decode($pw))===true? 0:1), //must be integer, boolean not acceptable
			'dt' => date(DATE_ATOM)
		));
		
		return($result);
	
    }//end function checkUserPassAction


    /**
     * check if a given password is banned
	 ** COPIED from User Controller **
     * @param string $password
     * @return boolean
     */
    private function isPasswordBanned($password) {
        $sql = new \Zend\Db\Sql\Sql($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $select = $sql->select();
        $select->from('bannedPasswords')->where(array('bpwd_text' => $password));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        if (count($results) > 0) {
            // increment the bad password count
            $update = $sql->update('bannedPasswords');
            $update->set(array('bpwd_count' => new \Zend\Db\Sql\Expression('bpwd_count+1')));
            $update->where(array('bpwd_text' => $password));
            $updatestatement = $sql->prepareStatementForSqlObject($update);
            $updatestatement->execute();

            return true;
        } else {
            return false;
        }
    }

	
	
	/*
	 * Output JSON header for AJAX call
	 *
	*/
    private function createHeader() {
		//header('Content-Type: application/json');
		header('Content-Type: text/plain');
		header('Cache-Control: no-cache');
	}

    /**
     * Check if user is already authenticated
     * 
     */
    protected function checkAuthentication() {
        if (!$this->laIdentity()->hasIdentity()) {
            return $this->redirect()->toRoute('user', array('action' => 'signin'));
        }
    }

    private function getGeoContinent() {
        return $this->getServiceLocator()->get('geoContinentTable');
    }

    private function getGeoCountry() {
        return $this->getServiceLocator()->get('geoCountryTable');
    }

    private function getGeoStateCity() {
        return $this->getServiceLocator()->get('geoStateCityTable');
    }

    private function getGeoCity() {
        return $this->getServiceLocator()->get('geoCityTable');
    }

    private function getGeoLang() {
        return $this->getServiceLocator()->get('geoLangTable');
    }

    private function getUserTable() {
        return $this->getServiceLocator()->get('UserTable');
    }
	
}//end class SysController
