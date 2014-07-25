
<div id='__templates' type='ADMIN-SIGNIN'><!--
<script src='js/passwordreset.js'></script>
<table class='userAcessForms'><tr>
<td style='vertical-align:middle;'>
	<br/><br/>
	<form id='signin' name='signin' class='signinform' action="admin-signin.php" method="post">
		<div align='left'> 
			<div class='heading' style=''>Admin Sign In</div><br/>
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
					error("password","User ID or password did not match, please try again! Make sure you have Admin Sign In priviledges.");
				}
			}				
			?>
			<input type="hidden" name='option' value="<?php echo $_GET['option']?>" />
			<input type="hidden" name='sent' value="signin" />
			<input type="submit" value="Go" />
		</div>
	</form>
	<br/>
	Forgot your password? <a id='reset' class='decorate' href="#">Reset.</a><br/>
	<a class='decorate' href="index.php">User Sign In.</a>
	<br/>
</td>
</tr></table>	
--></div>

<div id='__templates' type='ADMIN_LEFT_MENU'><!--
<div id='leftMenu' style='padding:2px;'>

	<div class='container disable_select' style='border-bottom:solid 1px rgb(240,240,240);'>
		
		<a href='admin.php'>
			<div class='listItem selectedListItem' id='d'>	
				Dashboard		
			</div>
		</a>
		<a href='clients.php'>
			<div class='listItem' id='c'>	
				Clients	
				<img src='files/downarrow.png' align='right' style='margin-top:7px;height:10px;'/>
			</div>
		</a>
		
		<div class='listItem subListItem c' id='cr'>Company Details</div>
		<div class='listItem subListItem c' id='ur'>User Details</div>

	</div>
	<br/>
	<div class='container' style=''>
		<a href='support.php'>
			<div class='listItem' id='s'>Support</div>
		</a>
	</div>
</div>
--></div>

<div id='__templates' type='ADMIN-SUMMARY'><!--
<div id='Wrapper'>
<div class='rows'>

	<div id='adminlatestusers' class='box one-third'>
	<div class='box-inner' style='min-height:200px;'>
		<div class='box-header'>
			Latest Users
			<a href='clients.php#users'><img src='files/enter.png' class='cursor' style='height:25px;float:right;'/></a>
		</div>
		<div class='box-content' style='font-size:15px;'></div>
	</div>
	</div>
	<div id='adminstats' class='box one-third'>
	<div class='box-inner' style='min-height:200px;'>
		<div class='box-header'>Statistics</div>
		<div class='box-content'></div>
	</div>
	</div>
	<div id='adminlatestclients' class='box one-third'>
	<div class='box-inner' style='min-height:200px;'>
		<div class='box-header'>
			Latest Clients
			<a href='clients.php#clients'><img src='files/enter.png' class='cursor' style='height:25px;float:right;'/></a>
		</div>
		<div class='box-content'></div>
	</div>	
	</div>	

</div>
</div>
--></div>

<div id='__templates' type='SUMMARYUSERSROW'><!--
<div class='newuserrow' style='min-height:70px;border-bottom:solid 1px rgb(240,240,240)'>
<v id='iduser' style='display:none;'></v>
<table style='border-spacing:0px;width:100%;'>
<tr>
	<td style='vertical-align:middle;'><div id='useravator' style='height:80px;width:80px;'></div></td>
	<td style='width:72%;vertical-align:top;'>
	<table id='pic_details' style='height:50px;width:100%;'>
		<tr>
			<td id='user' style='width:80%;vertical-align:top;'></td>
		</tr>
	</table>
	<table id='company_lastseen' style='font-size:12px;height:20%;width:100%;'>
		<tr>
			<td id='companyname' style='width:60%;'></td>
			<td id='date' style='text-align:right;font-style:italic;'></td>
		</tr>
	</table>	
	</td>
</tr>
</table>
</div>
--></div>

<div id='__templates' type='SUMMARYCLIENTSROW'><!--
<div class='newclientrow' style='min-height:100px;border-bottom:solid 1px rgb(240,240,240)'>
<v id='iduser' style='display:none;'></v>
<table id='pic_details' style='height:80px;width:100%;font-size:15px;'>
	<tr>
		<td id='image' style='vertical-align:middle;'><img style='height:40px;min-wdith:35px;'></td>
		<td id='company' style='width:80%;vertical-align:top;'></td>
		<td id='regdisplay' style='width:80%;vertical-align:top;'>
			<canvas id="myChart" width="55" height="65"></canvas>
		</td>
	</tr>
