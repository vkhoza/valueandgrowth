<?php 
require ('head.php');
loginCheck();
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
	
	var html = nt.loadTemplate("SUPPORT");
	nt.displayElems("#rightDiv", html);
	
	var html = nt.loadTemplate("<?php  echo ($myadmin == 1) ? "ADMIN_LEFT_MENU" : "LEFT_MENU";  ?>");
	nt.displayElems("#leftDiv", html);
	
	$('.rows').sortable({ handle: '.box-header'});
	
	leftMenuUtilities('#s', true);
});

function roll(self) {
	$(self).next().slideToggle(function() {
		$(self).find('img').attr('src', $( $(self).next() ).is(":visible") ? 'files/uparrow.png' : 'files/downarrow.png' );			
	});
}
</script>

<?php require('footer.php');?>