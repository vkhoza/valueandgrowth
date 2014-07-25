function AssessmentForm () {

this.fields = [];
this.buffer = [];
this.cfdata = {};
this.definedkpas = {};
this.completeform = {};
this.tempcompleteform = {};
this.progress;
this.submittedform = {};
this.submit = false;
this.ids = ["ASSESSMENT_FORM_ONE","ASSESSMENT_FORM_TWO","ASSESSMENT_FORM_THREE"];
this.tabs = ["assessment","hpap"];

}


AssessmentForm.prototype.init = function() {
self = this;
$('#formContainer').find('form').each(function() {
	item = this;
	formId = $(item).attr('name');
	
	self.loadForm(formId, item);

	$(item).find('input, select, textarea').each(function(){//function to assign datepicker to appropriate form values 
		var name = $(this).attr('name');
		if(name == 'internal_date' || name == 'external_date')	$(this).datepicker({maxDate: 0, dateFormat: "D d M yy"});
		$(this).change(function() {//onchange every input field item should save
			self.updateCompleteForm(formId);
			self.saveForm();
		});
	});
});	

}

AssessmentForm.prototype.saveForm = function() {
self = this;
var good = self.formCheck('#formContainer');

//alert(good.ok);
if ( ! good.ok) {
	self.error(good.elem);
	return;
}

//getkeys(self.completeform.hpapformtwo,true);

$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image
$('#submitforms').css({"background":"grey"}).attr({"id":"disablesubmitforms"});//disabled save button
var dets = JSON.stringify(self.completeform);

if( self.submit )	var sdets = JSON.stringify(self.submittedform);//if user has submitted a form then
else	var sdets = "false";
var perc = self.percentage();

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: { data: dets, sdata: sdets, action: 'store', perc: perc },
	success: function(response, status) {
//		log(response);
		data = JSON.parse(response);
		$("#loadimage").hide();//hide loading image
		if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
		else if( ! data.ok) {
			if(data.redirect) {}
			else alert(data.message);
		}
		setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
		$('.errortable').fadeOut(1000);
		$('#disablesubmitforms').css({"background":"rgb(35,85,165)"}).attr({"id":"submitforms"});//enabled save button
	}
});

function setlastseen(lastseen) { $('#lastseen').text(lastseen.lastseen); }
//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
self.summary(setlastseen);
}

