function HPAPForm () {

AssessmentForm.call(this);
this.ids = ["HPAP_FORM_ONE","HPAP_FORM_TWO","HPAP_FORM_THREE"];
this.kpas = [];
this.projects = [];
this.plans = [];
this.rowNameBuffer = [];
this.projectRowNameBuffer = [];
this.planRowNameBuffer = [];
this.submittedforms = {};
this.tabs = ["hpap","assessment"];

}


HPAPForm.prototype = new AssessmentForm();

HPAPForm.prototype.constructor = HPAPForm;

//the script loads certain key properties and methods from the AssessmentForm object because the forms are similarly managed
//however some methods and values are replaced in order to better suite the HPAP Form
//AssessmentForm methods used by the HPAPForm are summary(), formCheck(), saveForm(), tabChanger(), leftMenuChanger(), error()

HPAPForm.prototype.init = function() {
self = this;

$('#rightDiv').find('form').each(function() {
item = this;
var formId = $(item).attr('name');
self.loadForm(formId, item);


$(item).find('input, select, textarea').each(function(){//function to assign datepicker to appropriate form values 
	var name = $(this).attr('name');
	if(name == 'internal_date' || name == 'external_date')	$(this).datepicker({minDate: 0, dateFormat: "D d M yy"});
	$(this).change(function() {//onchange every input field item should save
		self.saveForm();
	});
});
});

}


