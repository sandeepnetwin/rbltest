<?php
/**
* @Programmer: SMW
* @Created: 26 May 2015
* @Modified: 
* @Description: Webservice to get programs
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUTS
$sRelayNo = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;

#PROCESS
if($sRelayNo != ''){
	$sSql = "SELECT * FROM rlb_relay_prog WHERE relay_prog_delete='0' AND relay_number='".$sRelayNo."' ORDER BY relay_number ";

	$rResult = mysql_query($sSql) or die('ERR: @sSql => '.mysql_error());
	$iCnt = mysql_num_rows($rResult);
	if($iCnt){
		while($aRow = mysql_fetch_assoc($rResult)){
			$aData[] = array_map('utf8_encode', $aRow);
		}
		$aResult['status'] = 1;
	}else{
		$aResult['msg'] = "No records found.";
	}
}else{
	$aResult['msg'] = "Invalid relay number.";
}

$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);