
<div id='__templates' type='LEFT_MENU'><!--
<div id='leftMenu' style='padding:2px;width:100%;'>

	<div class='container disable_select' style=''>
		
		<a href='index.php'>
			<div class='listItem selectedListItem' id='s'>	
				Dashboard		
			</div>
		</a>
		<a href='forms.php'>
			<div class='listItem' id='f'>	
				Forms	
				<img src='files/downarrow.png' align='right' style='margin-top:7px;height:10px;'/>
			</div>
		</a>
		
		<div class='listItem subListItem f' id='af'>	Assessment Form		</div>
		<div class='listItem subListItem f' id='hf'>	HPAP Form	</div>

	</div>
	<br/>
	<div class='container' style=''>
		<a href='support.php'>
			<div class='listItem' id='s'>Support</div>
		</a>
	</div>
	
</div>

--></div>

<div id='__templates' type='SIGNIN'><!--
<script src='js/passwordreset.js'></script>
<table class='userAcessForms'><tr>
<td style='vertical-align:middle;'>
	<br/><br/>
	<form id='signin' name='signin' class='signinform' action="signin.php" method="post">
		<div align='left'> 
			<div class='heading' style=''>User Sign In</div><br/>
			
			Email<br/>
			<input class='text form-text' type="text" id="email" name="email" 
			
			<?php if(isset($_GET['u'])) {echo " value='".htmlentities($_GET['u'])."'";} echo"/>";?>
			<br/>
			Password<br/>
			<input class='text form-text' type="password" id="password" name="password" /><br/>
			<?php 
			if(isset($_GET['bl'])) {
				if($_GET['bl']==2){
					error("password","Please enter your password");
				}elseif($_GET['bl']==3){
					error("password","User ID or password did not match, please try again!");
				}
			}				
			?>
			<input type="hidden" name='option' value="<?php echo $_GET['option']?>" />
			<input type="hidden" name='sent' value="signin" />
			<input type="submit" value="Go" /><br/>
		</div>
	</form><br/>
	Forgot your password? <a id='reset' class='decorate' href="#">Reset.</a><br/>
	Don't have an account? <a class='decorate' href="register.php">Register!</a><br/>
	<a class='decorate' href="admin.php">Admin Sign In.</a>
	<br/>
</td>
</tr></table>	
--></div>

<div id='__templates' type='RESET'><!--
<div id='pwreset' class='formsubmitpopup' style='left:450px;z-index:20;position:absolute;border-spacing:0px;min-width:400px;'>
	<div class='box-header' style=''>
		User Password Reset
		<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
		<img id='close' src='files/close.png' style='float:right;height:25px;'/>
	</div>
	<div class='box-content' style='text-align:center;padding:10px;'>
		<form name='passwordreset' id='passwordreset'>
			<input class='text form-text' id='email' name='email' type='text'>
			<div id='resetmessage' style='text-align:center;'>
			Please enter the email you used to Sign Up. When you click 'Go', an email containing a password reset link will be sent to that email address.
			Follow the link in that email to complete your password reset.
			</div>
			<button id='go' class='button' onclick='resetGo();' style='margin-top:10px;'>Go</button>
		</form>
	</div>
</div>
--></div>


<div id='__templates' type='REGISTRATION'><!--
<script>
$(function() {
$('#go').click(function(event) {
$('#loadimage').show();
event.preventDefault();//stops browser from doing its reload thing

var d1 = $('#register').serializeObject();
var d2 = $('#register2').serializeObject();
mix(d2, d1);//merging the two form objects. For some reason if a form spans across two table columns it is not possible to serialize the form all at once

$.ajax({
	url: 'registration.php',
	type: 'POST',
	data: d1,
	success: function(response) {
		//alert(response);
		var data = JSON.parse(response);
		if(data.ok) {
			window.location.href = "thankyou.php?sent&name="+data.message.name+"&email="+data.message.email;
		}else {
			var na = new AssessmentForm();
			na.error(data.message);
		};
		$('#loadimage').hide();
	}
});  //Ajax Submit form

});
});
</script>
<?php

if( ! empty($error) || ! isset($_POST['sent'])) {
echo"
	<br/>	<br/>
	<div class='userAcessForms' style='margin-left:120px;'>
	<table id='completeprofileform' class='signinform' style='width:800px;margin-left:0px;'><tr>
	<tr><td>
		<div class='heading' style=''>
			User Registration
			<img id='loadimage' src='files/loading.gif' style='height:15px;display:none;'/>
		</div>
	<br/></td><td></td></tr>
	<tr>
	<td style='width:49%;vertical-align:top;padding:7px;'>

	<form autocomplete='off' id='register' name='register' action='register.php' method='post' enctype='multipart/form-data'>
		";
		if(isset($error['avator'])) error("avator",$error['avator']);
		echo"		

			Full Name <font style='font-size:12px;font-style:italic;'>(e.g. John Kyle)</font><br/>
			<input class='text form-text' type='text' id='name' name='name' ";
			if(isset($error)&&isset($_POST['name'])){echo " value='".htmlentities($_POST['name'])."'";} echo"/>";
			if(isset($error['name'])) error("name",$error['name']);
			echo"
			<br/> 			
			
			Company's ID <font style='font-size:12px;font-style:italic;'>(If uncertain contact Administrator for assistance)</font><br/>
			<input class='text form-text' type='text' id='companyid' name='companyid' ";
			if(isset($error)&&isset($_POST['companyid'])){echo " value='".htmlentities($_POST['companyid'])."'";} echo"/>";
			if(isset($error['companyid'])) error("companyid",$error['companyid']);
			echo"
			<br/> 
			
			Gender<br/>
			<div id='choosegender'>
			Male: <input style='' class='' type='radio' "; 
			if(isset($_POST['gender'])) echo $_POST['gender'] == 'Male' ? "checked='checked'" : ""; 
			echo" value='Male' id='gender' name='gender'/> &nbsp&nbsp
			Female: <input style='' class='' type='radio' "; 
				if(isset($_POST['gender'])) echo $_POST['gender'] == 'Female' ? "checked='checked'" : ""; 
			echo" value='Female' id='gender' name='gender'/><br/>
			</div>";
			if(isset($error['gender'])) error("choosegender",$error['gender']);
			echo"<br/>";
			
			echo"
			Designation <br/>
			<input class='text form-text' type='text' id='designation' name='designation' ";
			if(isset($error)&&isset($_POST['designation'])){echo " value='".htmlentities($_POST['designation'])."'";} echo"/>";
			if(isset($error['designation'])) error("designation",$error['designation']);
			echo"
			<br/>

		</form>
	</td>
	<td width='1%' style='border-left:solid 1px rgb(210,210,210);'></td>
	<td style='width:50%;vertical-align:top;padding:7px;'>

	<form autocomplete='off' id='register2' name='register2' action='register.php' method='post' enctype='multipart/form-data'>";
		
		echo"
		Contact Number <font style='font-size:12px;font-style:italic;'>(e.g. 0772345678)</font><br/>
		<input class='text form-text' type='text' id='phone' name='phone' autocomplete='off'";
		if(isset($error)&&isset($_POST['phone'])){echo " value='".htmlentities($_POST['phone'])."'";} echo"/>";
		if(isset($error['phone'])) error("phone",$error['phone']);
		echo"
		<br/>
		
		Email<br/>
		<input class='text form-text' type='text' id='email' name='email'";
		if(isset($error)&&isset($_POST['email'])){echo " value='".htmlentities($_POST['email'])."'";} echo"/>";
			if(isset($error['email'])) error("email",$error['email']);
		echo"
		<br/>
		
		Password<br/>
		<input style='width:50%;' class='text form-text' type='password' id='password' name='password' />";
		if(isset($error['password'])) error("password",$error['password']);
		echo"<br/>
		
		Repeat Password<br/>
		<input style='width:50%;' class='text form-text' type='password' id='rpassword' name='rpassword' /><br/>
		<input type='hidden' name='action' value='register' />
		<input type='hidden' name='sent' value='register' />
		<input id='go' type='submit' value='Go' />
	</form>
</td>
</tr></table>
<br/>
Already have an account? <a class='decorate' href='index.php'>Sign In!</a>
</div>
<br/>	";
}
?>
--></div>

