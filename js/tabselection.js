$(function() {

var formids = ["HPAP_FORM_ONE","HPAP_FORM_TWO","HPAP_FORM_THREE","ASSESSMENT_FORM_ONE","ASSESSMENT_FORM_TWO","ASSESSMENT_FORM_THREE"];
var tabs = ["assessment","hpap"];

var urlhash = location.hash;//get the part of the url after the hash  i.e. after #

if ( urlhash != "" ) {
	var dets = urlhash.split("#");
	var dets = dets[1].split("+");

	tab = dets[0];
	form = dets[1];

	var i = $.inArray(form, formids);
	var f = $.inArray(tab, tabs);

	if( i == -1 || f == -1 ) {
		tab = "assessment";
		form = "ASSESSMENT_FORM_ONE";
	}

	if(tab == 'hpap') header = "HPAP_FORM_HEADER";
	else if(tab == 'assessment') header = "ASSESSMENT_FORM_HEADER";

}else {

	tab = "assessment";
	form = "ASSESSMENT_FORM_ONE";
	header = "ASSESSMENT_FORM_HEADER";

}

});