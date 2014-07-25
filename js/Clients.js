function Clients() {

this.clients = [];
this.users = [];
this.sortedUsers = [];
this.rowNameBuffer = [];
this.dropDownBuffer = [];
this.listLimit = 10;
}

Clients.prototype.initClients = function(){
self = this;
//self.addNewClientRow();

$('.rows').sortable({ handle: '.box-header'});

self.loadClients(true, false);
	
$('#rightDiv').on('click', '#addClient', function () {
	var ret = true;
	$('#rightDiv').find('#newClient').each(function() {
		ret = false; 
	});
	
	if(ret) {
		var nt = new Templates();
		var html = nt.loadTemplate("NEW_CLIENT_POPUP");
		dimBack();
		$('#rightDiv').append(html);
		$('#newClient').draggable({handle: '.box-header'});
		
		$('#close').click(function(){
			$('#newClient').remove();
			clearBack();
		});
		
		$('#go').unbind('click');
		$('#go').on('click', function() {
			det = {"edit":false,"id":"-1"};
			var ret = self.updateClient(det);
			if(ret) {
				clearBack();
				self.saveClients();
			}
		});
	}

});

}

Clients.prototype.leftMenuChanger = function() {
self = this;
	

$('#cr').on('click', function() {
	
	location.hash = "clients";
	var nt = new Templates();
	nt.displayElems("#rightDiv", nt.loadTemplate("CLIENTS"));
	
	var nc = new Clients();
	nc.initClients();//initialize form

});

$('#ur').on('click', function() {

	location.hash = "users";
	var nt = new Templates();
	nt.displayElems("#rightDiv", nt.loadTemplate("USERS"));
	
	var nc = new Clients();
	nc.initUsers();	

});

}

Clients.prototype.loadClients = function (b, func) {
self = this;
$('#loadimage').show();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'admin-load'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {//if save file found
		//getkeys(data.message, true);
		self.clients = data.message;
		if(b) {
			if(length(self.clients) < self.listLimit) self.populateClientRows(self.clients);
			else requestClientPage(1, self.listLimit);
		}
		if(typeof func == 'function') func();
		$('#loadimage').hide();
	}else if(data.redirect) location.href = 'index.php';
	else alert(data.message);
});

}