<div id='__templates' type='THANKYOU'><!--
<br/>
<div class='box-inner leftmenu' style='margin-left:250px;;width:400px;' align='center'>
	<div class='box-header'>Registration Confirmation</div>
	<div style='padding:10px;'>
		<p><b>Dear <?php echo $_GET['name']; ?></b></p>
		Thank you for registering.
		Your email has been saved as <b><?php echo $_GET['email']; ?></b> So from this point forward your email is your ID. 
		However you will not be able to sign in until you confirm ownership of your email address.<br/>
		An email has been sent to your email address so click on the link in that email to confirm ownership of this email address.
		<br/>
		<br/>
		In the meantime, please upload a portrait photo of yourself below:
		<form id='avatorupload' enctype='multipart/form-data'>
			<div id='imgcontainer' style='margin:10px;width:100px;min-height:100px;'>
				<div class='imagePreview' style=''><img id='currentprofilepicture' src='files/avator.png' style='height:100px;margin:0px;'/></div>
				<div id='triggerupload' class='cursor' style='border:solid 1px;height:20px;width:100%;background:grey;color:white;
				border-radius:4px;font-size:12px;line-height:170%;margin-left:-1px;'>
					Select Picture
				</div>
			</div>
			<input type='file' name='avator' id='avator' class='' style='display:none' onchange='previewImage(this,[100],2);'/>
			<input type='hidden' name='action' value='picture-update'/>
			<input type='hidden' name='_r'/>
		</form>
	</div>
</div>
--></div>


<div id='__templates' type='CONFIRM'><!--
		
<?php
echo "<br/><br/>
<div class='box-inner leftmenu' style='margin:auto auto;margin-left:250px;width:400px;' align='center'>
<div class='box-header'>Registration Confirmation</div>
<div style='padding:10px;'>
";
if(empty($err_msg)) {
	echo"
	<p><b>Dear $firstname $lastname</b></p>
	Thank you for registering.
	Your email account has now been confirmed.<br/> 
	You can now access your Dashboard through the 
	<a class='decorate' href='index.php'>Sign In</a> 
	portal and start interacting with the tools there. ";
}else{
	foreach($err_msg as $a=>$b) echo '<br/>'.$a.': '.$b;
}
echo"
<br/><br/><br/>
<a href='index.php' class='decorate'>Sign In</a> or <a href='register.php' class='decorate'>Register</a> to proceed!
<br/>
</div>
</div>
<br/><br/>";		

?>
--></div>


<div id='__templates' type='SUMMARY'><!--
<div id='Wrapper'>
<div class='rows'>

	<div id='summaryuser' class='box one-third'>
	<div class='box-inner' style='min-height:200px;'>
		<div class='box-header'><?php echo $name; ?></div>
		<div class='box-content'>
			<table>
			<tr>
				<td id='imgcontainer' style='width:40%;text-align:center'><div id='useravator' style='height:130px;width:130px;'></div></td>
				<td id='infocontainer' style='line-height:150%;vertical-align:top;width:60%;text-align:left;padding-left:5px;font-size:15px;'></td>
			</tr>
			</table>
		</div>
	</div>
	</div>
	<div id='summarynotifications' class='box one-third'>
	<div class='box-inner' style='min-height:200px;'>
		<div class='box-header'>Notifications</div>
		<div class='box-content'>
		</div>
		<div id='box-controls'>
			<table id='pagination_controls' style='display:none;'><tr><td id='back' title='Previous Page'></td><td id='forward' title='Next Page'></td></tr></table>
			<script>requestPage(1, 4);</script>
		</div>
	</div>
	</div>
	<div id='summarycalender' class='box one-third'>
	<div class='box-inner' style='height:200px;'>
		<div class='box-header'>Calender</div>
		<div class='box-content'>
		</div>
	</div>	
	</div>	

</div>

<div class='rows'>
	<div class='box two-thirds'>
	<div class='box-inner'>
		<div class='box-header'>
			Activity Summary
			<a href='forms.php'><img src='files/enter.png' class='cursor' style='height:25px;float:right;'/></a>
		</div>
		<div class='box-content'>
			<table id='summarylastseen' style='width:100%;font-size:15px;'>
			<tr>
				<td style='width:40%;'>Last Seen: </td>
				<td id='lastseen' style='font-weight:normal;font-style:italic;text-indent:15px;text-align:center;'></td> 
			</tr>
			<tr>
				<td style='width:40%;'>Form % Completion: </td>
				<td id='completion' style='font-weight:normal;'>
					<div id='percentage' style='height:20px;width:90%;margin:auto auto;'>
						<div id='percentage-inner' style='text-align:center;font-size:12px;float:left;width:100%;height:100%;line-height:150%;'></div>	
					</div>
				</td>
			</tr>
			</table>
		</div>
	</div>
	</div>
	<div id='summarycalender' class='box one-third' style='border:hidden;'></div>	
</div>

</div>
--></div>

<div id='__templates' type='NOTIFICATION'><!-- 
<a id='href' href=''>
<table id='dropnotifications' class='newnt' style='width:100%;border-spacing:0px;'>
	<tr><td></td></tr>
</table>
</a>
--></div>

<div id='__templates' type='CALENDER'><!-- 
<table id='calenderheader'>
	<tr>
		<td><img id='leftarrow' class='cursor' src='files/leftarrow.png' style='height:10px;'></td>
		<td></td>
		<td><img id='rightarrow' class='cursor' src='files/rightarrow.png' style='height:10px;'></td>
	</tr>
</table>
<table id='calender'>
	<tr id='days'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thur</td><td>Fri</td><td>Sat</td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
</table>
--></div>

<div id='__templates' type='QUICK_SUMMARY'><!--
<div name='QUICK_SUMMARY' id='leftMenu' style='padding:7px;padding-bottom:0px;'>
	<div class='container disable_select' style='font-size:16px;border-bottom:solid 1px rgb(210,210,210);'>
		
		<div class='' id=''>
			<b>Last Seen:</b><br/>
			<div id='lastseen' style='margin-bottom:15px;height:25px;font-size:16px;font-style:italic;'></div>
		</div>
		<div class='' id=''>
			<b>Session % Complete</b><br/>
			<div id='percentage' style='margin-bottom:10px;height:20px;width:90%;'>
				<div id='percentage-inner' style='text-align:center;font-size:12px;float:left;width:100%;height:100%;line-height:150%;'></div>
			</div>
		</div>

	</div>
</div>

--></div>