HPAPForm.prototype.loadForm = function(form, elem) {
self = this;

$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image

//disable buttons to avoid ajax calls before info is loaded
$('#save').css({"background":"grey"}).attr({"id":"savedisabled"});
$('#submit').css({"background":"grey"}).attr({"id":"submitdisabled"});

var perc = self.percentage();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'load', perc: perc},	
	success: function(data) {
	//log(data);
	data = JSON.parse(data);
	if(data.ok) {
		for(var a in data.message.hpapformone) self.kpas[a] = data.message.hpapformone[a];//update kpas 
		for(var b in data.message.hpapformtwo) self.projects[b] = data.message.hpapformtwo[b];//update projects 
		for(var b in data.message.hpapformthree) self.plans[b] = data.message.hpapformthree[b];//update plans 
		
		self.completeform = data.message;//update rest of the form
		self.submittedform = data.sdata;
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

		self.submittedform.formone = iterate(self.completeform.formone);
		self.submittedform.formtwo = iterate(self.completeform.formtwo);
		self.submittedform.formthree = iterate(self.completeform.formthree);
		
		self.submittedform.hpapformone = self.submittedform.hpapformtwo = self.submittedform.hpapformthree = {};
		
		for(var b in self.completeform.hpapformone) self.submittedform.hpapformone[b] = iterate(self.completeform.hpapformone[b]);
		for(var c in self.completeform.hpapformtwo) self.submittedform.hpapformtwo[c] = iterate(self.completeform.hpapformtwo[c]);
		for(var d in self.completeform.hpapformthree) self.submittedform.hpapformthree[d] = iterate(self.completeform.hpapformthree[d]);
		self.submit = true;
		
		log(self.submittedform);
	}
	

	if( form == 'HPAP_FORM_ONE' ) {
	//log(length(self.completeform.hpapformone));
		if(length(self.completeform.hpapformone) > 0 ) {
			
			self.populateRows();
			self.initNewRow();
			
		}else self.initNewRow();
	}else if ( form == 'HPAP_FORM_TWO' ) {
		if(length(self.completeform.hpapformtwo) > 0 ) {
			
			self.populateProjectRows();
			self.initNewProjectRow();
			
		}else self.initNewProjectRow();
	}else if ( form == 'HPAP_FORM_THREE' ) {
		if(length(self.completeform.hpapformthree) > 0 ) {
			
			self.populatePlanRows();
			self.initNewPlanRow();
			
		}else self.initNewPlanRow();
	}

	$("#loadimage").hide('slow');//hide loading image
	self.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly

	if( typeof adminFormViewer == "undefined" ) //check if user is admin, if not run user script
		self.getComments('#formContainer');//checking for new comments

	else if( typeof adminFormViewer != 'undefined' ) {
		var afv = new adminFormViewer();
		afv.initiate();
	}

	if( self.cfdata.hpapsignedoff == 1 ) //check if user's form has been approved, if so disable buttons
		disable_buttons();
	
	//dealing with the submit form button
	if(self.cfdata.hpap == 1) $('#submitdisabled').css({"background":"orange"}).text('Form Submitted').attr({"id":"submitted"});
	else $('#submitdisabled').css({"background":"rgb(35,85,165)"}).attr({"id":"submit"});

	//enable buttons
	$('#savedisabled').css({"background":"rgb(35,85,165)"}).attr({"id":"save"});
	$('#submit').unbind('click');
	$('#submit').on('click', function() {//binding the submit form functionality to the submit button
		self.finalFormCheck("HPAP", function() {
			dimBack();
			var nt = new Templates();
			var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");
			$('#rightDiv').append(html);
			$('.formsubmitpopup').draggable({handle: '.box-header'});
			$('#close').click(function() {
				$('.formsubmitpopup').remove();
				clearBack();
			});
			$('#submitconfirm .box-content').html("Are you sure you want to submit the information in this HPAP Form? <br/>\
			Once you submit the form your superior will be able to approve it. As soon as your superior approves your form you will no\
			longer be able to edit it!\
			<div id='yes' class='button' style='width:50px;margin-top:15px;'>Yes</div>");

			$('#yes').on('click', function() {
				$('#submitconfirm #loadimage').show();
				$.ajax({
					url: 'server.php',
					type: 'POST',
					data: {action: "submit", form: "HPAP"},
					success: function(response) {
						log(response);
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


HPAPForm.prototype.updateCompleteForm = function(id) {
self = this;

var temp = {};
var i = $.inArray(id, self.ids);

if ( i == 0 ) {//if the form is hpapformone
	for(var a in self.kpas) temp[a] = self.kpas[a];
}else if ( i == 1 ) {//if the form is hpapformtwo
	for(var a in self.projects) temp[a] = self.projects[a];
}else if ( i == 2 ) {//if the form is hpapformthree
	for(var a in self.plans) temp[a] = self.plans[a];
}

self.completeform.hpapformone 	= (i == 0) 	? 	temp : 	self.completeform.hpapformone;
self.completeform.hpapformtwo 	= (i == 1) 	? 	temp : 	self.completeform.hpapformtwo;
self.completeform.hpapformthree = (i == 2) 	? 	temp : 	self.completeform.hpapformthree;

copyofcompleteform = JSON.parse( JSON.stringify( self.completeform ) );

if( self.submit ) self.updateSubmittedForm(copyofcompleteform);//updating the submitted form if its there!
self.submittedforms = copyofcompleteform;

}

//=================================================================================


//=========================== My High Performance Plan Code =======================
//=================================================================================


HPAPForm.prototype.addNewRow = function() {
self = this;
var nt = new Templates();
var kparow = nt.loadTemplate('KPA_ROWS');
$('.kpawrapper').append(kparow);

var kpaobj = nt.loadTemplate('OBJECTIVE_ROWS');
$('#newobjwrapper').append(kpaobj);


}

HPAPForm.prototype.initNewRow = function() {
self = this;

var newObjective = '<div class="addobjective" style="display:table;"><div class="center">\
<img src="files/add.png" style="height:20px;"/></div><div class="center" style="color:grey;">Add an Objective...</div></div>';

$('#newkpaname').html(newObjective);

$('.addobjective').on('click', function() {//onclick open a Add New KPA window
	elem = this;
	var ret = true;

	$('#rightDiv').find('#newObjective').each(function() {
		ret = false; 
	});
	
	if(ret) {// if theres no kpa window already open then run the script

	$('#loading #loadimage').show();//show loading image
		
	$.ajax({//load kpas and populate select field
		url: 'server.php',
		type: 'POST',
		data: {action: 'loadkpas'},
		success: function(response) {
			//log(response);
			
			var nt = new Templates();
			var html = nt.loadTemplate("NEW_OBJECTIVE");
			dimBack();
			$('#rightDiv').append(html);
			$('#newObjective').draggable();
			$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});			
			
			var data = JSON.parse(response);
			var kpas = data.message;
			var options = "\
			<option value='"+kpas.kpa0+"'>"+kpas.kpa0+"</option>\
			<option value='"+kpas.kpa1+"'>"+kpas.kpa1+"</option>\
			<option value='"+kpas.kpa2+"'>"+kpas.kpa2+"</option>\
			<option value='"+kpas.kpa3+"'>"+kpas.kpa3+"</option>\
			<option value='"+kpas.kpa4+"'>"+kpas.kpa4+"</option>\
			<option value='"+kpas.kpa5+"'>"+kpas.kpa5+"</option>";
			
			if(data.ok) $('#newObjective #kpaname').append(options);

			$('#loading #loadimage').hide();//hide loading image
	
			$('#go').click(function() {

				$('#loading #loadimage').show();//show loading image

				var name = $('#newObjective #kpaname').val();

				//getkeys(nh.completeform, true);
				var knames = [];
				for( var h in self.completeform.hpapformone) knames[h] = self.completeform.hpapformone[h].kpaname;

				if( name != "null" && name != "" ) {
/*					var z = $.inArray(name, knames);
					if(z != -1) val = {"name":name,"id":z};//get both KPA name and KPA name's key 
					else val = {"name":name,"id":-1};
					//getkeys(val, true);
*/					var val = {"id": -1};
 
					var ret = self.updateObjective(val, 'form[name="NEW_OBJECTIVE"]', true);
					if(ret) clearBack();
				
					var good = self.formCheck('#formContainer');

					if ( ! good.ok) {
						self.error(good.elem);
						return;
					}

					//getkeys(self.completeform.hpapformtwo,true);
					var dets = JSON.stringify(self.completeform);
					
					if( self.submit )	var sdets = JSON.stringify(self.submittedform);//if user has submitted a form then
					else	var sdets = "false";
					var perc = self.percentage();
					
					$.ajax({
						url: 'server.php',
						type: 'POST',
						data: { data: dets, sdata: sdets, action: 'store', perc: perc },
						success: function(response, status) {
							//log(response);
							data = JSON.parse(response);
							$("#loadimage").hide();//hide loading image
							if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
							else if( ! data.ok) {
								if(data.redirect) {}
								else alert(data.message);
							}
							setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
							$('.errortable').fadeOut(1000);
						}
					});
					self.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
				}else if (name == 'null' || name == '') {
				
					var err = {"ok":false,"elems":{"tag":"#newObjective #kpaname","message":"Choose a KPA first! If you have not defined a KPA, exit this pop-up and click the green button!"}};
					self.error(err);
				}
				$('#loading #loadimage').hide();//show loading image

			});


			$('#close').click(function()  {
				$('.kpatoedit').parents('#hpap_kpas').removeClass('kpatoedit');
				$('form[name="NEW_OBJECTIVE"]').remove();
				clearBack();
			});

/*			}else if (name == '' || typeof (name) == 'undefined') {
			
				if(typeof (name) == 'undefined' ) var input = $('#clicktoadd');
				else var input = $('#newKPA');
				
				var err = {"ok":false,"elems":{"tag":input,"message":"Add a KPA first!"}};
				self.error(err);
			}
*/		}
	});
	}
});

}

HPAPForm.prototype.updateObjective = function(val, container, edit) {
self = this;
var good = self.formCheck(container);

if(good.ok) {
	
	var objectiveForm = $(container).formParams();

	if(val.id == '-1')	self.kpas.push(objectiveForm);//if its a new KPA then add to the end
	else	self.kpas[val.id] = objectiveForm;//if the KPA is being edited then overwrite the old one
	
	self.updateCompleteForm("HPAP_FORM_ONE");
	$(container).remove();
	
	$('#newkpas').empty();//remove "Add New KPA" utilities
	$('#newobjwrapper #objective').empty();//remove "Add New KPA" utilities

	if( edit) {//clear all rows and delete the edited KPA from the restrictive buffer. Rows will be redrawn by self.populateRows()
		$('#rightDiv').empty();
		var nt = new Templates();
		var hdr = nt.loadTemplate("HPAP_FORM_HEADER");
		var html = nt.loadTemplate("HPAP_FORM_ONE");
		$('#rightDiv').append(hdr);
		$('#rightDiv').append(html);

		self.tabChanger();
		$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
			self.init();//initialize form
		});	
		
		self.rowNameBuffer = [];
	}
	
//	getvalues(self.rowNameBuffer);alert(val.name);
	self.populateRows();//reinsert info in the rows to add our new KPA
	self.initNewRow();//append "Add New KPA" utilities to the last row

	ret = true;
}else if( ! good.ok) {
	self.error(good.elem);

	ret = false;
}

return ret;
}

HPAPForm.prototype.populateRows = function() {
self = this;
var form = self.completeform.hpapformone;

delete form.newKPA; // this property is unnecessarily assigned and causes problems below so we DELETE IT! :)
//getkeys(self.completeform.hpapformone, true);

for(var a in form) {
	// the buffer stops the script from loading a KPA from the save file thats already been loaded. 
	// reloading of KPAs happens because the init function that calls this function is run numerous times in order to
	// populate form fields
	// alert(a);
	var i = $.inArray(a, self.rowNameBuffer);
	if( i == -1 ) {
		var KPA = '<div class="kpainfo">'+form[a].kpaname+'</div>';
		var objective = '<div class="kpainfo">'+form[a].objective+'</div>';
		var measurement = '<div class="kpainfo">'+form[a].measurement+'</div>';
		var completiondate = '<div class="kpainfo">'+form[a].cd+'</div>';
		
		$('#newkpaname').append(KPA).addClass('fcontainer');
		$('#newkpaname #idkpa').append(a);
		$('#newobjwrapper #objective').append(objective).addClass('fcontainer');
		$('#newobjwrapper #measurement').append(measurement).addClass('fcontainer');
		$('#newobjwrapper #completiondate').append(completiondate).addClass('fcontainer');
		
		$('#newobjwrapper #editbutton').show();
		
		$('#newkpaname').attr({'id':'kpaname', 'name':'kpaname'});
		$('#newobjwrapper').attr('id','objwrapper');

		self.rowNameBuffer.push(a);
		self.addNewRow();


	};
}


}


function edit(item) {

$(item).parents('#hpap_kpas').addClass('kpatoedit');
var t = $('.kpatoedit').find('#kpaname #idkpa').text();

var nt = new Templates();
var html = nt.loadTemplate("NEW_OBJECTIVE");
dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});

var nh = new HPAPForm();
$('.heading #loadimage').show();//show loading image
var perc = nh.percentage();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'load', perc: perc},
	success:	function(data) {
		//alert(data);
		data = JSON.parse(data);
		if(data.ok) {
			for(var a in data.message.hpapformone) nh.kpas[a] = data.message.hpapformone[a];//update kpas 
			nh.completeform = data.message;//update rest of the form
			if( length(data.sdata) > 0 )	{//update submitted form if any!
				nh.submittedform = data.sdata;
				nh.submit = true;
			}
		}

		$.ajax({//load kpas and populate select field
			url: 'server.php',
			type: 'POST',
			data: {action: 'loadkpas'},
			success: function(response) {
			
				var data = JSON.parse(response);
				var kpas = data.message;
				var options = "<option value='"+kpas.kpa0+"'>"+kpas.kpa0+"</option><option value='"+kpas.kpa1+"'>"+kpas.kpa1+"</option><option value='"+kpas.kpa2+"'>"+kpas.kpa2+"</option><option value='"+kpas.kpa3+"'>"+kpas.kpa3+"</option><option value='"+kpas.kpa4+"'>"+kpas.kpa4+"</option><option value='"+kpas.kpa5+"'>"+kpas.kpa5+"</option>";
				
				if(data.ok) $('#newObjective #kpaname').append(options);
				
				for( var h in nh.completeform.hpapformone) {
					if(t == h)	{
						var val = {"id":h};//get both KPA name and KPA name's key 
						var omit = [];
						nh.updateFormValues('form[name="NEW_OBJECTIVE"]', nh.completeform.hpapformone[h], omit);
						$('#kpaname option[value="'+nh.kpas[h].kpaname+'"]').prop('selected', true);
					}
				}


				$('#go').click(function() {

					//log(nh.ids[0]);
					$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image
					
					var name = $('#newObjective #kpaname').val();
					
					if( name != "null" && name != "" ) {
						var ret = nh.updateObjective(val, 'form[name="NEW_OBJECTIVE"]', true);
						if(ret) clearBack();
						
						var good = nh.formCheck('#formContainer');

						if ( ! good.ok) {
							nh.error(good.elem);
							return;
						}

						//getkeys(nh.completeform.hpapformtwo,true);
						var dets = JSON.stringify(nh.completeform);
						
						if( nh.submit )	var sdets = JSON.stringify(nh.submittedform);//if user has submitted a form then
						else	var sdets = "false";
						var perc = nh.percentage();
						
						$.ajax({
							url: 'server.php',
							type: 'POST',
							data: { data: dets, sdata: sdets, action: 'store', perc: perc },
							success: function(response, status) {
								//alert(response);
								data = JSON.parse(response);
								$("#loadimage").hide();//hide loading image
								if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
								else if( ! data.ok) {
									if(data.redirect) {}
									else alert(data.message);
								}
								setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
								$('.errortable').fadeOut(1000);
							}
						});
						nh.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
					}else if (name == 'null' || name == '') {
					
						var err = {"ok":false,"elems":{"tag":"#newObjective #kpaname","message":"Choose a KPA first! If you have not defined a KPA, exit this pop-up and click the green button!"}};
						self.error(err);
					}
				});


				$('#close').click(function()  {
					$('.kpatoedit').removeClass('kpatoedit');
					$('form[name="NEW_OBJECTIVE"]').remove();
					clearBack();
				});

				$(".heading #loadimage").hide('slow');//hide loading image
			}
		});
	}
});
}

function definekpas() {

var nt = new Templates();
var html = nt.loadTemplate("DEFINEKPAS");

dimBack();
$('#rightDiv').append(html);
$('#newObjective').draggable({handle: '.box-header'});

$('#newObjective .heading #loadimage').show();//show loading image

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'loadkpas'},	
	success: function(data) {
	//log(data);
	data = JSON.parse(data);
	if(data.ok) {
		
		var definedkpas = data.message;
		if( length(definedkpas) > 0 ) {
			var na = new AssessmentForm();
			na.updateFormValues('#newObjective', definedkpas, []);
		}
	}else if(data.redirect) location.href = 'index.php';
	else alert( data.message );

	$('#newObjective .heading #loadimage').hide();//hide loading image
	}
});

