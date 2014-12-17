<?php

/**
 * Wrap of all client's information 
 *
 * @author kimsreng
 */

namespace Common\ClientSniffer;

require_once 'browser_detection_php_ar.php';

class Sniffer {

    //vars for the browser_detection_php_ar script
    private $uaInfo = NULL;
    public $userOS = "N/A";
    public $userOSver = "N/A";
    public $userOSmake = "N/A";
    public $userBrowser = "N/A";
    public $userBrowserVer = "N/A";
    public $userMobile = false;
    public $logicalXDPI = "";
    public $logicalYDPI = "";

    private function get_uaInfo() {
        $this->uaInfo = browser_detection('full');

        // $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number
        if ($this->uaInfo[8] == 'mobile') {
            $this->userMobile = true;
            if ($this->uaInfo[13][0]) {
                $this->userOS = ucwords($this->uaInfo[13][0]);
                if ($this->uaInfo[13][7]) {
                    $this->userOSver = $this->uaInfo[13][7];
                }
            }
            if ($this->uaInfo[13][3]) {
                // detection is actually for cpu os here, so need to make it show what is expected
                if ($this->uaInfo[13][3] == 'cpu os') {
                    $this->uaInfo[13][3] = 'ipad os';
                }
                $this->userOSver = $this->uaInfo[13][4];
            }
            // let people know OS couldn't be figured out
            if (!$this->uaInfo[5]) {
                $this->userOSver = "N/A";
            }
            if ($this->uaInfo[13][1]) {
                $this->userBrowserVer = ucwords($this->uaInfo[13][1]) . ' ' . $this->uaInfo[13][2];
            }
        }

        switch ($this->uaInfo[5]) {
            case 'win':
                $this->userOS = 'Windows ';
                break;
            case 'nt':
                $this->userOS = 'Windows NT ';
                break;
            case 'lin':
                $this->userOS = 'Linux ';
                $this->userOSmake = 'Linux ';
                break;
            case 'mac':
                $this->userOS = 'Mac ';
                break;
            case 'iphone':
                $this->userOS = 'Apple ';
                break;
            case 'unix':
                $this->userOS = 'Unix';
                $this->userOSmake = 'Unix ';
                break;
            default:
                $this->userOS = $this->uaInfo[5];
        }

        if ($this->uaInfo[5] == 'nt') {
            $this->userOSmake = "Microsoft";
            if ($this->uaInfo[6] == 5) {
                $this->userOSver = '5.0 (Windows 2000)';
            } elseif ($this->uaInfo[6] == 5.1) {
                $this->userOSver = '5.1 (Windows XP)';
            } elseif ($this->uaInfo[6] == 5.2) {
                $this->userOSver = '5.2 (Windows XP x64 Edition or Windows Server 2003)';
            } elseif ($this->uaInfo[6] == 6.0) {
                $this->userOSver = '6.0 (Windows Vista)';
            } elseif ($this->uaInfo[6] == 6.1) {
                $this->userOSver = '6.1 (Windows 7)';
            } elseif ($this->uaInfo[6] == 'ce') {
                $this->userOSver = 'CE';
            }
        } elseif ($this->uaInfo[5] == 'iphone') {
            $this->userOS = "IPhone OS";
            $this->userOSmake = "Apple";
            if (!$this->userMobile)
                $this->userOSver = $this->uaInfo[6];
        }elseif (( $this->uaInfo[5] == 'mac' ) && ( strstr($this->uaInfo[6], '10') )) {
            // note: browser detection now returns os x version number if available, 10 or 10.4.3 style
            $this->userOSmake = "Apple";
            $this->userOSver = $this->uaInfo[6];
        } elseif ($this->uaInfo[5] == 'lin') {
            $this->userOSmake = "Linux / UNIX";
            $this->userOSver = ( $this->uaInfo[6] != '' ) ? 'Distro: ' . ucwords($this->uaInfo[6]) : 'Smart Move!!!';
        } elseif ($this->uaInfo[5] && $this->uaInfo[6]) {
            // default case for where version number exists
            $this->userOSver.= " " . ucwords($this->uaInfo[6]);
        } elseif ($this->uaInfo[5] && $this->uaInfo[6] == '') {
            $this->userOSver = ' (version unknown)';
        } elseif ($this->uaInfo[5]) {
            $this->userOSver = ucwords($this->uaInfo[5]);
        }

        if ($this->uaInfo[0] == 'moz') {
            $a_temp = $this->uaInfo[10]; // use the moz array
            $this->userBrowser = ($a_temp[0] != 'mozilla') ? 'Mozilla/' . ucwords($a_temp[0]) . ' ' : ucwords($a_temp[0]) . ' ' . $a_temp[1];
            $this->userBrowserVer = $a_temp[1];
            //$this->userBrowserVer = ($a_temp[0] != 'galeon') ? $a_temp[3] : "";
        } elseif ($this->uaInfo[0] == 'ns') {
            $this->userBrowser = 'Netscape';
            $this->userBrowserVer = $this->uaInfo[1];
        } elseif ($this->uaInfo[0] == 'webkit') {
            $a_temp = $this->uaInfo[11]; // use the webkit array
            $this->userBrowser = ucwords($a_temp[0]) . " WebKit";
            $this->userBrowserVer = $a_temp[1];
        } elseif ($this->uaInfo[0] == 'ie') {
            $this->userBrowser = strtoupper($this->uaInfo[7]);
            if (array_key_exists('14', $this->uaInfo) && $this->uaInfo[14]) {
                $this->userBrowser .= '(compatibility mode)';
                $this->userBrowserVer = number_format($this->uaInfo[14], '1', '.', '');
                $full .= '<br />Compatibility Version: ' . $this->uaInfo[1];
            } else {//Full Version Info:
                $this->userBrowserVer = ( $this->uaInfo[1] ) ? $this->uaInfo[1] : 'Not Available';
            }
        } else {
            $this->userBrowser = ucwords($this->uaInfo[7]);
            $this->userBrowserVer = ( $this->uaInfo[1] ) ? $this->uaInfo[1] : 'Not Available';
        }

        return $this->uaInfo;
    }

//end  function get_uaInfo