<div id='__templates' type='SUBORDINATEROW'><!--
<div id='newsubrow' class='newsubrow' style='height:60px;border-bottom:solid 1px rgb(240,240,240)'>
<v id='iduser' style='display:none;'></v>
<a id='toforms' href='forms.php?admin=<?php echo get_column(session_value('token'), 'token');?>'>
<table style='border-spacing:0px;width:100%;'>
<tr>
	<td style='vertical-align:middle;'><div id='useravator' style='height:40px;width:40px;'></div></td>
	<td style='width:80%;vertical-align:top;'>
	<table id='pic_details' class='tight-tables' style='height:50px;width:100%;'>
		<tr><td id='user' style='width:80%;vertical-align:top;'><div></div></td></tr>
		<tr>
		<td id='percentagecontainer' style=''>
			<div id='percentage' style='height:10px;width:90%;'>
				<div id='percentage-inner' style='text-align:center;font-size:7px;float:left;width:100%;height:100%;'></div>
			</div>
		</td>
		</tr>
		<tr><td id='date' style='font-style:italic;'>Seen: </td></tr>
	</table>	
	</td>
</tr>
</table>
</a>
</div>
--></div>

<div id='__templates' type='FORM_SUBMIT_CONFIRM'><!--
<div id='submitconfirm' class='formsubmitpopup' style='top:100px;z-index:20;position:absolute;border-spacing:0px;'>
	<div class='box-header' style=''>
		Submit Forms
		<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
		<img id='close' src='files/close.png' style='float:right;height:25px;'/>
	</div>
	<div class='box-content' style='text-align:center'></div>
</div>
--></div>

<div id='__templates' type='FORM_SUBMIT_ERROR'><!--
<table id='submiterrors' class='submiterrors' style=''>
	<tr><td style='width:30%;'>Form:</td><td id='form'></td></tr>
	<tr><td style='width:30%;'>Stage:</td><td id='stage'></td></tr>
	<tr><td style='width:30%;'>Field:</td><td id='field'></td></tr>
</table>
--></div>

<div id='__templates' type='ERROR'><!--
<table class='errortable' style='z-index:5;position:absolute;border-spacing:0px;'>
<tr>
	<td>
		<div id='arrow'></div>
</td>
</tr>
<tr>
	<td>
		<div class='error' id='error'></div>
	</td>
</tr>
</table>
--></div>

<div id='__templates' type='COMMENT'><!--
<table class='commenttable' style='border-spacing:0px;width:250px;display:none;z-index:5;position:absolute;border-spacing:0px;'>
<tr>
	<td style='width:100%;background:rgb(255,250,140);'>
	<table id='comment-content' class='tight-tables' style='border-spacing:0px;width:100%;height:100%;'>
	<tr style='height:10px;'><td id='commentorname'></td></tr>
	<tr>
		<td style='width:97%;'>
			<div class='comment error' id='newcomment' style='box-shadow:none;width:100%;height:50px;background: rgb(255,250,140);color:rgb(35,85,165);
			margin:0px;' <?php if( isset($admin) && $admin ) echo "onclick='commentor(this);'";?>></div>
		</td>
		<td style='vertical-align:middle;'>
			<div id='deletecomment' style='margin:auto;height:100%;text-align:center;'>
				<img id='commentclose' class='cursor' src='files/close.png' style='display:none;height:15px;' onclick='deleteComment(this);'/>
				<img id='loadimage' src='files/loading.gif' style='padding:0px;height:10px;display:none;'/>
			</div>
		</td>
	</tr>
	</table>
	</td>
</tr>	
<tr><td><div id='downarrow' ></div></td></tr>
</table>
--></div>

<div id='__templates' type='ASSESSMENT_FORM_HEADER'><!--

<div class='container' style='width:100%;'>
<div id='optionsmove' style='background:rgb(240,240,240);width:100%;'>
<?php 

if( isset($assessmentsignedoff) && $assessmentsignedoff ) {

echo"
<table id='formlocked' style='float:right;'>
	<tr>
	<td style='width:60%;'>This form has been approved and as a result, can no longer be edited!</td>
	</tr>
</table><br/>
";

}

view_user_notification(); //display View Mode notification

if( isset($_SESSION['view']) ) {//this facilitates the Approval functionality
		
$viewed = $_SESSION['view'];
$viewer = get_column(session_value('token'), 'id');

$vq = mysql_query("SELECT*FROM teams WHERE leaderid = '$viewer' AND memberid = '$viewed'");//checking for leader
$vr = mysql_fetch_assoc($vq);

$aq = mysql_query("SELECT*FROM memberinfo WHERE id = '$viewer' AND admin = '1'");//checking for admin
$ar = mysql_fetch_assoc($aq);

$pq = mysql_query("SELECT*FROM completeforms WHERE userid = '$viewed' AND name = 'ASSESSMENT'");//checking whether form has been approved
$pr = mysql_fetch_assoc($pq);

if( mysql_num_rows($pq) == 1 ) {//only when the assessment form has been submitted
	echo"
	<table id='formlocked' style='float:right;'>
	<tr>
	<td style='width:60%;'>This form has been submitted for approval!</td>
	<td style='text-align:right;'>";
		if( $pr['signedbyleader'] == 1 ) echo "Form approved by Leader";
		elseif ( $vr = mysql_num_rows($vq) == 1 ) {
		echo"
			<div id='leaderapprove' class='cursor' onclick='approve(this);' style='text-decoration:underline;padding-right:5px;'>
				Approve Form as Leader
			</div>";
		}
	echo"
	</td>
	<td style='text-align:right;'>";
		if( $pr['signedbyadmin'] == 1 ) echo "Form approved by Admin";
		elseif(mysql_num_rows($aq) == 1){
		echo"
			<div id='adminapprove' class='cursor' onclick='approve(this);' style='text-decoration:underline;padding-right:5px;'>
				Approve Form as Admin
			</div>";
		}
	echo"
	</td>
	<td><img id='aloadimage' src='files/loading.gif' style='height:13px;display:none;'/></td>
	</tr>
	</table><br/>
	";
}
}

?>
<table class='tabContainer disable_select' style='width:100%;margin:auto;border-spacing:5px;'>
<tr>
	<td id='ASSESSMENT_FORM_ONE' class='tabs selected' style='border-right:;width:10%;min-width:85px;'>Stage One</td>
	<td id='ASSESSMENT_FORM_TWO' class='tabs' style='border-right:;width:10%;min-width:85px;'>Stage Two</td>
	<td id='ASSESSMENT_FORM_THREE' class='tabs' style='border-right:;width:10%;min-width:95px;'>Stage Three</td>
	<td id='newstage' class='' style='width:3.5%;min-width:20px;'></td>
	<td id='' style='width:55%;'>
		<div id='loading' style='float:left;'></div>
		<div id='save' class='button' style='height:20px;float:right;padding:3px;'>Save</div> &nbsp
		<div id='submit' class='button' style='height:20px;float:right;padding:3px;margin-right:5px;'>Submit Form</div>
		<div id='export' class='button' style='float:right;padding:3px;margin-right:5px;'>Export Form</div>
	</td>
</tr>
</table>
</div>
</div>
<script>$(function() { $("#optionsmove").move(70); });</script>
<div id='formContainer' style=''></div>
--></div>

<div id='__templates' type='ASSESSMENT_FORM_ONE'><!--
<?php 

