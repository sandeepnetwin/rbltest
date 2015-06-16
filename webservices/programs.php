<?php
/**
* @Programmer: SMW
* @Created: 26 May 2015
* @Modified: 
* @Description: Webservice for program add/edit/delete
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUT
$sRelayNo = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;
$iRelayProgId = isset($_REQUEST['rpn']) ? $_REQUEST['rpn'] : '' ;

$sProgNameAE = isset($_REQUEST['prog_name']) ? $_REQUEST['prog_name'] : '' ;
$iRelayNumAE = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : '' ;
$iProgTypeAE = isset($_REQUEST['prog_type']) ? $_REQUEST['prog_type'] : '' ;
$sProgDaysAE = isset($_REQUEST['prog_days']) ? implode(',', $_REQUEST['prog_days']) : '' ;
$sStartTimeAE = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : '' ;
$sEndTimeAE = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : '' ;
$sPTask = isset($_REQUEST['task']) ? $_REQUEST['task'] : '' ;

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;

#PROCESS
//if($_REQUEST){echo '<pre>';print_r($_REQUEST);die;}

$sResponse = get_rlb_status();
$sValves = $sResponse['valves'];
$sRelays = $sResponse['relay'];
$iRelayCount = strlen($sRelays);
$iValveCount = strlen($sValves);

if(in_array($sPTask, array('Add', 'Update', 'Delete'))){
	if( $sRelayNo > ($iRelayCount-1) || $sRelayNo < 0){
		$aResult['msg'] = "Invalid relay number.";
	}else{
		$sProgDaysAE = ($sProgDaysAE) ? $sProgDaysAE : 0;
		$sCreatedDate = date('Y-m-d');
		$sModifiedDate = $sCreatedDate;
		if($sProgNameAE && $iRelayNumAE!='' && $iProgTypeAE && $sStartTimeAE && $sEndTimeAE){
			if($sPTask == 'Add'){
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
					$aResult['status'] = 1;
				}
			}else{
				if($sPTask == 'Update' && $iRelayProgId){
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
					$aResult['status'] = 1;
					
				}else{
					$aResult['msg'] = "Program number not present.";
				}
			}
		}elseif($sPTask == 'Delete'){
			if($iRelayProgId){
				$sSqlDel = "UPDATE rlb_relay_prog SET relay_prog_delete='1' WHERE relay_prog_id='".$iRelayProgId."'";
				$rResultDel = mysql_query($sSqlDel) or die('ERR: @sSqlDel=> '.mysql_error());
				$aResult['status'] = 1;
			}else{
				$aResult['msg'] = "Program number not present.";
			}
		}else{
			$aResult['msg'] = "All fields are mandatory";
		}
	}
}else{
	$aResult['msg'] = "Invalid operation.";
}
$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);
