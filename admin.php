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
	
	var html = nt.loadTemplate("<?php  echo isset($user) ? "ADMIN-SUMMARY" : "ADMIN-SIGNIN";  ?>");
	nt.displayElems("#rightDiv", html);
	
	var html = nt.loadTemplate("ADMIN_LEFT_MENU");
	nt.displayElems("#leftDiv", html);
	
	$('.rows').sortable({ handle: '.box-header'});
	
	leftMenuUtilities('#c');
	
	var cs = new ClientSummary();
	<?php if(isset($user)) echo"cs.init();"; ?>
	
});
</script>

<?php require('footer.php');?>