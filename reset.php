<?php
require('config.php');
require('require/phpmailer/class.phpmailer.php');
//require_once 'require/swift/lib/swift_required.php';
	
	// Store variables in memberdetails table in the selected database
	foreach($_POST as $q => $e) $_POST[$q] = mysql_real_escape_string($e);//cleaning up	
	foreach($_POST as $f => $r){${$f} = $r;}//create variables that have same names as form fields 
	
	$q = mysql_query("SELECT*FROM memberinfo WHERE email = '".$_POST['email']."'");
	$r = mysql_fetch_array($q);

	if( mysql_num_rows($q) == 0 ) {
		echo '{"ok":false,"message":"Email not found in database!"}';
		exit();
	}
	
	//encrypting password
	$reset = mt_rand(10000000, 99999999);
	mysql_query("UPDATE memberinfo SET reset = '$reset' WHERE email = '".$email."'");
	
	$oldnew = mt_rand(10000000, 99999999);
	$onpassword = bin2hex($oldnew);
	mysql_query("UPDATE memberinfo SET password = '$onpassword' WHERE email = '".$email."'");
	
	
	$firstname = $r['firstname'];
	$lastname = $r['lastname'];
	$name = $firstname." ".$lastname;
	
	$start_date = date('Y-m-d G:i:s');

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
	$mail->Subject	= "Password Reset Request | ValueandGrowth";
	$mail->AltBody	= "To view the message, please use an HTML compatible email viewer!";
	$mail->AddAddress($email);
	$mail->Send();

	$mail->Body = "
	<html lang='en'>
	<head>
		<title>Password Reset Request | ValueandGrowth</title>
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
		<p>
		A Password Reset Request was made using this email address. <br/>
		Your old Password has been replaced with this temporary one:<br/><b>$oldnew</b>.<br/>
		Copy and use this password to create a new password in your profile settings.<br/>
		Click on the button below to proceed:<br/><br/>
		
		<a href='http://www.value-and-growth.com/colbrad/show.php?email=".$email."&reset=$reset' 
		style='padding:5px;background:rgb(240,240,240);border-style:hidden;width:75px;height:23px;color:#000088;font-size:19px;line-height:30%;
		border:solid 1px #000088;box-shadow: 0 0 2px grey;font-weight:bold;text-decoration:none;'>
			 Reset Your Password
		</a><br/>
		<br/>
		If the button doesn't work click on the link below or copy and paste it to your browser:<br/>
		<a href='http://www.value-and-growth.com/colbrad/show.php?email=".$email."&reset=$reset'>http://www.value-and-growth.com/colbrad/show.php?email=".$email."&reset=$reset</a>
		</p>

		<p><font size='2' style='font-style:italic;'>[if this message is spam, please click not spam]</font></p>
		Kind Regards,<br/>

		Value & Growth Transformation Team <br/>
		</div>
		<br/>
		<div id='emailfooter' style='font-weight:bold;height:60px;background:rgb(35,85,165);color:white;'>
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
	
	echo '{"ok":true,"message":{"name":"'.$name.'","email":"'.$email.'"}}';
?>