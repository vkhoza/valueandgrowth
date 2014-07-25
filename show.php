<?php 

require('head.php');
loginCheck();
require('header.php');

if(isset($_GET['reset']) && isset($_GET['email'])) {

//call password reset script if GET variable is set and the user has been confirmed to have requested a password reset

$reset = $_GET['reset'];
$email = $_GET['email'];
$q = mysql_query("SELECT*FROM memberinfo WHERE reset = '$reset' AND email = '$email'");
$r = mysql_fetch_array($q);

$cipher = getToken(strlen($_GET['email']), $_GET['email']);
mysql_query("UPDATE memberinfo SET token = '$cipher' WHERE email = '".$_GET['email']."'");

//set session values
$_SESSION['token'] = $cipher;

if(mysql_num_rows($q) == 1) echo "<script src='js/confirmreset.js'></script><script src='js/headerscript.js'></script>";
else echo "<script>window.location.href = 'index.php';</script>";

foreach ($r as $a => $b) ${$a} = $b;

}

?>

<script>
$(function(){
		
	$('.rows').sortable({ handle: '.box-header'});
	
	leftMenuUtilities('#f');
	
});
</script>

<?php require('footer.php');?>