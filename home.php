<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: RelayBoard views
**/

#SETUP
session_start();
include("include/functions.php");

#INPUT
$sTask = isset($_REQUEST['btn_submit']) ? $_REQUEST['btn_submit'] : '' ;
$sIpAddress = isset($_SESSION['relayboard']['ip_addres']) ? $_SESSION['relayboard']['ip_addres'] : '' ;
$aRelayName = isset($_REQUEST['relayname']) ? $_REQUEST['relayname'] : '' ;
$aRelayStatus = isset($_REQUEST['relaystatus']) ? $_REQUEST['relaystatus'] : '' ;
$aValveName = isset($_REQUEST['valvename']) ? $_REQUEST['valvename'] : '' ;
$aValveStatus = isset($_REQUEST['valvestatus']) ? $_REQUEST['valvestatus'] : '' ;
$aPowercenterName = isset($_REQUEST['powercenter']) ? $_REQUEST['powercenter'] : '' ;
$aPowercenterStatus = isset($_REQUEST['powercenterstatus']) ? $_REQUEST['powercenterstatus'] : '' ;

$iMode = isset($_REQUEST['relay_mode']) ? $_REQUEST['relay_mode'] : '' ;
$sMTask = isset($_REQUEST['task']) ? $_REQUEST['task'] : '' ;
$sErrMsg = '';
$aModes = array();