AssessmentForm.prototype.loadForm = function(form, elem) {
self = this;

$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image
$('#save').css({"background":"grey"}).attr({"id":"savedisabled"});
$('#submit').css({"background":"grey"}).attr({"id":"submitdisabled"});

$.ajax({
url:	'server.php',
type:	'POST',
data: 	{ action: 'load' },
success: function(data){
	//log(data);
	data = JSON.parse(data);
	if(data.ok) {
		//log(data.message);
		self.completeform = data.message;
		self.submittedform = data.sdata;
		self.definedkpas = data.kpas;
		self.cfdata = data.cfdata;
		self.submit = true;
		
		log(self.cfdata);
	}else if( ! data.ok){// no save file found. This is only necessary when loading from a file instead of from MySql
		self.completeform.formone 	= {"qualification":"","years":"","months":"","external_programme":"","external_institution":"","external_date":"","external_duration":"","internal_programme":"","internal_institution":"","internal_date":"","internal_duration":""};
		self.completeform.formtwo	= {"q1":"","q2":"","q3":"","q4":"","q5":"","q6":"","q7":"","q8":"","q9":"","q10":"","q11":"","q12":"","q13":"","q14":"","q15":"","q16":"","q17":"","q18":"","q19":"","q20":"","q21":"","q22":"","q23":"","q24":"","q25":"","q26":"","q27":"","q28":"","q29":"","q30":"","q31":"","q32":"","q33":"","q34":"","q35":"","q36":"","q37":"","q38":"","q39":"","q40":"","q41":"","q42":"","q43":"","q44":"","q45":"","q46":"","q47":"","q48":"","q49":"","q50":"","q51":"","q52":"","q53":"","q54":"","q55":"","q56":"","q57":""};
		self.completeform.formthree = {"accomplishments":"","traits_skills":"","goals":"","status":"","dev_opportunities":"","dev_resource_plan":"","key_goal":"","resource_plan":""};
		self.completeform.hpapformone 	= {};
		self.completeform.hpapformtwo	= {};
		self.completeform.hpapformthree = {};
		
		function iterate(obj) {
			for(var a in obj) {
				obj[a] = {"value":obj[a],"comment":"","commentor":"","commenttime":"","seen":1}
			}
			return obj;
		}

		var copyofcompleteform = JSON.parse( JSON.stringify( self.completeform ) );
		
		self.submittedform.formone = iterate(copyofcompleteform.formone);
		self.submittedform.formtwo = iterate(copyofcompleteform.formtwo);
		self.submittedform.formthree = iterate(copyofcompleteform.formthree);
		
		self.submittedform.hpapformone = self.submittedform.hpapformtwo = self.submittedform.hpapformthree = {};
		
		for(var b in copyofcompleteform.hpapformone) self.submittedform.hpapformone[b] = iterate(copyofcompleteform.hpapformone[b]);
		for(var c in copyofcompleteform.hpapformtwo) self.submittedform.hpapformtwo[c] = iterate(copyofcompleteform.hpapformtwo[c]);
		for(var d in copyofcompleteform.hpapformthree) self.submittedform.hpapformthree[d] = iterate(copyofcompleteform.hpapformthree[d]);
		self.submit = true;
		
		//log(self.submittedform);
	}

	var i = $.inArray(form, self.ids);
	var update = {}
		if 		(i == 0) 	update = self.completeform.formone;
		else if (i == 1) 	update = self.completeform.formtwo;
		else if (i == 2) 	update = self.completeform.formthree;
		
	var omit = ["company_name","industry","firstname","surname","number","email"];
	self.updateFormValues(elem, update, omit);
	$("#loadimage").hide('slow');//hide loading image

	function setlastseen(lastseen) { $('#lastseen').text(lastseen.lastseen); }//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
	self.summary(setlastseen);
	
	if( typeof adminFormViewer == "undefined" ) //check if user is admin, if not run user script
		self.getComments('#formContainer');//checking for new comments
	else if ( typeof adminFormViewer != "undefined" ) {
		disable_buttons();
	}
	
	if( self.cfdata.assessmentsignedoff == 1 ) //check if user's form has been approved, if so disable buttons
		disable_buttons();
		
	//dealing with the submit form button
	if(self.cfdata.assessment == 1) $('#submitdisabled').css({"background":"orange"}).text('Form Submitted').attr({"id":"submitted"});
	else $('#submitdisabled').css({"background":"rgb(35,85,165)"}).attr({"id":"submit"});
	
	$('#savedisabled').css({"background":"rgb(35,85,165)"}).attr({"id":"save"});
	$('#save').unbind('click');
	$('#save').on('click', function() {//binding the save form functionality to the save button
		$('#formContainer').find('form').each(function() {
			var id = $(this).attr('name');
			
			self.updateCompleteForm(id);
			self.saveForm();
		});
	});	

	$('#submit').unbind('click');
	$('#submit').on('click', function() {//binding the submit form functionality to the submit button
		self.finalFormCheck("ASSESSMENT", function() {
			dimBack();
			var nt = new Templates();
			var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");
			$('#rightDiv').append(html);
			$('.formsubmitpopup').draggable({handle: '.box-header'});
			$('#close').click(function() {
				$('.formsubmitpopup').remove();
				clearBack();
			});
			$('#submitconfirm .box-content').html("Are you sure you want to submit the information in this Assessment Form? <br/>\
			Once you submit the form your superior will be able to approve it. As soon as your superior approves your form you will no\
			longer be able to edit it!\
			<div id='yes' class='button' style='width:50px;margin-top:15px;'>Yes</div>");

			$('#yes').on('click', function() {
				$('#submitconfirm #loadimage').show();
				$.ajax({
					url: 'server.php',
					type: 'POST',
					data: {action: "submit", form: "ASSESSMENT"},
					success: function(response) {
						//log(response);
						var data = JSON.parse(response);
						$('#submit').text(data.message).css({background:"rgb(255,60,60)"});
						$('#submitconfirm').remove();
						clearBack();
					}
				});
			});
		});
	});

	$('#export').on('click', function() {
		self.exportForms();
	});
	
	}
});

}