$('#go').click(function() {
	
	$('#kpaloading #loadimage').show();//show loading image
	
	var kpaform = $('form[name="DEFINEKPAS"]').formParams(true);
	var savekpas = JSON.stringify(kpaform);
	
	$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action: 'savekpas', kpas: savekpas},
	success: function(response) {
		//log(response);
		data = JSON.parse(response);
		$("#loadimage").hide();//hide loading image
		if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
		else if( ! data.ok) {
			if(data.redirect) {}
			else alert(data.message);
		}
		setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
		$('.errortable').fadeOut(1000);
		
		$('form[name="DEFINEKPAS"]').remove();
		clearBack();
	}
	});
	
});

$('#close').click(function()  {
	$('form[name="DEFINEKPAS"]').remove();
	clearBack();
});

}
//=================================================================================
//=================================================================================



//=========================== My Leader Value Project / Bold Play Code ===================
//=================================================================================


HPAPForm.prototype.addNewProjectRow = function() {
self = this;
var nt = new Templates();
var prjrow = nt.loadTemplate('PRJ_ROWS');
$('#formWrapper').append(prjrow);

var prjobj = nt.loadTemplate('PROJECT_ROWS');
$('#newprjwrapper').append(prjobj);

}

HPAPForm.prototype.initNewProjectRow = function() {
self = this;

var newProject = '<div class="addobjective newProject" style="display:table;"><div class="center">\
<img src="files/add.png" style="height:20px;"/></div><div class="center" style="color:grey;">Add a Project...</div></div>';

$('#newproject').empty();
$('#newproject').append(newProject);

$('.newProject').on('click', function() {//onclick open a Add New Project window
	elem = this;
	var ret = true;
	
	$('#rightDiv').find('#newObjective').each(function() {
		ret = false; 
	});
	
	if(ret) {// if theres no kpa window already open then run the script
		
	var nt = new Templates();
	var html = nt.loadTemplate("NEW_PROJECT");
	dimBack();
	$('#rightDiv').append(html);
	$('#newObjective').draggable();
	$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});
	
	$('#go').click(function() {

		$('#loading #loadimage').show();//show loading image

		var prjnames = [];
		for( var h in self.completeform.hpapformtwo ) prjnames[h] = self.completeform.hpapformtwo[h].projectname;
		
		var val = {};
		var name = $('#projectname').val();
		var z = $.inArray(name, prjnames);
		if(z != -1) val = {"name":name,"id":z};//get both project name and project name's key 
		else val = {"name":name,"id":-1};
		//getkeys(val, true);
		
		var ret = self.updateProject(val, 'form[name="NEW_PROJECT"]', false);
		if(ret) clearBack();
		
		//getkeys(self.completeform.hpapformtwo,true);
		var dets = JSON.stringify(self.completeform);

		if( self.submit )	var sdets = JSON.stringify(self.submittedform);//if user has submitted a form then
		else	var sdets = "false";
		var perc = self.percentage();
		
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: { data: dets, sdata: sdets, action: 'store', perc: perc },
			success: function(response, status) {
				//alert(response);
				data = JSON.parse(response);
				$("#loadimage").hide();//hide loading image
				if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
				else if( ! data.ok) {
					if(data.redirect) {}
					else alert(data.message);
				}
				setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
				$('.errortable').fadeOut(1000);
			}
		});
		self.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly

	});


	$('#close').click(function()  {
		$('form[name="NEW_PROJECT"]').remove();
		clearBack();
	});

	}
});

}

