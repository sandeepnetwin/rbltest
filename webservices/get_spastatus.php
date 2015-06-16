<?php
/**
* @Programmer: SMW
* @Created: 15 Jun 2015
* @Modified: 
* @Description: Webservice to get remote spa status
**/

#SETUP
session_start();
include("../include/functions.php");

#INPUTS

$aResult = array();
$aData = array();
$aResult['msg'] = "";
$aResult['status'] = 0;

#PROCESS
$sResponse = get_rlb_status();
$aData['remote_spa_ctrl_st'] = $sResponse['remote_spa_ctrl_st'];
$aData['pump_seq_0_st'] = $sResponse['pump_seq_0_st'];
$aData['pump_seq_1_st'] = $sResponse['pump_seq_1_st'];
$aData['pump_seq_2_st'] = $sResponse['pump_seq_2_st'];

$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);