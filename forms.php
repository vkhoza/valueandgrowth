<?php 
require('head.php');
loginCheck_redirect();


if( isset($_GET['admin']) && isset($_GET['see']) && $admin ) {//dealing with the admin scripts to run

$userid = $_GET['see'];
$_SESSION['view'] = $userid;
$qr = mysql_query("SELECT*FROM memberinfo WHERE id = '$_SESSION[view]'");
$rr = mysql_fetch_assoc($qr);
		
$session = getToken(strlen($rr['email']), $rr['email']);
echo "<v id='ai' value='$userid'></v>";

}
else{//should only run when not in view mode
$cf = mysql_query("SELECT*FROM completeforms WHERE userid = '$userid'");
while( $cr = mysql_fetch_array($cf) ){//if the user has submitted at least one form
	$name = $cr['name'];
	if( $name == "ASSESSMENT" ) {
		if(	$cr['signedbyleader'] == 1 || $cr['signedbyadmin'] == 1 ){ //if assessmentform has been signed of on
			$assessmentsignedoff = 1;
		}
	}
	elseif( $name == "HPAP" ) {
		if( $cr['signedbyleader'] == 1 || $cr['signedbyadmin'] == 1 ){ //if hpapform has been signed of on
			$hpapsignedoff = 1;
		}
	}
}
}

require('header.php');

?>

<script src='js/tabselection.js'></script>
<script>
$(function(){

	$('#subordinatesDiv').show();
	var nt = new Templates();
	
	<?php if(isset($user)) echo "$('#summaryDiv').show();";?>
	var html = nt.loadTemplate("LEFT_MENU");
	nt.displayElems("#leftDiv", html);	
	
	//load html elements
	$("#rightDiv").html(nt.loadTemplate(header));
	$("#formContainer").append(nt.loadTemplate(form));//load first form upon first page load. Header has already been loaded at this point

	$('.tabContainer').find('.tabs').each(function(){
		$('.tabs').attr('class', 'tabs');
	});
	$('.tabContainer #'+form).attr('class', 'tabs selected');

	//assign events to the left menu list items
	leftMenuUtilities('#f', true);	
	
	var html = nt.loadTemplate("QUICK_SUMMARY");
	$('#summaryDiv').append(html);
	
	if( tab == "assessment" ) {
	
		var ns = new AssessmentForm();
		
		ns.tabChanger();	
		ns.leftMenuChanger();
		ns.init();//initialize form
		$('.tabs').on('click', function(){ //this is necessary to reassign events after appending a new template
			ns.init();//initialize form
		});	

		ns.subInit();
		
	}else if( tab == "hpap" ) {
	
		var hpap = new HPAPForm();
		hpap.tabChanger();	
		hpap.leftMenuChanger();
		hpap.init();	

		$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
			hpap.init();//initialize form
		});			
		
		hpap.subInit();
	}
	
	if( typeof adminFormViewer != 'undefined')
		disable_buttons();
	
});
</script>


<?php require('footer.php');?>
