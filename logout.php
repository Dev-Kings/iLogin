<?php
session_start();

//Unset all of session variables
$_SESSION=array();

//Destroy session
session_destroy();

//Redirect to login page
header("location: login.php");
exit;
?>