$qe = mysql_query("SELECT*FROM memberinfo WHERE id = '$userid'");
$re = mysql_fetch_array($qe);
$qc = mysql_query("SELECT*FROM company WHERE companyid = '".$re['companyid']."'");
$rc = mysql_fetch_array($qc);
?>
<form name='ASSESSMENT_FORM_ONE'  action = '' method='post'>
<style>.text{border:hidden;}</style>
<div id='formWrapper'>
	<table id='infotable'>
	<tr>
		<td class='left_side'>COMPANY NAME:</td>
		<td class='right_side'><?php echo $rc['companyname'];?></td>
		<td class='left_side'>INDUSTRY:</td>
		<td class='right_side'><?php echo $rc['industry'];?></td>
	</tr>

	<tr>
		<td class='left_side'>FULL NAME:</td><td class='right_side'><?php echo $re['firstname'];?></td>
		<td class='left_side'>SURNAME:</td><td class='right_side'><?php echo $re['lastname'];?></td>
	</tr>
	<tr>
		<td class='left_side'>DESIGNATION:</td>
		<td class='right_side' style='border-spacing:0px;'><?php echo $re['designation'];?></td>
		<td class='left_side'>CONTACT NUMBER:</td><td class='right_side'><?php echo $re['phone'];?></td>
	</tr>
	<tr>
		<td class='left_side'>EMAIL:</td><td class='right_side'><?php echo $re['email'];?></td>
		<td class='left_side'>GENDER:</td>
		<td class='right_side'><?php echo $re['gender'];?></td>
	</tr>
	</table>
	<br/>
	
	
	<table id='first_table'>
	<tr>
		<td class='left_side'>HIGHEST QUALIFICATION</td>
		<td class='right_side fieldcontainer' style='border-top:solid 1px;'>
			<input id='qualification' name='qualification' class='text'/>
		</td>
		<td class='left_side'>DURATION IN CURRENT POSITION</td>
		<td class='right_side' style='border-top:solid 1px;'>
			<table class='inner_table' style='padding:0px;height:100%;'>
				<tr>
					<td class='inner_column fieldcontainer' style='height:40px;width:35%;'>	
						<input id='years' style='font-size:20px;width:90%;border:hidden;' name='years' type='text'/>
					</td>
					<td class='inner_column' style='width:15%;border-right:solid 1px;'>YEARS</td>
					<td class='inner_column fieldcontainer' style='height:40px;'>
						<input id='months' style='font-size:20px;width:90%;border:hidden;' name='months' type='text'/>
					</td>
					<td class='inner_column'>MONTHS</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<table style='height:100px;border-spacing:0px;width:99%;'>
	<tr>
		<td class='right_side' style='padding-right:7px;text-align:right;border-right:hidden;
		border:hidden;width:20%;box-shadow:0 0 0 0;'>
			WHEN LAST DID YOU ATTEND AN EXTERNAL LEADERSHIP DEVELOPMENT PROGRAMME
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:38%; border-top:solid 1px;'>
			Name of Leadership Development Programme:<br/>
			<textarea class='text' id='external_programme' name='external_programme' style='height:70px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:25%; border-top:solid 1px;'>
			Institutions:
			<textarea class='text' id='external_institution' name='external_institution' style='height:70px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
		</td>
		<td class='right_side' style='vertical-align:top;padding:5px;width:17%; border-top:solid 1px;'>
			<div class='fieldcontainer' style='border-bottom:solid 1px;height:45px;width:100%;'>Date Attended:
				<input id='external_date' name='external_date' class='text' placeholder='Click here'/>
			</div>
			<div class='fieldcontainer' style='width:100%;height:45px;'>Duration (days):
				<input id='external_duration' name='external_duration' class='text'/>
			</div>
		</td>
	</tr>
</table><br/>
<table style='height:100px;border-spacing:0px;width:99%;'>
	<tr>
		<td class='right_side' style='padding-right:7px;text-align:right;border-right:hidden;
		border:hidden;width:20%;box-shadow:0 0 0 0;'>
			WHEN LAST DID YOU ATTEND AN INTERNAL LEADERSHIP DEVELOPMENT PROGRAMME
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:38%; border-top:solid 1px;'>
			Name of Leadership Development Programme:
			<textarea class='text' id='internal_programme' name='internal_programme' style='height:70px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:25%; border-top:solid 1px;'>
			Institutions:
			<textarea class='text' id='internal_institution' name='internal_institution' style='height:70px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
		</td>
		<td class='right_side' style='vertical-align:top;padding:5px;width:17%; border-top:solid 1px;'>
			<div class='fieldcontainer' style='border-bottom:solid 1px;height:45px;width:100%;'>Date Attended:
				<input id='internal_date' name='internal_date' class='text' placeholder='Click here'/>
			</div>
			<div class='fieldcontainer' style='width:100%;height:45px;'>Duration (days):
				<input id='internal_duration' name='internal_duration' class='text'/>
			</div>
		</td>
	</tr>
</table>
</div>
<br/>
<div style='width:100%;text-align:center;'>
</div>
</form>


--></div>

<div id='__templates' type='ASSESSMENT_FORM_TWO'><!-- 
<form name='ASSESSMENT_FORM_TWO'  action = '' method='post'>

<?php
echo"

<div id='formWrapper'>

<div style='text-align:center;width:100%;font-weight:bold;'>EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR - LEADER VALUE & GROWTH PERFORMANCE ASSESSMENT  </div>
<br/>
<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>PERSONAL PERFORMANCE FACTORS</td></tr>
</table>

<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >1.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q1'>
		ANALYSIS - Critical problem examination, leading to identification of components and their relationships;  developing solutions for critical issues
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
		echo"
		<td class='second_columns'><input name='q1' type='radio' value='$a'/></td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>	
<td class='first_second_columns' >2.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q2'>
		COOPERATION - Working effectively with others to achieve common goals - board of directors, volunteers, staff, other organisations and the community
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q2' type='radio' value='$a' /></td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >3.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q3'>
		CREATIVITY - Improvement of the organisation by exploring new ideas;  seeks additional knowledge, skills and advancement opportunities	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q3' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >4.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q4'>
		COMMUNICATION - Oral and written presentation of ideas, both within and outside the organisation, understands and follows established policies
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q4' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >5.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q5'>
	INITIATIVE - Self-confident, enthusiastic performance of responsibilities with a minimum of direction; tries new ideas, willing to experiment and take risks
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q5' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >6.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q6'>
	JUDGMENT - Formation of sound evaluations by careful study of available facts and options and minimising personal bias in decision making
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q6' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >7.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q7'>
	RELIABILITY AND EFFECTIVENESS - Consistently delivers results, dependable; instils confidence in others
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q7' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>
<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>PROFESSIONAL ATTRIBUTES</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >8.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q8'>
	ORGANISATIONAL UNDERSTANDING - Breadth of knowledge of the organisation's mission and objectives;  understanding of the fundamentals required for organisational 
	effectiveness
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q8' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >9.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q9'>
	PLANNING - Balanced development of long-term strategic objectives, annual budgeting and forecasting and staying ahead of day-to-day activities
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q9' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >10.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q10'>
	COMMUNITY IMAGE - Consistent positioning as a respected community leader; effective interface with business executive and other civic leaders and 
	development of the organisation's brand image
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q10' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >11.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q11'>
	FINANCIAL MANAGEMENT - Thorough grounding in asset management and financial responsibility;  prudent judgment on financial matters	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q11' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >12.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q12'>
	LEADERSHIP - Effectively motivates the action of others - staff, supervisors, managers and others;  focuses on the future of the organisation
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q12' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>
<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>SUPERVISORY PERFORMANCE</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >13.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q13'>
	ORGANISATION - Establishes staff goals, assigns priorities, details actions, creates time schedules and follows through to successfully 
	achieve goals and responsibilities for the work unit
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q13' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >14.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q14'>
	LEADERSHIP - Leads by example, impeccable behaviour and values
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q14' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >15.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q15'>
	TRAINING - Assists subordinates in developing and utilising knowledge and skills to complete assigned responsibilities
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q15' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >16.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q16'>
	COACHING/COUNSELING - Explains or demonstrates work techniques to subordinates and provides feedback for their performance	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q16' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >17.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q17'>
	EMPLOYEE DEVELOPMENT - Assesses the strengths and weaknesses of 
	subordinates and works out a programme of continuing developmental abilities so that the subordinate's job knowledge, skills and results can be increased
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q17' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >18.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q18'>
	ECONOMY - Effectively controls costs and manages resources to bring about effective utilisation of money technology, human resources and time
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q18' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >19.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q19'>
	FOCUS ON DIVERSITY - Implements positive actions to ensure compliance with policies and practices relative to employee diversity issues
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q19' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>
<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>LEADERSHIP:  AS EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR ...</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >20.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q20'>
	I have clearly communicated the organisation's vision to my team
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q20' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >21.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q21'>
	I have clearly communicated the basic purpose or mission of the organisation to my team	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q21' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >22.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q22'>
	I have attracted a high-performing senior management & supervisory team with the knowledge, skills, energy and passion to make the 
	mission and vision a reality	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q22' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >23.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q23'>
	I lead a planning process that establishes annual goals, strategies and action plans for the team that are consistent with the 
	vision and mission of the organisation
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q23' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >24.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q24'>
	I lead a High Performance Leadership process that ensures accountability 
	at all levels of the team and makes quarterly adjustments in goals and strategies as necessary
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q24' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >25.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q25'>
	I consistently makes decisions that enable the team to achieve the organisational  goals better
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q25' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >26.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q26'>
	I regularly demonstrates creativity in identifying new opportunities and solving issues that the team is facing	
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q26' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >27.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q27'>
	I communicate effectively with internal and external stakeholders to build support for the mission, vision, goals and direction of the organisation
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q27' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >28.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q28'>
	I hold quarterly performance review sessions to ensure that my team understand the 
	importance of executing priorities and of meeting performance expectations
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q28' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >29.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q29'>
	I vigorously promote a Customer Service Excellence Culture
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q29' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>