HPAPForm.prototype.updateProject = function(val, container, edit) {
self = this;
var good = self.formCheck(container);

if(good.ok) {
	
	var projectForm = $(container).formParams();

	if(val.id == -1)	self.projects.push(projectForm);//if its a new Project then add to the end
	else	self.projects[val.id] = projectForm;//if the Project is being edited then overwrite the old one
	
	self.updateCompleteForm("HPAP_FORM_TWO");

	$(container).remove();
	
	$('#newproject').empty();//remove "Add New Project" button

	if( edit) {//clear all rows and delete the edited Project from the restrictive buffer. Rows will be redrawn by self.populateProjectRows()
		$('#rightDiv').empty();
		var nt = new Templates();
		var hdr = nt.loadTemplate("HPAP_FORM_HEADER");
		var html = nt.loadTemplate("HPAP_FORM_TWO");
		$('#rightDiv').append(hdr);
		$('#rightDiv').append(html);

		self.tabChanger();
		$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
			self.init();//initialize form
		});	
		
		$('.selected').removeClass('selected');
		$('#HPAP_FORM_TWO').addClass('selected');

		self.projectRowNameBuffer = [];
	}
	
//	getvalues(self.rowNameBuffer);alert(val.name);
	self.populateProjectRows();//reinsert info in the rows to add our new Project
	self.initNewProjectRow();//append "Add New Project" button to the last row

	ret = true;
}else if( ! good.ok) {
	self.error(good.elem);

	ret = false;
}

