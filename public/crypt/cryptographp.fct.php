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

if(session_id() == "") session_start();
$_SESSION['cryptdir']= "./";


function display_crypto($showReload=true){
	echo "<img name='cryptogram' id='cryptogram' src='/crypt/cryptographp.php' title='Crypto Key' alt='Crypto Key'>&nbsp;&nbsp;";
	if($showReload){
		echo "<a  style=\"cursor:pointer\"";
		echo " onclick=\"document.getElementById('cryptogram').src='/crypt/cryptographp.php?seed='+Math.random();\">";
		echo "<img src=\"/crypt/images/reload.png\" title='Refresh Crypto Key' alt='Refresh Crypto Key'></a>&nbsp;&nbsp;&nbsp;";
	}//end if reload
	//echo "</tr></table>";
}//end function display_crypto


function check_crypto($code){
	// verify code
	if(!empty($_SESSION['configfile'])) include ($_SESSION['configfile']);
	$code = addslashes ($code);
	$code = str_replace(' ','',$code); //filter out spaces
	$code = ($difuplow?$code:strtoupper($code));
	switch (strtoupper($cryptsecure)) {    
		case "MD5"  : $code = md5($code);break;
		case "SHA1" : $code = sha1($code);break;
	}//end switch cryptsecure
	if($_SESSION['cryptcode'] && ($_SESSION['cryptcode'] == $code)){
		unset($_SESSION['cryptreload']);
		if($cryptoneuse) unset($_SESSION['cryptcode']);   
		return true;
	}else{
		 $_SESSION['cryptreload']= true;
		 return false;
	}//end if cryptcode
}//end function check_crypto

?>
