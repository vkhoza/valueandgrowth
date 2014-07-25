<?php
date_default_timezone_set("Africa/Harare");

function connect(){
//	$db = mysql_connect("localhost", "vandg", "TJat789661") or die(mysql_error());
//	mysql_select_db("colbrad") or die(mysql_error());
	$db = mysql_connect("localhost", "ukhoza", "lmbo89") or die(mysql_error());
	mysql_select_db("colbrad") or die(mysql_error());
}

connect();

$q = mysql_query("SELECT * FROM memberinfo WHERE confirm = '1' AND auth = '1'");

$users = array();

// set user credentials in an array for easy access and use by other scripts
if(mysql_num_rows($q) > 0) {
	while($r = mysql_fetch_array($q)) {
		$users[$r['email']] = $r;
	}
}

//foreach($r as $a => $b) echo $a.'<br/>';

/*this is a dummy array for testing purposes in the absence of mysql
$users = Array(
	"session1" => Array(
		"password" => 1234,
		"email" => "vusakhoza@gmail.com"
	),
	"session2" => Array(
		"password" => 1234,
		"email" => "khozathu89@gmail.com"
	));
*/

//functions that aid functionality :)
session_start();

function counter() { 
	if( ! isset ($va)) $va = 0; 
	else $va++; 
	return $va; 
}

function session_value($name) {
	global $_SESSION;
	if( isset($_SESSION[$name]) and $_SESSION[$name] ) {
		return $_SESSION[$name];
	}else return false;
}

function get_settings ($userid) {
	$qr = mysql_query("SELECT*FROM settings WHERE userid = '$userid'");
	$rs = mysql_fetch_array($qr);
	if( mysql_num_rows($qr) == 0 )  $rs = Array("progress"=>0,"lastseen"=>0);
	return $rs;
}

function set_settings ($perc, $userid) {
	$lastseen = date('D M d Y G:i:s');
	mysql_query("UPDATE settings SET progress = '$perc', lastseen = '$lastseen' WHERE userid = '$userid'");
	return true;
}

function get_column($session, $column) {
	$qa = mysql_query("SELECT*FROM memberinfo WHERE token = '$session'");
	$ra = mysql_fetch_array($qa);
	
	if(mysql_num_rows($qa) == 1) return $ra[$column];
	else return false;
}

function getToken($length,$user){
	$token = $length;
	$token .= hash('md5',$user);
	return $token;
}

function get_stage($form, $b){
	$hpap = Array("hpapformone"=>"HPAP_FORM_ONE","hpapformtwo"=>"HPAP_FORM_TWO","hpapformthree"=>"HPAP_FORM_THREE");
	$assessment = Array("formone"=>"ASSESSMENT_FORM_ONE","formtwo"=>"ASSESSMENT_FORM_TWO","formthree"=>"ASSESSMENT_FORM_THREE");
	
	if( $b ) {
		if(array_key_exists($form, $assessment)) {
			$ret = Array("assessment", $assessment[$form]);
			return $ret;
		}elseif(array_key_exists($form, $hpap)) {
			$ret = Array("hpap", $hpap[$form]);
			return $ret;
		}
	}elseif ( ! $b ) {
		if(in_array($form, $assessment)) {
			$key = array_search($form, $assessment);
			$ret = Array("assessment", $key);
			return $ret;
		}elseif(in_array($form, $hpap)) {
			$key = array_search($form, $hpap);
			$ret = Array("hpap", $key);
			return $ret;
		}
	}
}