return ret;
}

HPAPForm.prototype.populateProjectRows = function() {
self = this;

var form = self.completeform.hpapformtwo;

for(var a in form) {
	// the buffer stops the script from loading a KPA from the save file thats already been loaded. 
	// reloading of KPAs happens because the init function that calls this function is run numerous times in order to
	// populate form fields
	// alert(a);
	var i = $.inArray(form[a].projectname, self.projectRowNameBuffer);
	if( i == -1 ) {
		var projectname = '<div class="kpainfo">'+form[a].projectname+'</div>';
		var measurement = '<div class="kpainfo">'+form[a].measurement+'</div>';
		var completiondate = '<div class="kpainfo">'+form[a].cd+'</div>';
		$('#newproject').append(projectname);
		$('#newproject #idprj').append(a);
		$('#newprjwrapper #prmeasurement').append(measurement);
		$('#newprjwrapper #date').append(completiondate);
		
		$('#newprjwrapper #editbutton').show();
		
		$('#newproject').attr('id','project');
		$('#newprjwrapper').attr('id','prjwrapper');

		self.addNewProjectRow();

		self.projectRowNameBuffer.push(form[a].projectname);

	};
}

}


function editProject(item) {

$(item).parents('#hpap_kpas').addClass('rowtoedit');
var t = $('.rowtoedit').find('#project #idprj').text();

var nt = new Templates();
var html = nt.loadTemplate("NEW_PROJECT");
dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});

