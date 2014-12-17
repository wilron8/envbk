<?PHP
/************************************************************************/
/* CrimShield.com - cryptographp.inc.php			                    */
/* ==========================================                           */
/*                                                                      */
/*	Written by Richie Bartlett Jr 										*/
/*  tests the CAPTCHA image with different profiles						*/
/*                                                                      */
/* 		Copyright 2007. ALL RIGHTS RESERVED. CrimShield.com				*/
/************************************************************************/


include("cryptographp.fct.php");

?>


<HTML><TITLE>Crypto Test</TITLE><BODY>
<DIV align="center">

<FORM action="verifier.php" method="post">
<TABLE cellpadding=1>
  <TR><TD align="center">Type this Code:<BR><?php display_crypto();?></TD></TR>
  <TR><TD align="center"><INPUT type="text" name="sec_code"></TD></TR>
  <TR><TD align="center"><INPUT type="submit" name="submit" value="submit"></TD></TR>
</TABLE>
</FORM>
<BR><BR><BR>

</DIV>
<? 

//phpinfo(INFO_VARIABLES);//
?></BODY>
</HTML>