Clients.prototype.saveClients = function() {
self = this;
$('#loadimage').show();
$('#addClient').css({"background":"grey"}).attr({"id":"disabledaddClient"});

var dets = JSON.stringify(self.clients);
//getvalues(dets[3]);

$.ajax({
	url: 'server.php',
	data: {data: dets, action: 'admin-save'},
	type: 'POST'
	}).done(
	function(response, status) {
		//alert(response);
		data = JSON.parse(response);
		$("#loadimage").hide();//hide loading image
		if(data.ok) {
			$.ajax({ url: 'server.php', type: 'POST', data:	{action: 'admin-load'}})
			.done(function(resp) {
				//alert(data);
				resp = JSON.parse(resp);
				if(resp.ok) self.clients = resp.message;
				$('#disabledaddClient').css({"background":"rgb(35,85,165)"}).attr({"id":"addClient"});
			});
		$('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
			
		}else if(data.redirect) location.href = 'index.php';
		else alert(data.message);
		
		setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
		$('.errortable').fadeOut(1000);
});
}

Clients.prototype.addNewClientRow = function() {
self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('NEW_CLIENT_ROW');

$('#clientRowContainer').append(clientrow);
var p = 0;//p will represent a company's progress and will be loaded dynamically
$( ".newclientrow #percentage" ).progressbar({ // displaying them on the progress bar
	value: p
});
p = Math.floor(p);//make a round figure for the display
$( ".newclientrow #percentage-inner" ).text(p+'%'); // displaying them on the progress bar

var buttons = nt.loadTemplate('CLIENT_BUTTONS');
$('.newclientrow #actions').append(buttons);
}

Clients.prototype.updateClient = function(det) {
self = this;
var elems = {};
var err = {"ok":true};

var newClient = $('form[name="newClient"]').serializeObject(true);


var nd = new Date();//create date object 
var ls = nd.toDateString();
var time = nd.toTimeString();
var pcs = time.split(' ');
time = pcs[0];//convert date to readable string

for(var a in newClient) {
	if(newClient[a] == '') {
		elems[a] = {"tag":"#"+a,"message":"Please fill in this section!"}
		err = {"ok":false,"elems":elems};
	}
}

newClient.date = (det.edit) ? self.clients[det.key].date : ls;
newClient.id = det.id;// id will determine if client is new or an old one being edited
newClient.companyid = det.companyid;
newClient.overallprogress = det.overallprogress;
newClient.registeredemployees = det.registeredemployees;


var ns = new AssessmentForm();

if(err.ok) {//if there are no empty fields in the form
	var err = ns.formCheck('.formsubmitpopup');
	if(err.ok) {//if there are no incorrectly filled fields in the form 
		if(det.edit) {
			$('#rightDiv').empty();
			var nt = new Templates();
			var hdr = nt.loadTemplate("CLIENTS");
			$('#rightDiv').append(hdr);

			self.rowNameBuffer = [];
			self.clients[det.key] = newClient;
			//getvalues(self.clients[det.key]);
		}else	self.clients.push(newClient);
		
		$('#newClient').remove();
		if(length(self.clients) < self.listLimit) self.populateClientRows(self.clients);
		else requestClientPage(1, self.listLimit);
	}else {
		ns.error(err.elem);
	}
}else if( ! err.ok) {
	ns.error(err.elems);
}
//alert(err.elems['overallprogress'].message);
return err.ok;
}

Clients.prototype.populateClientRows = function(clients) {
self = this;
for(var a in clients) {

	
	var i = $.inArray(a, self.rowNameBuffer);
	if( i == -1 ) {
		self.addNewClientRow();
		var companyname = '<div class="kpainfo">'+clients[a].companyname+'</div>';
		var staffcomplement = '<div class="kpainfo">'+clients[a].staffcomplement+'</div>';
		var registeredemployees = typeof clients[a].registeredemployees == 'undefined' ? 0 : clients[a].registeredemployees;
		var registeredemployees = '<div class="kpainfo">'+registeredemployees+'</div>';
		var overallprogress = typeof clients[a].overallprogress == 'undefined' ? 0 : clients[a].overallprogress;
		
		var nd = new Date(clients[a].date);//create date object using last seen string
		var ls = nd.toDateString();
		var time = nd.toTimeString();
		var pcs = time.split(' ');
		ls = ls + ' ' + pcs[0];//convert last seen date to preferred string

		var date = '<div class="kpainfo">'+ls+'</div>';

		$( ".newclientrow #percentage" ).progressbar({ // displaying % progress on the progress bar
			value: overallprogress
		});
		$( ".newclientrow #percentage-inner" ).text(overallprogress+'%'); // displaying them on the progress bar
		
		$('.newclientrow #idcompany').append(clients[a].id);
		$('.newclientrow #registered').append(registeredemployees);
		
		$('.newclientrow #company').append(companyname);
		$('.newclientrow #date').append(date);
		$('.newclientrow #complement').append(staffcomplement);
		
		$('.newclientrow').removeClass('newclientrow');

		self.rowNameBuffer.push(a);
	};
}
}

function requestClientPage(pn, rpp) {
var cs = new Clients();


cs.loadClients(false, stuffToBeDone);

function stuffToBeDone() {
	var clients = cs.clients;
	var total = length(clients);
	c2d = split(clients, ((pn - 1)*rpp), rpp);//clients to display
	
	$('#clientRowContainer').empty();
	
	log(c2d);
	cs.rowNameBuffer = [];
	cs.populateClientRows(c2d);

	var back = forward = center = "";
	var pages = Math.ceil(total/rpp);
	// Change the pagination controls
	// Only if there is more than 1 page worth of results give the user pagination controls
	if(pages > 1){
		if (pn > 1) back = '<button class="pgcontrols back cursor" style="width:20px;" onclick="requestClientPage('+(pn-1)+', '+rpp+')"></button>';
		for( var i = 1; i <= pages; i++ ) {
			center += '<button id="pgbutton" class="pgcontrols '+( i == pn ? 'pgselected' : '' )+' cursor" onclick="requestClientPage('+(i)+', '+rpp+')">'+i+'</button>';
		}
		if (pn != pages) forward = '<button class="pgcontrols forward cursor" style="width:20px;" onclick="requestClientPage('+(pn+1)+', '+rpp+')"></button>';
		$("#clientslist #pagination_controls").show();
		$("#clientslist #pagination_controls #back").html(back);
		$("#clientslist #pagination_controls #center").html(center);
		$("#clientslist #pagination_controls #forward").html(forward);

	}else $("#clientslist #pagination_controls").hide();
}

}

function view(elem) {

$(elem).parents('#clientrow').addClass('clienttoedit');
var t = $('.clienttoedit').find('#company #idcompany').text();

var nt = new Templates();
var html = nt.loadTemplate("VIEW_CLIENT");

dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#popuploadimage').show();
$('#go').css({"background":"grey"}).attr({"id":"disabledgo"});


var nc = new Clients();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'admin-load'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {//if save file found
		//getkeys(data.message, true);
		nc.clients = data.message;
		$('#popuploadimage').hide();

		for(var c in nc.clients) {
			if(t == nc.clients[c].id)	{
				det = {"key":c,"edit":true,"id":nc.clients[c].id,"companyid":nc.clients[c].companyid,"overallprogress":nc.clients[c].overallprogress,
				"registeredemployees":nc.clients[c].registeredemployees};
				$('#cid').append(nc.clients[c].companyid);
				nc.updateFormValues('form[name="newClient"]', nc.clients[c]);
			}
		}
			
		$('#close').click(function(){
			$('.clienttoedit').removeClass('clienttoedit');
			$('.formsubmitpopup').remove();
			clearBack();
		});

		$('#disabledgo').css({"background":"rgb(35,85,165)"}).attr({"id":"go"});

		$('#go').unbind('click');
		$('#go').on('click', function() {
			$('.clienttoedit').removeClass('clienttoedit');
			var ret = nc.updateClient(det);
			if(ret) { 
				clearBack();
				nc.saveClients();
			}
		});

	}
});


}


