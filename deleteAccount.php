<?php
session_start();

include ("connect.php");

$id = $_GET['id'];

$q = mysql_query("delete from `YOUR_TABLE_NAME` where id=$id");

header('Location: index.php');
?>