<?php
//Turn off errors
error_reporting(E_ALL);
ini_set('display_errors', '0');

//Put errors in the errorlog
ini_set("log_errors", 1);
ini_set("error_log", "../errorlogs/error.log");

//Login data
$db_hostname = 'localhost';
$db_username = '/*Username*/';
$db_password = '/*Password*/';
$db_database = '/*DB name*/';

//Database connection variable
$mysqli = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);


//If there is no connection show an error code
if (!$mysqli) {
    echo "Fout: geen connnectie naar database. <br>";
    echo "Errno: " . mysqli_connect_errno() . "<br>";
    echo "Error: " . mysqli_connect_error() . "<br>";
    exit;
}
