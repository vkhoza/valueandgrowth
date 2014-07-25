$(function() {

var ret = true;
var na = new AssessmentForm();
var nt = new Templates();
var html = nt.loadTemplate("PROFILE");

$('html').find('#completeprofileform').each(function() { ret = false; });
if(ret) {
dimBack();
$('#rightDiv').append(html);

$('#reset').slideDown(function() {
$('#highlight').animate({backgroundColor:"rgb(255,255,190)"}, 500).animate({backgroundColor:"rgb(240,240,240)"}, 500);
$('#highlight').animate({backgroundColor:"rgb(255,255,190)"}, 500).animate({backgroundColor:"rgb(240,240,240)"}, 500);
$(this).append('<input type=\'hidden\' name=\'isreset\' id=\'isreset\'/>');
});

$('.formsubmitpopup').draggable({handle: '.box-header'});
$.ajax({// this is necessary because when a new picture is uploaded the template remains with an invalid url. This ajax request updates the url
	url:'server.php',
	type:'POST',
	data:{action:"get",t:"memberinfo",cl:"email"}
}).done(function(response) {
	//alert(response);
	dt = JSON.parse(response);
	if(dt.ok) {
		$('#currentprofilepicture').attr({"src":dt.message.avator});
		na.updateFormValues('#profileform', dt.message, []);
		na.updateFormValues('#profileform2', dt.message, ["designation"]);
		$('#name').val(dt.message.firstname+' '+dt.message.lastname);
	}else window.location.href = 'index.php';
});



$('#submitchanges').unbind('click');
$('#submitchanges').click(function(event) {
event.preventDefault();//stops browser from doing its reload thing

var d1 = $('#profileform').serializeObject();
var d2 = $('#profileform2').serializeObject();
mix(d2, d1);//merging the two form objects. For some reason if a form spans across two table columns it is not possible to serialize the form all at once

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: d1,
	success: function(response) {
	//	alert(response);
		var data = JSON.parse(response);
		if(data.ok) {
			$('#transparent').remove();
			$('.formsubmitpopup').remove();
			clearBack();
			$("#loadimage").hide('slow');//hide loading image
			window.location.href = 'index.php';
		}else {
			na.error(data.message);
		};
	}
});  //Ajax Submit form

});

$('#close').click(function()  {
	$('#transparent').remove();
	$('.formsubmitpopup').remove();
	clearBack();
	window.location.href = 'index.php';
});
}

});


function applypicture(reload) {
//this function is actually declared in headerscript.js
//i am declaring it again because headerscript.js is called for signed in users only 
//whereas confirmreset.js is called for those who haven't signed in yet applypicture() is used in either instance

$('#picupload #loadimage').show();
$('#loading').show();

var options = {
    url:   'server.php',   // target element(s) to be updated with server response 
    // beforeSubmit:  beforeSubmit,  // pre-submit callback 
	type: 'POST',
	success : function(response) {
		//alert(response);
		var data = JSON.parse(response);
		if(data.ok) {

			$('#picupload #loadimage').hide('slow');
			$("#loading").fadeOut('4000');//hide loading text
			setTimeout(function() {
				$('#picupload').slideUp('slow'); 
				if(typeof reload == 'function') reload();
			},2000);
			$('#profilepicture').attr({"src":data.url != "" ? data.url : "files/avator.png"});
			$('#currentprofilepicture').attr({"src":data.url != "" ? data.url : "files/avator.png"});
		}else if(data.redirect) window.location.href = 'index.php';
		 else alert(data.message);
	}
}; 

$('#avatorupload').unbind('submit');
$('#avatorupload').submit(function( event ) {
event.preventDefault();//stops browser from doing its reload thing

$(this).ajaxSubmit(options);  //Ajax Submit form

});

}