AssessmentForm.prototype.updateFormValues = function(elem, update, omit) {

$(elem).find('input, textarea, select').each(function(){//function to update form values 
	var name = $(this).attr('name');
	var i = $.inArray(name, omit) 
	if(i == -1) {
	for (var h in update) {//iterate through saved values
		if(h == name) {//match field names with saved names
			var type = $(this).attr('type');
			if(type == 'text')			$(this).val(update[h]);//update inputs with type 'text'
			else if (type == 'radio') {//update inputs with type 'radio'
				var val = $(this).attr('value');
				if(val == update[h])	$(this).prop('checked',true);
			}//update textareas
			else 						$(this).val(update[h]);
			
			if( $(this).hasClass('select') ) {
				$('#'+name+' option[value="'+update[h]+'"]').prop('selected',true);
			}
		}
	}
	}
});

}

AssessmentForm.prototype.leftMenuChanger = function() {
self = this;

$('#af').on('click', function() {
	
	if( typeof adminFormViewer != "undefined" ) { //check if user is admin, if so run admin script
		var afv = new adminFormViewer();
		afv.initiate('#rightDiv');
	}
	
	//self.saveForm();
	location.hash = "assessment+ASSESSMENT_FORM_ONE";
	var nt = new Templates();
	nt.displayElems("#rightDiv", nt.loadTemplate("ASSESSMENT_FORM_HEADER"));
	nt.displayElems("#formContainer", nt.loadTemplate("ASSESSMENT_FORM_ONE"));
	
	var ns = new AssessmentForm();
	ns.tabChanger();	
	ns.init();//initialize form

	$('.tabs').on('click', function(){ //this is necessary to reassign events after appending a new template
		ns.init();//initialize form
	});	
});

$('#hf').on('click', function() {

	if( typeof adminFormViewer != "undefined" ) { //check if user is admin, if so run admin script
		var afv = new adminFormViewer();
		afv.initiate('#rightDiv');
	}

	//self.saveForm()
	location.hash = "hpap+HPAP_FORM_ONE";
	
	var nt = new Templates();
	nt.displayElems("#rightDiv", nt.loadTemplate("HPAP_FORM_HEADER"));
	nt.displayElems("#formContainer", nt.loadTemplate("HPAP_FORM_ONE"));
	
	var hpap = new HPAPForm();
	hpap.tabChanger();	
	hpap.init();	

	$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
		hpap.init();//initialize form
	});	
});
}

AssessmentForm.prototype.tabChanger = function() {
self = this;
$('.tabs').click(function(){

	$('html, body').animate({//position the window nicely. Necessary to snap into position when the user has scrolled down 
		scrollTop: 0
	}, 10);
	
	//the following ids are actually the names of the forms to be loaded
	var bf = $('.selected').attr('id');//id for form to be buffered
	var cl = $(this).attr('class');
	var id = $(this).attr('id');//id for form to be displayed

	var i = $.inArray(bf, self.ids);//this handles the url changes
	var tab = ( i == -1 ) ? self.tabs[1] : self.tabs[0];
	location.hash = tab+"+"+id;
	
	if(cl != 'tabs selected')	{

		self.updateCompleteForm(bf);
		var parent = $('form[name="'+bf+'"]').parent();
		var removedForm = $('form[name="'+bf+'"]').detach();
		removedForm.parent = parent;
		if(self.buffer.length>0)	{
			//alert(self.buffer.length);
			names = [];
			for(var c in self.buffer) names[c] = self.buffer[c].selector;
			var is = $.inArray('form[name="'+id+'"]', names);//check if form to be displayed is in the buffer
			if(is>=0) {
				var elem = self.buffer[is];
				elem.parent.append(elem);
				delete self.buffer[is];
			}else {//if not load new template
				var nt = new Templates;
				var html = nt.loadTemplate(id);
				nt.displayElems("#formContainer", html);
			}
		}else {//if the buffer hasnt been initialized, load new template
			var nt = new Templates;
			var html = nt.loadTemplate(id);
			nt.displayElems("#formContainer", html);
		}
		self.buffer.push(removedForm);
		//alert(removedForm.selector);
	}
	$('#cd').datepicker({maxDate: 0, dateFormat: "D d M yy"});

	$('.tabContainer').find('.tabs').each(function(){
		$('.tabs').attr('class', 'tabs');
	});
	$(this).attr('class', 'tabs selected');
	
	if( typeof adminFormViewer != "undefined" ) { //check if user is admin, if so run admin script
		var afv = new adminFormViewer();
		afv.initiate('#rightDiv');
	}	

});
}

