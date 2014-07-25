function Templates(){
	this.body = "";
	this.scripts;
	this.templates ;
}

Templates.prototype.loadTemplate = function(type){
	var self = this;
	$('#Templates').children('#__templates').each(function(){
	
	var temp = $(this).attr("type");
//	alert(temp);
	if(type == temp)	{
		$(this).contents().filter(function(){
			if( this.nodeType == 8){
				
				self.body = this.nodeValue;
				//self.body.find('<script>');
				//alert("in");
			}else self.body = "TEMPLATE FORMAT ERROR";
		});
		//break;
	}

	});
	
	if(self.body == "") self.body = type+"_TEMPLATE NOT FOUND";
	
	//look for scripts within the template
	
	return self.body;
}

Templates.prototype.assignClicktoElem = function(elem, parent, template){
	var self = this;
	var html = this.loadTemplate(template);
	$(elem).click(function(){
		self.displayElems(parent, html);
	});
}

Templates.prototype.displayElems = function(parent, html){
	$(parent).empty();
	$(parent).append(html);
}