<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>MANAGEMENT:  AS EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR ...</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >30.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q30'>
	I have established an effective team structure, ensuring that there is focus on key functions necessary for the organisation to deliver on its mission
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q30' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >31.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q31'>
	I delegate effectively to members of my team";
	if(isset($error[31])) error("one",$error[31]);
	echo"
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q31' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >32.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q32'>
	I clearly articulates priorities and ensures focus and accountability around addressing priorities
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q32' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >33.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q33'>
	I solicit feedback from the organisation's stakeholders including employees at all levels as input to the direction and operation of the team
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q33' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >34.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q34'>
	I regularly delivers a consistent message to all team members regarding the vision, mission and priorities of the organisation
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q34' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >35.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q35'>
	I ensure the work of the team is supported by effective processes for planning, 
	communicating, measuring, governing, delivering quality and providing for a safe work environment
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q35' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >36.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q36'>
	I ensure there are clear policies established for how the team will operate
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q36' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >37.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q37'>
	I communicate quarterly business performance results and progress to the team
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q37' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>

<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>WORKING WITH THE CEO:  AS EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR ...</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >38.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q38'>
	I understand the organisation's requirement for governance practices and supports the 
	President/CEO/MD in its governance duties by providing necessary information and access to people
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q38' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >39.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q39'>
	I have a strong working relationship with the President/CEO/MD
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q39' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >40.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q40'>
	I have a strong working relationship with Board Directors
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q40' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >41.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q41'>
	I update the President/CEO/MD regularly on plans, performance, issues, priorities  and opportunities
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q41' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >42.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q42'>
	I ensure the President/CEO/MD receives information destined for outside stakeholders before it is communicated to them
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q42' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >43.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q43'>
	I help educate the President/CEO/MD on team activities
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q43' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >44.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q44'>
	I execute direction that is provided by the President/CEO/MD
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q44' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >45.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q45'>
	I help the President/CEO/MD identify the organisation's assets and to ensure that these assets are protected legally and physically from outside threats
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q45' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>

<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>FINANCIAL MANAGEMENT:  AS EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR ...</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >46.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q46'>
	I have a solid, up-to-date understanding of the organisation's sales figures, income statement, 
	balance sheet, cash flow and other financial measures relevant to its business and financial situation
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q46' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >47.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q47'>
	I am supported by a qualified and competent finance officer who has day to day accountability for managing and monitoring the team's finances
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q47' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >48.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q48'>
	I understand the concept of value creation and makes decisions on where to allocate resources based on maximising value to the team
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q48' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >49.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q49'>
	I ensure that the teams financial records are accurate and up to date
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q49' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >50.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q50'>
	I have met and or exceeded the business performance targets set for the last Financial Year
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q50' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>

<br/>

<table  class='second_tables' style='border-spacing:0px;'>
<tr><td class='heading'>CUSTOMER MANAGEMENT:  AS EXECUTIVE DIRECTOR/BUSINESS UNIT DIRECTOR...</td></tr>
</table>
<table class='second_tables'>
<tr id='bordertop' style='text-align:center;font-weight:bold;'>
	<td id='firstcolumn' class='first_second_columns' ></td>
	<td id='firstcolumn' class='first_second_columns'>
	</td>
	<td id='firstcolumn' class='second_columns'>NO 1
	</td>
	<td id='firstcolumn' class='second_columns'>2
	</td>
	<td id='firstcolumn' class='second_columns'>3
	</td>
	<td id='firstcolumn' class='second_columns'>4
	</td>
	<td id='firstcolumn' class='second_columns'>5
	</td>
	<td id='firstcolumn' class='second_columns'>6
	</td>
	<td id='firstcolumn' class='second_columns'>7
	</td>
	<td id='firstcolumn' class='second_columns'>8
	</td>
	<td id='firstcolumn' class='second_columns'>9
	</td>
	<td id='firstcolumn' class='second_columns'>YES 10
	</td>
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >51.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q51'>
	I am totally committed to giving customers the best possible service at all times
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q51' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >52.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q52'>
	I set high standards for performance on dimensions that matter to customers
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q52' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >53.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q53'>
	I evaluate the effectiveness of customer service on a regular basis
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q53' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >54.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q54'>
	I understand trends in the industry and ensure customers are presented with innovated solutions
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q54' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >55.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q55'>
	I deliver products and services on time, every time
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q55' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr class='fieldcontainer'>
<td class='first_second_columns' >56.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q56'>
	I perform regular assessments and review of the customer service strategy to ensure that customer service is a key performance area
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q56' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
<tr  class='even fieldcontainer'>
	<td class='first_second_columns' >57.</td>
	<td class='first_second_columns' style=''>
	<div class='fcontainer' name='q57'>
	I ensure that the team understand their role as champions of the customer service strategy
	</div>
	</td>";	
	for($a = 1; $a < 11; $a++) {
	echo"
	<td class='second_columns'><input name='q57' type='radio' value='$a' />
	</td>";
	}
echo"
</tr>
</table>

</div>
<br/>
<div style='width:100%;text-align:center;'>
</div>
<br/>

";
?>
</form> --></div>

<div id='__templates' type='ASSESSMENT_FORM_THREE'><!-- 
<form name='ASSESSMENT_FORM_THREE'  action = '' method='post'>
<style>
.insetheading{ font-weight:normal;font-size:17px; }
textarea[name] { font-size:20px; }
</style>
<div id='formWrapper'>

<table  class='third_tables' style='border-spacing:0px;'>
<tr><td class='heading insetheading'><b>ACCOMPLISHMENTS</b><br/>
List your major accomplishments over the past year as Executive Director/Business Unit Director, then identify the traits/skills you exhibited in making them happen
</td></tr>
</table>
<table class='third_tables'>
<tr style='text-align:center;font-weight:bold;'>
	<td class='third_columns'>ACCOMPLISHMENTS</td>
	<td class='third_columns'>TRAITS/SKILLS</td>