AssessmentForm.prototype.formCheck = function (area) {
var err = {'ok':true}
var elems = [];
$(area).find('form').each(function () {
	$(this).find('input, radio, textarea').each(function() {
		
		var checkbox = [];
		var numbers = ["number","years","months","external_duration","internal_duration","staffcomplement","overallprogress"];
		var email = ["email"];
		var dates = [];
		var text = ["company_name","industry","firstname","surname","qualification","external_programme","external_institution","internal_programme","internal_institution",
		"accomplishments","traits_skills","goals","status","dev_opportunities","dev_resource_plan","key_goal","resource_plan","kpa","objective","measurement","jobtitle",
		"jobdescription","companyname","companyaddress","industry"];
		
		var name = $(this).attr('name');
		var value = $(this).val();
		
		var i = $.inArray(name, numbers);
		if( i >= 0 ) {
			if( ! value.match(/^[0-9]+$/) &&  value != "") {
				//alert('Value must be a number');
				elems.push({'tag': this, 'message': 'This must be in digits only, try again!' });
				err = {'ok':false,  'elem': elems};
			}else if( name == 'number' && value.length < 10 &&  value != "") {
				elems.push({'tag': this, 'message': 'Cell number has too few digits, try again!' });
				err = {'ok':false,  'elem': elems};
			}
		}
		
		var j = $.inArray(name, email);
		if( j >= 0 ) {
			if( ! value.match (/.+@.+\..+/) &&  value != "") {
				elems.push({'tag': this, 'message': 'Your email doesn\'t seem quite right, try again!' });
				err = {'ok':false,  'elem': elems};
			}
		}
		
		var k = $.inArray(name, dates);
		if( k >= 0 ) {
			if( ! value.match(/\d\d\/\d\d\/\d\d/) &&  value != "") {
				elems.push({'tag': this, 'message': 'Your date format doesn\'t seem quite right, try again!' });
				err = {'ok':false,  'elem': elems};
			}
		}
				
		var l = $.inArray(name, text);
		if( l >= 0 ) {
			if( ! value.match(/[a-zA-Z]+/) &&  value != "") {
				elems.push({'tag': this, 'message': 'This section must contain letters, try again!' });
				err = {'ok':false,  'elem': elems};
			}
		}

	});
});
if ( ! err.ok) return err;
else return err;
}

AssessmentForm.prototype.error = function(elem) {
var nt = new Templates();
var html = nt.loadTemplate('ERROR');
var newid = [];

for(var a in elem) {//cycle through all the elements with error messages

$(elem[a].tag).after(html);
var id = $(elem[a].tag).attr('name');

newid[a] = 'error_'+id;//setting a unique id to allow each error message to be treated differently
$('#error').attr('id', newid[a]);
newid[a] = '#'+newid[a];
$(newid[a]).html(elem[a].message).fadeIn('slow');
}
//alert(newid[0]+newid[1]+newid[2]);

function fade(self) {
	$(self).fadeIn('slow');	
	setTimeout(function(){
		$(self).fadeOut('slow');			
	},4000);
}


setTimeout(function(){
for(var c in newid) $(newid[c]).fadeOut('slow');
},7000);

setTimeout(function(){ 
$('.errortable').hide('2000'); 
},10000);
}

