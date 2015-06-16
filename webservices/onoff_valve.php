<?php
/**
* @Programmer: SMW
* @Created: 26 May 2015
* @Modified: 
* @Description: Webservice to on/off valve
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUTS
$sValveNo = isset($_REQUEST['vn']) ? $_REQUEST['vn'] : '' ;
$iValveStatus = isset($_REQUEST['vs']) ? $_REQUEST['vs'] : '' ; // 0, 1, 2

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;
$aValveRV = array('0', '1', '2'); //respective values of valve.

#PROCESS

//get current active mode
$iMode = get_current_mode();

//check for manual mode
if($iMode == 2){
	if($sValveNo != '' && in_array($iValveStatus, $aValveRV)){
		$sResponse = get_rlb_status();
		$sValves = $sResponse['valves'];
		$iValveCount = strlen($sValves);
		
		if( $sValveNo > ($iValveCount-1) || $sValveNo < 0){
			$aResult['msg'] = "Invalid valve number.";
		}else{
			$sValveNewResp = replace_return($sValves, $iValveStatus, $sValveNo );
			onoff_rlb_valve($sValveNewResp);		
			$aResult['status'] = 1;
		}
	}else{
		$aResult['msg'] = "Invalid valve number Or valve status.";
	}
}else{
	$aResult['msg'] = "Invalid mode to perform this operation.";
}
$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);