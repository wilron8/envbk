<?php 

include("cryptographp.fct.php");
?>


<HTML>
<?php
if(check_crypto($_POST['sec_code'])) 
	echo "<a><font color='#009700'>=> Correct !</font></a>" ;
else echo "<a><font color='#FF0000'>=> Error: Wrong Code</font></a>" ;
?>
</HTML>

