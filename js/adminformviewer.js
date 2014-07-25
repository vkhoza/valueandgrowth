function adminFormViewer () {}

adminFormViewer.prototype.initiate = function(container) {
var self = this;

self.getAdminComments();
disable_buttons();

}

adminFormViewer.prototype.getAdminComments = function(elem) {
var self = this;

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

		$('#rightDiv').find('input, textarea, select, .fcontainer').not('[type="radio"]').each(function() {
			$(this).prop('disabled', true).css('background','white');
	
			var elemtag = $(this).attr('name');
			var item = $(this);

			if( item.length > 0 ) {//did we find anything?
				var form = item.parents('form').attr('name');
				var parent = item.parents('.fieldcontainer');
				var container = item.parents('#hpap_kpas');
				
				var rowid = 0;
				if( $(this).hasClass('fcontainer') ) {//fcontainer is used by hpap forms
					if( container.find('.id').length > 0 ) {
						rowid = container.find('.id').text();
						var value = elemtag+"-"+rowid+":"+form;
					}else	var value = elemtag+":"+form;//assessment forms will use this
					
					$(this).css('background', parent.css('background'));
				}else	var value = elemtag+":"+form;//assessment forms will use this

				
				if( $("#comment_"+elemtag+"_"+rowid).length === 0 ) item.before(html);
				if(data.ok) {
					for(var a in elem) {//cycle through all the comments 
						if( elemtag == elem[a].tag && elem[a].formkey == rowid) {//if theres a comment on this field
							$('#newcomment').parents('.commenttable').find('#commentorname').html("<div id='commentor'>"+elem[a].commentorname+":</div>");
							$('#newcomment').html(elem[a].comment);

							break;
						}else {//if theres no comment on this field
							$('#newcomment').html("Click to Add Comment");
						}
					}
				}else {//if theres no comment on any field
					$('#newcomment').html("Click to Add Comment");
				}
				pos = parent.offset().top;
				elemheight = $('#newcomment').parents('.commenttable').outerHeight();
				quarterheight = elemheight/4;

				$('#newcomment').parents('.commenttable').css({ "margin-top": - elemheight+"px" });

				parent.attr({"onmouseover":"$(this).find('.commenttable').show();","onmouseleave":"$(this).find('.commenttable').hide();"});

				$('#newcomment').attr({"id":"comment_"+elemtag+"_"+rowid+"", "content":value});
			}
		});
		$('[type="radio"]').prop('disabled', true);//radio buttons weren't dealt with above so we deal with them here
	}
});

}

adminFormViewer.prototype.saveComment = function(elem, func) {

var self = this;
$('#loadimage').show();
elem = JSON.stringify(elem);

var userid = $('html').find('#ai').attr('value');//this is quite vulnerable it would be good to find a better way of doing this

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action:'comments', field:elem, userid:(typeof userid != 'undefined') ? userid : ""},
	success: function(response) {
	//log(response);
	var data = JSON.parse(response);
	$('#loadimage').hide();

	if(data.ok) $('#savemessage').html(data.message+'...');	
	else if (data.redirect) location.href = 'index.php';
	else alert(data.message);

	setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);
	func();
	}
});

}

function commentor(self) {

$(self).addClass('toedit');

if( $('.toedit #newcomment').length === 0 ) {
	var oldcomment = $('.toedit').text(); 
	$('.toedit').empty(); 
	$('.toedit').html(
	"<textarea class='newcomment' name='newcomment' id='newcomment' style='font-size:12px;width:100%;border:hidden;background:rgb(255,250,140);font-family:tahoma;'></textarea>"
	);
}else {
	var oldcomment = $('.toedit #newcomment').val(); 
}

if(oldcomment != '' && oldcomment != 'Click to Add Comment') $('.toedit #newcomment').val(oldcomment);
else $('.toedit #newcomment').val('Click to Add Comment');

$('.toedit #newcomment').focus();
$('.toedit #newcomment').blur(function() {
	var val = $(this).val();
	if(val != ''  && val != 'Click to Add Comment') $('.toedit').text(val);
	else $('.toedit').text('Click to Add Comment');
	
	$('.toedit #newcomment').hide();
	$('.toedit').removeClass('toedit');
}); 

$('.toedit #newcomment').on('savecomment', function(){
	var dets = $(self).attr('content');
	var splitdets = dets.split(":");
	
	var id = splitdets[0];
	var form = splitdets[1];
	var comment = $(this).val();
	var elem = {"form":form,"field":id,"comment":comment};
	
	var afv = new adminFormViewer();
	afv.saveComment(elem, function() {
		var na = new AssessmentForm();
		na.init();
		na.tabChanger();
		$('.tabs').on('click', function(){ //this is necessary to reassign events after appending a new template
			na.init();//initialize form
		});	
	});
});

$('.toedit #newcomment').change(function() {
	$('.toedit #commentorname').hide('slow');
	$('.toedit #newcomment').trigger('savecomment');
});

}

function approve (elem) {
$('#aloadimage').show();
var tempform = $('#rightDiv').find('form').attr('name');
var tf = tempform.split('_');
var form = tf[0];

$.ajax({
url: 'server.php',
type: 'POST',
data: {action: 'approve', form: form},
success: function(response) {
	//log(response);
	var data = JSON.parse(response);
	$(elem).text(data.message);
	$('#aloadimage').hide();
}
});
}
