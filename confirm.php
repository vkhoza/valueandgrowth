<?php 
require ('head.php');
connect();
if(isset($_COOKIE['cb_ID'])){

	$cookie = mysql_real_escape_string($_COOKIE['cb_ID']);
	$temp = $cookie;
	
	$token_query = mysql_query("SELECT*FROM memberinfo WHERE token = '$temp' AND confirm = '1'");
	$token_result = mysql_fetch_array($token_query);
	$token = getToken(strlen($temp),$temp);
	if(mysql_fetch_array($token_query)==1){
		$user = $token_result['email'];
		header("location:index.php");
	}
}

require('header.php');

$email = isset($_GET['email']) ? mysql_real_escape_string($_GET['email']) : "";

if ($email == "") header ('index.php');
$err_msg = array();

	$q = mysql_query("SELECT*FROM memberinfo WHERE email = '$email' AND confirm = '0'");
	$r = mysql_fetch_array($q);
	if(mysql_num_rows($q)>0) {
	
	
	$start_date = date('D M d Y G:i:s');
	mysql_query("UPDATE memberinfo SET confirm = '1' WHERE id = '".$r['id']."'");
	mysql_query("INSERT INTO settings (userid, lastseen, progress) VALUES ('".$r['id']."', '$start_date', '0')");
	
	$token = getToken(strlen($email),$email);
	
	$firstname = $r['firstname'];
	$lastname = $r['lastname'];

	}else $err_msg['Account Error'] = 'Account details not found in database or account already activated! Contact admin for help.';

//}else $err_msg['Account Error'] = 'Account details not found in database! Contact admin for help.';


?>

<script>
$(function(){
	var nt = new Templates();
	
	var html = nt.loadTemplate("CONFIRM");
	nt.displayElems("#rightDiv", html);
	
});
</script>

<?php require('footer.php');?>
