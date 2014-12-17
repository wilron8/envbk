<?PHP
/************************************************************************/
/* RichieBartlett.com - cryptographp.inc.php			                */
/* ==========================================                           */
/*                                                                      */
/*	Written by Richie Bartlett Jr 										*/
/*  creates the CAPTCHA image with different profiles					*/
/*                                                                      */
/* 		Copyright 2013. ALL RIGHTS RESERVED. RichieBartlett.com			*/
/************************************************************************/



$a = session_id();
if($a == '') @session_start();//make sure we have a current session
//error_reporting(E_ALL ^ E_NOTICE);
setcookie("cryptcookietest", "1");
$cfgProfile=0;
srand((double)microtime()*1000000);
$cfgProfile = substr(rand (0, 15) * time(), 2, 1);
//echo "cfgProfile=$cfgProfile\n<BR>";
switch($cfgProfile){
	case 1:
		$cfgProfile="profile/blackwhite/cryptographp.cfg.php";
		break;
	case 2:
		$cfgProfile="profile/bluenoise/cryptographp.cfg.php";
		break;
	case 3:
		$cfgProfile="profile/colornumber/cryptographp.cfg.php";
		break;
	case 4:
		$cfgProfile="profile/gray/cryptographp.cfg.php";
		break;
	case 5:
		$cfgProfile="profile/pencil/cryptographp.cfg.php";
		break;
	case 6:
		$cfgProfile="profile/xcolor/cryptographp.cfg.php";
		break;
	default:
		$cfgProfile=0;
		break;
}//end cfgProfile
$_SESSION['configfile']=$cfgProfile;

//echo "cfgProfile=$cfgProfile\n<BR>";

Header("Location: cryptographp.inc.php");
?>
