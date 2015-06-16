<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: RelayBoard Configuration
**/

#SETUP
session_start();
include("include/functions.php");

#INPUT
$sTask = isset($_REQUEST['btn_submit']) ? $_REQUEST['btn_submit'] : '' ;
$sIpAddress = isset($_SESSION['relayboard']['ip_addres']) ? $_SESSION['relayboard']['ip_addres'] : '' ;
$sRelayNo = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;
$iRelayProgId = isset($_REQUEST['rpn']) ? $_REQUEST['rpn'] : '' ;

$sProgNameAE = isset($_REQUEST['prog_name']) ? $_REQUEST['prog_name'] : '' ;
$iRelayNumAE = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;
$iProgTypeAE = isset($_REQUEST['prog_type']) ? $_REQUEST['prog_type'] : '' ;
$sProgDaysAE = isset($_REQUEST['prog_days']) ? implode(',', $_REQUEST['prog_days']) : '' ;
$sStartTimeAE = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : '' ;
$sEndTimeAE = isset($_REQUEST['end_name']) ? $_REQUEST['end_name'] : '' ;
$sPTask = isset($_REQUEST['task']) ? $_REQUEST['task'] : '' ;

$sErrMsg = ''; $sSuccMsg = ''; $sTblHTML = '';
$aResults = array();
$aDays = array( 0 => 'All', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
$aTypes = array(1 => 'Daily', 2 => 'Weekly' );
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
$sTime = $sResponse['time'];
$aTime = explode(':',$sTime);
$iRelayCount = strlen($sRelays);
$iValveCount = strlen($sValves);

if( $sRelayNo > ($iRelayCount-1) || $sRelayNo < 0){
	die('Invalid relay number.');
}

//get list of all program for selected relay.
$sSql = "SELECT * FROM rlb_relay_prog WHERE relay_number='".$sRelayNo."' AND relay_prog_delete='0' ORDER BY relay_prog_created_date ASC";
$rResult = mysql_query($sSql) or die('ERR: @sSql=> '.mysql_error());
$iCnt = mysql_num_rows($rResult);
while($aRow = mysql_fetch_assoc($rResult)){
	$aResults[] = $aRow;
}


if($sTask){
	$sProgDaysAE = ($sProgDaysAE) ? $sProgDaysAE : 0;
	$sCreatedDate = date('Y-m-d');
	$sModifiedDate = $sCreatedDate;
	if($sProgNameAE && $iRelayNumAE!='' && $iProgTypeAE && $sStartTimeAE && $sEndTimeAE){
		if($sTask == 'Add'){
			$sSqlAP = "INSERT INTO rlb_relay_prog SET 
						relay_prog_name='".$sProgNameAE."',
						relay_number='".$iRelayNumAE."',
						relay_prog_type='".$iProgTypeAE."',
						relay_prog_days='".$sProgDaysAE."',
						relay_start_time='".$sStartTimeAE."',
						relay_end_time='".$sEndTimeAE."',
						relay_prog_created_date='".$sCreatedDate."',
						relay_prog_modified_date='".$sModifiedDate."'";
			$rResultAP = mysql_query($sSqlAP) or die('ERR: @sSqlAP=> '.mysql_error());
			$iInsertId = mysql_insert_id();
			if($iInsertId){
				$_SESSION['succ_msg'] = 'Relay program added successfully.';
				header('Location: programs.php?rn='.$iRelayNumAE );
				die;
			}
		}elseif($sTask == 'Update'){
			$sSqlEP = "UPDATE rlb_relay_prog SET 
						relay_prog_name='".$sProgNameAE."',
						relay_number='".$iRelayNumAE."',
						relay_prog_type='".$iProgTypeAE."',
						relay_prog_days='".$sProgDaysAE."',
						relay_start_time='".$sStartTimeAE."',
						relay_end_time='".$sEndTimeAE."',
						relay_prog_created_date='".$sCreatedDate."',
						relay_prog_modified_date='".$sModifiedDate."'
						WHERE relay_prog_id='".$iRelayProgId."'";
			$rResultEP = mysql_query($sSqlEP) or die('ERR: @sSqlEP=> '.mysql_error());
			$_SESSION['succ_msg'] = 'Relay program updated successfully.';
			header('Location: programs.php?rn='.$iRelayNumAE );
			die;
		}
	}else{
		$sErrMsg = 'All fields are mandatory.';
	}
}else{
	if($iRelayProgId){
		$sSqlEdit = "SELECT * FROM rlb_relay_prog WHERE relay_prog_id='".$iRelayProgId."'";
		$rResultEdit = mysql_query($sSqlEdit) or die('ERR: @sSqlEdit=> '.mysql_error());
		$iCnt = mysql_num_rows($rResultEdit);
		if($iCnt){
			$aRowEdit = mysql_fetch_assoc($rResultEdit);
			$sProgNameAE = $aRowEdit['relay_prog_name'];
			$iRelayNumAE = $aRowEdit['relay_number'];
			$iProgTypeAE = $aRowEdit['relay_prog_type'];
			$sProgDaysAE = $aRowEdit['relay_prog_days'];
			$sStartTimeAE = $aRowEdit['relay_start_time'];
			$sEndTimeAE = $aRowEdit['relay_end_time'];
		}
		$sSubmitBtn = 'Update';
	}
}

if($sPTask == 'delprog' && $iRelayProgId){
	$sSqlDel = "UPDATE rlb_relay_prog SET relay_prog_delete='1' WHERE relay_prog_id='".$iRelayProgId."'";
	$rResultDel = mysql_query($sSqlDel) or die('ERR: @sSqlDel=> '.mysql_error());
	$_SESSION['succ_msg'] = 'Relay program deleted successfully.';
	header('Location: programs.php?rn='.$iRelayNumAE );
	die;
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
					<label for="prog_name">Program Name:</label>
					<input type="input" class="form-control" id="prog_name" placeholder="Enter program name" name="prog_name" value="<?php echo $sProgNameAE;?>"  required />
				  </div>
				  <div class="form-group">
					<label for="prog_type">Program Type:</label>
					<div>
						<label class="radio-inline">
							<input type="radio" name="prog_type" id="prog_type_daily" value="1" <?php if($iProgTypeAE == 1 || !$iProgTypeAE){ echo ' checked="checked" '; } ?> onclick="dispalyDays(1);" />Daily
						</label>
						<label class="radio-inline">
							<input type="radio" name="prog_type" id="prog_type_weekly" value="2" <?php if($iProgTypeAE == 2){ echo ' checked="checked" '; } ?> onclick="dispalyDays(2);" />Weekly
						</label>
					</div>
				  </div>
				<?php
					$aDaysEdit = explode(',',$sProgDaysAE);
				?>
				  <div class="form-group" id="tr_prog_days" <?php if($iProgTypeAE == 1 || !$iProgTypeAE){ echo 'style="display:none;"';} ?> >
					<label for="prog_days">Program Days:</label>
					<div class="checkbox">
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="1" <?php if(in_array(1, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Monday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="2" <?php if(in_array(2, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Tuesday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="3" <?php if(in_array(3, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Wednesday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="4" <?php if(in_array(4, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Thursday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="5" <?php if(in_array(5, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Friday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="6" <?php if(in_array(6, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Saturday
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" name="prog_days[]" value="7" <?php if(in_array(7, $aDaysEdit)){ echo ' checked="checked" '; } ?> />Sunday
						</label>
					</div>
				  </div>
				  <div class="form-group">
					<label for="start_time">Start Time:</label>
					<input type="input" class="form-control timepicker" id="start_time" placeholder="Enter start time" name="start_time" value="<?php echo $sStartTimeAE;?>" required  />
				  </div>
				  <div class="form-group">
					<label for="end_name">End Time:</label>
					<input type="input" class="form-control timepicker" id="end_name" placeholder="Enter program name" name="end_name" value="<?php echo $sEndTimeAE;?>" required />
				  </div>
				<input type="submit" name="btn_submit" id="btn_submit" value="<?php echo $sSubmitBtn; ?>" class="btn btn-lg btn-primary" />
				<input type="hidden" name="rn" value="<?php echo $sRelayNo; ?>" />
				<input type="hidden" name="rpn" value="<?php echo $iRelayProgId; ?>" />
				</form>
			</div><br/>

			<div class="table-responsive">
				<?php
				if($iCnt){
					$sTblHTML .= <<<EOF
					<table border="1" class="table">
						<tr>
							<th>
								Program Name
							</th>
							<th>
								Relay Number
							</th>
							<th>
								Program Type
							</th>
							<th>
								Program Days
							</th>
							<th>
								Start Time
							</th>
							<th>
								End Time
							</th>
							<th>
								Edit
							</th>
							<th>
								Delete
							</th>
						</tr>
EOF;
					foreach($aResults as $aResult){
						$iProgId = $aResult['relay_prog_id'];
						$sProgName = $aResult['relay_prog_name'];
						$sProgType = $aTypes[$aResult['relay_prog_type']];
						$aProgDays = switch_arrays($aDays, explode(',',$aResult['relay_prog_days']));
						$sProgDays = implode(',',$aProgDays);						
						$sProgStartTime = $aResult['relay_start_time'];
						$sProgEndTime = $aResult['relay_end_time'];
						$sTblHTML .= <<<EOF
						<tr>
							<td>
								$sProgName
							</td>
							<td>
								$sRelayNo
							</td>
							<td>
								$sProgType
							</td>
							<td>
								$sProgDays
							</td>
							<td>
								$sProgStartTime
							</td>
							<td>
								$sProgEndTime
							</td>
							<td>
								<a href="programs.php?rn=$sRelayNo&rpn=$iProgId" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>								
							</td>
							<td>
								<a href="programs.php?rn=$sRelayNo&rpn=$iProgId&task=delprog" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
							</td>
						</tr>
EOF;
					}
					
					$sTblHTML .= <<<EOF
					</table>
EOF;
				}
				echo $sTblHTML;
				?>
			</div>
		</div>
		</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.timepicker.min.js"></script>
	<script type="text/javascript">
		function dispalyDays(iVal){
			if(iVal==1){
				var dayscheck = document.getElementsByName('prog_days[]');
				for(i=0; i < dayscheck.length; i++){
					dayscheck[i].checked = false;
				}
				document.getElementById('tr_prog_days').style.display = 'none';
			}
			if(iVal==2){
				document.getElementById('tr_prog_days').style.display = 'block';
			}
		}
		$(document).ready(function(){
			$('input.timepicker').timepicker({timeFormat: 'HH:mm:ss'});
		});
	</script>
	</body>
</html>