function deleteItem (elem) {

$(elem).parents('#clientrow').addClass('clienttoedit');
var t = $('.clienttoedit').find('#company #idcompany').text();

var nt = new Templates();
var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");

dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('.formsubmitpopup .box-content').html("Are you sure? This action cannot be reversed!<br/><div id='yes' class='button' style='width:50px;margin-top:15px;'>Yes</div>");

$('#close').click(function(){
	$('.clienttoedit').removeClass('clienttoedit');
	$('.formsubmitpopup').remove();
	clearBack();
});
		
$('#yes').click(function() {
$('.formsubmitpopup #loadimage').show();
var nc = new Clients();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'admin-load'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {//if save file found
		//getkeys(data.message, true);
		nc.clients = data.message;

		for(var c in nc.clients) {
			if(t == nc.clients[c].id)	{
				$.ajax({
					url: 	'server.php',
					type: 	'POST',
					data:	{action: 'delete', table:'company', id:nc.clients[c].id}
				}).done(function(response) {
					//alert(response);
					var resp = JSON.parse(response);
					if(resp.ok)  {

						for(var a in nc.clients) if(t == nc.clients[a].id) delete nc.clients[a];
						
						$('#rightDiv').empty();
						var nt = new Templates();
						var hdr = nt.loadTemplate("CLIENTS");
						$('#rightDiv').append(hdr);
						
						if(length(nc.clients) < nc.listLimit) nc.populateClientRows(nc.clients);
						else requestClientPage(1, nc.listLimit);
						
						$('.clienttoedit').removeClass('clienttoedit');
						$('.formsubmitpopup').remove();
						clearBack();
						
						$('#loading').html('<i id="savemessage">'+resp.message+'...</i>');
						setTimeout(function(){ $('#savemessage').fadeOut('slow'); }, 2000);
					}
				});
			}
		}

	}
});

});


}

//============================= END OF CLIENTS CODE ================

//============================= USERS CODE ===================================

Clients.prototype.initUsers = function() {
self = this;

$('.rows').sortable({ handle: '.box-header'});

self.loadClients(false, {});
self.loadUsers(true, {});

}

Clients.prototype.loadUsers = function(t, func) {
self = this;
$('#loadimage').show();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'employees'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {/*if save file found*/
		self.users = data.message;
		if(t) {
			if(length(self.users) < self.listLimit) self.populateUserRows(self.users);
			else requestUserPage(1, self.listLimit);
		}
		if(typeof func == 'function') func(self.users);
	}else if(data.redirect) location.href = 'index.php';
	else alert(data.message);
	$('#loadimage').hide();
});

}

