<?php
session_start();
$_SESSION['uid']="";
$_SESSION['access_token']="";
$_SESSION['username']="";
session_destroy();
header('Location: index.php');
?>