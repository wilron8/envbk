<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_ztWeb = "localhost";
$database_ztWeb = "envitz";
$username_ztWeb = "root";
$password_ztWeb = "1sexyG0d";
$ztWeb = mysql_pconnect($hostname_ztWeb, $username_ztWeb, $password_ztWeb) or trigger_error(mysql_error(),E_USER_ERROR); 
?>