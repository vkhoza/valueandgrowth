
<?php 
if( isset($user) && ! isset($_SESSION['view']) ) {//taking into consideration viewing mode.
	echo"
	<script src='js/headerscript.js'></script>
	<script>
	$(function() {
		$('#leftDiv, #menuOnLeft').show();
		requestNotificationsPage(1, 4);
	});
	</script>
	";
}elseif( isset($user) ) echo"<script>$(function() { $('#leftDiv, #menuOnLeft').show(); }); </script>";
?>

<table id='body'>
<tr style='height:70px;'>
<td class='bodycolumn' style='vertical-align:top;'>
<div id='move'>
<table class='header'>
	<tr>
		<td class='headerRow icon' style='width:80%;padding:0px;'>
			<a href='index.php'><img src='files/colbrad.jpg' style='margin-left:10px;height:60px;'/></a>
		</td>
		<td class='headerRow' style='width:15%;vertical-align:bottom;'>
		<table id='userpanel' style='border-spacing:0px;height:30px;width:260px;'>
			<tr>
				<td id='home' class='headerOptions' style='padding:4px;text-align:center;height:30px;width:10%'>
					<a href='
					<?php 
					if(isset($user)) {
						$q = mysql_query("SELECT*FROM memberinfo WHERE admin = '1' AND email = '$user'");
						$r = mysql_fetch_array($q);
						
						if(mysql_num_rows($q) > 0) echo "admin.php";
						else echo "index.php";
					}else echo "index.php";
						
					?>'><img src='files/home.png' style='height:20px;'/></a>
				</td>
				<td id='notifications' class='headerOptions' style='padding:4px;height:30px;width:10%'>
					<div id='notifyer'></div>
					<img id='notificationsimage' src='files/notifications.png' style='height:28px;'/>
					<div id='notificationsdropdown' class='drop-down' style='text-align:left;top:66px;width:300px;'>
						<div id='content'></div>
						<div id='controls'>
							<table id='pagination_controls' style='display:none;'>
								<tr><td id='back' title='Previous Page'></td><td id='forward' title='Next Page'></td></tr>
							</table>
						</div>
					</div>
				</td>
				<td id='user' class='headerOptions' style='padding:4px;line-height:170%;height:30px;width:70%;'>
				<table style='width:100%;'><tr>
				<td style='width:80%;'>
					<img src='files/downarrow.png' style='float:left;height:10px;margin-top:10px;margin-right:10px;'/>
					<?php 
					$display = isset($user) ? $user : "Guest"; 
					echo (strlen($display)>16) ? substr_replace($display, '...', 12) : $display;//sorting out the length of the users email
					?>

					<div id='userdropdown' class='drop-down' style='top:66px;width:181px;'>
						<div id='profile' class='cursor' style='width:100%;' <?php if(isset($user)) echo "onclick='showProfile();'"; ?> >Profile</div>
						<?php echo isset($user) ? "<a href='signout.php'><div style='width:100%;'>Signout!</div></a>" : 
						"<a href='register.php'><div style='width:100%;'>Register!</div></a>"; ?>
					</div>
				</td>
				<td style='width:;'>
					<div id='useravator'  style='background-image:url(<?php echo (get_column(session_value('token'), 'avator') != '') ? 
					get_column(session_value('token'), 'avator') : "files/avator.png"; ?>);float:right;height:30px;width:30px;'></div>
				</td>
				</tr></table>
				</td>
			</tr>	
		</table>
		</td>
		<td style='width:5%'></td>
	</tr>
</table>
</div>
</td></tr>