    /**
     * get_browserName() Returns the string of detected browser name
     * 
     * 
     * */
    public function get_browserName() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userBrowser;
    }

//end  function get_browserName

    /**
     * get_browserVer() Returns the string of detected browser version
     * 
     * 
     * */
    public function get_browserVer() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userBrowserVer;
    }

//end  function get_browserVer

    /**
     * get_osName() Returns the string of detected OS manufacturer
     * 
     * 
     * */
    public function get_osMake() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userOSmake;
    }

//end  function get_osMake

    /**
     * get_osName() Returns the string of detected OS name
     * 
     * 
     * */
    public function get_osName() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userOS;
    }

//end  function get_osName

    /**
     * get_osVer() Returns the string of detected OS version
     * 
     * 
     * */
    public function get_osVer() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userOSver;
    }

//end  function get_osVer

    /**
     * get_userMobile() Returns the boolean of true of hand-held device
     * 
     * 
     * */
    public function isUserMobile() {
        // get the ua info if needed
        $this->uaInfo = $this->uaInfo != "" ? $this->uaInfo : $this->get_uaInfo();
        return $this->userMobile;
    }

//end  function get_userMobile

    /**
     * get_logicalXDPI() Returns the height of user display
     * 
     * 
     * */
    public function get_logicalXDPI() {
        //if ===0 check for session var
        if ($this->logicalXDPI == "") {
            //$this->logicalXDPI=min(intval($_SESSION["logicalXDPI"]),0); // >0+
            $this->logicalXDPI = $_SESSION["logicalXDPI"];
        }//end if not stored
        return $this->logicalXDPI;
    }

//end  function get_logicalXDPI

    /**
     * get_logicalYDPI() Returns the width of user display
     * 
     * 
     * */
    public function get_logicalYDPI() {
        //if ===0 check for session var
        if ($this->logicalYDPI == "") {
            //$this->logicalYDPI=min(intval($_SESSION["logicalYDPI"]),0); // >0+
            $this->logicalYDPI = $_SESSION["logicalYDPI"];
        }//end if not stored
        return $this->logicalYDPI;
    }

//end  function get_logicalYDPI

    /**
     * set_logicalXDPI() sets the height of user display
     * 
     * 
     * */
    public function set_logicalXDPI($logicalXDPI) {
        $this->logicalXDPI = $logicalXDPI;
    }

//end  function get_logicalXDPI

    /**
     * set_logicalYDPI() sets the width of user display
     * 
     * 
     * */
    public function set_logicalYDPI($logicalYDPI) {
        $this->logicalYDPI = $logicalYDPI;
    }

}
