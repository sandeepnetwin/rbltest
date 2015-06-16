<?php
/**
* @Programmer: SMW
* @Created: 14 May 2015
* @Modified: 
* @Description: Cron for check response from relay board if hit by manually then change mode
**/

#SETUP
session_start();
include("functions.php");

//get current active mode
$iMode = get_current_mode();

//check for not manual mode
if($iMode != 2){

	//check status for relay hit by manually
	$sResponse = get_rlb_status();
	
	//condition for relay hit by manually if hit then change mode
	if(isset($sResponse['push']) && strpos($sResponse['push'], '1') !== false){
	
		//Set all mode inactive
		$sSqlChange = "UPDATE rlb_modes SET mode_status = '0' ";
		$rChange = mysql_query($sSqlChange) or die('ERR: @sSqlChange=> '.mysql_error());
		
		//Set mode active manual mode
		$sSqlActive = "UPDATE rlb_modes SET mode_status = '1' WHERE mode_id='2' ";
		$rChangeActive = mysql_query($sSqlActive) or die('ERR: @sSqlActive=> '.mysql_error());
	}
}
