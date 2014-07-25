<?php 
require('head.php');
loginCheck();
require('header.php');

if( ! isset($_GET['sent']) || ! isset($_GET['name'])  || ! isset($_GET['email'])) header('location:index.php');

$q = mysql_query("SELECT*FROM memberinfo WHERE email = '".$_GET['email']."' AND confirm = '0'"); 
$r = mysql_fetch_array($q);
if( mysql_num_rows($q) == 0 ) header('location:index.php');//check if user has registered but not yet confirmed email address, if not redirect

$cipher = getToken(strlen($_GET['email']), $_GET['email']);
mysql_query("UPDATE memberinfo SET token = '$cipher' WHERE email = '".$_GET['email']."'");

//set session values
$_SESSION['token'] = $cipher;

?>
<script src='js/headerscript.js'></script>
<script>
function reloadPage() {
window.location.href= 'index.php';
}

$(function(){

	$('#menuOnLeft').hide();
	var nt = new Templates();
	
	var html = <?php  
	echo "nt.loadTemplate('"; echo (isset($_GET['sent']) && isset($_GET['name'])  && isset($_GET['email'])) ? "THANKYOU" : "SIGNIN"; echo "')";
	?>
	
	nt.displayElems("#rightDiv", html);
	
	//$('.rows').sortable({ handle: '.box-header'});
	
	$('#triggerupload').click(function() {
		$('#avator').show().trigger('click').hide();  
	}); 
	
	$('#apply button').attr({"onclick":"applypicture(reloadPage)"});

});
</script>
<script src='js/Usersummary.js'></script>

<?php require('footer.php');?>


