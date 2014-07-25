<?php 

require ('head.php');

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

?>

<script>
$(function(){
	var nt = new Templates();
	
	var html = nt.loadTemplate("REGISTRATION");
	nt.displayElems("#rightDiv", html);
	
});
</script>

 
<?php require('footer.php');?>
