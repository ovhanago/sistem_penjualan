<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$host = "hayabusa.proxy.rlwy.net";
$user = "root";
$pass = "DMvXGKbzVCmxKrQOeXClGKMckpscCgAY";
$db   = "railway";
$port = 15624;

$conn = mysqli_connect($host,$user,$pass,$db,$port);

if(!$conn){
    die(mysqli_connect_error());
}

echo "DATABASE CONNECTED";