function get_proper_name($tag) {// fields are saved using code format, which is not legible to users.
// as such this function helps sanitize field names for display to users. Used most by notifications functionality
	$elements = array("company_name"=>"Company Name","industry"=>"Industry","firstname"=>"First Name","surname"=>"Surname","designation"=>"Designation",
	"number"=>"Contact Number","email"=>"Email Address","gender"=>"Gender","qualification"=>"Highest Qualification","years"=>"Years in current Position",
	"months"=>"Months in current Position","external_programme"=>"External Leadership Programme Attended",
	"external_institution"=>"Institution at which External Leadership Programme Attended","external_date"=>"Date when External Programme Attended",
	"external_duration"=>"Duration of External Programme","internal_programme"=>"Internal Leadership Programme Attended",
	"internal_institution"=>"Institution at which Internal Leadership Programme Attended","internal_date"=>"Date when Internal Programme Attended",
	"internal_duration"=>"Duration of External Programme",//formone
	"q1"=>"Question 1","q2"=>"Question 2","q3"=>"Question 3","q4"=>"Question 4","q5"=>"Question 5","q6"=>"Question 6","q7"=>"Question 7","q8"=>"Question 8",
	"q9"=>"Question 9","q10"=>"Question 10","q11"=>"Question 11","q12"=>"Question 12","q13"=>"Question 13","q14"=>"Question 14","q15"=>"Question 15","q16"=>"Question 16",
	"q17"=>"Question 17","q18"=>"Question 18","q19"=>"Question 19","q20"=>"Question 20","q21"=>"Question 21","q22"=>"Question 22","q23"=>"Question 23","q24"=>"Question 24",
	"q25"=>"Question 25","q26"=>"Question 26","q27"=>"Question 27","q28"=>"Question 28","q29"=>"Question 29","q30"=>"Question 30","q31"=>"Question 31","q32"=>"Question 32",
	"q33"=>"Question 33","q34"=>"Question 34","q35"=>"Question 35","q36"=>"Question 36","q37"=>"Question 37","q38"=>"Question 38","q39"=>"Question 39","q40"=>"Question 40",
	"q41"=>"Question 41","q42"=>"Question 42","q43"=>"Question 43","q44"=>"Question 44","q45"=>"Question 45","q46"=>"Question 46","q47"=>"Question 47","q48"=>"Question 48",
	"q49"=>"Question 49","q50"=>"Question 50","q51"=>"Question 51","q52"=>"Question 52","q53"=>"Question 53","q54"=>"Question 54","q55"=>"Question 55","q56"=>"Question 56",
	"q57"=>"Question 57",//formtwo
	"accomplishments"=>"Accomplishments","traits_skills"=>"Traits / Skills","goals"=>"Goals",
	"status"=>"Status of Goals","dev_opportunities"=>"Development Opportunities","dev_resource_plan"=>"Dev Resource / Plan","key_goal"=>"Key Goal",
	"resource_plan"=>"Key Goal, Resource / Plan",//formthree
	"kpaname"=>"My KPA","objective"=>"Goals and/or Objectives",//hpapformone
	"plan"=>"What Are You Planning To Change/Improve or Implement","measurement"=>"Measurement - KPI's (How will this be measured)","cd"=>"Completion Date",//hpapformtwo
	"education"=>"Through Education","experience"=>"Through Experience","exposure"=>"Through Exposure","cd"=>"Completion Date"//hpapformthree
	);
	
	if(array_key_exists( $tag, $elements)) return $elements[$tag];
	else return false;
}


function loginCheck(){
if(session_value('token')){
	connect();
	$session = session_value('token');
	$temp = $session;
	$token_query = mysql_query("SELECT*FROM memberinfo WHERE token = '$temp' AND confirm = '1'");
	$token_result = mysql_fetch_array($token_query);
	
	if (mysql_num_rows($token_query)==1){
		global $user;
		global $number;
		$user = $token_result['email'];
		$number = $token_result['phone'];
	}
}
}

