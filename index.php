<?php 
require('head.php');
loginCheck();
require('header.php');

if(isset($user)) {//declare user name variable for templates below

$qy = mysql_query("SELECT*FROM memberinfo WHERE email = '$user'");
$rw = mysql_fetch_array($qy);
$name = $rw['firstname'].' '.$rw['lastname'];

}

?>
<script>
$(function(){
	$('#subordinatesDiv').show();
	
	var nt = new Templates();
	
	var html = nt.loadTemplate("<?php  echo isset($user) ? "SUMMARY" : "SIGNIN";  ?>");
	nt.displayElems("#rightDiv", html);
	
	var html = nt.loadTemplate("LEFT_MENU");
	nt.displayElems("#leftDiv", html);
	
	//$('.rows').sortable({ handle: '.box-header'});
	
	leftMenuUtilities('#f');

	$('#summarycalender .box-content').calender();//$.jQuery.extend function
	
	var na = new AssessmentForm();
	na.subInit();
});
</script>
<script src='js/Usersummary.js'></script>

<?php require('footer.php');?>