var nh = new HPAPForm();
$('.heading #loadimage').show();//show loading image
var perc = nh.percentage();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'load', perc: perc}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {
		for(var a in data.message.hpapformtwo) nh.projects[a] = data.message.hpapformtwo[a];//update kpas 
		nh.completeform = data.message;//update rest of the form
		if( length(data.sdata) > 0 )	{//update submitted form if any!
			nh.submittedform = data.sdata;
			nh.submit = true;
		}
	}

	for( var h in nh.completeform.hpapformtwo) {
		if(t == h)	{
			var val = {"id":h};//get both KPA name and KPA name's key 
			var omit = [];
			nh.updateFormValues('form[name="NEW_PROJECT"]', nh.completeform.hpapformtwo[h], omit);
		}
	}

	$('#go').click(function() {

		//alert(nh.ids[0]);
		$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image

		var ret = nh.updateProject(val, 'form[name="NEW_PROJECT"]', true);
		if(ret) clearBack();
		
		nh.tabChanger();
		$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
			nh.init();//initialize form
		});	
		
		var good = nh.formCheck('#newObjective');

		//alert(good.ok);
		if ( ! good.ok) {
			nh.error(good.elem);
			return;
		}

		//getkeys(nh.completeform.hpapformtwo,true);
		var dets = JSON.stringify(nh.completeform);

		if( nh.submit )	var sdets = JSON.stringify(nh.submittedform);//if user has submitted a form then
		else	var sdets = "false";
		var perc = nh.percentage();
		
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: { data: dets, sdata: sdets, action: 'store', perc: perc },
			success: function(response, status) {
				//alert(response);
				data = JSON.parse(response);
				$("#loadimage").hide();//hide loading image
				if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
				else if( ! data.ok ) {
					if(data.redirect) location.href = 'index.php';
					else alert( data.message );
				}
				setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
				$('.errortable').fadeOut(1000);
			}
		});
		nh.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
	});


	$('#close').click(function()  {
		$('.rowtoedit').removeClass('rowtoedit');
		$('form[name="NEW_PROJECT"]').remove();
		clearBack();
	});

	$(".heading #loadimage").hide('slow');//hide loading image
});
}