</tr>
<tr>
	<td class='third_columns fieldcontainer'>
		<textarea name='accomplishments' id='accomplishments' id='accomplishments' 
		style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
	<td class='third_columns fieldcontainer'>
		<textarea name='traits_skills' id='traits_skills' id='traits_skills' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
</tr>
</table>
<br/>

<table  class='third_tables' style='border-spacing:0px;'>
<tr><td class='heading insetheading'><b>GOALS</b><br/>
List your key goals for the past year and the status of achievement of each

</td></tr>
</table>
<table class='third_tables'>
<tr style='text-align:center;font-weight:bold;'>
	<td class='third_columns'>GOALS</td>
	<td class='third_columns'>STATUS</td>
</tr>
<tr>
	<td class='third_columns fieldcontainer'>
		<textarea name='goals' id='goals' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
	<td class='third_columns fieldcontainer'>
		<textarea name='status' id='status' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
</tr>
</table>
<br/>
 
<table  class='third_tables' style='border-spacing:0px;'>
<tr><td class='heading insetheading'><b>OPPORTUNITIES TO INCREASE PERFORMANCE</b><br/>
List the areas where you could improve personal performance and how those areas could be developed
</td></tr>
</table>
<table class='third_tables'>
<tr style='text-align:center;font-weight:bold;'>
	<td class='third_columns'>DEVELOPMENT OPPORTUNITIES</td>
	<td class='third_columns'>RESOURCE/PLAN</td>
</tr>
<tr>
	<td class='third_columns fieldcontainer'>
		<textarea name='dev_opportunities' id='dev_opportunities' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
	<td class='third_columns fieldcontainer'>
		<textarea name='dev_resource_plan' id='dev_resource_plan' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
</tr>
</table>
<br/>
 
<table  class='third_tables' style='border-spacing:0px;'>
<tr><td class='heading insetheading'>
<b>PERFORMANCE GOALS - UPCOMING YEAR</b><br/>
List your key goals for the team in the upcoming year and an outline of how each goal will be accomplish

</td></tr>
</table>
<table class='third_tables'>
<tr style='text-align:center;font-weight:bold;'>
	<td class='third_columns'>KEY GOAL</td>
	<td class='third_columns'>RESOURCE/PLAN</td>
</tr>
<tr>
	<td class='third_columns fieldcontainer'>
		<textarea name='key_goal' id='key_goal' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
	<td class='third_columns fieldcontainer'>
		<textarea name='resource_plan' id='resource_plan' style='height:150px;width:98%;border:hidden;border-top:solid 1px rgb(230,230,230);'></textarea>
	</td>
</tr>
</table>
<br/>
 
 
</div>
<br/>


<div style='width:100%;text-align:center;'>
</div>
<br/>
</form> --></div>

<div id='__templates' type='HPAP_FORM_HEADER'><!--

<div class='container' style='width:100%;'>
<div id='optionsmove' style='background:rgb(240,240,240);width:100%;'>
<?php 

if( isset($hpapsignedoff) && $hpapsignedoff ) {

echo"
<table id='formlocked' style='float:right;'>
	<tr>
	<td style='width:60%;'>This form has been approved and as a result, can no longer be edited!</td>
	</tr>
</table><br/>
";

}

view_user_notification(); //display View Mode notification

if( isset($_SESSION['view']) ) {//this facilitates the Approval functionality
		
$viewed = $_SESSION['view'];
$viewer = get_column(session_value('token'), 'id');

$vq = mysql_query("SELECT*FROM teams WHERE leaderid = '$viewer' AND memberid = '$viewed'");//checking for leader
$vr = mysql_fetch_assoc($vq);

$aq = mysql_query("SELECT*FROM memberinfo WHERE id = '$viewer' AND admin = '1'");//checking for admin
$ar = mysql_fetch_assoc($aq);

$pq = mysql_query("SELECT*FROM completeforms WHERE userid = '$viewed' AND name = 'HPAP'");//checking whether form has been approved
$pr = mysql_fetch_assoc($pq);

if( mysql_num_rows($pq) == 1 ) {//only when the assessment form has been submitted
	echo"
	<table id='formlocked' style='float:right;'>
	<tr>
	<td style='width:60%;'>This form has been submitted for approval!</td>
	<td style='text-align:right;'>";
		if( $pr['signedbyleader'] == 1 ) echo "Form approved by Leader";
		elseif ( $vr = mysql_num_rows($vq) == 1 ) {
		echo"
			<div id='leaderapprove' class='cursor' onclick='approve(this);' style='text-decoration:underline;padding-right:5px;'>
				Approve Form as Leader
			</div>";
		}
	echo"
	</td>
	<td style='text-align:right;'>";
		if( $pr['signedbyadmin'] == 1 ) echo "Form approved by Admin";
		elseif(mysql_num_rows($aq) == 1){
		echo"
			<div id='adminapprove' class='cursor' onclick='approve(this);' style='text-decoration:underline;padding-right:5px;'>
				Approve Form as Admin
			</div>";
		}
	echo"
	</td>
	<td><img id='aloadimage' src='files/loading.gif' style='height:13px;display:none;'/></td>
	</tr>
	</table><br/>
	";
}
}
?>
<table class='tabContainer disable_select' style='width:100%;margin:auto;border-spacing:6px;'>
	<td id='HPAP_FORM_ONE' class='tabs selected' style='width:10%;min-width:85px;'>Stage One</td>
	<td id='HPAP_FORM_TWO' class='tabs' style='width:10%;min-width:85px;'>Stage Two</td>
	<td id='HPAP_FORM_THREE' class='tabs' style='width:10%;min-width:95px;'>Stage Three</td>
	<td id='newstage' class='' style='width:3.5%;min-width:20px;'></td>
	<td id='' style='width:55%;height:30px;'>
		<div id='loading' style='float:left;'></div>
		<div id='submit' class='button' style='height:20px;float:right;padding:3px;margin-right:5px;'>Submit Form</div>
		<div id='export' class='button' style='display:none;float:right;padding:3px;margin-right:5px;'>Export Form</div>
	</td>
</table>
</div>
</div>
<script>$(function() { $("#optionsmove").move(70); });</script>
<div id='formContainer'></div>
--></div>

<div id='__templates' type='NEW_OBJECTIVE'><!-- 
<form name='NEW_OBJECTIVE'  style='' method='post'>
<style>.obj-text {font-size:16px;text-indent:0px;}</style>
<div id='newObjective'>
<div class='heading box-header'>
	Add New Objective &nbsp
	<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
	<img id='close' class='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	Key Performance Area<br/>
	<select id='kpaname' name='kpaname' class='text form-text obj-text' style='text-indent:5px;'>
		<option value='null'>-- Choose KPA --</option>
	</select>
	<br/>
	Goal or Objective<br/>
	<textarea rows='3' id='objective' name='objective' class='text form-text obj-text'></textarea><br/>
	Measurement - KPI's (How will this be measured?)<br/>
	<textarea rows='3' id='measurement' name='measurement' class='text form-text obj-text'></textarea><br/>
	Completion Date<br/>
	<input id='cd' name='cd' class='text form-text obj-text' style='width:50%;text-indent:5px;' placeholder='Date Here..'/><br/>
	<table>
		<tr>
			<td><div id='go' class='button go' style='width:50px;float:left;'>Go</div></td>
			<td><div id='kpaloading' style='float:left;'></div></td>
		</tr>
	</table>
</div>
</div>
</form>
--></div>

