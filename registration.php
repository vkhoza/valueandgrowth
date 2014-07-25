<?php
require('config.php');
require('require/phpmailer/class.phpmailer.php');
//require_once 'require/swift/lib/swift_required.php';


if(isset($_POST['sent'])) {

$elements = array('name'=>'','idnumber'=>'','email'=>'','address'=>'','gender'=>'','company'=>'','phone'=>'','password'=>'','rpassword'=>'');
$error = array();

foreach($_POST as $f => $v) {
	switch ($f) {
case 'name' :
	if($v == '') $error[$f] = "Please fill this space in!";
	elseif( ! preg_match('/.+ .+/',$v)) $error[$f] = "Your name doesn't look quite right, try again!";
	elseif( ! preg_match('/[a-zA-Z]+[0-9_]*/',$v)) $error[$f] = "Your name doesn't look quite right, try adding letters!";
	break;
	
case 'idnumber' :
	if($v == '') $error[$f] = "Please fill this space in!";
	//elseif( ! preg_match('/[a-zA-Z]+[0-9_-]*/',$v)) $error[$f] = "Your id doesn't look quite right, try again!";
	break;
		
case 'designation' :
	if($v == '') $error[$f] = "Please fill this space in!";
	elseif( ! preg_match('/[a-zA-Z]+[0-9_]*/',$v)) $error[$f] = "Your designation doesn't look quite right, try adding letters!";
	break;
	
case 'companyid' :
	if($v == '') $error[$f] = "Please fill this space in!";
	elseif( ! preg_match('/[a-zA-Z]+[0-9_]*/',$v)) $error[$f] = "This doesn't look quite right, try adding letters!";
	$qr = mysql_query("SELECT*FROM company WHERE companyid = '$v'");
	if(mysql_num_rows($qr) == 0)	$error[$f] = "Company ID does not exists in our database, contact your company's administrator for assistance!";
	break;
	
case 'phone' :
	$PQ = mysql_query("SELECT*FROM memberinfo WHERE phone = '$v'");

	if($v == '') $error[$f] = "Please fill this space in!";
	
	elseif (strlen($v) != 10 ) $error[$f] = "This doesn't look right, check and try it again!";
	
	elseif( mysql_num_rows($PQ)>0)	$error[$f] = "Phone number already exists in our database, try another one!";
	
	break;
	
case 'email' :
	$EQ = mysql_query("SELECT*FROM memberinfo WHERE email = '$v'");

	if($v == '') $error[$f] = "Please fill this space in!";
	elseif( ! preg_match('/.+@.+\..+/',$v)) $error[$f] = "This doesn't look right, check and try it again!";
	elseif( mysql_num_rows($EQ)>0)	$error[$f] = "Email already exists in our database, try another one!";
	break;
	
case 'password' :
	if($v == '') $error[$f] = "Please fill this space in!";
	elseif( strlen($v)<8 ) $error[$f] = "Password must have at least 8 characters!";
	elseif( $v != $_POST['rpassword'])  $error[$f] = "The two passwords do not match, please try again!";
	break;
	

	}
}

//checking and handling the radio option
if( ! isset($_POST['gender'])) $error['gender'] = "Please choose an option here!";

//checking and handling picture upload
$pic_ext = array('gif','jpeg','jpg','GIF','JPEG','JPG');
if(isset($_FILES['avator']['name']) && $_FILES['avator']['name'] != ''){

$temp = explode('.', $_FILES['avator']['name']);
$temp = end($temp);
if( ! in_array($temp, $pic_ext) || $_FILES['avator']['size']>2050000){
	$error['avator'] = "The picture is either to big or is in the wrong format, try another one!";
}else{
//sorting out the picture
$folder = "data/avators/".session_value('token');
$avator = $folder.basename($_FILES['avator']['name']);
	if(move_uploaded_file( $_FILES['avator']['tmp_name'], $avator)){
		unset($error['avator']);
		//echo 'moved';
		$avator = image_minimize($avator);//minimize image
	}else	$error['avator'] = "We encountered a problem with this picture, please make sure the file isn't too big!";
}
}else $avator = '';


if(empty($error)) {

	// Store variables in memberdetails table in the selected database
	foreach($_POST as $q => $e) $_POST[$q] = mysql_real_escape_string($e);//cleaning up	
	foreach($_POST as $f => $r){${$f} = $r;}//create variables that have same names as form fields 
	
	//encrypting password
	$password = bin2hex ($password);
	
	//separating first and last name
	$name = trim($name);
	$pcs = explode(' ',$name);
	$firstname = $pcs[0];
	$lastname = $pcs[1];
	
	$start_date = date('D M d Y G:i:s');
	
	mysql_query ("INSERT INTO memberinfo (firstname,lastname,avator,idnumber,companyid,designation,address,gender,email,password,date,token,confirm,auth,phone)
	VALUES('$firstname','$lastname','$avator','$idnumber','$companyid','$designation','$address','$gender','$email','$password','$start_date','0','0','1','$phone')")
	or die(mysql_error());

	$q = mysql_query("SELECT*FROM memberinfo WHERE email = '$email'");
	$res = mysql_fetch_array($q);
	mysql_query("INSERT INTO sessions (email, data, date, name) VALUES ('$email', '{}', '', '')");

	define('SMTP_HOST','localhost');
	define('SMTP_PORT',25);
	define('SMTP_USERNAME','info@value-and-growth.com');
	define('SMTP_PASSWORD','fruitfuln3$$');
	define('SMTP_AUTH',true);

	$mail	= new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug	= 1;                  
	$mail->SMTPAuth	=	SMTP_AUTH;                
	$mail->Host	= SMTP_HOST;
	$mail->Port	= SMTP_PORT;
	$mail->Username	=  SMTP_USERNAME;
	$mail->Password	= SMTP_PASSWORD;
	$mail->SetFrom(SMTP_USERNAME,'ValueandGrowth');
	$mail->AddReplyTo(SMTP_USERNAME,"ValueandGrowth");
	$mail->Subject	= "Confirm User Registration | ValueandGrowth";
	$mail->AltBody	= "To view the message, please use an HTML compatible email viewer!";
	$mail->AddAddress($email);
	$mail->Send();

	$mail->Body = "
	<html lang='en'>
	<head>
		<title>Confirm User Registration | ValueandGrowth</title>
	</head>
	<body style='font-family:calibri;' align='center'>

	<div style='padding:50px;width:100%;background:rgb(240,240,240);'>
	<div style='color:rgb(47,56,130);width:70%;border:solid 1px rgb(210,210,210);
	border-bottom:solid 1px rgb(47,56,130);margin:auto auto;background:white;'>
		<table style='background:white;width:100%;height:70px;border-bottom:solid 3px rgb(35,85,165);border-spacing:0px;z-index:30;position:relative;'>
			<tr>
				<td class='headerRow icon' style='width:80%;'>
					<a href='http://www.value-and-growth.com'>
						<img src='http://www.value-and-growth.com/colbrad/files/colbrad-international-logo.png' style='margin-left:10px;height:48px;'/>
					</a>
				</td>
				<td class='headerRow' style='width:15%;vertical-align:bottom;'></td>
				<td style='width:5%'></td>
			</tr>
		</table>
		<br/>

		<div style='padding:10px;'>
		<p style='font-weight:bold;font-size:20px;'>Dear $name</p>
		<p>Thank you for registering. To activate your profile click on the button below:<br/><br/>
		<a href='http://www.value-and-growth.com/colbrad/confirm.php?email=$email' 
		style='padding:5px;background:rgb(240,240,240);border-style:hidden;width:75px;height:23px;color:#000088;font-size:19px;line-height:30%;
		border:solid 1px #000088;box-shadow: 0 0 2px grey;font-weight:bold;text-decoration:none;'>
			 Activate Your Profile
		</a><br/>
		<br/>
		If the button doesn't work click on the link below or copy and paste it to your browser:<br/>
		<a class='decorate' href='http://www.value-and-growth.com/colbrad/confirm.php?email=$email'>http://www.value-and-growth.com/colbrad/confirm.php?email=$email</a></p>

		<p><font size='2' style='font-style:italic;'>[if this message is spam, please click not spam]</font></p>
		Kind Regards,<br/>

		Value & Growth Transformation Team <br/>
		</div>
		<br/>
		<div id='emailfooter' style='padding:10px;font-weight:bold;height:60px;background:rgb(35,85,165);color:white;'>
			<div id='footer' style='text-indent:10px;'>
			Colbrad. Copyright &copy 2014. All rights reserved. v1.0 
			</div>
		</div>
	</div>
	</div>
	</body>
	</html>
	";

	$mail->Send();

	/*if(!$mail->Send()) {
		echo 'Mail error: '.$mail->ErrorInfo;
		return false;
	} else {
		echo 'Message sent!';
		return true;
	}*/
	
	echo '{"ok":true,"message":{"name":"'.$name.'","email":"'.$email.'"}}';
	exit();
}else{
	$err = Array();
	foreach($error as $a => $b) {
		$elems["tag"] = "#".$a;
		$elems["message"] = $b;
		$err[] = $elems;
	}
	$allerrors = json_encode($err);
	echo '{"ok":false,"message":'.$allerrors.'}';
	exit();

}
}

?>