<?php

/**
 * Description of MailServer
 *
 * @author kimsreng
 */

namespace Common\Mail;

class MailServer {

    const DOWN_MESSAGE = "down";

    /**
     * Mail server configuration from global config
     * 
     * @var array 
     */
    protected $mailOption = [];

    /**
     *
     * @var type 
     */
    protected $clientContinent=0;

    /**
     * A list of servers that are marked as offline or unavailable due to misconfiguration
     * 
     * @var array 
     */
    protected $offServer = [];

    /**
     * the current server that is being tested against
     * 
     * @var array item 
     */
    protected $selectedServerOption;
    protected $mailService;
    protected $smptOption;
    protected $continentTable;

    public function __construct($mailOpions, $geoCoutryTable, $continentTable) {
        $this->mailOption = $mailOpions;
        $this->mailService = new \Common\Mail\Transport\Smtp();
        $this->smptOption = new \Zend\Mail\Transport\SmtpOptions();
        $this->continentTable=$continentTable;
        //set client continent
        $language = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        list($lang, $country) = explode('_', str_replace('-', '_', $language));
        $geocountry = $geoCoutryTable->getByISO3166(strtoupper($country));
        if($geocountry){
            $this->clientContinent=$geocountry->geoCountry_continent;
        }
        
    }

    public function getServer() {
        return $this->selectServer();
//        var_dump($this->offServer);
//        return $service;
    }

    /**
     * Get an available server after testing for connection and authentication
     * 
     * @return \Common\Mail\Transport\Smtp
     */
    protected function testServer($selectedOption) {
        $this->smptOption->setFromArray($selectedOption);
        $mail = new \Common\Mail\Transport\Smtp($this->smptOption);
        try {
            $mail->testConnection();
        } catch (\Exception $ex) {
            //echo $ex->getMessage();
            $mail->disconnect();
            return false;
        }
        $mail->disconnect();
        $this->mailService = $mail;
        return true;
    }

    /**
     * 
     * @return type
     */
    protected function selectServer() {

        $serverKeys = array_keys($this->mailOption['list']);
        $continentId = $this->clientContinent;
        if (in_array($continentId, $serverKeys) && !in_array($continentId, $this->offServer)) {
            for ($i = 0; $i < count($this->mailOption['list'][$continentId]); $i++) {
                if (!$this->mailOption['list'][$continentId][$i]['enable']) {
                    continue;
                }
                if ($this->testServer($this->mailOption['list'][$continentId][$i]['config'])) {
                    return $this->mailService;
                }
            }
            //mark as unavailable
            $this->offServer[] = (int) $continentId;
        }
        //check if the parent continent is available
        $parentContinent = $this->getParentContinent();
        if (in_array($parentContinent, $serverKeys) && !in_array($parentContinent, $this->offServer)) {
            for ($i = 0; $i < count($this->mailOption['list'][$parentContinent]); $i++) {
                if (!$this->mailOption['list'][$parentContinent][$i]['enable']) {
                    continue;
                }
                if ($this->testServer($this->mailOption['list'][$parentContinent][$i]['config'])) {
                    return $this->mailService;
                }
            }
            //mark as unavailable
            $this->offServer[] = (int) $parentContinent;
        }

        //get defualt server
        if (!in_array($this->mailOption['default'], $this->offServer)) {
            for ($i = 0; $i < count($this->mailOption['list'][$this->mailOption['default']]); $i++) {
                if (!$this->mailOption['list'][$this->mailOption['default']][$i]['enable']) {
                    continue;
                }
                if ($this->testServer($this->mailOption['list'][$this->mailOption['default']][$i]['config'])) {
                    return $this->mailService;
                }
            }
            //mark as unavailable
            $this->offServer[] = (int) $this->mailOption['default'];
        }
        //get other available servers
        $available = array_diff($serverKeys, $this->offServer);
        if (count($available) > 0) {
            foreach ($available as $value) {
                for ($i = 0; $i < count($this->mailOption['list'][$value]); $i++) {
                    if (!$this->mailOption['list'][$value][$i]['enable']) {
                        continue;
                    }
                    if ($this->testServer($this->mailOption['list'][$value][$i]['config'])) {
                        return $this->mailService;
                    }
                }
                $this->offServer[] = (int) $value;
            }
        }
        // var_dump($this->offServer);
        return self::DOWN_MESSAGE;
    }
    
    
    protected function getParentContinent(){
        $row = $this->continentTable->getById($this->clientContinent);
        if($row){
            return $row->geocontinent_parent;
        }
        return 0;
    }

}