AssessmentForm.prototype.getComments = function(container) {
$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action:'notifications'},
	success: function(response) {
		//log(response);
		var data = JSON.parse(response);

		var nt = new Templates();
		var html = nt.loadTemplate('COMMENT');
		var newid = [];
		var elem = data.message;

		if(data.ok) {
			for(var a in elem) {//cycle through all the comments 
				if( typeof elem[a].formkey != 'undefined' ) {
					var iditem = $('.id:contains("'+elem[a].formkey+'")');
					var container = iditem.parents('#hpap_kpas');
					var item = container.find('#'+elem[a].tag);
				}else {
					var item = $('[name="'+elem[a].tag+'"]').not('[type="radio"]');
				}				

				if( item.length > 0 ) {//if we've found any elements matching the tag
				
				
				var parent = item.parents('.fieldcontainer');
				var itemid = "0";//this makes unique ids in the hpap forms but changes nothing in the assessment forms
				if( item.hasClass('fcontainer') ) {//fcontainer is used by hpap forms as well as formtwo
					itemid = item.find('.id').text();
					item.css('background', parent.css('background'));
				}	

				if( $("#comment_"+elem[a].tag+"_"+itemid).length === 0 ) 
					item.before(html).parents('.fieldcontainer').addClass('commented');
				
				$('#newcomment').parents('.commenttable').find('#commentorname').html("<div id='commentor'>"+elem[a].commentorname+":</div>");
				$('#newcomment').html(elem[a].comment);
				parent.find('#commentclose').show();

				position = parent.offset().top;
				elemheight = $('#newcomment').parents('.commenttable').outerHeight();
				quarterheight = elemheight/4;

				$('#newcomment').parents('.commenttable').offset({ top: position - ( elemheight - quarterheight ) });
				
				parent.attr({"onmouseover":"$(this).find('.commenttable').show();","onmouseleave":"$(this).find('.commenttable').hide();"});

				$('#newcomment').attr({ "id": "comment_"+elem[a].tag+"_"+itemid, "content": elem[a].tag });
				}
			}
		}
	}
});
}

function deleteComment(self) {

var parent = $(self).parents('.commenttable');
var container = $(self).parents('.fieldcontainer');
parent.find('#commentclose').hide();
parent.find('#loadimage').show();

var field = parent.find('.comment').attr('content');

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action: 'update-notifications', field: field},
	success: function (response) {
		//log(response);
		$('#loadimage').hide();
		parent.remove();
		container.removeClass('commented');
		requestNotificationsPage(1, 4);
	}
});

}

AssessmentForm.prototype.updateCompleteForm = function(id) {
var i = $.inArray(id, self.ids);
self.completeform.formone 	= (i == 0) 	? 	$('form[name="'+id+'"]').serializeObject(true) : 	self.completeform.formone;
self.completeform.formtwo 	= (i == 1) 	? 	$('form[name="'+id+'"]').serializeObject(true) : 	self.completeform.formtwo;
self.completeform.formthree = (i == 2) 	? 	$('form[name="'+id+'"]').serializeObject(true) : 	self.completeform.formthree;

//assessment formtwo uses radio buttons. If they are not checked they are not serialized 
//so we check which ones weren't checked and reintroduce them into the completeform object
var formtwovalues = ["q1","q2","q3","q4","q5","q6","q7","q8","q9","q10","q11","q12","q13","q14","q15","q16","q17","q18","q19","q20","q21","q22","q23","q24","q25","q26","q27","q28","q29","q30","q31","q32","q33","q34","q35","q36","q37","q38","q39","q40","q41","q42","q43","q44","q45","q46","q47","q48","q49","q50","q51","q52","q53","q54","q55","q56","q57"];
for(var a in formtwovalues) {
	var f = formtwovalues[a] in self.completeform.formtwo;
	if( ! f) self.completeform.formtwo[formtwovalues[a]] = "";
}

var copyofcompleteform = JSON.parse( JSON.stringify( self.completeform ) );

if( self.submit ) self.updateSubmittedForm(copyofcompleteform);//updating the submitted form if its there!
}

