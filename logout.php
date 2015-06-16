<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: Logout Current Session
**/
session_start();
unset($_SESSION['relayboard']);
session_unset();
session_destroy();
header('Location: ./');
exit;
?>