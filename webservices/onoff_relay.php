<?php
/**
* @Programmer: SMW
* @Created: 26 May 2015
* @Modified: 
* @Description: Webservice to on/off relays
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUTS
$sRelayNo = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;
$iRelayStatus = isset($_REQUEST['rs']) ? $_REQUEST['rs'] : '' ;

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;
$aRelayRV = array('0', '1'); //respective values of relays.

#PROCESS

//get current active mode
$iMode = get_current_mode();

//check for manual mode
if($iMode == 2){
	if($sRelayNo != '' && in_array($iRelayStatus, $aRelayRV)){
		$sResponse = get_rlb_status();
		$sRelays = $sResponse['relay'];
		$iRelayCount = strlen($sRelays);
		
		if( $sRelayNo > ($iRelayCount-1) || $sRelayNo < 0){
			$aResult['msg'] = "Invalid relay number.";
		}else{
			$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayNo );
			onoff_rlb_relay($sRelayNewResp);		
			$aResult['status'] = 1;
		}
	}else{
		$aResult['msg'] = "Invalid relay number Or relay status.";
	}
}else{
	$aResult['msg'] = "Invalid mode to perform this operation.";
}
$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);