function loginCheck_redirect(){

if(session_value('token')){
	connect();
	$session = session_value('token');
	$temp = $session;
	$token_query = mysql_query("SELECT*FROM memberinfo WHERE token = '$temp' AND confirm = '1'");
	$token_result = mysql_fetch_array($token_query);

	if (mysql_num_rows($token_query)==1){
		global $user;
		global $number;
		$user = $token_result['email'];
		$number = $token_result['phone'];
	}else{
	//	if(isset($_SESSION['js_ok'])){$_SESSION['js_ok']=0;unset($_SESSION['js_ok']);}
		$_SESSION['redirect'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		unset($_SESSION['token']);
		header ("location:index.php");
	}
	
}else{
//	if(isset($_SESSION['js_ok'])){$_SESSION['js_ok']=0;unset($_SESSION['js_ok']);}
	$_SESSION['redirect'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	unset($_SESSION['token']);
	header ("location:index.php");
}
}


function loginCheckAdmin_redirect()	{
//echo session_value('token');/*
if(session_value('token')){
	connect();
	$session = session_value('token');
	$temp = $session;
	$token_query = mysql_query("SELECT*FROM memberinfo WHERE token = '$temp' AND confirm = '1' AND admin = '1'");
	$token_result = mysql_fetch_array($token_query);

	if (mysql_num_rows($token_query)==1){
		global $user;
		global $number;
		$user = $token_result['email'];
		$number = $token_result['phone'];
	}else{
	//	if(isset($_SESSION['js_ok'])){$_SESSION['js_ok']=0;unset($_SESSION['js_ok']);}
		$_SESSION['redirect'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		unset($_SESSION['token']);
		//header ("Location:admin.php");
	}
	
}

}


function error($input_name,$error){
	echo"
	<script>
	$(function(){
		setTimeout(function(){
			$('div#error_$input_name').hide();
		},7000);
		
		$('#$input_name').focus(function(){
			$('div#error_$input_name').fadeIn('slow');	
			setTimeout(function(){
				$('div#error_$input_name').fadeOut('slow');			
			},4000);
		});		
		
	});
	
	</script>
	
	<table class='' style='z-index:5;position:absolute;border-spacing:0px;'>
	<tr>
		<td>
			<div id='arrow'></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class='error' id='error_$input_name'>$error</div>
		</td>
	</tr>
	</table>
	";
}

function image_minimize($image){
$arr = getimagesize($image);

$wd_d = $arr[0];
$ht_d = $arr[1];
$wd = $arr[0];
$ht = $arr[1];


$w = 880;
$h = 600;

$sr = $w/$h;
$ar = $wd/$ht;

if($wd>$w||$ht>$h){
	if($ar<1){//if portrait
		$wd = $h;
		$ht = $h/$ar;
		if($ht>$w){//the purpose of this section is to adjust the picture to the prefer height and width
			$R = $w/$ht;
			$ht = $w;
			$wd = $h*$R;
		}
	}elseif($ar>1){//if landscape
		$ht = $h;
		$wd = $h*$ar;
		if($wd>$w){//the purpose of this section is to adjust the picture to the prefer height and width
			$R = $w/$wd;
			$wd = $w;
			$ht = $h*$R;
		}
	}
}

$myImage = imagecreatefromjpeg($image);
$myImageCrop =  imagecreatetruecolor($wd, $ht);

// Fill the image
$b=imagecopyresampled($myImageCrop,$myImage,0,0,0,0 ,$wd, $ht, $wd_d, $ht_d);	

if(imagejpeg( $myImageCrop,$image )) return $image;
else return false;
}

function empty_submit_form() {
return '{"formone":{"qualification":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"years":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"months":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"external_programme":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"external_institution":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"external_date":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"external_duration":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"internal_programme":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"internal_institution":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"internal_date":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"internal_duration":{"value":"","comment":"","commentor":"","commenttime":"","seen":1}},"formtwo":{"q1":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q2":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q3":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q4":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q5":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q6":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q7":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q8":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q9":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q10":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q11":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q12":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q13":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q14":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q15":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q16":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q17":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q18":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q19":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q20":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q21":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q22":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q23":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q24":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q25":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q26":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q27":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q28":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q29":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q30":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q31":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q32":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q33":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q34":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q35":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q36":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q37":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q38":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q39":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q40":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q41":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q42":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q43":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q44":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q45":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q46":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q47":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q48":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q49":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q50":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q51":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q52":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q53":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q54":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q55":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q56":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"q57":{"value":"","comment":"","commentor":"","commenttime":"","seen":1}},"formthree":{"accomplishments":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"traits_skills":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"goals":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"status":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"dev_opportunities":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"dev_resource_plan":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"key_goal":{"value":"","comment":"","commentor":"","commenttime":"","seen":1},"resource_plan":{"value":"","comment":"","commentor":"","commenttime":"","seen":1}},"hpapformthree":{},"hpapformtwo":{},"hpapformone":{}}';
}

function view_user_notification () {

if( isset($_SESSION['view']) ) {

$q = mysql_query("SELECT*FROM memberinfo WHERE id = '$_SESSION[view]'");
$r = mysql_fetch_array($q);
$name = $r['firstname']." ".$r['lastname'];

echo"
<table id='view_user_notification'>
<tr>
<td style='width:80%;'>	
	You are currently in View Mode and you're viewing <u>$name</u>'s forms. <br/>
	Note that in this mode you will not be able to access some functions!
</td>
<td style='text-align:right;'><div class='cursor' style='text-decoration:underline;'><a href='index.php'>Leave View Mode</a></div></td>
<td></td>
</tr>
</table>
";

}

}



//since this is the first file called by most pages
//i set these variables so that they can be used by other scripts elsewhere. 
//these variables are specific to the user i.e. are dependant on the user session 
$userid = get_column(session_value('token'), 'id');
$session = session_value('token');
$myadmin = get_column(session_value('token'), 'admin');

?>