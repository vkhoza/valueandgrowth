function log(item) {
	console.log(item);
}

function getkeys(obj) {
	ret = [];
	var keys = Object.keys(obj);
	for(var b in keys) {
		log(keys[b]);
	}
	return keys;
}

function length(obj) {
	var a = 0;
	for (var b in obj) {
		a++;
	}
	return a;
}

function getvalues (obj) {
	for(var a in obj) {
		log(obj[a]);
	}
}

function dimBack() {
	
	var a = $(document).height();
	$('.greyedoutbackdrop').height(a);
	$('.greyedoutbackdrop').slideDown();

}

function clearBack() {

	$('.greyedoutbackdrop').fadeOut();

}

function leftMenuUtilities (id, dm) {

if(dm)	{
	$('.container').find('.listItem').each(function() {
		if($(this).hasClass('selectedListItem')) {
			$(this).removeClass('selectedListItem').animate({'border-left':'hidden',background:'white'}, 5000);
		}
	});
	$(id).addClass('selectedListItem');
	var _id = $(id).attr('id');
	if($('.subListItem').hasClass(_id)) $('.subListItem').toggle();
}
	
	$('.listItem').click(function() {
		$('.container').find('.listItem').each(function() {
			if($(this).hasClass('selectedListItem')) {
				$(this).removeClass('selectedListItem').animate({'border-left':'hidden',background:'white'}, 5000);
			}
		});
		$(this).addClass('selectedListItem');
		var __id = $(this).attr('id');
		if($('.subListItem').hasClass(__id) && dm) $('.subListItem').toggle();
	});	
}

function mix(source, target) {
   for(var key in source) {
     if (source.hasOwnProperty(key)) {
        target[key] = source[key];
     }
   }
}

function split(source, beg, end) {
	var e = 0;
	if (typeof source == 'undefined') return false;
	if (typeof beg == 'undefined') var beg = 0;
	if (typeof end == 'undefined') var end = length(source);
	var type = typeof source;
	var n = type == "object" ? {} : [];
	end = end + beg;
	for(var key in source) {
		if (source.hasOwnProperty(key) && e >= beg && e < end ) {
			n[key] = source[key];
		}
		e++;
	}
	return n;
}

function disable_buttons() {
	$('#rightDiv').find('input, textarea, select').each( function() {
		$(this).prop('disabled', true).attr('id','disabled').css('background','white');
	});
	$('.button, .addobjective').each( function() {
		$(this).unbind('click');
		$(this).attr('onclick','');
		$(this).attr('id','disabled');
	});
	$('[type=\"radio\"]').prop('disabled', true);//radio buttons weren't dealt with above so we deal with them here
}

function assessmentApproved() {//little object that facilitates JS form approved functionality
	disable_buttons();
}

function hpapApproved() {//little object that facilitates JS form approved functionality
	disable_buttons();
}

