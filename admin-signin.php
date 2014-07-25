<?php
//require("require/functions.php");
require("config.php");


$username = isset($_POST['email']) ? $_POST['email'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$password = bin2hex ($password);//passwords saved on database are encrypted. This encodes entered password in order to match below

//echo 'user: '.$password.' database :'.$users[$username]['password'];


if(
$username AND $password
AND isset($users[$username])
AND $users[$username]['password'] == $password
AND $users[$username]['admin'] == '1'
) {

	if(isset($_COOKIE['cb_ID'])) setcookie('cb_ID',$username,time()-3600);
	
	$cipher = getToken(strlen($username),$username);
	//setcookie('cb_ID',$cipher,time()+60*60*24*30);
	
	connect();
	mysql_query("UPDATE memberinfo SET token = '$cipher' WHERE email = '$username'");
	mysql_query("UPDATE sessions SET name = '$cipher' WHERE email = '$username'");
	
	//set session values
	$_SESSION['token'] = $cipher;
	
	header("Location:admin.php");
	
//echo $username.$password;
}else header('location:admin.php?option=dash&bl=3&&u='.$username);
?>