#PROCESS
//Check for relay ip address if not then redirect to configure
if(!$sIpAddress && IP_ADDRESS == ''){
	header('Location: index.php');
	exit;
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

//print_r($sResponse);
//print_r($aTime);

//change mode of the relay board
if( $sMTask == 'change_mode' && $iMode){
	//Set all mode inactive
	$sSqlChange = "UPDATE rlb_modes SET mode_status = '0' ";
	$rChange = mysql_query($sSqlChange) or die('ERR: @sSqlChange=> '.mysql_error());
	
	//Set mode active
	$sSqlActive = "UPDATE rlb_modes SET mode_status = '1' WHERE mode_id='".$iMode."' ";
	$rChangeActive = mysql_query($sSqlActive) or die('ERR: @sSqlActive=> '.mysql_error());	
	
	if($iMode == 3 || $iMode == 1){ //1-auto, 2-manual, 3-timeout
		//off all relays
		$sRelayNewResp = str_replace('1','0',$sRelays);
		onoff_rlb_relay($sRelayNewResp);
		
		//off all valves
		$sValveNewResp = str_replace(array('1','2'), '0', $sValves);
		onoff_rlb_valve($sValveNewResp);		
	}
	header('Location: home.php');exit;
}else{
	//get list of relay modes.
	$sSql = "SELECT * FROM rlb_modes ";
	$rResult = mysql_query($sSql) or die('ERR: @sSql=> '.mysql_error());
	$iCnt = mysql_num_rows($rResult);
	while($aRow = mysql_fetch_assoc($rResult)){
		$aModes[] = $aRow;
		if($aRow['mode_status']){
			$iMode = $aRow['mode_id'];
		}
	}
}

//Change Status of Relay i.e. on off them
if(is_array($aRelayName) && is_array($aRelayStatus)){
	$sRelayName = $aRelayName[0];
	$iRelayStatus = $aRelayStatus[0];
	/* if($iRelayStatus == 0 ){
		$iRelayNewSatus = 1;
	}else if($iRelayStatus == 1 ){
		$iRelayNewSatus = 0;
	} */
	$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
	//echo '<br/>@sRelayNewResp=> ';
	//echo $sRelayNewResp;
	if($iMode == 2){
		onoff_rlb_relay($sRelayNewResp);
		header('Location: home.php');exit;
	}else{
		$sErrMsg = 'You can perform this operation in manual mode only.';
	}
}

//Change Status of valve i.e. on off them 0 <=> 1 <=> 2 
if(is_array($aValveName) && is_array($aValveStatus)){
	$sValveName = $aValveName[0];
	$iValveStatus = $aValveStatus[0];
	/* if($iRelayStatus == 0 ){
		$iRelayNewSatus = 1;
	}else if($iRelayStatus == 1 ){
		$iRelayNewSatus = 0;
	} */
	$sValveNewResp = replace_return($sValves, $iValveStatus, $sValveName );
	//echo '<br/>@sRelayNewResp=> ';
	//echo $sRelayNewResp;
	if($iMode == 2){
		onoff_rlb_valve($sValveNewResp);
		header('Location: home.php');exit;
	}else{
		$sErrMsg = 'You can perform this operation in manual mode only.';
	}
}

//Change Status of powercenter i.e. on off them
if(is_array($aPowercenterName) && is_array($aPowercenterStatus)){
	$sPowercenterName = $aPowercenterName[0];
	$iPowercenterStatus = $aPowercenterStatus[0];
	/* if($iPowercenterStatus == 0 ){
		$iPowercenterNewSatus = 1;
	}else if($iPowercenterStatus == 1 ){
		$iPowercenterNewSatus = 0;
	} */
	$sPowercenterNewResp = replace_return($sPowercenter, $iPowercenterStatus, $sPowercenterName );
	//echo '<br/>@sPowercenterNewResp=> ';
	//echo $sPowercenterNewResp;
	if($iMode == 2){
		onoff_rlb_powercenter($sPowercenterNewResp);
		header('Location: home.php');exit;
	}else{
		$sErrMsg = 'You can perform this operation in manual mode only.';
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
		
		<title>HOME | RLB CONTROLLER</title>
				
		<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="css/wcss.css" rel="stylesheet" type="text/css" />
		
		<!--[if lt IE 9]>
		  <script src="js/html5shiv.min.js"></script>
		  <script src="js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body >
		<div class="panel panel-primary">
		  <div class="panel-heading">
			<h2 class="panel-title">RLB Valves & Relays</h2>
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
		<?php if($sErrMsg){ ?>
			<div class="alert alert-danger"><?php echo $sErrMsg; ?></div>
		<?php } ?>
			<div class="table-responsive">
				<table class="table">
					<tr>
						<td >
						</td>
						<td  ></td>
						<td>
						<?php
							//$aModes = array('1' => 'Auto', '2' => 'Manual', '3' => 'Time-Out' );
							$sSelectModeOpt = '<option value="0" >Please Select Mode</option>';
							foreach($aModes as $aMode){
								$sSelectModeOpt .= '<option value="'.$aMode['mode_id'].'"';
								if($aMode['mode_status']){
									$sSelectModeOpt .= ' SELECTED ';
								}
								$sSelectModeOpt .= '>'.$aMode['mode_name'].'</option>';
							}
							echo <<<EOF
							<form name="frmmode" id="frmmode" action="" method="post" >
								<select name="relay_mode" id="relay_mode" onchange="document.getElementById('frmmode').submit();"  class="form-control" >
									$sSelectModeOpt
								</select>
								<input type="hidden" name="task" value="change_mode" />
							</form>
EOF;
						?>
						</td>
					</tr>
					<?php 
					if($iRelayCount){ 
						echo <<<EOF
						<tr>
							<th class="station_name" valign="top" >
								Relays List
							</th>
							<th></th>
							<th></th>
						</tr>
EOF;
						for ($i=0;$i < $iRelayCount; $i++){
							$iRelayVal = $sRelays[$i];
							$iRelayNewValSb = 1;
							if($iRelayVal == 1){
								$iRelayNewValSb = 0;
							}
							$sRelayVal = 'off';
							if($iRelayVal)
								$sRelayVal = 'on';
							$sRelayNameDb = get_device_name(1, $i);
							echo <<<EOF
							<tr>
								<td class="station_name" valign="top" >
									<a href="rename.php?dn=$i&dt=1" >$sRelayNameDb</a>
								</td>
								<td class="station_running" colspan="2" >
									<form action="" method="post" name="frmstation$i" id="frmstation$i" >

									<table>
										<tr>
											<td>
												<button id="r$i" class="toggle manual narrow $sRelayVal" onclick="document.getElementById('frmstation$i').submit();" ><span class="toggleleft">On</span><span class="togglesep">&nbsp;</span><span class="toggleright">Off</span></button>
												<input type="hidden" name="relayname[]" value="$i" />
												<input type="hidden" name="relaystatus[]" value="$iRelayNewValSb" />
											</td>
											<td>
												&nbsp;<a href="programs.php?rn=$i" title="Set Program for Relay $i" class="btn btn-primary btn-xs">Programs</a>
											</td>
											<!--<td>
												<input name="endtime[]" value="" />
											</td>-->
										</tr>
									</table>
									</form>
								<!--</td>
								<td>-->

								</td>
							</tr>
EOF;
						}
					}
					?>
					<tr>
						<td valign="top" colspan="3" >&nbsp;
						</td>
					</tr>
					<?php 
					if($iValveCount){ 
						echo <<<EOF
						<tr>
							<th class="station_name" valign="top" >
								Valve List
							</th>
							<th class="station_name_head" valign="top" > Set Value to 0/1 </th>
							<th class="station_name_head" valign="top" > Set Value to 1/2</th>
						</tr>
EOF;
						for ($i=0; $i < $iValveCount; $i++){
							$iValveVal = $sValves[$i];
							$iValveValNewVal1 = 1;
							$iValveValNewVal2 = 2;
							if($iValveVal == 1){
								$iValveValNewVal1 = 0;
							}
							if($iValveVal == 2){
								$iValveValNewVal2 = 1;
							}
							$sValveVal1 = 'off';
							$sValveVal2 = 'off';
							if($iValveVal == 1)
								$sValveVal1 = 'on';
							if($iValveVal == 2)
								$sValveVal2 = 'on';
							$sValveNameDb = get_device_name(2, $i);
							echo <<<EOF
							<tr>
								<td class="station_name" valign="top" >
									<a href="rename.php?dn=$i&dt=2" >$sValveNameDb</a>
								</td>
								<td class="station_running" >
									<form action="" method="post" name="frmvalvei$i" id="frmvalvei$i" >
									<table>
										<tr>
											<td>
												<button id="bti$i" class="toggle manual narrow $sValveVal1" onclick="document.getElementById('frmvalvei$i').submit();" ><span class="toggleleft">On</span><span class="togglesep">&nbsp;</span><span class="toggleright">Off</span></button>
												<input type="hidden" name="valvename[]" value="$i" />
												<input type="hidden" name="valvestatus[]" value="$iValveValNewVal1" />
											</td>
										</tr>
									</table>
									</form>
								</td>
								<td>
									<form action="" method="post" name="frmvalves$i" id="frmvalves$i" >
									<table>
										<tr>
											<td>
												<button id="bts$i" class="toggle manual narrow $sValveVal2" onclick="document.getElementById('frmvalves$i').submit();" ><span class="toggleleft">On</span><span class="togglesep">&nbsp;</span><span class="toggleright">Off</span></button>
												<input type="hidden" name="valvename[]" value="$i" />
												<input type="hidden" name="valvestatus[]" value="$iValveValNewVal2" />
											</td>
										</tr>
									</table>
									</form>
								</td>
							</tr>
EOF;
						}
					}
					?>
					<tr>
						<td valign="top" colspan="3" >&nbsp;
						</td>
					</tr>
					
					
					<?php 
					if($iPowercenterCount){ 
						echo <<<EOF
						<tr>
							<th class="station_name" valign="top" >
								Power center List
							</th>
							<th></th>
							<th></th>
						</tr>
EOF;
						for ($i=0;$i < $iPowercenterCount; $i++){
							$iPowercenterVal = $sPowercenter[$i];
							$iPowercenterNewValSb = 1;
							if($iPowercenterVal == 1){
								$iPowercenterNewValSb = 0;
							}
							$sPowercenterVal = 'off';
							if($iPowercenterVal)
								$sPowercenterVal = 'on';
							$sPowercenterNameDb = get_device_name(3, $i);
							echo <<<EOF
							<tr>
								<td class="station_name" valign="top" >
									<a href="rename.php?dn=$i&dt=3" >$sPowercenterNameDb</a>
								</td>
								<td class="station_running" colspan="2" >
									<form action="" method="post" name="frmpwrcntr$i" id="frmpwrcntr$i" >

									<table>
										<tr>
											<td>
												<button id="r$i" class="toggle manual narrow $sPowercenterVal" onclick="document.getElementById('frmpwrcntr$i').submit();" ><span class="toggleleft">On</span><span class="togglesep">&nbsp;</span><span class="toggleright">Off</span></button>
												<input type="hidden" name="powercenter[]" value="$i" />
												<input type="hidden" name="powercenterstatus[]" value="$iPowercenterNewValSb" />
											</td>
											<!--<td>
												&nbsp;<a href="programs.php?rn=$i" title="Set Program for Relay $i" class="btn btn-primary btn-xs">Programs</a>
											</td>
											<td>
												<input name="endtime[]" value="" />
											</td>-->
										</tr>
									</table>
									</form>
								<!--</td>
								<td>-->

								</td>
							</tr>
EOF;
						}
					}
					?>
					<tr>
						<td valign="top" colspan="3" >&nbsp;
						</td>
					</tr>
					
					
				</table>
			</div>
		  </div>
		</div>

		<div class="wrapper" >
			<div class="stationsdiv"> 

			</div>
		</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	</body>
</html>