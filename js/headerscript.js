$(function() {

$('#user').click(function() {//for the user profile menu
	$('#notificationsdropdown').hide();
	ret = true;
	$('#userdropdown').slideToggle(function(){
		$('html').find('#transparent').each(function() { ret = false; });
		if(ret) $('body').append('<div id="transparent" style="position:absolute;width:100%;height:100%;top:0px;left:0px;"></div>');
		$('#transparent').click(function() {
			$(this).remove();
			$('#userdropdown').slideUp();
		});
	});

});

$('#notifications').click(function() {//for notifications
	$('#userdropdown').hide();//remove any other drop down
	ret = true;
	$('#notificationsdropdown').slideDown(function(){
		$('html').find('#transparent').each(function() { ret = false; });
		if(ret) $('body').append('<div id="transparent" style="position:absolute;width:100%;height:100%;top:0px;left:0px;"></div>');
		
		$('#transparent').click(function() {
			$(this).remove();
			$('#notificationsdropdown').slideUp();
		});
	});

});

var ntf = $('#notificationsdropdown');
ntf.css({"left": "900px"});//set the notifications element position

//==== calling the move() jQuery.fn.extend function =========
$("#leftmenumove").move(70);
//===========================================================

});

function requestNotificationsPage (pn, rpp) {//declare drop down notifications paginaton function
$.ajax({//checking for notifications!!
	url: 'server.php',
	type: 'POST',
	data: {action:'notifications'},
	success: function(response) {
		//log(response);
		var data = JSON.parse(response);
			
		var nt = new Templates();
		var html = nt.loadTemplate('NOTIFICATION');
		
		if(data.ok) {
			var oldComments = data.message;
			var total = length(oldComments);
			
			$('#notifyer').notification(total);//$.fn.extend function for displaying styled notifications
			var comments = split(oldComments, ((pn - 1)*rpp), rpp);

			if(total > 0){
				$('#notificationsdropdown #content').empty();
				for(var a in comments) {
					$('#notificationsdropdown #content').append(html);
					$('.newnt').css({"padding":"5px","font-size":"14px","border-bottom":"solid 1px rgb(240,240,240)"});
					$('.newnt td').html("<b>"+comments[a].commentorname+"</b> commented on the <b>"+comments[a].field+"</b> field in your form");
					$('.newnt').parent().attr({"href":"forms.php#"+comments[a].hash,"target":"_blank"});
					$('.newnt').removeClass();
				}
				
				var back = forward = "";
				var pages = Math.ceil(total/rpp);
				// Change the pagination controls
				// Only if there is more than 1 page worth of results give the user pagination controls
				if(pages != 1){
					if (pn > 1) back = '<button class="pgcontrols back cursor" onclick="requestNotificationsPage('+(pn-1)+', '+rpp+')"></button>';
					//paginationCtrls += '<button class="pgcontrols forward cursor" onclick="requestNotificationsPage('+(pn)+', '+rpp+')">'+pn+'</button>';
					if (pn != pages) forward = '<button class="pgcontrols forward cursor" onclick="requestNotificationsPage('+(pn+1)+', '+rpp+')"></button>';
					$("#notificationsdropdown #pagination_controls").show();
					$("#notificationsdropdown #pagination_controls #back").html(back);
					$("#notificationsdropdown #pagination_controls #forward").html(forward);
				}else $("#notificationsdropdown #pagination_controls").hide();
			}
		}else {
			$('#notificationsdropdown #content').append(html);
			$('.newnt').css({"color":"grey","padding":"5px","font-size":"14px","border-bottom":"solid 1px rgb(240,240,240)"});
			$('.newnt td').text("No New Notifications!");
		}
	}
});
}

function showProfile() {//declare the profile pop up function

var ret = true;
var na = new AssessmentForm();
var nt = new Templates();
var html = nt.loadTemplate("PROFILE");

$('html').find('#completeprofileform').each(function() { ret = false; });
if(ret) {
dimBack();
$('#rightDiv').append(html);

$('#close').on('click', function()  {
	$('#transparent').remove();
	$('.formsubmitpopup').remove();
	clearBack();
});

function itemScroll () {
$('html, body').animate({//position the window nicely
	scrollTop: $('#completeprofileform').offset().top - 200
}, 100);
}
itemScroll();

$(document).unbind('scroll');
$(document).scroll(function() {
	setTimeout(function () { itemScroll(); }, 2000);
});

$('.formsubmitpopup').draggable({handle: '.box-header'});
$.ajax({
	url:'server.php',
	type:'POST',
	data:{action:"get",t:"memberinfo",cl:"email"},
	success: function(response) {
	//alert(response);
	dt = JSON.parse(response);
	if(dt.ok) {
		$('#currentprofilepicture').attr({"src":dt.message.avator});
		na.updateFormValues('#profileform', dt.message, []);
		na.updateFormValues('#profileform2', dt.message, []);
		$('#name').val(dt.message.firstname+' '+dt.message.lastname);
	}else window.location.href = 'index.php';
	}
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
		
		}else {
			na.error(data.message);
		};
	}
});  //Ajax Submit form

});

}

}

function applypicture(reload) {//declare picture upload function

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
