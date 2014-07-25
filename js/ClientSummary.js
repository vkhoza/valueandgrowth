function ClientSummary () {

Clients.call(this);
this.clientsRowNameBuffer = [];

}


ClientSummary.prototype = new Clients();

ClientSummary.prototype.constructor = ClientSummary;

ClientSummary.prototype.init = function() {
self = this;

self.loadClients(false, function() {

self.populateClientRows(self.clients);
self.loadUsers(false, function() {
	self.populateUserRows(self.users);
});
self.populateStats();

});

}

ClientSummary.prototype.UseraddNewRow = function() {
self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('SUMMARYUSERSROW');
$('#adminlatestusers .box-content').append(clientrow);
}

ClientSummary.prototype.populateUserRows = function(users) {
self = this;

for(var a in users) {// populating the employee rows

	var i = $.inArray(a, self.rowNameBuffer);
	if( i == -1 && length(self.rowNameBuffer) < 4 ) {
		self.UseraddNewRow();
		var user = '<div>'+users[a].firstname+' '+users[a].lastname+'</div>';
		var designation = '<div>'+users[a].designation+'</div>';

		var td = new Date();
		var nd = new Date(users[a].date);
		var secs = nd.getTime();
		var todaysecs = td.getTime();
		
		var minutes = 1000 * 60;
		var hours = minutes * 60;
		var days = hours * 24;
		var years = days * 365;

		var ago = Math.round(todaysecs/days) - Math.round(secs/days);
		//log(users[a].date);
		var message;
		if ( ago < 1) {
			ago = Math.round(todaysecs/hours) - Math.round(secs/hours);
			message = ' hours ago';
			if ( ago < 1) {
				ago = Math.round(todaysecs/minutes) - Math.round(secs/minutes);
				message = ' minutes ago';
				if ( ago < 1) {
					ago = Math.round(todaysecs/1000) - Math.round(secs/1000);
					message = ' seconds ago';
				}
			}
		}
		else if( ago == 1 ) message = ' day ago';
		else message = ' days ago';
		
		$('.newuserrow #date').append(ago+message);
		
		$('.newuserrow #useravator').css({'background-image': 'url('+ (users[a].avator != "" ? users[a].avator : "files/avator.png") +')'});
		$('.newuserrow #iduser').append(users[a].id);
		$('.newuserrow #user').append(user);
		$('.newuserrow #user').append(designation);
		$('.newuserrow #companyname').append(users[a].companyname);
		
		$('.newuserrow').removeClass('newuserrow');
		self.rowNameBuffer.push(a);
	};
}
}


Clients.prototype.ClientaddNewRow = function() {
self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('SUMMARYCLIENTSROW');
$('#adminlatestclients .box-content').append(clientrow);
}

ClientSummary.prototype.populateClientRows = function() {
self = this;
var clients = self.clients;
for(var a in clients) {

	var i = $.inArray(a, self.rowNameBuffer);
	if( i == -1 &&  length(self.clientsRowNameBuffer) < 3 ) {
		self.ClientaddNewRow();
		var companyname = '<div class="kpainfo">'+clients[a].companyname+'</div>';
		var industry = '<div class="kpainfo">'+clients[a].industry+'</div>';
		var companyaddress = '<div class="kpainfo">'+clients[a].companyaddress+'</div>';
		var companyid = '<div class="kpainfo">'+clients[a].companyid+'</div>';
		var date = '<div class="kpainfo">'+clients[a].date+'</div>';
		var staffcomplement = '<div class="kpainfo">'+clients[a].staffcomplement+'</div>';
		var registeredemployees = typeof clients[a].registeredemployees == 'undefined' ? 0 : clients[a].registeredemployees;
		var overallprogress = typeof clients[a].overallprogress == 'undefined' ? 0 : clients[a].overallprogress;
		
		var td = new Date();
		var nd = new Date(clients[a].date);
		var secs = nd.getTime();
		var todaysecs = td.getTime();

		var minutes = 1000 * 60;
		var hours = minutes * 60;
		var days = hours * 24;
		var years = days * 365;

		var ago = Math.round(todaysecs/days) - Math.round(secs/days);
		var message = '';
		if ( ago < 1) {
			ago = Math.round(todaysecs/hours) - Math.round(secs/hours);
			message = ' hours ago';
			if ( ago < 1) {
				ago = Math.round(todaysecs/minutes) - Math.round(secs/minutes);
				message = ' minutes ago';
				if ( ago < 1) {
					ago = Math.round(todaysecs/1000) - Math.round(secs/1000);
					message = ' seconds ago';
				}
			}
		}
		else if( ago == 1 ) message = ' day ago';
		else message = ' days ago';

		$('.newclientrow #image img').attr({"src":clients[a].avator != "" ? clients[a].avator : "files/companyavator.png"});
		$('.newclientrow #date').append( ago + message );
		
		$('.newclientrow #idcompany').append(clients[a].id);
		
		$('.newclientrow #company').append(companyname);
		$('.newclientrow #company').append(industry);
		$('.newclientrow #company').append(companyaddress);
		$('.newclientrow #companyid').append(companyid);
		
		//drawing the Pie Chart for the particular client
		//Get the context of the canvas element we want to select
		var ctx = $(".newclientrow #myChart").get(0).getContext("2d");
		
		//log(registeredemployees+' '+clients[a].staffcomplement);
		var data = [{ value: +registeredemployees, color:"#F7464A" },{ value : +clients[a].staffcomplement, color : "#E0E4CC" }];
		
		var myNewChart = new Chart(ctx).Pie(data);
		
		$('.newclientrow').removeClass('newclientrow');

		self.clientsRowNameBuffer.push(a);
	};
}
}

ClientSummary.prototype.populateStats = function() {

self = this;
var nt = new Templates();
var clientrow = nt.loadTemplate('SUMMARYSTATISTICS');
$('#adminstats .box-content').append(clientrow);

var tempts = ["company"];
var ts = JSON.stringify(tempts);

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action: 'statistics', t:ts},
	success: function(response) {
		//log(response);
		var info = JSON.parse(response);
		var results = info.message;
		var labels = [];
		var staffcomplement = [];
		
		for(var u in results) {
			var cn = results[u].companyname;
			var sc = +results[u].staffcomplement;
			labels.push (cn.length > 15 ? cn.substr(0, 15)+'...' : cn);
			staffcomplement.push (sc.length > 15 ? sc.substr(0, 15) : sc);
		}
		
		labels = labels.slice(0, 5);
		staffcomplement = staffcomplement.slice(0, 5);//just display details for 5 companies
		
		//log(JSON.stringify(labels)+JSON.stringify(staffcomplement));
		//drawing the Pie Chart for the particular client
		//Get the context of the canvas element we want to select
		var ctx = $("#adminstats #myChart").get(0).getContext("2d");
		//log(registeredemployees+' '+clients[a].staffcomplement);
		var data = {
			labels : labels,
			datasets : [
				{
					fillColor : "rgba(220,220,220,0.5)",
					strokeColor : "rgba(220,220,220,1)",
					pointColor : "rgba(220,220,220,1)",
					pointStrokeColor : "#fff",
					data : staffcomplement
				}]
		}
		
		var myNewChart = new Chart(ctx).Line(data);
	}
});
}