</table>
<table id='director_lastseen' style='height:20px;font-size:12px;width:100%;'>
	<tr>
		<td id='companyid' style='width:30%;'></td>
		<td id='date' style='width:40%;font-style:italic;text-align:center;'></td>
		<td id='ur' style='text-align:right;font-style:italic;'><v style='font-size:10px;'>% Users Registered</v></td>
	</tr>
</table>
</div>
--></div>

<div id='__templates' type='SUMMARYSTATISTICS'><!--
<div class='newstatsrow' style='border-bottom:solid 1px rgb(240,240,240)'>
	<canvas id="myChart" width="300" height="250" style='margin:auto;'></canvas>
	<div style='text-align:center;font-size:11px;'>Clients Staff Complement</div>
</div>
--></div>

<div id='__templates' type='CLIENTS'><!--
<div id='newWrapper'>
<div class='rows'>
	<div class='box box-shaded three-thirds'>
	<div id='clientslist' class='box-inner'>
		<div class='box-header'>Company Details</div>
		<div id='companydets' class='box-content'>
			<table style='width:99%;'>
				<tr>
				<td style='width:25%;'><div id='addClient' class='button' style='width:160px;margin:10px;'>Add New Client</div></td>
				<td style='width:20%;' id='loading'><img id="loadimage" src="files/loading.gif" style='height:15px;display:none;'/></td>
				<td style='width:'><div id='save' class='button' style='display:none;float:right;width:40px;'>Save</div></td>
				</tr>
			<table class='third_tables' style='width:98%;font-weight:bold;text-indent:7px;'>
			<tr>
				<td class='columns companydets'>Company Name</td>
				<td class='columns companydets'>Date Registered</td>
				<td class='columns companydets'>Staff Complement</td>
				<td class='columns companydets'>Staff Registered</td>
				<td class='columns companydets'>Company Progress</td>
				<td class='columns companydets' style='width:20%;'>Actions</td>
			</tr>
			</table>
			<div id='clientRowContainer'></div>
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

<div id='__templates' type='NEW_CLIENT_ROW'><!--
<table id='clientrow' class='column-table newclientrow' style='width:98%;'>
<tr>
	<td class='columns companydets' id='company'><v id='idcompany' style='display:none;'></v></td>
	<td class='columns companydets' id='date'></td>
	<td class='columns companydets' id='complement'></td>
	<td class='columns companydets' id='registered'></td>
	<td class='columns companydets' id='progress'>
		<div id='percentage' style='height:20px;width:90%;margin:auto auto;'>
			<div id='percentage-inner' style='text-align:center;font-size:12px;float:left;width:100%;height:100%;line-height:150%;'></div>	
		</div>
	</td>
	<td class='columns companydets' id='actions' style='width:20%;'></td>
</tr>
</table>	
--></div>

<div id='__templates' type='NEW_CLIENT_POPUP'><!--
<div class='formsubmitpopup' id='newClient' style='top:100px;width:400px;'>
<div class='box-header'>Add New Client<img id='close' src='files/close.png' style='float:right;height:25px;'/></div>
<div class='box-content' style='padding:10px;'>
	<form name='newClient'>
	Company Name:<br/>
	<input class='text form-text' id='companyname' name='companyname'/><br/>
	Company Address:<br/>
	<textarea class='text form-text' id='companyaddress' name='companyaddress'></textarea><br/>
	Industry:<br/>
	<input class='text form-text' id='industry' name='industry'/><br/>
	Telephone:<br/>
	<input class='text form-text' id='phone' name='phone' style='width:65%;'/><br/>
	Fax:<br/>
	<input class='text form-text' id='fax' name='fax' style='width:65%;'/><br/>
	Email:<br/>
	<input class='text form-text' id='email' name='email'/><br/>
	Staff Complement:<br/>
	<input class='text form-text' id='staffcomplement' name='staffcomplement'/><br/>
	Company CEO's Name:<br/>
	<input class='text form-text' id='ceo' name='ceo'/><br/>
	<div id='go' class='button' style='width:50px;float:left;margin-bottom:10px;'>Go</div><br/>
	</form>
</div>
</div>
--></div>

<div id='__templates' type='CLIENT_BUTTONS'><!--
<table style='width:90%;'><tr>
<td><div class='button' onclick='view(this);'>View</div></td>
<td><div class='button' onclick='deleteItem(this)' style='background:#d41e24;'>Delete</div></td>
</tr></table>
--></div>

