
<?php 
require('config.php');
$userid = 8;
$qe = mysql_query("SELECT*FROM memberinfo WHERE id = '$userid'");
$re = mysql_fetch_array($qe);
$qc = mysql_query("SELECT*FROM company WHERE companyid = '".$re['companyid']."'");
$rc = mysql_fetch_array($qc);


echo"
<html>
<head>
<title>Colbrad | Accelerated Leader Energy</title>
	<link rel='stylesheet' type='text/css' href='export.css'>
</head>
<body style='font-family:tahoma;'>
<div id='formWrapper'>
	<table id='infotable'>
	<tr>
		<td class='left_side'>COMPANY NAME:</td>
		<td class='right_side'>$rc[companyname]</td>
		<td class='left_side'>INDUSTRY:</td>
		<td class='right_side'>$rc[industry]</td>
	</tr>

	<tr>
		<td class='left_side'>FULL NAME:</td><td class='right_side'> $re[firstname]</td>
		<td class='left_side'>SURNAME:</td><td class='right_side'> $re[lastname]</td>
	</tr>
	<tr>
		<td class='left_side'>DESIGNATION:</td>
		<td class='right_side' style='border-spacing:0px;'> $re[designation]</td>
		<td class='left_side'>CONTACT NUMBER:</td><td class='right_side'> $re[phone]</td>
	</tr>
	<tr>
		<td class='left_side'>EMAIL:</td><td class='right_side'> $re[email]</td>
		<td class='left_side'>GENDER:</td>
		<td class='right_side'> $re[gender]</td>
	</tr>
	</table>
	<br/>
	
	
	<table id='first_table'>
	<tr>
		<td class='left_side'>HIGHEST QUALIFICATION</td>
		<td class='right_side fieldcontainer' style='border-top:solid 1px;'>
		</td>
		<td class='left_side'>DURATION IN CURRENT POSITION</td>
		<td class='right_side' style='border-top:solid 1px;'>
			<table class='inner_table' style='padding:0px;height:100%;'>
				<tr>
					<td class='inner_column fieldcontainer' style='height:40px;width:35%;'>	
					</td>
					<td class='inner_column' style='width:15%;border-right:solid 1px;'>YEARS</td>
					<td class='inner_column fieldcontainer' style='height:40px;'>
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
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:25%; border-top:solid 1px;'>
			Institutions:
		</td>
		<td class='right_side' style='vertical-align:top;padding:5px;width:17%; border-top:solid 1px;'>
			<div class='fieldcontainer' style='border-bottom:solid 1px;height:45px;width:100%;'>Date Attended:
			</div>
			<div class='fieldcontainer' style='width:100%;height:45px;'>Duration (days):
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
		</td>
		<td class='right_side fieldcontainer' style='vertical-align:top;padding:5px;border-right:hidden;width:25%; border-top:solid 1px;'>
			Institutions:
		</td>
		<td class='right_side' style='vertical-align:top;padding:5px;width:17%; border-top:solid 1px;'>
			<div class='fieldcontainer' style='border-bottom:solid 1px;height:45px;width:100%;'>Date Attended:
			</div>
			<div class='fieldcontainer' style='width:100%;height:45px;'>Duration (days):
			</div>
		</td>
	</tr>
</table>
</div>
<br/>
<div style='width:100%;text-align:center;'>
</div>

</div>
</body>
</html>
";
?>