<?php
date_default_timezone_set("Africa/Harare");

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

function sql_protector($user,$input){
	$input = hash('md5',$input);
	$page = $_SERVER['REQUEST_URI'];
	setcookie('sql_protect',$user.'::'.$input.'::'.$page,time()+60*60*24*30);
}

function sql_protector_check($user,$input){
	
	if(isset($_COOKIE['sql_protect'])) $temp = $_COOKIE['sql_protect'];
	else $temp = ' :: :: ';
	
	$pcs = explode('::',$temp);
	$act_user = $pcs[0];
	$act_input = $pcs[1];
	$act_page = $pcs[2];
	
	$input = hash('md5',$input);
	$page = $_SERVER['REQUEST_URI'];
	if($act_page==$page){
		if($act_input==$input&&$act_user==$user){
			return false;
		}else return true;
	}else return true;
}

function err_msg($heading,$mess){
	echo"
	<div class='info_borders' style='width:350px;margin-left:auto;margin-right:auto;'>
	<div class='heading' style='width:100%;'><b>&nbsp$heading</b></div>
		<div style='padding:10px;'>
			$mess
		</div>
	</div><br/>
	";
}

function jChecker(){

session_start();
if(!isset($_SESSION['js_ok'])||isset($_SESSION['js_ok'])&&$_SESSION['js_ok']==0){
echo"
<noscript>
<meta http-equiv='refresh' content='0; url=jchecker.php?ok=1&url=".$_SERVER['REQUEST_URI']."'>
</noscript>";
}
}

function drop_down_menu($rows=0,$names=array(),$add=array(),$div_id='-',$hover,$css=''){
//$rows=count($names);
echo"
<style>
a.drop:hover{
	text-decoration:underline;
}
</style>
<div id='$div_id' class='drop_down' style='border-color:rgb(47,56,130);display:none;$css'>";
	for($i=0;$i<=$rows;$i++){
		echo"<dd><a class='drop' href='$add[$i]'>$names[$i]</a></dd>";
	}
echo"
</div>
<script>
$(function(){
var hover_elem = '$hover';
var ref = 'div#$div_id';
var divTop = $(hover_elem).offset().top;
var divH = $(hover_elem).outerHeight();
var dropTop = (divH)+divTop;
$(window).scroll(function(){
	var wTop = $(window).scrollTop();
	//	$('input#searchtext').attr('value',dropTop+' '+divTop+' '+wTop+' '+divH);
	//if(wTop>0){
		var dropCss = dropTop - wTop;
		$(ref).css('top',dropCss+'px');
	//}
		if(wTop>divTop){
			var elemDiff = dropTop - divTop;
			$('div#drop').css('top',(elemDiff+40)+'px');
		}
});

$(hover_elem).hover(function(){
	$(ref).slideDown();
},function(){
	$(ref).stop(true,true);
	$(ref).slideUp(700);
});

});
</script>
";
}


function tracker($id,$type,$item,$concat){
	$mq = mysql_query("SELECT*FROM item_watch WHERE type = '$type' AND item_id = '$id' AND del = '0'");
	if(mysql_num_rows($mq)>0){
		$start_time = date('Y-m-d G:i:s');
		mysql_query("INSERT INTO tracker (item, type, item_id,item_date,dets) VALUES ('$item','$type','$id','$start_time','$concat')");
	}
}

function reciepter($conc_elems){
	$elem_pieces = explode('+',$conc_elems);
	$elems = array('buyer','seller','auction','bid_amount','auction_id','charge','tax');
	foreach($elem_pieces as $k => $v){
		if(!isset($k2))$k2=0;
		else $k2++;
		$keys = $elems[$k2];
		$insert[$keys] = $v;
	}
	
	$start_time = date('Y-m-d G:i:s');
	
	mysql_query("INSERT INTO reciepts (buyer,seller,auction,bid_amount,auction_id,charge,tax,buy_date)
	VALUES ('".$insert['buyer']."','".$insert['seller']."','".$insert['auction']."','".$insert['bid_amount']."','".$insert['auction_id']."',
	'".$insert['charge']."','".$insert['tax']."','$start_time')");
}

function get_ip_address() {
  // check for shared internet/ISP IP
  if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP']))
   return $_SERVER['HTTP_CLIENT_IP'];

  // check for IPs passing through proxies
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
   // check if multiple ips exist in var
    $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    foreach ($iplist as $ip) {
     if ($this->validate_ip($ip))
      return $ip;
    }
   }

  if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
   return $_SERVER['HTTP_X_FORWARDED'];
  if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
   return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
  if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
   return $_SERVER['HTTP_FORWARDED_FOR'];
  if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
   return $_SERVER['HTTP_FORWARDED'];

  // return unreliable ip since all else failed
  return $_SERVER['REMOTE_ADDR'];
 }