jQuery.fn.extend({
	notification: function(num) {
		ret = true;
		var elem = $(this);
		var parent = elem.parent();
		
		elem.find('#notification').each(function() { ret = false; });
		if(ret) {
			var table = "<table id='notification'><tr><td></td></tr></table>";
			elem.append(table);
			var height = $('#notification').innerHeight();
			elem.find('#notification').css({ "display":"table", "background":"rgb(255,60,60);" });
			elem.css({ "margin-bottom": - height+"px" });
		}
		elem.find('td').html(num);

	},
	
	calender: function() {
		var nt = new Templates();
		var html = nt.loadTemplate('CALENDER');
		var elem = $(this);
		elem.append(html);
		
		function Calender() {
			this.currentmonth = ""; 
			this.thismonth = ""; 
			this.today = ""; 
		}
		
		Calender.prototype.theday = function (d, e) {
			var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
			var shortdays = ["Sun","Mon","Tue","Wed","Thur","Fri","Sat"];
			var choice = (e) ? days : shortdays;
			for(var a in choice) {
				if(a == d) {
					return choice[a];
				}
			}
		}		
		
		Calender.prototype.themonth = function (d, e) {
			var month = ["January","February","March","April","May","June","July","August","September","October","November","December"];
			var shortmonth = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
			var choice = (e) ? month : shortmonth;
			for(var a in choice) {
				if(a == d) {
					return choice[a];
				}
			}
		}
		
		//Month is 0 based
		Calender.prototype.daysInMonth = function (month,year) {
			return new Date(year, (month+1), 0).getDate();
		}
		
		Calender.prototype.createmonth = function (m) {
			self = this;
			var ad = new Date();//create new Date object. Dates and months are zero based
			ad.setMonth( typeof m == 'boolean' ? ad.getMonth() : m );//sets the month. This comes as input from the user as they scroll across months
			var month = ad.getMonth();//get the current month
			var year = ad.getFullYear();//get the current year
			//log(m+" "+month);
			
			$('#calenderheader').find('td').eq(1).empty().append(self.themonth(month)+' '+year);
		
			var b = 1;//sets the week to 1
			var c = self.daysInMonth(month,year);//gets the number of days in a particular month
			for(var a = 1; a <= 42; a++) {	//populate the calender table using var "a" as the initial date
			var monthchanged = self.thismonth;//use this to indicate when month has changed 
				if(a > c) return//if the date is more than the number of days in a month go to next month
				
				/*
				if(a > c) {//if the date is more than the number of days in a month go to next month
					var temp = a - c;
					var newmonth = month+1;
					monthchanged = newmonth;
					ad.setMonth(newmonth); 
					if(newmonth != ad.getMonth()) ad.setMonth(newmonth);//at times for some reason setmonth doesnt do its job properly so we double check
					if(newmonth == 12) return;
				}else temp = a;
				*/
				
				ad.setDate(a);// set the date starting with the 1st of the month. This determines the days and dates for the particular month
				var day = ad.getDay();
				var date = ad.getDate();
				var td = $('#calender').find('tr').eq(b).find('td').eq(day).empty().append(date);;//using week, day and date values to update. 
				
				//log(b+" "+day+" "+date+" "+month+" "+newmonth+" "+m);
				if(date == self.today && month == monthchanged) td.addClass('today');//highlighting today
				else if(td.hasClass('today')) td.removeClass('today');
				
				if(day == 6) b++;//when we reach saturday we move to the next week.
			}
		}
		
		var cl = new Calender();
		var d = new Date();
		cl.currentmonth = d.getMonth(); 
		cl.thismonth = d.getMonth(); 
		cl.today = d.getDate(); 
		
		cl.createmonth(false);//onload create an initial instance 
		$('#calenderheader #leftarrow').click(function() {
			$('#calender').find('tr').not('#days').find('td').empty()
			cl.currentmonth --; //set value to previous month
			cl.createmonth(cl.currentmonth);//feed previous month into function
		});
		
		$('#calenderheader #rightarrow').click(function() {
			$('#calender').find('tr').not('#days').find('td').empty()
			cl.currentmonth ++; //set value to the following month
			cl.createmonth(cl.currentmonth);//feed following month into function
		});
		
	},
	
	move: function(off) {
		
		var elem = $(this);
		hasChanged = false;
		
		var ep = {//element properties go in here
			elem : elem,
			oldPosition : elem.css('position'),
			oldTop : elem.css('top'),
			height : elem.height(),
			width : elem.width(),
			elemtop : elem.offset().top,
			id : elem.attr('id'),
			off : off
		};
		
		if(typeof _me == 'undefined') _me = [];
		
		_me[ep.id] = ep;//add element properties to a moved elements object

		$(window).unbind('scroll');
		$(window).scroll(function() {
		var windowtop = $(window).scrollTop();

		for(var a in _me) {//onscroll cycle through moved elements object and apply necessary changes as per each elements details
			//log(a);
			if((windowtop + _me[a].off) > _me[a].elemtop) {
				_me[a].elem.css({"position":"fixed","top":off,"height":_me[a].height,"width":_me[a].width});
				hasChanged = true;
			}else if(hasChanged) {
				_me[a].elem.css({"position":_me[a].oldPosition,"top":_me[a].oldTop,"height":_me[a].height,"width":_me[a].width});
			}
		}
		});
		
	}
});
