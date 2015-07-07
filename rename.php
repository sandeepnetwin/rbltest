<?php
/**
* @Programmer: SMW
* @Created: 23 Jun 2015
* @Modified: 
* @Description: RelayBoard Rename relay, valve, powercenters
**/

#SETUP
session_start();
include("include/functions.php");

#INPUT
$sTask = isset($_REQUEST['btn_submit']) ? $_REQUEST['btn_submit'] : '' ;
$sIpAddress = isset($_SESSION['relayboard']['ip_addres']) ? $_SESSION['relayboard']['ip_addres'] : '' ;

$sDeviceNo = isset($_REQUEST['dn']) ? $_REQUEST['dn'] : '' ;

$sDeviceNameAE = isset($_REQUEST['device_name']) ? mysql_real_escape_string($_REQUEST['device_name']) : '' ;
$iDeviceNum = isset($_REQUEST['dn']) ? $_REQUEST['dn'] : '' ;
$iDeviceType = isset($_REQUEST['dt']) ? $_REQUEST['dt'] : '' ;

$sErrMsg = ''; $sSuccMsg = ''; $sTblHTML = '';
$aResults = array();
$aDeviceType = array(1, 2, 3);
$aDeviceTypeName = array( 1 => 'Relay', 2 => 'Valve' , 3 => 'Powercenter');
$aTbl = array('1' => 'rlb_relays', '2' => 'rlb_valves', '3' => 'rlb_powercenters');
$aFldWhere = array('1' => 'relay_number', '2' => 'valve_number', '3' => 'powercenter_number');
$aFldSel = array('1' => 'relay_name', '2' => 'valve_name', '3' => 'powercenter_name');

$sSubmitBtn = 'Add';

#PROCESS
//Check for relay ip address if not then redirect to configure
if(!$sIpAddress && IP_ADDRESS == ''){
	header('Location: index.php');
	exit;
}

if(isset($_SESSION['succ_msg']) && $_SESSION['succ_msg']){
	$sSuccMsg = $_SESSION['succ_msg'];
	$_SESSION['succ_msg'] = '';
}
//if($_REQUEST){echo '<pre>';print_r($_REQUEST);die;}

$sResponse = get_rlb_status();
$sValves = $sResponse['valves'];
$sRelays = $sResponse['relay'];
$sPowercenter = $sResponse['powercenter'];
$sTime = $sResponse['time'];
$aTime = explode(':',$sTime);
$iRelayCount = strlen($sRelays);
$iValveCount = strlen($sValves);
$iPowercenterCount = strlen($sPowercenter);

if( $iDeviceType == 1 && ($sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)){
	die('Invalid '. $aDeviceTypeName[$iDeviceType] .' number.');
}

if( $iDeviceType == 2 && ($sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)){
	die('Invalid '. $aDeviceTypeName[$iDeviceType] .' number.');
}

if( $iDeviceType == 3 && ($sDeviceNo > ($iPowercenterCount-1) || $sDeviceNo < 0)){
	die('Invalid '. $aDeviceTypeName[$iDeviceType] .' number.');
}



if($sTask && in_array($iDeviceType, $aDeviceType)){
	if($sDeviceNameAE){
		if($sTask == 'Add'){
			echo $sSqlAP = "INSERT INTO ".  $aTbl[$iDeviceType] ." SET 
						". $aFldSel[$iDeviceType] ." ='".$sDeviceNameAE."',
						". $aFldWhere[$iDeviceType] ." ='".$iDeviceNum."'
						";
			$rResultAP = mysql_query($sSqlAP) or die('ERR: @sSqlAP=> '.mysql_error());
			$iInsertId = mysql_insert_id();
			if($iInsertId){
				$_SESSION['succ_msg'] = $aDeviceTypeName[$iDeviceType].' name added successfully.';
				header('Location: home.php');
				die;
			}
		}elseif($sTask == 'Update'){
			$sSqlEP = "UPDATE ".  $aTbl[$iDeviceType] ." SET 
						". $aFldSel[$iDeviceType] ." ='".$sDeviceNameAE."'
						WHERE ". $aFldWhere[$iDeviceType] ." ='".$iDeviceNum."'";
			$rResultEP = mysql_query($sSqlEP) or die('ERR: @sSqlEP=> '.mysql_error());
			$_SESSION['succ_msg'] = $aDeviceTypeName[$iDeviceType].' name updated successfully.';
			header('Location: home.php');
			die;
		}
	}else{
		$sErrMsg = 'All fields are mandatory.';
	}
}else{
	if(is_numeric($iDeviceNum) && in_array($iDeviceType, $aDeviceType)){
		$sSqlEdit = "SELECT * FROM ". $aTbl[$iDeviceType] ." WHERE ". $aFldWhere[$iDeviceType] ." ='".$iDeviceNum."'";
		$rResultEdit = mysql_query($sSqlEdit) or die('ERR: @sSqlEdit=> '.mysql_error());
		$iCnt = mysql_num_rows($rResultEdit);
		if($iCnt){
			$aRowEdit = mysql_fetch_assoc($rResultEdit);
			$sDeviceNameAE = stripslashes($aRowEdit[$aFldSel[$iDeviceType]]);
			$sSubmitBtn = 'Update';
		}
	}
}

#OUTPUT
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		
		<title>PROGRAMS | RLB CONTROLLER</title>
				
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="css/wcss.css" rel="stylesheet" type="text/css" />
		<link href="css/jquery.timepicker.min.css" rel="stylesheet" type="text/css" />		
		<!--[if lt IE 9]>
		  <script src="js/html5shiv.min.js"></script>
		  <script src="js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="rlb_programs" >
		<div class="panel panel-primary">
		  <div class="panel-heading">
			<h2 class="panel-title">Relay Board Configuration</h2>
		  </div>
		  <div class="panel-body">
			<div class="clearfix" >
				<p id="deviceTime"><span class="hour"><?php echo $aTime['0']; ?></span><span class="sep">:</span><span class="minute"><?php echo $aTime['1']; ?></span><span class="second">:<?php echo $aTime['2']; ?></span><span class="ampm"></span></p>
					<?php 
						if(isset($_SESSION['relayboard']['ip_addres']) && $_SESSION['relayboard']['ip_addres']){
							echo <<<EOF
							<p id="deviceDate"><a href="logout.php" >LOGOUT</a></p>
EOF;
						}
					?>
			</div>
			<div class="container" >
				<form action="" method="post" name="frmProgram" id="frmProgram" class="form-horizontal">
				<?php if($sErrMsg){ ?>
					<div class="alert alert-danger"><?php echo $sErrMsg; ?></div>
				<?php } ?>
				  <div class="form-group">
					<label for="device_name"><?php echo $aDeviceTypeName[$iDeviceType];?> Name:</label>
					<input type="input" class="form-control" id="device_name" placeholder="Enter <?php echo strtolower($aDeviceTypeName[$iDeviceType]);?> name" name="device_name" value="<?php echo $sDeviceNameAE;?>"  required />
				  </div>
				<input type="submit" name="btn_submit" id="btn_submit" value="<?php echo $sSubmitBtn; ?>" class="btn btn-lg btn-primary" />
				<input type="hidden" name="dn" value="<?php echo $sDeviceNo; ?>" />
				</form>
			</div><br/>
		</div>
		</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>