Clients.prototype.addNewUserRow = function() {
self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('NEW_USER_ROW');
$('#userRowContainer').append(clientrow);

var p = 0;//p will represent a company's progress and will be loaded dynamically
$( ".newuserrow #percentage" ).progressbar({ // displaying them on the progress bar
	value: p
});
p = Math.floor(p);//make a round figure for the display
$( ".newuserrow #percentage-inner" ).text(p+'%'); // displaying them on the progress bar

var buttons = nt.loadTemplate('USER_BUTTONS');
$('.newuserrow #actions').append(buttons);
}

Clients.prototype.updateFormValues = function(elem, update) {
//alert(update);

$(elem).find('input, textarea, select, radio').each(function(){//function to update form values 
	var name = $(this).attr('name');
	for (var h in update) {//iterate through saved values
		if(h == name) {//match field names with saved names
			var type = $(this).attr('type');
			if(type == 'text')			$(this).val(update[h]);//update inputs with type 'text'
			else if (type == 'radio') {//update inputs with type 'radio'
				var val = $(this).attr('value');
				if(val == update[h])	$(this).prop('checked',true);
			}//update textareas
			else 						$(this).val(update[h]);
		}
	}
});

}

Clients.prototype.updateUsers = function(det) {
self = this;
var elems = {};
var err = {"ok":true};

$('#profileform2').find(':input:disabled').removeAttr('disabled');
var newUser = $('#profileform').serializeObject();
var newUser2 = $('#profileform2').serializeObject();
mix(newUser2, newUser);//merging the two form objects. For some reason if a form spans across two table columns it is not possible to serialize the form all at once

for(var a in newUser) {
	if(newUser[a] == '') {
		elems[a] = {"tag":"#"+a,"message":"Please fill in this section!"}
		err = {"ok":false,"elem":elems};
	}
}

var n = newUser.name;
var nn = n.split(' ');
newUser.firstname = nn[0];
newUser.lastname = nn[1];
delete newUser.name;

newUser.date = det.date;
newUser.id = det.id;
newUser.confirm = det.confirm;
newUser.overallprogress = det.overallprogress;
newUser.lastseen = det.lastseen;
if($('#authorize').is(':checked')) newUser.auth = 1;//if user has been authorized
else newUser.auth = 0;//if user has not been authorized!


var ns = new AssessmentForm();

if(err.ok) {//if there are no empty fields in the form
	var err = ns.formCheck('#profileform');
	var err2 = ns.formCheck('#profileform2');
	if(err.ok && err2.ok) {//if there are no incorrectly filled fields in the form 
		if(det.edit) {
			$('#rightDiv').empty();
			var nt = new Templates();
			var hdr = nt.loadTemplate("USERS");
			$('#rightDiv').append(hdr);
			//self.addNewClientRow();
			self.rowNameBuffer = [];
			self.users[det.key] = newUser;
			//getvalues(self.users[det.key]);
		}else	self.users.push(newUser);
		
		//getkeys(self.users, true);
	
		$('#newUser').remove();
		setTimeout(function() { 
			if(length(self.users) < self.listLimit) self.populateUserRows(self.users);
			else requestUserPage(1, self.listLimit);
		},500);
	}else {
		mix(err2.elem, err.elem);
		ns.error(err.elem);
	}
}else if( ! err.ok) {
	ns.error(err.elem);
}

return err.ok;
}


Clients.prototype.populateUserRows = function(users) {
self = this;

for(var a in users) {// populating the employee rows

	var i = $.inArray(a, self.rowNameBuffer);
	if( i == -1 ) {
		self.addNewUserRow();
		var user = '<div class="kpainfo">'+users[a].firstname+' '+users[a].lastname+'</div>';

		var nd = new Date(users[a].lastseen);//create date object using last seen string
		var ls = nd.toDateString();
		var time = nd.toTimeString();
		var pcs = time.split(' ');
		ls = ls + ' ' + pcs[0];//convert last seen date to preferred string

		var date = '<div class="kpainfo">'+ ls +'</div>';
		var companyid = '<div class="kpainfo">'+users[a].companyid+'</div>';
		var companyname = '<div class="kpainfo">'+users[a].companyname+'</div>';
		
		var temphref = $('.newuserrow #toforms').attr('href');
		temphref = temphref + '&see=' + users[a].id;
		$('.newuserrow #toforms').attr('href', temphref);//updating the forms button with the user id
		//alert(users[a].overallprogress);
		$( ".newuserrow #percentage" ).progressbar({ // displaying % progress on the progress bar
			value: +users[a].overallprogress
		});
		$( ".newuserrow #percentage-inner" ).text(users[a].overallprogress+'%'); // displaying them on the progress bar

		$('.newuserrow #iduser').append(users[a].id);
		$('.newuserrow #user').append(user);
		//$('.newuserrow #useravator img').attr('src', users[a].avator != "" ? users[a].avator : "files/avator.png");
		$('.newuserrow #useravator').css({'background-image': 'url('+ (users[a].avator != "" ? users[a].avator : "files/avator.png") +')'});
		$('.newuserrow #date').append(date);
		$('.newuserrow #companyid').append(companyid);
		
		$('.newuserrow #companyid').addClass(users[a].companyid);
		if(users[a].notifications > 0) $('.newuserrow #notifyer').notification(users[a].notifications);
		
		$('.newuserrow').removeClass('newuserrow');
		self.rowNameBuffer.push(a);
	};
}
}