<div id='__templates' type='VIEW_CLIENT'><!--
<div class='formsubmitpopup' id='newClient' style='top:100px;width:400px;'>
<div class='box-header'>
	Add New Client &nbsp
	<img id="popuploadimage" src="files/loading.gif" style='height:15px;display:none;'/>
	<img id='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	<form name='newClient'>
	<v id='cid'>Company ID: </v><br/><br/>
	Company Name:<br/>
	<input class='text form-text' id='companyname' name='companyname'/><br/>
	Company Address:<br/>
	<textarea class='text form-text' id='companyaddress' name='companyaddress'></textarea><br/>
	Industry:<br/>
	<input class='text form-text' id='industry' name='industry'/><br/>
	Telephone:<br/>
	<input class='text form-text' id='phone' name='phone' style='width:65%;'/><br/>
	Fax:<br/>
	<input class='text form-text' id='fax' name='fax' style='width:65%;'/><br/>
	Email:<br/>
	<input class='text form-text' id='email' name='email'/><br/>
	Staff Complement:<br/>
	<input class='text form-text' id='staffcomplement' name='staffcomplement'/><br/>
	Company CEO's Name:<br/>
	<input class='text form-text' id='ceo' name='ceo'/><br/>
	<div id='go' class='button' style='width:50px;float:left;margin-bottom:10px;'>Go</div><br/>
	</form>
</div>
</div>
--></div>


<div id='__templates' type='USERS'><!--
<div id='newWrapper'>
<div class='rows'>
	<div class='box box-shaded three-thirds'>
	<div id='userslist' class='box-inner'>
		<div class='box-header'>User Details</div>
		<div class='box-content'>
			<form id='usersearch' name='usersearch' onsubmit='return false;'>
			<input type='hidden' name='action' value='employees'>
			<input type='hidden' name='search' value='_'>
			<table style='width:99%;'>
				<tr>
				<td style='width:30%;'>
					&nbsp Search Users: <input id='employee' name='employee' class='text form-text' style='width:55%;font-size:18px;''/>
				</td>
				<td style='width:25%;' id='companies'>
					<select name='companyid' id='companyid' class='form-text text' style='width:88%;font-size:18px;'>
						<option value='all'>--Company--</option>
						<?php
						$qs = mysql_query("SELECT*FROM company");
						while($rs = mysql_fetch_array($qs)) {
							echo"<option value='$rs[companyid]'>$rs[companyname] ($rs[companyid])</option>";
						}
						
						?>
					</select>
					&nbsp<img id='search' src='files/search.png' style='height:15px;' onclick='searchForUsers(true, false);'/>
				</td>
				<td style='width:20%;' id='loading'><img id="loadimage" src="files/loading.gif" style='height:15px;display:none;'/></td>
				<td style='width:'><div id='save' class='button' style='display:none;float:right;width:40px;'>Save</div></td>
				</tr>
			</table>
			</form>
			<table class='third_tables' style='width:98%;font-weight:bold;text-indent:7px;'>
			<tr>
				<td class='columns companydets' style='width:17%;'>User Name</td>
				<td class='columns companydets' style='width:17%;'>Last Seen</td>
				<td class='columns companydets' style='width:15%;'>Company ID</td>
				<td class='columns companydets' style='width:8%;'>Level</td>
				<td class='columns companydets' style='width:20%;'>% Progress</td>
				<td class='columns companydets' style='width:18%;'>Actions</td>
			</tr>
			</table>
		<div id='userRowContainer'></div>
		</div>
		<div id='box-controls' style='margin-left:15px;margin-left:15px;'>
			<table id='pagination_controls' style='display:none;'>
				<tr>
					<td id='back' style='width:25px;' title='Previous Page'></td>
					<td id='center' title='Page' style=''></td>
					<td id='forward' style='width:25px;' title='Next Page'></td>
				</tr>
			</table>
		</div><br/>
	</div>
	</div>

</div>
</div>
--></div>

