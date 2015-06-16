<?php
/**
* @Programmer: SMW
* @Created: 10 April 2015
* @Modified: 
* @Description: Cron Programs
**/

#SETUP
session_start();
include("functions.php");

$sSql = "SELECT * FROM rlb_relay_prog WHERE relay_prog_delete='0'";
$rResult = mysql_query($sSql) or die('ERR: @sSql => '.mysql_error());
$iCnt = mysql_num_rows($rResult);
if($iCnt){
	//echo '<pre>';
	while($aRow = mysql_fetch_assoc($rResult)){
		$sResponse = get_rlb_status();
		$iMode = get_current_mode();
		$sRelayName = $aRow['relay_number'];
		$iProgId = $aRow['relay_prog_id'];
		$sDayret = $sResponse['day'];
		$sValves = $sResponse['valves'];
		$sRelays = $sResponse['relay'];		
		$sTime = $sResponse['time'];
		$aTime = explode(':',$sTime);
		$iRelayCount = strlen($sRelays);
		$iValveCount = strlen($sValves);
		//print_r($sResponse);
		//print_r($aRow);
		
		//Daily program
		if($aRow['relay_prog_type'] == 1){
			//on relay
			if($sTime >= $aRow['relay_start_time'] && $sTime < $aRow['relay_end_time'] && $aRow['relay_prog_active']==0){
				if($iMode == 1){
					$iRelayStatus = 1;
					$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
					onoff_rlb_relay($sRelayNewResp);
					update_prog_status($iProgId, 1);
				}
			}//off relay
			elseif($sTime >= $aRow['relay_end_time'] && $aRow['relay_prog_active'] == 1){
				$iRelayStatus = 0;
				$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
				onoff_rlb_relay($sRelayNewResp);
				update_prog_status($iProgId, 0);
			}
		}
		
		//Weekly program
		if($aRow['relay_prog_type'] == 2){
			$sDays = str_replace('7','0', $aRow['relay_prog_days']);
			$aDays = explode(',',$aRow['relay_prog_days']);
			if(in_array($sDayret, $aDays)){
				//on relay
				if($sTime >= $aRow['relay_start_time'] && $sTime < $aRow['relay_end_time'] && $aRow['relay_prog_active']==0){
					if($iMode == 1){
						$iRelayStatus = 1;
						$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
						onoff_rlb_relay($sRelayNewResp);
						update_prog_status($iProgId, 1);
					}
				}//off relay
				elseif($sTime >= $aRow['relay_end_time'] && $aRow['relay_prog_active'] == 1){
					$iRelayStatus = 0;
					$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
					onoff_rlb_relay($sRelayNewResp);
					update_prog_status($iProgId, 0);
				}
			}			
		}
	}
}

//die('@62');