AssessmentForm.prototype.updateSubmittedForm = function(form) {
//var newObj = {};
function iterate(obj, key, key2) {
	var submitObj = {};
	if( typeof key2 != 'undefined' ) submitObj = self.submittedform[key][key2];
	else if( typeof key2 == 'undefined' ) submitObj = self.submittedform[key];
	
	for(var a in obj) {
	if( typeof submitObj[a] == 'undefined' ) submitObj[a] = { "value":"", "comment":"", "commentor":"", "commenttime":"", "seen":1 };
		obj[a] = {
			"value":		obj[a],
			"comment":		submitObj[a]['comment'],
			"commentor":	submitObj[a]['commentor'],
			"commenttime":	submitObj[a]['commenttime'],
			"seen":			submitObj[a]['seen']
		}
	}
	return obj;
	
}

self.submittedform.formone = iterate(form.formone, "formone");
self.submittedform.formtwo = iterate(form.formtwo, "formtwo");
self.submittedform.formthree = iterate(form.formthree, "formthree");
for(var b in form.hpapformone) {
	self.submittedform.hpapformone[b] = {};
	self.submittedform.hpapformone[b] = iterate(form.hpapformone[b], 'hpapformone', b);
}
for(var c in form.hpapformtwo) {
	self.submittedform.hpapformtwo[c] = {};
	self.submittedform.hpapformtwo[c] = iterate(form.hpapformtwo[c], 'hpapformtwo', c);
}
for(var d in form.hpapformthree) {
	self.submittedform.hpapformthree[d] = {};
	self.submittedform.hpapformthree[d] = iterate(form.hpapformthree[d], 'hpapformthree', d);
}

//log (self.submittedform);
}

AssessmentForm.prototype.percentage = function() {
self = this;
var i = d = 0;
var x = y = z = 0;

function iterate(obj) {
var u = 0;
for(var a in obj) {
	if(obj[a] != '') u++;
}
return u;
}

var kpas = self.definedkpas;
var kpabuffer = [];
//counting the number of completed fields
i = i + iterate(self.completeform.formone);
i = i + iterate(self.completeform.formtwo);
i = i + iterate(self.completeform.formthree);

for(var b in self.completeform.hpapformone) {
	//there's 4 fields in each hpapformone[b]. if x > 24 then theres more than 6 High Performance Plans. All we need is 6 High Performance Plans
	var thiskpa = self.completeform.hpapformone[b].kpaname;

	var is = $.inArray(thiskpa, kpabuffer);//this is an extra check that allows us to determine whether 6 KPA's have been defined
	if( is == -1 ) x += iterate(self.completeform.hpapformone[b]);
	kpabuffer.push(thiskpa);
}
d = x > 24 ? 24 : x;
i = i + d;

for(var b in self.completeform.hpapformtwo) {
	//there's 3 fields in each hpapformtwo[b]. if y > 3 then theres more than one Leader Value Project. All we need is one Leader Value Project
	y += iterate(self.completeform.hpapformtwo[b]);
}
d = y > 3 ? 3 : y;
i = i + d;

for(var b in self.completeform.hpapformthree) {
	//there's 4 fields in each hpapformthree[b]. if z > 4 then theres more than one Personal Development Plan. All we need is one Personal Development Plan
	z += iterate(self.completeform.hpapformthree[b]);
}
d = z > 4 ? 4 : z;
i = i + d;

var p = (+i/107)*100;
return p;


}