//=================================================================================
//=================================================================================

//=========================== My Personal Development Plan Code ===================
//=================================================================================


HPAPForm.prototype.addNewPlanRow = function() {
self = this;
var nt = new Templates();
var planrow = nt.loadTemplate('PLAN_ROWS');
$('#formWrapper').append(planrow);

var planobj = nt.loadTemplate('PLAN_SECTION_ROWS');
$('#newplanwrapper').append(planobj);

}

HPAPForm.prototype.initNewPlanRow = function() {
self = this;

var newPlan = '<div class="addobjective newPlan" style="display:table;"><div class="center">\
<img src="files/add.png" style="height:20px;"/></div><div class="center" style="color:grey;">Add a New Plan...</div></div>';

$('#newplanwrapper #education').empty();
$('#newplanwrapper #education').append(newPlan);

$('.newPlan').on('click', function() {//onclick open a Add New Plan window
	elem = this;
	var ret = true;
	
	$('#rightDiv').find('#newObjective').each(function() {
		ret = false; 
	});
	
	if(ret) {// if theres no kpa window already open then run the script
		
	var nt = new Templates();
	var html = nt.loadTemplate("NEW_PLAN");
	dimBack();
	$('#rightDiv').append(html);
	$('#newObjective').draggable();
	$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});
	
	$('#go').click(function() {

		$('#loading').html('<img id="loadimage" src="files/loading.gif"/>');//show loading image

		var val = {"id":-1};
		var ret = self.updatePlan(val, 'form[name="NEW_PLAN"]', false);
		if(ret) clearBack();
		
		//getkeys(self.completeform.hpapformthree,true);
		var dets = JSON.stringify(self.completeform);

		if( self.submit )	var sdets = JSON.stringify(self.submittedform);//if user has submitted a form then
		else	var sdets = "false";
		var perc = self.percentage();
		
		$.ajax({
			url: 'server.php',
			type: 'POST',
			data: { data: dets, sdata: sdets, action: 'store', perc: perc },
			success: function(response, status) {
				//alert(response);
				data = JSON.parse(response);
				$("#loadimage").hide();//hide loading image
				if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
				else if( ! data.ok) {
					if(data.redirect) {}
					else alert(data.message);
				}
				setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
				$('.errortable').fadeOut(1000);
			}
		});
		self.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly

	});


	$('#close').click(function()  {
		$('form[name="NEW_PLAN"]').remove();
		clearBack();
	});

	}
});

}

HPAPForm.prototype.updatePlan = function(val, container, edit) {
self = this;
var good = self.formCheck(container);

if(good.ok) {
	
	var planForm = $(container).formParams();

	if(val.id == -1)	self.plans.push(planForm);//if its a new Plan then add to the end
	else	self.plans[val.id] = planForm;//if the Plan is being edited then overwrite the old one
	
	self.updateCompleteForm("HPAP_FORM_THREE");

	$(container).remove();
	
	$('#newplanwrapper #education').empty();//remove "Add New Plan" button

	if( edit) {//clear all rows and delete the edited Plan from the restrictive buffer. Rows will be redrawn by self.populatePlanRows()
		$('#rightDiv').empty();
		var nt = new Templates();
		var hdr = nt.loadTemplate("HPAP_FORM_HEADER");
		var html = nt.loadTemplate("HPAP_FORM_THREE");
		$('#rightDiv').append(hdr);
		$('#rightDiv').append(html);

		self.tabChanger();
		$('.tabs').on('click', function(){ //this is necessary to reassign events as a user scrolls across tabs
			self.init();//initialize form
		});	
		
		$('.selected').removeClass('selected');
		$('#HPAP_FORM_THREE').addClass('selected');
		
		self.planRowNameBuffer = [];
	}
	
//	getvalues(self.rowNameBuffer);alert(val.name);
	self.populatePlanRows();//reinsert info in the rows to add our new Plan
	self.initNewPlanRow();//append "Add New Plan" button to the last row

	ret = true;
}else if( ! good.ok) {
	self.error(good.elem);

	ret = false;
}

return ret;
}

