<?php
// Database connection variables
$db_host = 'localhost';
$db_user = 'admin';
$db_password = 'Password123';
$db_name = 'ecommercedb';

// Create connection
$con = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if(!$con){
    die(mysqli_error($con));
}
?>