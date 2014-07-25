<?php
//require ('require/functions.php');
require ('config.php');
//$cb_ID = $_COOKIE['cb_ID'];
//setcookie('cb_ID','',time()-3600);
$session = session_value('token');
$email = get_column($session, 'email');

mysql_query("UPDATE memberinfo SET token = '0' WHERE email = '$email'");
unset($_SESSION['token']);

//mysql_query("UPDATE sessions SET name = '' WHERE email = '$session'");
error_reporting(E_ALL | E_WARNING | E_NOTICE);
ini_set('display_errors', TRUE);

header("location:index.php");
die('should have redirected by now');
		
?>
