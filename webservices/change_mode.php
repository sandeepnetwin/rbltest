<?php
/**
* @Programmer: SMW
* @Created: 29 May 2015
* @Modified: 
* @Description: Webservice to set mode of relay board
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUTS
$iModeNo = isset($_REQUEST['mn']) ? $_REQUEST['mn'] : '' ; //1-Auto, 2-Manual, 3-Timeout
$sMTask = isset($_REQUEST['action']) ? $_REQUEST['action'] : '' ;

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;

#PROCESS

if($iModeNo && $sMTask == 'change_mode'){

	//get status of relay board
	$sResponse = get_rlb_status();
	$sValves = $sResponse['valves'];
	$sRelays = $sResponse['relay'];
	
	//Set all mode inactive
	$sSqlChange = "UPDATE rlb_modes SET mode_status = '0' ";
	$rChange = mysql_query($sSqlChange) or die('ERR: @sSqlChange=> '.mysql_error());
	
	//Set mode active
	$sSqlActive = "UPDATE rlb_modes SET mode_status = '1' WHERE mode_id='".$iModeNo."' ";
	$rChangeActive = mysql_query($sSqlActive) or die('ERR: @sSqlActive=> '.mysql_error());	
	
	if($iModeNo == 3 || $iModeNo == 1){ //1-auto, 2-manual, 3-timeout
		//off all relays
		$sRelayNewResp = str_replace('1','0',$sRelays);
		onoff_rlb_relay($sRelayNewResp);
		
		//off all valves
		$sValveNewResp = str_replace(array('1','2'), '0', $sValves);
		onoff_rlb_valve($sValveNewResp);
	}
	$aResult['status'] = 1;
}else{
	$aResult['msg'] = "Invalid mode number.";
}

$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);