HPAPForm.prototype.populatePlanRows = function() {
self = this;

var form = self.completeform.hpapformthree;

for(var a in form) {
	// the buffer stops the script from loading a KPA from the save file thats already been loaded. 
	// reloading of KPAs happens because the init function that calls this function is run numerous times in order to
	// populate form fields
	// alert(a);
	var i = $.inArray(a, self.planRowNameBuffer);
	if( i == -1 ) {
		var education = '<div class="kpainfo">'+form[a].education+'</div>';
		var experience = '<div class="kpainfo">'+form[a].experience+'</div>';
		var exposure = '<div class="kpainfo">'+form[a].exposure+'</div>';
		var completiondate = '<div class="kpainfo">'+form[a].cd+'</div>';

		$('#newplanwrapper #pID').append(a);

		$('#newplanwrapper #education').append(education);
		$('#newplanwrapper #experience').append(experience);
		$('#newplanwrapper #exposure').append(exposure);
		$('#newplanwrapper #cdate').append(completiondate);
		
		$('#newplanwrapper #editbutton').show();
		
		$('#newplanwrapper').attr('id','planwrapper');

		self.addNewPlanRow();

		self.planRowNameBuffer.push(a);

	};
}

}


function editPlan(item) {

$(item).parents('#hpap_kpas').addClass('rowtoedit');
var t = $('.rowtoedit').find('#planwrapper #pID').text();

var nt = new Templates();
var html = nt.loadTemplate("NEW_PLAN");
dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#cd').datepicker({minDate: 0, dateFormat: "D d M yy"});

var nh = new HPAPForm();
$('#loading #loadimage').show();//show loading image
var perc = nh.percentage();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'load', perc: perc},
	success: function(data) {
		//alert(data);
		data = JSON.parse(data);
		if(data.ok) {
			for(var a in data.message.hpapformthree) nh.plans[a] = data.message.hpapformthree[a];//update kpas 
			nh.completeform = data.message;//update rest of the form
			if( length(data.sdata) > 0 )	{//update submitted form if any!
				nh.submittedform = data.sdata;
				nh.submit = true;
			}
		}

		for( var h in nh.completeform.hpapformthree) {
			//log("t:  "+t+" h:  "+h);
			if(t == h)	{
				var val = {"id":h};//get both KPA name and KPA name's key 
				var omit = [];
				nh.updateFormValues('form[name="NEW_PLAN"]', nh.completeform.hpapformthree[h], omit);
			}
		}

		$('#go').click(function() {

			//alert(nh.ids[0]);
			$('#loading #loadimage').show();//show loading image

			var ret = nh.updatePlan(val, 'form[name="NEW_PLAN"]', true);
			if(ret) clearBack();
			
			var good = nh.formCheck('#newObjective');

			//alert(good.ok);
			if ( ! good.ok) {
				nh.error(good.elem);
				return;
			}

			//getkeys(nh.completeform.hpapformthree,true);
			var dets = JSON.stringify(nh.completeform);
			
			if( nh.submit )	var sdets = JSON.stringify(nh.submittedform);//if user has submitted a form then
			else	var sdets = "false";
			var perc = nh.percentage();
			
			$.ajax({
				url: 'server.php',
				type: 'POST',
				data: { data: dets, sdata: sdets, action: 'store', perc: perc },
				success: function(response, status) {
					//alert(response);
					data = JSON.parse(response);
					$("#loadimage").hide();//hide loading image
					if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
					else if( ! data.ok ) {
						if(data.redirect) location.href = 'index.php';
						else alert( data.message );
					}
					setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
					$('.errortable').fadeOut(1000);
				}
			});
			nh.summary();//initialize the summary bar on the left of the screen. This must happen after every form load or upon saving in order to update accordingly
		});


		$('#close').click(function()  {
			$('.rowtoedit').removeClass('rowtoedit');
			$('form[name="NEW_PLAN"]').remove();
			clearBack();
		});
		$(".heading #loadimage").hide('slow');//hide loading image
	}
});
}


//=================================================================================
//=================================================================================
