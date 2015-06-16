<?php
/**
* @Programmer: SMW
* @Created: 12 Jun 2015
* @Modified: 
* @Description: Webservice to get temperature of sensors
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
$aData['controller_temp'] = $sResponse['controller_temp'];
$aData['temp_sensor_1'] = $sResponse['temp_sensor_1'];
$aData['temp_sensor_2'] = $sResponse['temp_sensor_2'];
$aData['temp_sensor_3'] = $sResponse['temp_sensor_3'];
$aData['temp_sensor_4'] = $sResponse['temp_sensor_4'];
$aData['temp_sensor_5'] = $sResponse['temp_sensor_5'];

$aResult['data'] = $aData;

#OUTPUT
echo json_encode($aResult);