AssessmentForm.prototype.summary = function(func) {
self = this;  

var p = self.percentage();

$( "#percentage" ).progressbar({ // displaying % progress on the progress bar
	value: p
});
p = Math.floor(p);//make a round figure for the display
$( "#percentage-inner" ).text(p+'%'); // displaying them on the progress bar

//checking the last seen date
$.ajax({
	url:	'server.php' ,
	type:	'POST' ,
	data:	{action:'settings', perc:p},
	success: function(data) {
	//alert(data);
	var d = JSON.parse(data);
	if(d.ok) {
		
		var settings = d.message;
		//getkeys(settings, true);
		
		var nd = new Date(settings.lastseen);//create date object using last seen seconds
		var ls = nd.toDateString();
		var time = nd.toTimeString();
		var pcs = time.split(' ');
		ls = ls + ' ' + pcs[0];//convert last seen date to readable string
		var finaldate = (ls == 'Invalid Date Invalid') ? settings.lastseen : ls;
		settings.lastseen = finaldate;

		if(typeof func == 'function') func(settings);
		return settings;
	}//else alert(d.message);
	}
});

}

AssessmentForm.prototype.exportForms = function() {
self = this;
var ret = true;
$('#rightDiv').find('.formsubmitpopup').each(function () {
	ret = false;
});
if(ret) {
	$('html, body').animate({//position the window nicely. Necessary to snap into position when the user has scrolled down 
		scrollTop: 0
	}, 100);
	//the following function checks the completeness of the data saved then either proceeds to export or creates error alerts
	//depending on what the data in the user's form!
	self.finalFormCheck("BOTH", function () {
		dimBack();
		var nt = new Templates();
		var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");
		$('#rightDiv').append(html);
		$('.formsubmitpopup').draggable({handle: '.box-header'});
		$('#close').click(function() {
			$('.formsubmitpopup').remove();
			clearBack();
		});
		$('#submitconfirm .box-content').html("Are you sure you want to export the information in these forms to a pdf file? <br/>\
		<div id='yes' class='button' style='width:50px;margin-top:15px;'>Yes</div>");

		$('#yes').on('click', function() {
					
		$('#submitconfirm #loadimage').show();
		var form = JSON.stringify(self.submittedform);
				
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: {action:'export',data:form},
			success: function(response) {
				//log(response);
				
				$('.formsubmitpopup').remove();
				clearBack();
					
				$('#submitconfirm #loadimage').hide();
			}
		});
		});
	});
}

}

AssessmentForm.prototype.finalFormCheck = function(stage, goodfunc) {
self = this;
var ret = {};
var fields = [];

function iterate(obj, stg, form) {
var u = true;
for(var a in obj) {
	if(obj[a] == '' || typeof (obj[a]) == 'undefined') {
		u = false;
		fields.push({"field":a, "stage":stg, "form":form});
		ret = {'ok':false, 'elems':fields};
	}
}
return u;
}

$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image

$.ajax({
url:	'server.php',
type:	'POST',
data: 	{ action: 'load' },
success: function(data){
	$("#loadimage").hide('slow');//hide loading image
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {
		var theform = data.message;
		var submittedform = data.sdata;

		function checkAssessment() {
			//counting the number of completed fields
			if(iterate(theform.formone, 1, "Assessment Form")) {
				if(iterate(theform.formtwo, 2, "Assessment Form")) {
					if(iterate(theform.formthree, 3, "Assessment Form"))	{
						ret = {'ok':true};
					}
				}
			}
		}
			
		function checkHPAP() {
			if(length(theform.hpapformone)>=6) ret = {'ok':true};
			else {
				fields.push({"field":"At least 6 KPA's Required", "stage":"1", "form":"HPAP Form"});
				ret = {'ok':false, 'elems':fields};
			}						
			if(length(theform.hpapformtwo)>=1) ret = {'ok':true};
			else {
				fields.push({"field":"At least 1 Leader Value Project Required", "stage":"2", "form":"HPAP Form"});
				ret = {'ok':false, 'elems':fields};
			}						
			if(length(theform.hpapformthree)>=1) ret = {'ok':true};
			else {
				fields.push({"field":"At least 1 Personal Development Plan Required", "stage":"3", "form":"HPAP Form"});
				ret = {'ok':false, 'elems':fields};
			}
		}
			
		if(stage == 'ASSESSMENT') checkAssessment();
		else if(stage == 'HPAP') checkHPAP();
		else if(stage == 'BOTH') {
			checkAssessment();
			checkHPAP();
		}
	}else {
		fields.push({"field":"Could not access Save Data!", "stage":"1", "form":"Assessment Form"});
		ret = {'ok':false, 'elems':fields};
	}
	
	if(ret.ok) {
		if(typeof goodfunc == 'function') goodfunc();		
	}else if( ! ret.ok) self.notYet(ret);
	
}
});
	
function setlastseen(lastseen) { $('#lastseen').text(lastseen.lastseen); }//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
self.summary(setlastseen);

}