<div id='__templates' type='DEFINEKPAS'><!-- 
<form name='DEFINEKPAS'  style='' method='post'>
<style>.obj-text {font-size:17px;text-indent:5px;width:100%;}</style>
<div id='newObjective'>
<div class='heading box-header'>
	Define KPAs &nbsp
	<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
	<img id='close' class='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	My Key Performance Area 1<br/>
	<input id='kpa0' name='kpa0' class='text form-text obj-text' style=''/><br/>
	My Key Performance Area 2<br/>
	<input id='kpa1' name='kpa1' class='text form-text obj-text' style=''/><br/>
	My Key Performance Area 3<br/>
	<input id='kpa2' name='kpa2' class='text form-text obj-text' style=''/><br/>
	My Key Performance Area 4<br/>
	<input id='kpa3' name='kpa3' class='text form-text obj-text' style=''/><br/>
	My Key Performance Area 5<br/>
	<input id='kpa4' name='kpa4' class='text form-text obj-text' style=''/><br/>
	My Key Performance Area 6<br/>
	<input id='kpa5' name='kpa5' class='text form-text obj-text' style=''/><br/>
	My Job Title<br/>
	<input id='jobtitle' name='jobtitle' class='text form-text obj-text' value='<?php echo get_column($session, 'designation');?>'/><br/>
	My Job Purpose<br/>
	<textarea id='jobpurpose' name='jobpurpose' class='text form-text obj-text' style='width:99%;text-indent:5px;' placeholder='My Job Purpose'></textarea><br/>
	<table>
		<tr>
			<td><div id='go' class='button go' style='width:50px;float:left;'>Go</div></td>
			<td><div id='kpaloading' style='float:left;'><img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/></div></td>
		</tr>
	</table>
</div>
</div>
</form>
--></div>

<div id='__templates' type='HPAP_FORM_ONE'><!-- 

<form name='HPAP_FORM_ONE'  action = '' method='post'>
<style>.third_columns{ width:20%;border-left:solid 1px;}</style>
<div id='formWrapper' class='kpawrapper'>

<table style='width:100%;'>
<tr>
	<td style='width:30%;'><div id='definekpas' class='button' style='background:green;margin:auto;width:160px;' onclick='definekpas();'>Define KPAs</div></td>
	<td style='width:70%;'><div class='form-heading' style='width:50%;margin-left:0px;'>My High Performance Plan</div></td>
</tr>
</table>
<?php

if(isset($_SESSION['view'])) {

$q = mysql_query("SELECT*FROM kpas WHERE userid = '$userid'");
$r = mysql_fetch_assoc($q);

echo"
<style>
#infotable td { width:25%;vertical-align:top; } 
#infotable td:nth-child(odd) { font-weight:bold; } 
</style>
<table id='infotable'>
<tr>
	<td class='left_side'>My Job Title:</td>
	<td class='right_side'>$r[jobtitle]</td>
	<td class='left_side'>My Job Purpose:</td>
	<td class='right_side'>$r[jobpurpose]</td>
</tr>
<tr>
	<td class='left_side'>My Key Perfomance Area 1:</td>
	<td class='right_side'>$r[kpa0]</td>
	<td class='left_side'>My Key Perfomance Area 2:</td>
	<td class='right_side'>$r[kpa1]</td>
</tr>
<tr>
	<td class='left_side'>My Key Perfomance Area 3:</td><td class='right_side'>$r[kpa2]</td>
	<td class='left_side'>My Key Perfomance Area 4:</td><td class='right_side'>$r[kpa3]</td>
</tr>
<tr>
	<td class='left_side'>My Key Perfomance Area 5:</td>
	<td class='right_side' style='border-spacing:0px;'>$r[kpa4]</td>
	<td class='left_side'>My Key Perfomance Area 6:</td>
	<td class='right_side'>$r[kpa5]</td>
</tr>
</table><br/>";

}

?>
	
<table id='kpaheader' class='third_tables' style='border:solid 1px rgb(47,56,130);'>
	<tr style='font-weight:bold;'>
		<td class='columns kpainfo' style='width:20%'>My KPA</td>
		<td class='columns' style='width:80%;border-bottom:hidden;'>	
		<table id='' style='width:100%;border-spacing:0px;'>
		<tr>
			<td class='columns kpainfo' style='width:50%'>Goals and/or Objectives</td>
			<td class='columns kpainfo' style='width:25%'>Measurement - KPI's</td>
			<td class='columns kpainfo' style='width:15%'>Completion Date</td>
			<td class='columns kpainfo' style='width:10%'>Actions</td>
		</tr>
		</table>
		</td>
	</tr>
</table>
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td class='columns fieldcontainer' style='width:20%;'>
			<div id='newkpaname' name='newkpaname'><v id='idkpa' class='id' style='display:none;'></v></div>
		</td>
		<td id='newobjwrapper' class='columns' style='width:80%;border-bottom:hidden;'>
			<table id='objrow' style='width:100%;border-spacing:0px;'>
			<tr>
				<td class='columns fieldcontainer' style='width:50%;'><div id='objective' name='objective'></div></td>
				<td class='columns fieldcontainer' style='width:25%;'><div id='measurement' name='measurement'></div></td>
				<td class='columns fieldcontainer' style='width:15%;'><div id='completiondate' name='completiondate'></div></td>
				<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
					<div id='editbutton' class='button' onclick='edit(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</div>
</form>
--></div>

<div id='__templates' type='KPA_ROWS'><!-- 
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td class='columns fieldcontainer' style='width:20%;'>
			<div id='newkpaname' name='newkpaname' class=''><v id='idkpa' class='id' style='display:none;'></v></div>
		</td>
		<td id='newobjwrapper' class='columns' style='width:80%;border-bottom:hidden;'></td>
	</tr>
</table>
--></div>

<div id='__templates' type='OBJECTIVE_ROWS'><!-- 
<table id='objrow' style='width:100%;border-spacing:0px;'>
<tr>
	<td class='columns fieldcontainer' style='width:50%;'><div id='objective' name='objective' class=''></div></td>
	<td class='columns fieldcontainer' style='width:25%;'><div id='measurement' name='measurement' class=''></div></td>
	<td class='columns fieldcontainer' style='width:15%;'><div id='completiondate' name='completiondate' class=''></div></td>
	<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
		<div id='editbutton' class='button' onclick='edit(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
	</td>
</tr>
</table>
--></div>


<div id='__templates' type='HPAP_FORM_TWO'><!-- 
<form name='HPAP_FORM_TWO'  action = '' method='post'>
<div id='formWrapper'>
<div class='form-heading'>My Leader Value Project / Bold Play </div>
<table id='prjheader' class='third_tables' style='border:solid 1px rgb(47,56,130);'>
	<tr style='font-weight:bold;'>
		<td class='columns' style='width:100%;border-bottom:hidden;'>	
		<table id='' style='width:100%;border-spacing:0px;'>
		<tr>
			<td class='columns kpainfo' style='border-bottom:hidden;width:45%'>What Are You Planning To Change/Improve or Implement</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Measurement - KPI's</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Completion Date</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:10%'>Actions</td>
		</tr>
		</table>
		</td>
	</tr>
</table>
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td id='newprjwrapper' class='columns' style='width:100%;border-bottom:hidden;'>
			<table id='' style='width:100%;border-spacing:0px;'>
			<tr>
				<td class='columns fieldcontainer' style='width:45%;'>
					<div id='newproject' name='newproject' class='fcontainer'><v id='idprj' class='id' style='display:none;'></v></div>
				</td>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='prmeasurement' name='prmeasurement' class='fcontainer'></div></td>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='date' name='date' class='fcontainer'></div></td>
				<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
					<div id='editbutton' class='button' onclick='editProject(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</div>
