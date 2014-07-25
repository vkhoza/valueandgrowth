<?php 
//require('require/functions.php');
require('config.php');
if(isset ($_SESSION['view'])) unset($_SESSION['view']);//destroy this variable just to make sure

?>
<!doctype html>
<html>
<head>
<title>Colbrad | Accelerated Leader Energy</title>
	<link rel="icon" type="image/png" href="favicon.png">
	<link rel="icon" type="image/gif" href="favicon.png">
	<link rel="icon" type="image/jpeg" href="favicon.png">
	<link rel="icon" type="image/vnd.microsoft.icon" href="favicon.png">
	<link rel='stylesheet' type="text/css" href='style.css'>
	<link rel='stylesheet' type="text/css" href='addstyle.css'>
	<link rel='stylesheet' type="text/css" href='dashboard.css'>
	<link rel='stylesheet' type="text/css" href='js/libraries/jquery-ui.css'/>
	<script src="js/libraries/jquery-1.11.1.js"></script>
	<script src='js/libraries/jquery-ui.js'></script>
	<script src='js/libraries/jquery.serialize-object.min.js'></script>
	<script src='js/libraries/jquery.serialize-object.js'></script>
	<script src='js/libraries/jquery.formparams.js'></script>
	<script src='js/libraries/jquery.form.js'></script>
	<script src='js/libraries/jquery.color-2.1.2.js'></script>
	<script src='js/libraries/html5.image.preview.min.js'></script>
	<script src="js/libraries/Chart.js"></script>
	<script src="js/libraries/Chart.min.js"></script>
	<script src="js/libraries/testbrowser.js"></script>
	<?php
	if( isset($_GET['admin']) && isset($_GET['see'])) {//dealing with the admin scripts to run
		//i need this to run in the head.php file becuase some of the JS files need to check if
		//adminFormViewer() has been declared;
		
		$adminsession = $_GET['admin'];
		$isadminid = get_column($adminsession, 'id');

		$qr = mysql_query("SELECT*FROM memberinfo WHERE id = '$isadminid' AND admin = '1'");
		$admin = (mysql_num_rows($qr) > 0)	? true : false;//will be utilized for admin confirmation purposes
	
		if( $admin ){
		echo "
		<script src='js/adminformviewer.js'></script>
		<script>
		$(function() {
			var afv = new adminFormViewer();
			afv.initiate('#rightDiv');
		});
		</script>
		";
		}
	}
	?>
	<script src='js/Templates.js'></script>
	<script src='js/Utilities.js'></script>
	<script src='js/Assessmentform.js'></script>
	<script src='js/HPAPForm.js'></script>
	<script src='js/Clients.js'></script>
	<script src='js/Completeforms.js'></script>
	<script src='js/ClientSummary.js'></script>
	<script src='js/vgCalender.js'></script>
</head>
<body>

