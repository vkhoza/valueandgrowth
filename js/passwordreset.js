$(function() {

$('#reset').click(function(event) {
var nt = new Templates();
var html = nt.loadTemplate("RESET");

dimBack();
$('#rightDiv').append(html);
$('#passwordreset #email').focus();

$('#close').click(function()  {
	$('#transparent').remove();
	$('.formsubmitpopup').remove();
	clearBack();
});

event.preventDefault();

});


});


function resetGo() {

var email = $('#passwordreset #email').val();

var elems = [];
if( ! email.match (/.+@.+\..+/) ||  email == "") {
	elems.push({'tag': '#passwordreset #email', 'message': 'Your email doesn\'t seem quite right, try again!' });
	err = {'ok':false,  'elem': elems};
	var na = new AssessmentForm();
	na.error(err.elem);
	$('#loadimage').hide();
	event.preventDefault();
	return false;
}

$('#loadimage').show();
var options = {
    url:   'reset.php',   // target element(s) to be updated with server response 
    // beforeSubmit:  beforeSubmit,  // pre-submit callback 
	type: 'POST',
	success : function(response) {
		//alert(response);
		var data = JSON.parse(response);
		if(data.ok) {
			$('#pwreset .box-content').html("A password reset link has been sent to the email address <b>"+data.message.email+"</b>. Follow the link in that \
			email to complete you pasword reset.");
			$('#loadimage').hide();
		}else if( ! data.ok) {
			$('#loadimage').hide();
			var na = new AssessmentForm();
			na.error([{'tag': '#passwordreset #email', 'message': data.message }]);
		}else if(data.redirect) window.location.href = 'index.php';
		 else alert(data.message);
	}
}; 

$('#passwordreset').unbind('submit');
$('#passwordreset').submit(function( event ) {
event.preventDefault();//stops browser from doing its reload thing

$(this).ajaxSubmit(options);  //Ajax Submit form

});
}