function searchForUsers(t, func) {

$('#loadimage').show();
$("#userslist #pagination_controls").hide();//just to make sure a fresh set of page options is loaded

$('#usersearch').ajaxSubmit({
	url: 'server.php',
	type: 'POST',
	success: function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok){
		var nc = new Clients();
		nc.sortedUsers = data.message;
		if(t) {
			$('.column-table').remove();
			nc.rowNameBuffer = [];

			if(length(nc.sortedUsers) < nc.listLimit) nc.populateUserRows(nc.sortedUsers);
			else requestUserPage(1, nc.listLimit);
		}
		if( typeof func == 'function' ) func(nc.sortedUsers);
		}else alert (data.message);
	$('#loadimage').hide();
	}
});
//return false;
event.preventDefault();
}

function requestUserPage(pn, rpp, s) {
var cs = new Clients();

if ( s ) cs.loadUsers(false, stuffToBeDone);
else if ( ! s ) searchForUsers(false, stuffToBeDone);

function stuffToBeDone(users) {


	u2d = split(users, ((pn - 1)*rpp), rpp);//users to display
	var total = length(users);
	
	$('#userRowContainer').empty();
	
	cs.rowNameBuffer = [];
	cs.populateUserRows(u2d);

	var back = forward = center = "";
	var pages = Math.ceil(total/rpp);
	// Change the pagination controls
	// Only if there is more than 1 page worth of results give the user pagination controls
	if(pages > 1){
		if (pn > 1) back = '<button class="pgcontrols back cursor" style="width:20px;" onclick="requestUserPage('+(pn-1)+', '+rpp+')"></button>';
		for( var i = 1; i <= pages; i++ ) {
			center += '<button id="pgbutton" class="pgcontrols '+( i == pn ? 'pgselected' : '' )+' cursor" onclick="requestUserPage('+(i)+', '+rpp+')">'+i+'</button>';
		}
		if (pn != pages) forward = '<button class="pgcontrols forward cursor" style="width:20px;" onclick="requestUserPage('+(pn+1)+', '+rpp+')"></button>';
		$("#userslist #pagination_controls").show();
		$("#userslist #pagination_controls #back").html(back);
		$("#userslist #pagination_controls #center").html(center);
		$("#userslist #pagination_controls #forward").html(forward);

	}else $("#userslist #pagination_controls").hide();
}

}

