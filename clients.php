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

var tabs = ["clients","users"];

var urlhash = location.hash;//get the part of the url after the hash  i.e. after #

if ( urlhash != "" ) {
	var dets = urlhash.split("#");
	tab = dets[1];
	var f = $.inArray(tab, tabs);
	if( f == -1 ) tab = "clients";

}else tab = "clients";

var nt = new Templates();

var html = nt.loadTemplate("ADMIN_LEFT_MENU");
nt.displayElems("#leftDiv", html);
	
leftMenuUtilities('#c', true);

if( tab == "clients" ) {

	var html = nt.loadTemplate("<?php  echo isset($user) ? "CLIENTS" : "ADMIN-SIGNIN";  ?>");
	nt.displayElems("#rightDiv", html);
	var nc = new Clients();
	nc.initClients();
	nc.leftMenuChanger();
		
}else if( tab == "users" ) {
	
	nt.displayElems("#rightDiv", nt.loadTemplate("USERS"));
	
	var nc = new Clients();
	nc.initUsers();	
	nc.leftMenuChanger();
	
}

	
});
</script>

<?php require('footer.php');?>