AssessmentForm.prototype.notYet = function(isGood) {

dimBack();
var nt = new Templates();
var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");
$('#rightDiv').append(html);

$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#close').click(function() {
	$('.formsubmitpopup').remove();
	clearBack();
});
if( ! isGood.ok) {
	var html = nt.loadTemplate("FORM_SUBMIT_ERROR");
	//getkeys(isGood.elems, true);
	$('#submitconfirm .box-content').html('The following areas require your attention before you submit the form:');
	var w = 0;
	for (var a in isGood.elems) {
		$('#submitconfirm .box-content').append(html);
		$('#submiterrors #form').html(isGood.elems[a].form);
		$('#submiterrors #stage').html(isGood.elems[a].stage);
		$('#submiterrors #field').html(isGood.elems[a].field);
		$('#submiterrors').attr('id','submiterrors-p');
		w++;
		if(w > 4) {//im only comfortable with displaying 4 items at a time
			var len = length(isGood.elems);
			len = len - 4;
			$('#submiterrors .box-content').append('<i>'+len+' more...</i>');
			break;
		}
	}
}
}

AssessmentForm.prototype.subInit = function() {
var self = this;

$.ajax({

url: 'server.php',
type: 'POST',
data: {action: 'subordinates'},
success: function(response) {
	//log(response)
	var data = JSON.parse(response);
	
	self.populateSubordinates(data.message);	
	
}
});

}

AssessmentForm.prototype.addNewSubRow = function() {

var self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('SUBORDINATEROW');
$('#subordinates .box-content').append(clientrow);

}

AssessmentForm.prototype.populateSubordinates = function(users) {
var self = this;

$('#subordinates .box-content').empty();

for(var a in users) {// populating the employee rows

	var i = $.inArray(a, self.buffer);
	if( i == -1 && length(self.buffer) < 4 ) {
		self.addNewSubRow();
		var user = '<div><b>'+users[a].firstname+' '+users[a].lastname+'</b></div>';
		var designation = '<div>'+users[a].designation+'</div>';

		var nd = new Date(users[a].lastseen);//create date object using last seen string
		var ls = nd.toDateString();
		var time = nd.toTimeString();
		var pcs = time.split(' ');
		ls = ls + ' ' + pcs[0];//convert last seen date to preferred string
		
		var temphref = $('.newsubrow #toforms').attr('href');
		temphref = temphref + '&see=' + users[a].id;
		$('.newsubrow #toforms').attr('href', temphref);//updating the forms button with the user id

		$( ".newsubrow #percentage" ).progressbar({ // displaying % progress on the progress bar
			value: +users[a].overallprogress
		});
		$( ".newsubrow #percentage-inner" ).text(users[a].overallprogress+'%'); // displaying them on the progress bar
		
		$('.newsubrow #useravator').css("background-image", "url("+ (users[a].avator != "" ? users[a].avator : "files/avator.png" )+")");
		$('.newsubrow #iduser').append(users[a].id);
		$('.newsubrow #user').append(user);
		$('.newsubrow #user').append(designation);
		$('.newsubrow #date').append(ls);
		
		$('.newsubrow').removeClass('newsubrow');
		self.buffer.push(a);
	};
}

if( length(users) == 0 ) 
	$('#subordinates .box-content').html("<div style='font-size:14px;margin-top:5px;width:100%;color:grey;text-align:center;font-style:normal;'>No Team Members Yet!</div>");

}