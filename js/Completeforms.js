function CompleteForms() {
this.buffer = [];
};

CompleteForms.prototype.init = function() {
self = this;

self.tabChanger();

}

function comments (self) {

var id = $(self).parents('tr').attr('id');
var form = $(self).parents('tr').attr('name');

var oldcomment = $('#'+id+' #oldcomment').text(); 

if(oldcomment != '') $('#'+id+' #newcomment').val(oldcomment);
$('#'+id+' #oldcomment').empty(); 

$('#'+id).find('#newcomment').show();
$('#'+id+' #newcomment').focus();
$('#'+id+' #newcomment').blur(function() {
	var val = $(this).val();
	if(val != '') {
		$('#'+id+' #oldcomment').text(val);
	}
	
	$('#'+id+' #newcomment').hide();
}); 

$('#'+id+' #newcomment').on('savecomment', function(){
	var comment = $(this).val();
	var elem = {"form":form,"field":id,"comment":comment};
	var nc = new CompleteForms();
	//getvalues(elem);
	nc.saveComment(elem);
	
	$('#'+id+' #newcomment').on('keypress',function() {
		$(this).unbind('keypress');
		$('#'+id+' #newcomment').trigger('savecomment');
	});
});

$('#'+id+' #newcomment').change(function() {
	$('#'+id+' #newcomment').trigger('savecomment');
	$(this).unbind('keypress');
});

}

CompleteForms.prototype.saveComment = function(elem) {
self = this;
$('#loadimage').show();
elem = JSON.stringify(elem);

var userid = $('html').find('#ai').attr('value');

$.ajax({
	url: 'server.php',
	type: 'POST',
	data: {action:'comments', field:elem, userid:"", userid:(typeof userid != 'undefined') ? userid : ""}
}).done(function(response) {
	//alert(response);
	var data = JSON.parse(response);
	$('#loadimage').hide();

	if(data.ok) $('#savemessage').html(data.message+'...');	
	else alert(data.message);

	setTimeout(function() { $('#savemessage').fadeOut(1000); }, 1000);

});
}


CompleteForms.prototype.tabChanger = function() {
self = this;
$('.tabs').click(function(){
	//the following ids are actually the names of the forms to be loaded
	var bf = $('.selected').attr('id');//id for form to be buffered
	var cl = $(this).attr('class');
	var id = $(this).attr('id');//id for form to be displayed

	if(cl != 'tabs selected')	{
		var parent = $('#formWrapper').parent();
		var removedForm = $('#formWrapper').detach();
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
	
	$('.tabContainer').find('.tabs').each(function(){
		$('.tabs').attr('class', 'tabs');
	});
	$(this).attr('class', 'tabs selected');
	

});
}

