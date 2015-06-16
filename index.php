<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: RelayBoard Configuration
**/
/* 
$actresp = "S,152,0,2,02:36:12,5,14,00000,000000,00000000,0,0,0,0,0,77.8F,,,,,,0.00,0000,0,0,0";
$aResponse = explode(',',$actresp);
echo '@aResponse =><pre>'; print_r($aResponse); echo '</pre>';

//$anotheresp = "S,46,0,5,09:37:24,5,14,00000,000000,00000000,0,0,0,0,0,68.7F,,,,,,0.00,0000,0,0,0";
$anotheresp = "S,46,0,5,09:37:25,5,14,00000,000000,00000000,0,0,0,0,0,68.7F,,,,,,0.00,1000,0,0,0";
$aResponseAnot = explode(',',$anotheresp);
echo '@aResponseAnot => <pre>'; print_r($aResponseAnot); echo '</pre>';

die('@Befor #SETUP'); */

#SETUP
session_start();
include("include/functions.php");

#INPUT
$sTask = isset($_REQUEST['btn_submit']) ? $_REQUEST['btn_submit'] : '' ;
$sIpAddress = isset($_REQUEST['ip_address']) ? $_REQUEST['ip_address'] : '' ;
$sPortNo = isset($_REQUEST['port_no']) ? $_REQUEST['port_no'] : '' ;
$sErrMsg = '';

#PROCESS
//Check for relay ip address if present then redirect to home
if((IP_ADDRESS && PORT_NO) || (isset($_SESSION['relayboard']['ip_addres']) && $_SESSION['relayboard']['ip_addres'] && isset($_SESSION['relayboard']['port_no']) && $_SESSION['relayboard']['port_no'] )){	
	header('Location: home.php');
	exit;
}

if($sTask == 'Submit'){
	if($sIpAddress){
		$_SESSION['relayboard']['ip_addres'] = $sIpAddress;
		if($sPortNo){
			$_SESSION['relayboard']['port_no'] = $sPortNo;
			header('Location: home.php');
			exit;
		}else{
			$sErrMsg = 'Please enter port no.';
		}
	}else{
		$sErrMsg = 'Please enter ip address.';
	}
}

#OUTPUT
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<title>WELCOME | RLB CONTROLLER</title>
		
		<!--<link href="css/styles.css" rel="stylesheet" type="text/css" />-->
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
		
		<!--[if lt IE 9]>
		  <script src="js/html5shiv.min.js"></script>
		  <script src="js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body >
		<div class="panel panel-primary">
		  <div class="panel-heading">
			<h2 class="panel-title">Relay Board Configuration</h2>
		  </div>
		</div>
		<div class="container" >
			<form method="post" action="" name="frm_login" id="frm_login"  >
			<?php if($sErrMsg){ ?>
				<div class="alert alert-danger"><?php echo $sErrMsg; ?></div>
			<?php } ?>
			  <div class="form-group">
				<label for="ip_address">IP ADDRESS:</label>
				<input type="input" class="form-control" id="ip_address" placeholder="Enter ip address" name="ip_address" value="<?php echo $sIpAddress;?>" >
			  </div>
			  <div class="form-group">
				<label for="port_no">PORT NO:</label>
				<input type="input" class="form-control" id="port_no" placeholder="Enter port no" name="port_no"  value="<?php echo $sPortNo;?>" >
			  </div>
			  <input type="submit" class="btn btn-lg btn-primary btn-block" name="btn_submit" id="btn_submit" value="Submit" />
			</form>
		</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>