function validate_ip($ip) {
     if (filter_var($ip, FILTER_VALIDATE_IP, 
                         FILTER_FLAG_IPV4 | 
                         FILTER_FLAG_IPV6 |
                         FILTER_FLAG_NO_PRIV_RANGE | 
                         FILTER_FLAG_NO_RES_RANGE) === false)
         return false;
     self::$ip = $ip;
     return true;
 }


function event_logger($user,$exp,$type,$USER=''){
//	mysql_connect("mysql.2freehosting.com", "u550511168_dulas ", "lmbo89") or die(mysql_error());
//	mysql_select_db("u550511168_dulas") or die(mysql_error());
/*	mysql_connect("localhost", "ukhoza", "lmbo89") or die(mysql_error());
	mysql_select_db("logs") or die(mysql_error());
	if($USER!='') $use_USER = $USER;
	else $use_USER = '';
	$ip = get_ip_address();
	$page = $_SERVER['REQUEST_URI'];
	$date = date('Y-m-d G:i:s');
	if($type=='bid'){
		mysql_query("INSERT INTO bids_logs (bidder,seller,ipaddress,page,log_date,explanation) VALUES ('$user','$use_USER','$ip','$page','$date','$exp')");
	}
	if($type=='auction'){
		mysql_query("INSERT INTO auction_logs (seller,ipaddress,page,log_date,explanation) VALUES ('$user','$ip','$page','$date','$exp')");
	}
	connect();
	*/
}


function checkmobile(){
	include 'mobile detect/Mobile_Detect.php';
	$detect = new Mobile_Detect;
	
	if ($detect -> isMobile()) return true;
}

function is_pay4app_notif(){
    $apisecret = "B10R153A62J85062D99M6362727315KPEQ11D8T0553NH3YF4EA478LZ1I4AZ3IAM35P18KOIBT4LQKO";
    $merchant = "dulaxsa";

    if ( isset($_GET['merchant']) AND isset($_GET['checkout']) AND isset($_GET['order'])
          AND isset($_GET['amount']) AND isset($_GET['email']) AND isset($_GET['phone'])
          AND isset($_GET['timestamp']) AND isset($_GET['digest']) 
        ){ 


        //for readability the concatenation is split over two lines   
        $digest = $_GET['merchant'].$_GET['checkout'].$_GET['order'].$_GET['amount'];
        $digest .= $_GET['email'].$_GET['phone'].$_GET['timestamp'].$apisecret;

        $digesthash = hash("sha256", $digest);

        if ($_GET['digest'] !== $digesthash){

            return FALSE;

          }

      return TRUE;
    }
    else{
      
      return FALSE;

    }
}

function is_pay4app_transfer_pending_redirect(){
    $apisecret = "B10R153A62J85062D99M6362727315KPEQ11D8T0553NH3YF4EA478LZ1I4AZ3IAM35P18KOIBT4LQKO";
    $merchant = "dulaxsa";
  if ( isset($_GET['merchant']) AND isset($_GET['order']) AND isset($_GET['digest']) ){
    $expecteddigest = $_GET['merchant'].$_GET['order'].$apisecret;
    $expecteddigest = hash("sha256", $expecteddigest);
    if ($_GET['digest'] !== $expecteddigest){
      return FALSE;
    }
    return TRUE;
  }
  return FALSE;      
}

function click_notification($elem,$mess,$id){
	$w = strlen($mess)*7;
	if($w>200) $w=200;
	echo"
	<style>
	div.notif{
		color:black;
		border-top:solid 1px #000088;
		border-left:solid 1px #000088;
		background:white;
		position:fixed;
		border-top-left-radius:4px;
		padding-left:10px;
		padding-top:5px;	
		padding-bottom:8px;	
		padding-right:5px;
		z-index:5;
		font-size:13px;
//		box-shadow: 1px 1px 1px #888888;
		font-align:right;
		display:none;
		width:".$w."px;
	}
	</style>	
	<div id='$id' class='notif'>$mess</div>
	<script>
		$(function(){
			$('$elem').focus(function(){
				var y = $(this).offset().top;
				$('div#$id').css({
					'position':'absolute',
					'top':y+'px',
					'left':'25px'
				});;
				$('div.notif').hide();
				$('div#$id').fadeIn();
				setTimeout(function(){
					$('div#$id').fadeOut();
				},15000);
			});
		});
	</script>
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


function image_thumbnail($image){
$arr = getimagesize($image);

$wd_d = $arr[0];
$ht_d = $arr[1];
$wd = $arr[0];
$ht = $arr[1];

$w = 480;
$h = 400;

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
$pc = explode('/',$image);
$image = 'thumbs/'.$pc[1];

if(imagejpeg( $myImageCrop,$image )) return $image;
else return false;
}



?>