var na =  new AssessmentForm();

function requestPage (pn, rpp) {
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
			
			$('#summarynotifyer').notification(total);//$.fn.extend function for displaying styled notifications
			var comments = split(oldComments, ((pn - 1)*rpp), rpp);

			if(total > 0){
				$('#summarynotifications .box-content').empty();
				for(var a in comments) {
					$('#summarynotifications .box-content').append(html);
					$('.newnt').css({"padding":"5px","font-size":"14px","border-bottom":"solid 1px rgb(240,240,240)"});
					$('.newnt td').html("<b>"+comments[a].commentorname+"</b> commented on the <b>"+comments[a].field+"</b> field in your form");
					$('.newnt').parent().attr({"href":"forms.php#"+comments[a].hash});
					$('.newnt').removeClass();
				}
				
				var back = forward = "";
				var pages = Math.ceil(total/rpp);
				// Change the pagination controls
				// Only if there is more than 1 page worth of results give the user pagination controls
				if(pages != 1){
					if (pn > 1) back = '<button class="pgcontrols back cursor" onclick="requestPage('+(pn-1)+', '+rpp+')"></button>';
					//paginationCtrls += '<button class="pgcontrols forward cursor" onclick="requestPage('+(pn)+', '+rpp+')">'+pn+'</button>';
					if (pn != pages) forward = '<button class="pgcontrols forward cursor" onclick="requestPage('+(pn+1)+', '+rpp+')"></button>';
					$("#summarynotifications #pagination_controls").show();
					$("#summarynotifications #pagination_controls #back").html(back);
					$("#summarynotifications #pagination_controls #forward").html(forward);
				}else $("#summarynotifications #pagination_controls").hide();
			}
		}else {
			$('#summarynotifications .box-content').append(html);
			$('.newnt').css({"color":"grey","padding":"5px","font-size":"14px","border-bottom":"solid 1px rgb(240,240,240)"});
			$('.newnt td').text("No New Notifications!");
		}
	}
});
}

$.ajax({//checking for the last seen as well as the percentage progress
	url:	'server.php',
	type:	'POST',
	data: 	{ action: 'load' },
	success: function(data){
		//alert(data);
		data = JSON.parse(data);
		if(data.ok) {
			//alert(data.message);
			na.completeform = data.message;
		}else {// no save file found. This is only necessary when loading from a file instead of from MySql
			na.completeform.formone 	= {"qualification":"","years":"","months":"","external_programme":"","external_institution":"","external_date":"","external_duration":"","internal_programme":"","internal_institution":"","internal_date":"","internal_duration":""};
			na.completeform.formtwo	= {"q1":"","q2":"","q3":"","q4":"","q5":"","q6":"","q7":"","q8":"","q9":"","q10":"","q11":"","q12":"","q13":"","q14":"","q15":"","q16":"","q17":"","q18":"","q19":"","q20":"","q21":"","q22":"","q23":"","q24":"","q25":"","q26":"","q27":"","q28":"","q29":"","q30":"","q31":"","q32":"","q33":"","q34":"","q35":"","q36":"","q37":"","q38":"","q39":"","q40":"","q41":"","q42":"","q43":"","q44":"","q45":"","q46":"","q47":"","q48":"","q49":"","q50":"","q51":"","q52":"","q53":"","q54":"","q55":"","q56":"","q57":""};
			na.completeform.formthree = {"accomplishments":"","traits_skills":"","goals":"","status":"","dev_opportunities":"","dev_resource_plan":"","key_goal":"","resource_plan":""};
			na.completeform.hpapformone 	= {};
			na.completeform.hpapformtwo	= {};
			na.completeform.hpapformthree = {};
		}
		function showsummary(settings) {
			$('#summarylastseen #lastseen').html(settings.lastseen);
			
			$( "#summarylastseen #percentage" ).progressbar({ // displaying % progress on the progress bar
				value: +settings.progress
			});
			$( "#summarylastseen #percentage-inner" ).text(settings.progress+'%'); // displaying them on the progress bar
		}
		na.summary(showsummary);


		$.ajax({//getting users details
			url: 'server.php',
			type: 'POST',
			data: {action:"get",t:"memberinfo",cl:"id"},
			success: function(resp) {
				//alert(resp);
				dt = JSON.parse(resp);
				if(dt.ok) {
					var user = dt.message;
					$('#summaryuser #useravator').css({'background-image': 'url('+ (user.avator != "" ? user.avator : "files/avator.png") +')'});
					var infostring = user.firstname+" "+user.lastname+"<br/>"+user.designation+"<br/>"+user.email+"<br/>"+user.phone;
					$('#infocontainer').append(infostring);
				}
			}
		});
	}
});