function viewUser(elem) {

$(elem).parents('#userrow').addClass('usertoedit');
var t = $('.usertoedit').find('#user #iduser').text();

var nt = new Templates();
var html = nt.loadTemplate("VIEW_USER_POPUP");

dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('#go').css({"background":"grey"}).attr({"id":"disabledgo"});


var nc = new Clients();
$('#newClient #loadimage').show();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'employees'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {//if save file found
		//getkeys(data.message, true);
		nc.users = data.message;
		$('#newClient #loadimage').hide();

		for(var c in nc.users) {//updating the pop up with the relevant information
			var name = nc.users[c].firstname+' '+nc.users[c].lastname;
			if(t == nc.users[c].id)	{
				det = {"key":c,"edit":true,"id":nc.users[c].id,"date":nc.users[c].date,"confirm":nc.users[c].confirm,"overallprogress":nc.users[c].overallprogress,
				"notifications":nc.users[c].notifications,"lastseen":nc.users[c].lastseen};
				$('#name').val(name);
				
				switch(nc.users[c].auth) {//switch statement regulates the initial user authorization notification
				case '1':
					$('#authorize').prop('checked', true);
					$('#isauthorized').css('background','rgb(35,85,165)').text('User Authorized');
					break;
				case '0': $('#isauthorized').css('background','#d41e24').text('User Not Authorized'); break;
				}
				
				switch(nc.users[c].confirm) {//switch statement regulates the email confirmation notification
				case '1': $('#emailconfirmed').text('Email Confirmed'); break;
				case '0': $('#emailconfirmed').css('background','#d41e24').text('Email Not Confirmed'); break;
				}
		
				nc.updateFormValues('form[name="profileform"]', nc.users[c]);
				nc.updateFormValues('form[name="profileform2"]', nc.users[c]);
				$('#email').prop('disabled', true);
				
				$('#currentprofilepicture').attr({"src":nc.users[c].avator != "" ? nc.users[c].avator : "files/avator.png"});
			}
		}
			
		$('#authorize').click(function() {//dealing with the authorization box
			if($('#authorize').is(':checked')) {// if authorization checkbox is not checked a click will check the box then fire off the event
				$('#isauthorized').css('background','rgb(35,85,165)').text('User Authorized');
			}else {// if authorization checkbox is checked a click will uncheck the box then fire off the event
				$('#isauthorized').css('background','#d41e24').text('User Not Authorized');
			}
		});	
			
		$('#close').click(function(){
			$('.usertoedit').removeClass('usertoedit');
			$('.formsubmitpopup').remove();
			clearBack();
		});

		$('#disabledgo').css({"background":"rgb(35,85,165)"}).attr({"id":"go"});
		
		$('#go').on('click', function() {
			$('.usertoedit').removeClass('usertoedit');
			var ret = nc.updateUsers(det);
			
			var dat = JSON.stringify(nc.users);
			//alert(dat);
			$("#loadimage").hide();//hide loading image
			$.ajax({
				url: 'server.php',
				type: 'POST',
				data: {'action':'employees-save','data':dat}
			}).done(function(response) {
				//alert(response);
				var data = JSON.parse(response);
				//getkeys(data.message, true);
				if(data.ok) $('#loading').html('<i id="savemessage">'+data.message+'...</i>');	
				else if(data.redirect) location.href = 'index.php';
				else alert(data.message);
				
				setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
			});
			
			if(ret) clearBack();
		});
	}
});


}

function deleteUsers (elem) {

$(elem).parents('#userrow').addClass('usertoedit');
var t = $('.usertoedit').find('#user #iduser').text();

var nt = new Templates();
var html = nt.loadTemplate("FORM_SUBMIT_CONFIRM");

dimBack();
$('#rightDiv').append(html);
$('.formsubmitpopup').draggable({handle: '.box-header'});
$('.formsubmitpopup .box-content').html("Are you sure? This action cannot be reversed!<br/><div id='yes' class='button' style='width:50px;margin-top:15px;'>Yes</div>");

$('#close').click(function(){
	$('.usertoedit').removeClass('usertoedit');
	$('.formsubmitpopup').remove();
	clearBack();
});
		
$('#yes').click(function() {
$('.formsubmitpopup #loadimage').show();
var nc = new Clients();

$.ajax({
	url: 	'server.php',
	type: 	'POST',
	data:	{action: 'employees'}
})
.done(function(data) {
	//alert(data);
	data = JSON.parse(data);
	if(data.ok) {//if save file found
		//getkeys(data.message, true);
		nc.users = data.message;

		for(var c in nc.users) {
			var name = nc.users[c].firstname+' '+nc.users[c].lastname
			if(t == nc.users[c].id)	{
				$.ajax({
					url: 	'server.php',
					type: 	'POST',
					data:	{action: 'delete', table:'memberinfo', id:nc.users[c].id}
				}).done(function(response) {
					//alert(response);
					var resp = JSON.parse(response);
					if(resp.ok)  {
						for(var a in nc.users) if(t == nc.users[a].id) delete nc.users[a];//delete this user from the object
						
						$('#rightDiv').empty();
						var nt = new Templates();
						var hdr = nt.loadTemplate("USERS");
						$('#rightDiv').append(hdr);
						
						if(length(nc.users) < nc.listLimit) nc.populateUserRows(nc.users);
						else requestUserPage(1, nc.listLimit);
						
						$('.usertoedit').removeClass('usertoedit');
						$('.formsubmitpopup').remove();
						clearBack();
						
						$('#loading').html('<i id="savemessage">'+resp.message+'...</i>');
						setTimeout(function(){ $('#savemessage').fadeOut('slow'); }, 2000);
					}
				});
			}
		}

	}
});

});


}
