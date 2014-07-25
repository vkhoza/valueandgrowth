<?php 
require ('head.php');
loginCheckAdmin_redirect();
require('header.php');

if(isset($user)) {
$qy = mysql_query("SELECT*FROM memberinfo WHERE email = '$user'");
$rw = mysql_fetch_array($qy);
$name = $rw['firstname'].' '.$rw['lastname'];
}
?>

<script>
$(function(){
	var nt = new Templates();
	
	var html = nt.loadTemplate("<?php  echo isset($user) ? "BIG_CALENDER" : "SIGNIN";  ?>");
	nt.displayElems("#rightDiv", html);
	
	var html = nt.loadTemplate("LEFT_MENU");
	nt.displayElems("#leftDiv", html);
	
	$('.rows').sortable({ handle: '.box-header'});
	
	leftMenuUtilities('#cl', true);
	
	var vc = new vgCalender();
	<?php if(isset($user)) echo"vc.init();"; ?>
	
});
</script>

<?php require('footer.php');?>