<div id='__templates' type='NEW_USER_ROW'><!--
<table id='userrow' class='column-table newuserrow' style='width:98%;'>
<tr>
	<td class='columns userdets' style='width:17%;'>
		<table style='width:100%;margin-left:2px;padding:0px;border-spacing:0px;'>
		<tr>
			<td style='width:20%;'><div id='useravator' style='width:40px;height:40px;'></div></td>
			<td id='user'><v id='iduser' style='display:none;'></v></td>
		</tr>
		</table>
	</td>
	<td class='columns userdets' id='date' style='width:17%;'></td>
	<td class='columns userdets' id='companyid' style='width:15%;'></td>
	<td class='columns userdets' id='level' style='width:8%;'></td>
	<td class='columns userdets' id='progress' style='width:20%;'>
		<div id='percentage' style='height:20px;width:90%;margin:auto auto;'>
			<div id='percentage-inner' style='text-align:center;font-size:12px;float:left;width:100%;height:100%;line-height:150%;'></div>	
		</div>
	</td>
	<td class='columns companydets' id='actions' style='width:18%;'></td>
</tr>
</table>	
--></div>

<div id='__templates' type='USER_BUTTONS'><!--
<table style=''>
	<tr>
	<td><div class='button' onclick='viewUser(this);'>View</div></td>
	<td>
		<a id='toforms' href='forms.php?admin=<?php echo get_column(session_value('token'), 'token');?>' target='_blank'>
		<div id='notifyer' style='z-index:5;'></div>
		<div class='button' style='z-index:2;background:grey;'>Forms</div></a>
	</td>
	<td><div class='button' onclick='deleteUsers(this);' style='background:#d41e24;'>Delete</div></td>
	</tr>
</table>
--></div>

<div id='__templates' type='VIEW_USER_POPUP'><!--
<div class='formsubmitpopup' id='newClient' style='left:250px;top:100px;width:800px;'>
<div class='box-header'>
	User Details
	<img id="loadimage" src="files/loading.gif" style='height:15px;display:none;'/>
	<img id='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
	
<table id='completeprofileform' style='width:100%;'><tr>
<td style='width:49%;vertical-align:top;'>

	<div class='imagePreview'><img id='currentprofilepicture' src='' style='border-radius:5px;height:100px;'/></div>
	
	<form autocomplete='off' id='profileform' name='profileform' action='server.php' method='post' enctype='multipart/form-data'>

		Authorize User <br/><font style='font-size:12px;font-style:italic;'>(Allows user to start interacting with forms after email confirmation)</font><br/>
		<input type='checkbox' id='authorize' name='authorize'/> <v id='isauthorized'></v><br/> 

		Full Name <font style='font-size:12px;font-style:italic;'>(e.g. John Kyle)</font><br/>
		<input class='text form-text' type='text' id='name' name='name' />
		<br/> 			
		
		Company's ID <font style='font-size:12px;font-style:italic;'>(If uncertain contact Administrator for assistance)</font><br/>
		<input class='text form-text' type='text' id='companyid' name='companyid' />
		<br/> 
		
		Home Address<br/>
		<textarea class='text form-text' id='address' name='address' style='width:98%;'></textarea>
		<br/>
	</form>
</td>
<td width='1%' style='border-left:solid 1px rgb(210,210,210);'></td>
<td style='width:50%;vertical-align:top;'>

	<form autocomplete='off' id='profileform2' name='profileform2' action='server.php' method='post' enctype='multipart/form-data'>
		Gender<br/>
		<div id='choosegender'>
		Male: <input style='' class='' type='radio' value='Male' id='gender' name='gender'/> &nbsp&nbsp
		Female: <input style='' class='' type='radio' value='Female' id='gender' name='gender'/><br/>
		</div><br/>
		
		Designation <br/>
		<input class='text form-text' type='text' id='designation' name='designation' disabled/>

		Contact Number <font style='font-size:12px;font-style:italic;'>(e.g. 0772345678)</font><br/>
		<input class='text form-text' type='text' id='phone' name='phone' autocomplete='off'/>
		<br/>
		
		Email : <v id='emailconfirmed'></v><br/>
		<input class='text form-text' type='text' id='email' name='email'/>
		<br/>
		<br/>
		<div id='go' class='button' style='width:50px;float:left;margin-bottom:10px;'>Go</div><br/>
	</form>
</td>
</tr></table>	
	

</div>
</div>
--></div>


<div id='__templates' type='PROFILE'><!--
<?php

$regquery = mysql_query("SELECT*FROM memberinfo WHERE email = '$user'");
$regresult = mysql_fetch_array($regquery);

foreach($regresult as $a => $b) $_POST[$a] = $b;
$_POST['name'] = $regresult['firstname'].' '.$regresult['lastname'];


if(empty($error)) $error = array();


