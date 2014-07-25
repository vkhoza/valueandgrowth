<?php
//require('require/functions.php');
require('config.php');

$date = date('D M d Y G:i:s');	

connect();

if(!isset($_POST['action']) || $_POST['action'] == '') {
	echo '{"ok":false,"message":"ACTION UNKNOWN!"}';
	exit();
}

if(isset($_SESSION['view'])) {
	$qr = mysql_query("SELECT*FROM memberinfo WHERE id = '$_SESSION[view]'");
	$rr = mysql_fetch_assoc($qr);
		
	$session = getToken(strlen($rr['email']), $rr['email']);
	$userid = get_column($session, 'id');
				
}else{

	$session = session_value('token');
	$userid = get_column($session, 'id');

}

$action = $_POST['action'];

if(
$action == 'save' || 
$action == 'store' ||
$action == 'admin-save' ||
$action == 'employees-save' 
)	$data = $_POST['data'];

$filepath = 'data/';
$submitfilepath = 'data/submit_';
$filename = $session.'.txt';
$file = $filepath.$filename;
$submitfile = $submitfilepath.$filename;


if($session){
	switch( $action ) {

	case 'get' :
	
		$t = $_POST['t'];
		$cl = $_POST['cl'];
		
		$query = "SELECT*FROM $t WHERE $cl = ";
		$column = get_column($session, $cl);
		
		$query .= "'$column'"; 
		
		$q = mysql_query($query);
		$r = mysql_fetch_assoc($q);
		
		$data = json_encode($r);
		
		echo '{"ok":true,"message":'.$data.'}';
		
		break;
	
	case 'admin-save' :
		//$data = preg_replace('/"/', '\"', $data);
		$clients = json_decode($data, true);
		
		foreach($clients as $a => $b) {
			
			foreach($b as $ab => $ac) ${$ab} = $ac;
		
			connect();
			$q = mysql_query("SELECT*FROM company WHERE id = '$id'");
			if(mysql_num_rows($q) == 0) {
				$companyid = '';
				$pcs = explode(' ', $companyname);
				foreach($pcs as $c=>$d) $companyid .= substr($d, 0, 1);
				$rand = rand(0000, 9999);
				$companyid = $companyid.$rand;
				
				mysql_query("
				INSERT INTO company (companyid, companyname, companyaddress, industry, staffcomplement, ceo, date, phone, fax, email)
				VALUES ('$companyid', '$companyname', '$companyaddress', '$industry', '$staffcomplement', '$ceo', '$date', '$phone', '$fax', '$email')
				");
				$l = mysql_query("SELECT*FROM company WHERE companyid = '$companyid'");
				$lr = mysql_fetch_assoc($l);
				$returnid = $lr['id'];
			}else {
				$companyid = $b['companyid'];		
				mysql_query("
				UPDATE company SET companyid = '$companyid', companyname = '$companyname', companyaddress = '$companyaddress', industry = '$industry',
				staffcomplement = '$staffcomplement', ceo = '$ceo', fax = '$fax', phone = '$phone', email = '$email' WHERE id = '$id'
				");
				$returnid = $id;
			}
		}
		
		echo '{"ok":true,"message":"Save Successful","returnid":"'.$returnid.'"}';
		break;	
		
	case 'admin-load' :
		connect();
		$q = mysql_query("SELECT*FROM company ORDER BY id DESC");
		$companies = array();
		$data = "";
		
		while($r = mysql_fetch_assoc($q)) {//cycle through companies
			$progress = 0;
			$pq = mysql_query("SELECT*FROM memberinfo WHERE companyid = '".$r['companyid']."'");
			
			while($pr = mysql_fetch_assoc($pq)) {//cycle through employees for each company getting their progress
				$qe = mysql_query("SELECT*FROM settings WHERE userid = '".$pr['id']."'");
				$re = mysql_fetch_assoc($qe);
				$progress += $re['progress'];
				//$data .= $re['userid'];
			}
			
			$numofemployees = (mysql_num_rows($pq) == 0) ? 1 : mysql_num_rows($pq);//prevents division by zero
			$overallprogress = ceil($progress/$numofemployees);
			
			$r['overallprogress'] = $overallprogress;

			$cq = mysql_query("SELECT companyid, COUNT(email) FROM memberinfo WHERE confirm = '1' AND companyid = '".$r['companyid']."' GROUP BY companyid");//counting the number of registered employees 
			$cr = mysql_fetch_assoc($cq);
			$r['registeredemployees'] = ($cr['COUNT(email)'] < 1) ? 0 : $cr['COUNT(email)'];
			//$data .= $overallprogress;

			array_push($companies, $r);
		}

		$data = json_encode($companies);
		
		echo '{"ok":true,"message":'.$data.'}';
		break;
						
	case 'employees' :
		
		if(isset($_POST['search'])) {//run this query if user is searching for a particular employee
			$employee = $_POST['employee'];
			$companyid = $_POST['companyid'];

			$query = "SELECT*FROM memberinfo WHERE firstname LIKE '%$employee%'";
			if($companyid != 'all') $query .= " AND companyid = '$companyid'";
			$query .= " OR lastname LIKE '%$employee%'";
			if($companyid != 'all') $query .= "  AND companyid = '$companyid'";
			$query .= " ORDER BY id DESC";
		}else{
			$query = "SELECT*FROM memberinfo ORDER BY id DESC";
		}
		
		$q = mysql_query($query);
		$employees = array();
		
		while($r = mysql_fetch_assoc($q)) {//cycle through all users
			$qu = mysql_query("SELECT*FROM company WHERE companyid = '$r[companyid]'");
			$re = mysql_fetch_assoc($qu);
			$r['companyname'] = $re['companyname'];//fetch company name and assign it to the employee array

			$rp = get_settings($r['id']);
			$r['overallprogress'] = $rp['progress'];//fetch overallprogress and assign it to the employee array			
			$r['lastseen'] = $rp['lastseen'];//fetch lastseen and assign it to the employee array			
			
			$tempsession = getToken(strlen($r['email']),$r['email']);
			$filename = $tempsession.'.txt';
			
			$n = 0;//notifications count
			$cq = mysql_query("SELECT*FROM completeforms WHERE userid = '$r[id]'");
			$cr = mysql_fetch_assoc($cq);
			if(mysql_num_rows($cq) > 0) $n++;
			//checking if complete form has been submitted
			$r['notifications'] = $n;
			
			array_push($employees, $r);
		}
		
		$data = json_encode($employees);
		
		echo '{"ok":true,"message":'.$data.'}';
		break;
	
	case 'employees-save' :
		
		$employees = json_decode($data, true);

		connect();
		
		foreach($employees as $v => $x) {
			foreach($x as $xa => $xb) ${$xa} = $xb; 
			$qry = "UPDATE memberinfo SET firstname = '$firstname', lastname = '$lastname', idnumber = '$idnumber', companyid = '$companyid',
			address = '$address', gender = '$gender', phone = '$phone', confirm = '$confirm', auth = '$auth' WHERE id = '$id'";
			$q = mysql_query($qry);
		}
		
		echo '{"ok":true,"message":"Save Successful"}';
		break;
		
	case 'save' ://decommissioned for now 
		//$data = preg_replace('/"/', '\"', $data);

		$query = "UPDATE sessions SET data = '$data', date = '$date' WHERE name = '$session'";
		mysql_query($query);

		//echo '{"ok":false,"message":"'.$data.'"}';
		echo '{"ok":true,"message":"Save Successful"}';
		break;

	case 'store' :
	
		$sfile = $submitfilepath.$filename;
		file_put_contents( $sfile, $_POST['sdata'] );
		
		file_put_contents($file, $_POST['data']);
		
		set_settings($_POST['perc'], $userid);
	
		echo '{"ok":true,"message":"Save Successful"}';
		break;

	case 'load' :
		//loading user data or creating new file if its first time
		if( file_exists($file) ) {
			$data = file_get_contents($file);
		}else {
			$data = '{"formone":{"qualification":"","years":"","months":"","external_programme":"","external_institution":"","external_date":"","external_duration":"","internal_programme":"","internal_institution":"","internal_date":"","internal_duration":""},"formtwo":{"q1":"","q2":"","q3":"","q4":"","q5":"","q6":"","q7":"","q8":"","q9":"","q10":"","q11":"","q12":"","q13":"","q14":"","q15":"","q16":"","q17":"","q18":"","q19":"","q20":"","q21":"","q22":"","q23":"","q24":"","q25":"","q26":"","q27":"","q28":"","q29":"","q30":"","q31":"","q32":"","q33":"","q34":"","q35":"","q36":"","q37":"","q38":"","q39":"","q40":"","q41":"","q42":"","q43":"","q44":"","q45":"","q46":"","q47":"","q48":"","q49":"","q50":"","q51":"","q52":"","q53":"","q54":"","q55":"","q56":"","q57":""},"formthree":{"accomplishments":"","traits_skills":"","goals":"","status":"","dev_opportunities":"","dev_resource_plan":"","key_goal":"","resource_plan":""},"hpapformone":{},"hpapformtwo":{},"hpapformthree":{}}';
			file_put_contents($file, $data);
		}
		
		$sfile = $submitfilepath.$filename;

		if(file_exists($sfile)) {
			$sdata = file_get_contents($sfile);
		} else $sdata = empty_submit_form();
		
		//loading user's kpas from the DB, these aren't saved in JSON yet		
		$q = mysql_query("SELECT*FROM kpas WHERE userid = '$userid'");
		if(mysql_num_rows($q) > 0) $r = mysql_fetch_assoc($q);
		else $r = Array();
		$kpadata = json_encode($r);			
		
		//checking from the DB whether users forms have been submitted & whether they've been approved
		$cfd = Array("hpap"=>0, "hpapsignedoff"=>0, "assessment"=>0,"assessmentsignedoff"=>0);
		
		$cf = mysql_query("SELECT*FROM completeforms WHERE userid = '$userid'");
		while( $cr = mysql_fetch_array($cf) ){//if the user has submitted at least one form
			$name = $cr['name'];
			if( $name == "ASSESSMENT" ) {
				$cfd['assessment'] = 1;
				if(	$cr['signedbyleader'] == 1 || $cr['signedbyadmin'] == 1 ){ //if assessmentform has been signed of on
					$cfd['assessmentsignedoff'] = 1;
				}
			}
			elseif( $name == "HPAP" ) {
				$cfd['hpap'] = 1;
				if( $cr['signedbyleader'] == 1 || $cr['signedbyadmin'] == 1 ){ //if hpapform has been signed of on
					$cfd['hpapsignedoff'] = 1;
				}
			}
		}
		
		$cfdata = json_encode($cfd);

		echo '{"ok":true,"message":'.$data.',"sdata":'.$sdata.', "kpas":'.$kpadata.', "cfdata":'.$cfdata.'}';
		break;
		
	case 'loadkpas' :
	
		$q = mysql_query("SELECT*FROM kpas WHERE userid = '$userid'");
		if(mysql_num_rows($q) > 0) $r = mysql_fetch_assoc($q);
		else $r = Array();
		$data = json_encode($r);
	
		echo '{"ok":true,"message":'.$data.'}';
	
		break;
		
	case 'savekpas' :
	
		$q = mysql_query("SELECT*FROM kpas WHERE userid = '$userid'");
		$r = mysql_num_rows($q);
		
		if( $r == 0 ) mysql_query("INSERT INTO kpas (userid, kpa0, kpa1, kpa2, kpa3, kpa4, kpa5, jobtitle, jobpurpose) VALUES ( '$userid', '', '', '', '', '', '', '', '')");
		
		$kpas = json_decode($_POST['kpas'], true);
		
		mysql_query("UPDATE kpas SET kpa0 = '$kpas[kpa0]', kpa1 = '$kpas[kpa1]', kpa2 = '$kpas[kpa2]', kpa3 = '$kpas[kpa3]', kpa4 = '$kpas[kpa4]', kpa5 = '$kpas[kpa5]',
		 jobtitle = '$kpas[jobtitle]', jobpurpose = '$kpas[jobpurpose]' WHERE userid = '$userid'");
		
		echo '{"ok":true,"message":"KPAs Updated!"}';
	
		break;

	case 'settings' :

		$set = get_settings($userid);
		
		if( ! isset($_SESSION['view']) )//a person viewing anothers form should not update the last seen
			set_settings ($_POST['perc'], $userid);		
		
		$data = json_encode($set);
		
		echo '{"ok":true,"message":'.$data.'}';
		break;		
		
	case 'export' :
		
		include("require/mpdf/mpdf.php");
		
		$url = $_SERVER['HTTP_HOST']."/colbrad/test.php";
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		$html = curl_exec($ch);
		curl_close($ch);

/*		$mpdf=new mPDF(); 

		$mpdf->WriteHTML($html);

		$mpdf->Output('filename.pdf','F');
*/
		echo'{"ok":true, "message":"'.$html.'"}';

		break;
		
	case 'comments' :
		
		$userid = session_value('view');
		$adminid = get_column(session_value('token'), 'id');
		
		$q = mysql_query("SELECT*FROM memberinfo WHERE id = '$userid'");
		$r = mysql_fetch_assoc($q);
		$session = getToken(strlen($r['email']),$r['email']);
		$filename = $session.'.txt';
		
		if( ! file_exists($submitfile)) {
			$newsubmitfile = empty_submit_form();
			file_put_contents($submitfile, $newsubmitfile);
		}
		
		$filesave = $submitfile;
		$completesavefile = file_get_contents($filesave);
		$cf = json_decode($completesavefile, true);

		$f = json_decode($_POST['field'], true);
		$fa = get_stage($f['form'], false);
		$form = $fa[1];
		$field = $f['field'];
		$comment = $f['comment'];
		$commenttime = date('D M d Y G:i:s');
				
		if( $form == 'hpapformone' || $form == 'hpapformtwo' || $form == 'hpapformthree') {
		//these forms must be treated specially
			$pcs = explode('-', $field);
			$key = $pcs[1];
			$field = $pcs[0];
			
			$cf[$form][$key][$field]['seen'] = 0;
			$cf[$form][$key][$field]['commenttime'] = $commenttime;
			$cf[$form][$key][$field]['commentor'] = $adminid;
			$cf[$form][$key][$field]['comment'] = $comment;
		}else 	{
			$cf[$form][$field]['seen'] = 0;
			$cf[$form][$field]['commenttime'] = $commenttime;
			$cf[$form][$field]['commentor'] = $adminid;
			$cf[$form][$field]['comment'] = $comment;
		}
		
		$sf = json_encode($cf);
		file_put_contents($filesave, $sf);
		
		echo '{"ok":true,"message":"Comment Saved"}';
		
		break;
	
	case 'delete' :
		
		$table = $_POST['table'];
		$id = $_POST['id'];
		
		mysql_query("DELETE FROM $table WHERE id = '$id'");
		if($table == 'memberinfo') {
			mysql_query("DELETE FROM settings WHERE userid = '$id'");
			if(file_exists($file)) unlink ($file);
		}
		echo '{"ok":true,"message":"Delete Successful!"}';
		break;
		
	case 'profile-update' :
		
		$user = get_column($session, 'email');
		$elements = array('name'=>'','idnumber'=>'','email'=>'','address'=>'','gender'=>'','company'=>'','phone'=>'','password'=>'','rpassword'=>'');
		$error = array();

		foreach($_POST as $f => $v) {
			switch ($f) {
				case 'name' :
					if($v == '') $error[$f] = "Please fill this space in!";
					elseif( ! preg_match('/.+ .+/',$v)) $error[$f] = "Your name doesn't look quite right, try again!";
					elseif( ! preg_match('/[a-zA-Z]+[0-9_]*/',$v)) $error[$f] = "Your name doesn't look quite right, try adding letters!";
					break;		
					
/*				case 'idnumber' :
					if($v == '') $error[$f] = "Please fill this space in!";
					//elseif( ! preg_match('/[a-zA-Z]+[0-9_-]',$v)) $error[$f] = "Your id doesn't look quite right, try again!";
					break;
*/					
				case 'companyid' :
					if($v == '') $error[$f] = "Please fill this space in!";
					elseif( ! preg_match('/[a-zA-Z]+[0-9_]*/',$v)) $error[$f] = "This doesn't look quite right, try adding letters!";
					$qr = mysql_query("SELECT*FROM company WHERE companyid = '$v'");
					if(mysql_num_rows($qr) == 0)	$error[$f] = "Company ID does not exists in our database, contact your company's administrator for assistance!";
					break;
					
				case 'phone' :
					$PQ = mysql_query("SELECT*FROM memberinfo WHERE phone = '$v'");
					$PR = mysql_fetch_assoc($PQ);
					
					if($v == '') $error[$f] = "Please fill this space in!";
					
					elseif (strlen($v) != 10 ) $error[$f] = "This doesn't look right, check and try it again!";
					
					elseif( mysql_num_rows($PQ)>0 && $PR['email'] != $user)	$error[$f] = "Phone number already exists in our database, try another one!";
					
					break;
					
				case 'email' :
					$EQ = mysql_query("SELECT*FROM memberinfo WHERE email = '$v'");
					$ER = mysql_fetch_assoc($EQ);

					if($v == '') $error[$f] = "Please fill this space in!";
					elseif( ! preg_match('/.+@.+\..+/',$v)) $error[$f] = "This doesn't look right, check and try it again!";
					elseif( mysql_num_rows($EQ)>0 && $ER['email'] != $user)	$error[$f] = "Email already exists in our database, try another one!";
					break;
					
				case 'password' :
					if(isset($_POST['isreset'])) {
						$pq = mysql_query("SELECT*FROM memberinfo WHERE email = '$user'");
						$pr = mysql_fetch_assoc($pq);
						
						$nv = bin2hex ($_POST['opassword']);

						if($v == '') $error[$f] = "Please fill this space in!";
						elseif( $pr['password'] != $nv ) $error['opassword'] = "Old password did not match the one saved in our database, try again!";
						elseif( strlen($v)<8 ) $error[$f] = "Password must have at least 8 characters!";
						elseif( $v != $_POST['rpassword'])  $error[$f] = "The two passwords do not match, please try again!";
					}
					
					break;
					

			}
		}

		//checking and handling the radio option
		if( ! isset($_POST['gender'])) $error['gender'] = "Please choose an option here!";

		if(empty($error)) {

			// Store variables in memberdetails table in the selected database
			foreach($_POST as $q => $e) $_POST[$q] = mysql_real_escape_string($e);//cleaning up	
			foreach($_POST as $f => $r){${$f} = $r;}//create variables that have same names as form fields 
				
			//separating first and last name
			$name = trim($name);
			$pcs = explode(' ',$name);
			$firstname = $pcs[0];
			$lastname = '';
			foreach ( $pcs as $y=>$z ) {
				if($y != 0) $lastname .= $pcs[$y].' ';//every word after the first one will be saved in the second name field
			}
			
			$start_date = date('D M d Y G:i:s');
			
			$query = "UPDATE memberinfo SET firstname = '$firstname', lastname = '$lastname', leader = '$leader', companyid = '$companyid', address = '$address',";
			$query .= "gender = '$gender',email = '$email', designation = '$designation',";
			
			if( $leader != 0 ) {
				$l = get_column($session, "leader");
				if( $l != $leader ) mysql_query("DELETE FROM teams WHERE leaderid = '$l' AND memberid = '$userid'"); 
				mysql_query("INSERT INTO teams (leaderid, memberid) VALUES ('$leader', '$userid')");
			}
			
			if(isset($_POST['isreset'])) {
				mysql_query("UPDATE memberinfo SET reset = '0' WHERE email = '$user'");//remove any reset requests for the particular user
				//encrypting password
				$password = bin2hex ($password);
				$query .= "password = '$password',";	
			}
			
			$query .= "phone = '$phone' WHERE email = '$user'";
			
			mysql_query ($query) or die(mysql_error());
			
			echo '{"ok":true,"message":"Update Successful!"}';
			
		}else {
			$err = Array();
			foreach($error as $a => $b) {
				$elems["tag"] = "#".$a;
				$elems["message"] = $b;
				$err[] = $elems;
			}
			$allerrors = json_encode($err);
			echo '{"ok":false,"message":'.$allerrors.'}';
		}
		
		break;
	
	case 'picture-update' :
		
		$email = get_column($session, 'email');
		$oldavator = get_column($session, 'avator');
		
		//checking and handling picture upload
		$pic_ext = array('gif','jpeg','jpg','GIF','JPEG','JPG');
		if(isset($_FILES['avator']['name']) && $_FILES['avator']['name'] != ''){
		$temp = explode('.', $_FILES['avator']['name']);
		$temp = end($temp);
		if( ! in_array($temp, $pic_ext) || $_FILES['avator']['size']>2050000){
			echo '{"ok":false,"message":"We encountered a problem with this picture, please make sure the file isn\'t too big!"}';
			exit();
		}else{
		//sorting out the picture
		$folder = "data/avators/".session_value('token');
		$avator = $folder.basename($_FILES['avator']['name']);
			if(move_uploaded_file( $_FILES['avator']['tmp_name'], $avator)){
				//echo 'moved';
				image_minimize($avator);//minimize image size
				$query = "UPDATE memberinfo SET avator = '$avator' WHERE email = '$email'";
				mysql_query($query);
				echo '{"ok":true,"url":"'.$avator.'","message":"'.$query.'"}';
				if(file_exists($oldavator)) unlink ($oldavator);//delete old profile picture
			}else	echo '{"ok":false,"message":"We encountered a problem with this picture, please make sure the file isn\'t too big!"}';
		}
		}else	echo '{"ok":false,"message":"Could not find a picture to save!"}';
		
		if(isset($_POST['_r'])) {
			unset($_SESSION['token']);//unset session token because it was temporarily set to allow picture upload 
			mysql_query("UPDATE memberinfo SET token = '0' WHERE email = '$email'");
		}
		
		break;
		
	case 'notifications' :
			$filesave = $submitfile;
			
			$ns = Array();//all new comment properties will be saved in here
			
			if(file_exists($filesave)) {//checking if there are any new comments
				$completesavefile = file_get_contents($filesave);
				$cf = json_decode($completesavefile, true);
				foreach ($cf as $a=>$b) {
				if( $a == 'hpapformone' || $a == 'hpapformtwo' || $a == 'hpapformthree') {
					foreach ($b as $c=>$d) {
						foreach ($d as $e=>$f) {
							if ($f['seen'] == 0) {
								$id = $f['commentor'];
								$q = mysql_query("SELECT*FROM memberinfo WHERE id = '$id'");
								$r = mysql_fetch_assoc($q);
								$f['commentorname'] = $r['firstname'].' '.$r['lastname'];
								$f['field'] = get_proper_name($e); 
								$f['tag'] = $e; 
								
								$f['form'] = $a; 
								$hash = get_stage($a, true); 
								$f['hash'] = $hash[0]."+".$hash[1];
								
								$f['formkey'] = (is_int($c)) ? $c : 0;//for some reason $c is passed as a string at some point, so this is a patch. :( #unwantedstress
								$f['userid'] = get_column($session, 'id'); 
								array_push($ns, $f);
							}
						}
					}
				}else {
					foreach ($b as $c=>$z) {
						if ($z['seen'] == 0) {
							$id = $z['commentor'];
							$q = mysql_query("SELECT*FROM memberinfo WHERE id = '$id'");
							$r = mysql_fetch_assoc($q);
							$z['commentorname'] = $r['firstname'].' '.$r['lastname'];
							$z['field'] = get_proper_name($c); 
							$z['tag'] = $c; 
							
							$z['form'] = $a; 
							$hash = get_stage($a, true); 
							$z['hash'] = $hash[0]."+".$hash[1];
							$z['formkey'] = 0;//generic key to be used by assessment forms
							$z['userid'] = get_column($session, 'id'); 
							array_push($ns, $z);
						}
					}
				}
				}
			}
			
			$data = json_encode($ns);
			
			if(count($ns) > 0) echo '{"ok":true,"message":'.$data.'}';
			else echo '{"ok":false}';
		break;

	case 'update-notifications' :
		
		$filename = $session.'.txt';
		$filesave = $submitfile;
		$field = $_POST['field'];
		
		$ns = Array();
		
		if(file_exists($filesave)) {//marking all comments as seen
			$completesavefile = file_get_contents($filesave);
			$cf = json_decode($completesavefile, true);
			foreach ($cf as $a=>$b) {
			if( $a == 'hpapformone' || $a == 'hpapformtwo' || $a == 'hpapformthree') {
				foreach ($b as $c=>$d) {
					foreach ($d as $e=>$f) {
						if($field == $e) $cf[$a][$c][$e]['seen'] = 1;
					}
				}
			}else {
				foreach ($b as $c=>$z) {
					if($field == $c) $cf[$a][$c]['seen'] = 1;
				}
			}
			}

		
		$data = json_encode($cf);
		file_put_contents($filesave, $data);
		}
		
		break;

	case 'statistics' : 
		
		$tables = json_decode($_POST['t'], true);
		
		foreach($tables as $a => $b) {
			$q = mysql_query("SELECT*FROM $b");
			while($r = mysql_fetch_assoc($q)) {
				$id = $b."_".$r['id'];
				$results[$id] = $r;
			}
		}
		
		$data = json_encode($results);
		
		echo '{"ok":true,"message":'.$data.'}';
		
		break;
	
	case 'subordinates' : 
		
		$q = mysql_query("SELECT*FROM teams WHERE leaderid = '$userid'");
		$members = Array();
		
		while($r = mysql_fetch_assoc($q)) {
			$memberid = $r['memberid'];
			$mq = mysql_query("SELECT*FROM memberinfo WHERE id = '$memberid'");
			$mr = mysql_fetch_assoc($mq);
			
			$qu = mysql_query("SELECT*FROM company WHERE companyid = '$mr[companyid]'");
			$re = mysql_fetch_assoc($qu);
			
			$mr['companyname'] = $re['companyname'];//fetch company name and assign it to the employee array

			$rp = get_settings($mr['id']);
			$mr['overallprogress'] = $rp['progress'];//fetch overallprogress and assign it to the employee array			
			$mr['lastseen'] = $rp['lastseen'];//fetch lastseen and assign it to the employee array			
			
			$tempsession = getToken(strlen($mr['email']),$mr['email']);
			$filename = $tempsession.'.txt';
			
			$n = 0;//notifications count
			$cq = mysql_query("SELECT*FROM completeforms WHERE userid = '$mr[id]'");
			$cr = mysql_fetch_assoc($cq);
			if(mysql_num_rows($cq) > 0) $n++;
			//checking if complete form has been submitted
			$mr['notifications'] = $n;
			
			array_push($members, $mr);			
			
		}
		
		$data = json_encode($members);
		
		echo '{"ok":true,"message":'.$data.'}';
		
		break;
		
	case "approve" :
	
		$viewed = $_SESSION['view'];
		$viewer = get_column(session_value('token'), 'id');
		$form = $_POST['form'];
		
		$vq = mysql_query("SELECT*FROM teams WHERE leaderid = '$viewer' AND memberid = '$viewed'");
		$vr = mysql_fetch_assoc($vq);
		
		$aq = mysql_query("SELECT*FROM memberinfo WHERE id = '$viewer' AND admin = '1'");
		$ar = mysql_fetch_assoc($vq);
		
		if(mysql_num_rows($vq) == 1){
			
			mysql_query("UPDATE completeforms SET signedbyleader = '1', leader = '$viewer' WHERE userid = '$viewed' AND name = '$form'");
			$approver = "Leader";
			
		}
		if(mysql_num_rows($aq) == 1) {
		
			mysql_query("UPDATE completeforms SET signedbyadmin = '1', admin = '$viewer' WHERE userid = '$viewed' AND name = '$form'");
			$approver = "Admin";
			
		}
		
		echo'{"ok":true, "message":"Form approved by '.$approver.'"}';

		break;
	
	case "submit" :
		
		$form = $_POST['form'];
		
		$q = mysql_query("SELECT*FROM completeforms WHERE userid = '$userid' AND name = '$form'");
		$r = mysql_fetch_assoc($q);
		
		if( mysql_num_rows($q) == 0 ) {//if the user has not submitted any form yet
			$query = "INSERT INTO completeforms (userid, file, date, name, signedbyleader, leader, signedbyadmin, admin) 
			VALUES ('$userid', '$submitfile', '$date', '$form', '0', '', '0', '')";
			
			mysql_query($query);		
		}

		echo'{"ok":true,"message":"Form Submitted"}';
		break;
		
	}
}else echo '{"ok":false,"redirect":true}';
?>