</form>
--></div>

<div id='__templates' type='PRJ_ROWS'><!-- 
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td id='newprjwrapper' class='columns' style='width:100%;border-bottom:hidden;'></td>
	</tr>
</table>
--></div>

<div id='__templates' type='PROJECT_ROWS'><!-- 
<table id='' style='width:100%;border-spacing:0px;'>
<tr>
	<td class='columns fieldcontainer' style='width:45%;'>
		<div id='newproject' name='newproject' class='fcontainer'><v id='idprj' class='id' style='display:none;'></v></div>
	</td>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='prmeasurement' name='prmeasurement' class='fcontainer'></div></td>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='date' name='date' class='fcontainer'></div></td>
	<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
		<div id='editbutton' class='button' onclick='editProject(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
	</td>
</tr>
</table>
--></div>

<div id='__templates' type='NEW_PROJECT'><!-- 
<form name='NEW_PROJECT'  style='' method='post'>
<style>.obj-text {font-size:16px;text-indent:0px;}</style>
<div id='newObjective'>
<div class='heading box-header'>
	Add New Value Project / Bold Play &nbsp
	<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
	<img id='close' class='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	What Are You Planning To Change/Improve or Implement<br/>
	<textarea rows='3' id='projectname' name='projectname' class='text form-text obj-text'></textarea><br/>
	Measurement - KPI's (How will this be measured?)<br/>
	<textarea rows='3' id='measurement' name='measurement' class='text form-text obj-text'></textarea><br/>
	Completion Date<br/>
	<input id='cd' name='cd' class='text form-text obj-text' style='width:50%;text-indent:5px;' placeholder='Date Here..'/><br/>
	<table>
		<tr>
			<td><div id='go' class='button go' style='width:50px;float:left;'>Go</div></td>
			<td><div id='kpaloading' style='float:left;'></div></td>
		</tr>
	</table>
</div>
</div>
</form>
--></div>

<div id='__templates' type='HPAP_FORM_THREE'><!-- 
<form name='HPAP_FORM_THREE'  action = '' method='post'>
<div id='formWrapper'>
<div class='form-heading'>My Personal Development Plan </div>
<table id='prjheader' class='third_tables' style='border:solid 1px rgb(47,56,130);'>
	<tr style='font-weight:bold;'>
		<td class='columns' style='width:100%;border-bottom:hidden;'>	
		<table id='' style='width:100%;border-spacing:0px;'>
		<tr>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Through Education</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Through Experience</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Through Exposure</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:22.5%'>Completion Date</td>
			<td class='columns kpainfo' style='border-bottom:hidden;width:10%'>Actions</td>
		</tr>
		</table>
		</td>
	</tr>
</table>
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td id='newplanwrapper' class='columns' style='width:100%;border-bottom:hidden;'>
			<v id='pID' class='id' style='display:none;'></v>
			<table id='' style='width:100%;border-spacing:0px;'>
			<tr>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='education' name='education' class='fcontainer'></div></td>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='experience' name='experience' class='fcontainer'></div></td>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='exposure' name='exposure' class='fcontainer'></div></td>
				<td class='columns fieldcontainer' style='width:22.5%;'><div id='cdate' name='cdate' class='fcontainer'></div></td>
				<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
					<div id='editbutton' class='button' onclick='editPlan(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</div>
</form>
--></div>

<div id='__templates' type='PLAN_ROWS'><!-- 
<table id='hpap_kpas' class='column-table' style=''>
	<tr>
		<td id='newplanwrapper' class='columns' style='width:100%;border-bottom:hidden;'></td>
	</tr>
</table>
--></div>

<div id='__templates' type='PLAN_SECTION_ROWS'><!-- 
<v id='pID' class='id' style='display:none;'></v>
<table id='' style='width:100%;border-spacing:0px;'>
<tr>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='education' name='education' class='fcontainer'></div></td>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='experience' name='experience' class='fcontainer'></div></td>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='exposure' name='exposure' class='fcontainer'></div></td>
	<td class='columns fieldcontainer' style='width:22.5%;'><div id='cdate' name='cdate' class='fcontainer'></div></td>
	<td id='edit' class='columns' style='width:10%;border-right:hidden;'>
		<div id='editbutton' class='button' onclick='editPlan(this);' style='display:none;background:grey;width:60%;margin:auto auto;'>Edit</div>
	</td>
</tr>
</table>
--></div>

<div id='__templates' type='NEW_PLAN'><!-- 
<form name='NEW_PLAN'  style='' method='post'>
<style>.obj-text {font-size:16px;text-indent:0px;}</style>
<div id='newObjective'>
<div class='heading box-header'>
	Add New Personal Development Plan &nbsp
	<img id="loadimage" src="files/loading.gif" style='height:13px;display:none;'/>
	<img id='close' class='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	Through Education<br/>
	<textarea rows='3' id='education' name='education' class='text form-text obj-text'></textarea><br/>
	Through Experience<br/>
	<textarea rows='3' id='experience' name='experience' class='text form-text obj-text'></textarea><br/>
	Through Exposure<br/>
	<textarea rows='3' id='exposure' name='exposure' class='text form-text obj-text'></textarea><br/>
	Completion Date<br/>
	<input id='cd' name='cd' class='text form-text obj-text' style='width:50%;text-indent:5px;' placeholder='Date Here..'/><br/>
	<table>
		<tr>
			<td><div id='go' class='button go' style='width:50px;float:left;'>Go</div></td>
			<td><div id='planloading' style='float:left;'></div></td>
		</tr>
	</table>
</div>
</div>
</form>
--></div>

<div id='__templates' type='NOTIFICATION'><!-- 
<a id='href' href=''>
<table id='dropnotifications' class='newnt' style='width:100%;border-spacing:0px;'>
	<tr><td></td></tr>
</table>
</a>
--></div>

<div id='__templates' type='CALENDER'><!-- 
<table id='calenderheader'>
	<tr>
		<td><img id='leftarrow' class='cursor' src='files/leftarrow.png' style='height:10px;'></td>
		<td></td>
		<td><img id='rightarrow' class='cursor' src='files/rightarrow.png' style='height:10px;'></td>
	</tr>
</table>
<table id='calender'>
	<tr id='days'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thur</td><td>Fri</td><td>Sat</td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
</table>
--></div>


<div id='__templates' type='BIG_CALENDER'><!-- 
<div id='newWrapper'>
<div class='rows'>
	<div class='box box-shaded three-thirds'>
	<div id='clientslist' class='box-inner'>
		<div class='box-header'>Calender</div>
		<div class='box-content'>
			<div id='calendercontainer'>
			<table id='bigcalenderheader'>
				<tr>
					<td><img id='leftarrow' class='cursor' src='files/leftarrow.png' style='height:10px;'></td>
					<td></td>
					<td><img id='rightarrow' class='cursor' src='files/rightarrow.png' style='height:10px;'></td>
				</tr>
			</table>
			<table id='bigcalender'>
				<tr id='days'><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thur</td><td>Fri</td><td>Sat</td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			</table>
			</div>
		</div>
		<div id='box-controls' style='margin-left:15px;margin-left:15px;'>
			<table id='pagination_controls' style='display:none;'>
				<tr>
					<td id='back' style='width:25px;' title='Previous Page'></td>
					<td id='center' title='Page' style=''></td>
					<td id='forward' style='width:25px;' title='Next Page'></td>
				</tr>
			</table>
		</div>
		<br/>
	</div>
	</div>

</div>
</div>
--></div>

