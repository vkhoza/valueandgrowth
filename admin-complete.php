<?php 
if( ! isset($_GET['admin']) || ! isset($_GET['see'])) header ('location: admin.php');//if necessary GET variables are not set redirect

require('head.php');
loginCheck_redirect();
require('header.php');
?>

<script>
$(function(){
	var nt = new Templates();
	
	var html = nt.loadTemplate("ADMIN_LEFT_MENU");
	nt.displayElems("#leftDiv", html);	
	$('#c').show();
	//load html elements
	var html = nt.loadTemplate("<?php echo (isset($user)) ? "COMPLETE_FORM_HEADER" : "SUMMARY" ?>");
	nt.displayElems("#rightDiv", html);
	
	nt.displayElems("#formContainer", nt.loadTemplate("ASSESSMENT_FORM"));//load first form upon first page load. Header has already been loaded at this point

	//assign events to the left menu list items
	leftMenuUtilities('#C', false);	
	
	//====================================================================.
	//this functionality is usually handled by leftMenuUtilities(), but 
	//a special case had to be made for this page
	id = '#C';
	$('.container').find('.listItem').each(function() {
		if($(this).hasClass('selectedListItem')) {
			$(this).removeClass('selectedListItem').animate({'border-left':'hidden',background:'white'}, 5000);
		}
	});
	$(id).addClass('selectedListItem');
	var id = $(id).attr('id');
	if($('.subListItem').hasClass(id)) $('.subListItem').toggle();
	//=====================================================================
	
	var cf = new CompleteForms();
	cf.init();
		
});
</script>


<?php require('footer.php');?>