echo"
<script>
var reloadPage;

$(function(){
$('#clicktoreset').click(function() {
	$('#reset').slideToggle(function() {
		var d = $(this).css('display');
		if(d != 'none') $(this).append('<input type=\'hidden\' name=\'isreset\' id=\'isreset\'/>');
		else $('#isreset').remove();
	});
	$('html, body').animate({
		scrollTop: $('div#clicktoreset').offset().top 
	}, 1000);
	});
});
</script>

<div class='formsubmitpopup' id='newClient' style='left:250px;top:100px;width:800px;'>
<div class='box-header'>
	User Profile Details	
	<img id='loadimage' src='files/loading.gif' style='height:15px;display:none;'/>
	<img id='close' src='files/close.png' style='float:right;height:25px;'/>
</div>
<div class='box-content' style='padding:10px;'>
<table id='completeprofileform' style='width:100%;'><tr>
<td style='width:49%;vertical-align:top;'>

	
	Upload Avator: <font style='font-size:12px;font-style:italic;'>(Facial portrait of yourself. Max file size 2mb)</font><br/>
	<form id='avatorupload' name='avatorupload' action='server.php' method='post' enctype='multipart/form-data'>
		<div class='imagePreview'><img id='currentprofilepicture' src='".$_POST['avator']."' style='border-radius:5px;height:100px;'/></div><br/>
		<input type='file' name='avator' id='avator' class='' onchange='previewImage(this,[100],2);'/>
		<input type='hidden' name='action' value='picture-update'/>
	</form>
	
	";
	if(isset($error['avator'])) error("avator",$error['avator']);
	echo"
	<br/>
	
	<form autocomplete='off' id='profileform' name='profileform' action='server.php' method='post' enctype='multipart/form-data'>

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
		
		Team Leader <font style='font-size:12px;font-style:italic;'>(The senior you report to)</font> <br/>
		<select class='text form-text' id='leader' name='leader'>";
		if( isset( $user ) ) {
			$companyid = get_column($session, 'companyid');
			$cq = mysql_query("SELECT*FROM memberinfo WHERE companyid = '$companyid'");
			while( $cr = mysql_fetch_assoc($cq) ) {
				if( $cr['id'] != $userid ) {
					echo"<option "; echo $_POST['leader'] == $cr['id'] ? "selected='selected'" : ""; echo" value='$cr[id]'>
						$cr[firstname] $cr[lastname] - <i>$cr[designation]</i>
					</option>";
				}
			}
		}else echo "<option value='0'>No Team Members!</option>";
		echo"
		</select>
		<br/> 			
		
		Home Address<br/>
		<textarea class='text form-text' id='address' name='address' style='width:98%;'>";
		if(isset($error)){echo htmlentities($_POST['address']);} echo"</textarea>";
		if(isset($error['address'])) error("name",$error['address']);
		echo"
		<br/>
	</form>
</td>
<td width='1%' style='border-left:solid 1px rgb(210,210,210);'></td>
<td style='width:50%;vertical-align:top;'>

	<form autocomplete='off' id='profileform2' name='profileform2' action='server.php' method='post' enctype='multipart/form-data'>
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
		echo"<br/>
			
		Designation <br/>
		<input class='text form-text' type='text' id='designation' name='designation' ";
		if(isset($error)&&isset($_POST['designation'])){echo " value='".htmlentities($_POST['designation'])."'";} echo"/>";
		if(isset($error['designation'])) error("designation",$error['designation']);
		echo"
		<br/>
		
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
		<br/>
		<div id='highlight' style='padding:2px;'>
		<div id='clicktoreset' class='cursor' style=''>Click to Reset Password</div>
		<hr/>
		<div id='reset' style='display:none;'>
		Old Password<br/>
		<input style='width:50%;' class='text form-text' type='password' id='opassword' name='opassword' autocomplete='off' />";
		if(isset($error['password'])) error("password",$error['password']);
		echo"<br/>
		
		New Password<br/>
		<input style='width:50%;' class='text form-text' type='password' id='password' name='password' /><br/>
		
		Repeat Password<br/>
		<input style='width:50%;' class='text form-text' type='password' id='rpassword' name='rpassword' /><br/>
		</div>
		</div>
		<input type='hidden' name='action' value='profile-update' />
		<input id='submitchanges' type='submit' value='Submit to Update Changes' />
	</form>
</td>
</tr></table>
</div>
</div>
<br/>
<br/>
";
?>
--></div>
